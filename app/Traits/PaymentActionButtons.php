<?php

namespace App\Traits;

use App\Enums\PaymentStatusEnum;

trait PaymentActionButtons
{
    private function formatGateway($payment): string
    {
        $gateway = ucfirst($payment->gateway ?? 'fawaterak');

        if ($payment->gateway === 'instapay') {
            $badge = '<span class="badge badge-warning">Instapay</span>';
            if ($payment->status->value === 'pending') {
                $badge .= ' <span class="badge badge-info badge-sm">Needs Review</span>';
            }
            return $badge;
        }

        return '<span class="badge badge-primary">' . $gateway . '</span>';
    }

    private function getActionButtons($payment): string
    {
        // View button (always visible)
        $viewButton = '<a href="' . route('admin.payment_details.show', $payment->id) . '" target="_blank" class="btn btn-sm btn-success" title="View Details">
            <i class="fas fa-eye"></i>
        </a>';

        // Collect all action buttons
        $actionButtons = [];

        // Add quick action buttons for pending Instapay payments
        if ($payment->gateway === 'instapay') {
            $quickActions = $this->getQuickActionButtons($payment);
            if (!empty(trim($quickActions))) {
                $actionButtons[] = $quickActions;
            }
        }

        // Add update amount button for Instapay payments
        if ($payment->gateway === 'instapay') {
            $actionButtons[] = $this->getUpdateAmountButton($payment);
        }

        // Add update coupon button for all payments
        $actionButtons[] = $this->getUpdateCouponButton($payment);

        // Add update paid_at button for all payments
        $actionButtons[] = $this->getUpdatePaidAtButton($payment);

        // If no additional actions, just return view button
        if (empty($actionButtons)) {
            return $viewButton;
        }

        // Create responsive button structure
        return '
        <div class="btn-group" role="group">
            ' . $viewButton . '

            <!-- Desktop view: Show all buttons -->
            <div class="d-none d-lg-inline-flex">
                ' . implode('', $actionButtons) . '
            </div>

            <!-- Mobile view: Dropdown menu -->
            <div class="d-lg-none dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle ml-1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    ' . $this->getDropdownItems($payment) . '
                </div>
            </div>
        </div>';
    }

    private function getDropdownItems($payment): string
    {
        $dropdownItems = [];

        // Add quick action dropdown items for pending Instapay payments
        if ($payment->gateway === 'instapay') {
            // Show approve option only if status is not already 'paid'
            if ($payment->status->value !== PaymentStatusEnum::Paid->value) {
                $dropdownItems[] = '
                <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="dropdown-item-form quick-approve-form" data-payment-id="' . $payment->id . '">
                    ' . csrf_field() . '
                    ' . method_field('PUT') . '
                    <input type="hidden" name="status" value="paid">
                    <button type="button" class="dropdown-item quick-approve-btn">
                        <i class="fas fa-check text-success"></i> Approve Payment
                    </button>
                </form>';
            }

            // Show cancel option only if status is not already 'cancelled'
            if ($payment->status->value !== PaymentStatusEnum::Cancelled->value) {
                $dropdownItems[] = '
                <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="dropdown-item-form quick-reject-form" data-payment-id="' . $payment->id . '">
                    ' . csrf_field() . '
                    ' . method_field('PUT') . '
                    <input type="hidden" name="status" value="cancelled">
                    <button type="button" class="dropdown-item quick-reject-btn">
                        <i class="fas fa-times text-danger"></i> Reject Payment
                    </button>
                </form>';
            }

            // Add update amount for Instapay
            $dropdownItems[] = '
            <button type="button"
                    class="dropdown-item update-amount-btn"
                    data-payment-id="' . $payment->id . '"
                    data-current-amount="' . $payment->amount_confirmed . '"
                    data-expected-amount="' . $payment->amount_after_coupon . '">
                <i class="fas fa-edit text-warning"></i> Update Amount
            </button>';
        }

        // Add update coupon for all payments
        $dropdownItems[] = '
        <button type="button"
                class="dropdown-item update-coupon-btn"
                data-payment-id="' . $payment->id . '"
                data-current-coupon-id="' . ($payment->coupon_id ?? '') . '"
                data-current-coupon-code="' . (optional($payment->coupon)->code ?? 'No Coupon') . '"
                data-amount-before-coupon="' . $payment->amount_before_coupon . '">
            <i class="fas fa-tag text-info"></i> Update Coupon
        </button>';

        // Add update paid_at for all payments
        $dropdownItems[] = '
        <button type="button"
                class="dropdown-item update-paid-at-btn"
                data-payment-id="' . $payment->id . '"
                data-current-paid-at="' . ($payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '') . '">
            <i class="fas fa-calendar-alt text-secondary"></i> Update Payment Date
        </button>';

        return implode('', $dropdownItems);
    }

    private function getQuickActionButtons($payment): string
    {
        $buttons = '';

        // Show approve button only if status is not already 'paid'
        if ($payment->status->value !== PaymentStatusEnum::Paid->value) {
            $buttons .= '
            <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-approve-form" data-payment-id="' . $payment->id . '">
                ' . csrf_field() . '
                ' . method_field('PUT') . '
                <input type="hidden" name="status" value="paid">
                <button type="button" class="btn btn-sm btn-success quick-approve-btn" title="Quick Approve">
                    <i class="fas fa-check"></i>
                </button>
            </form>';
        }

        // Show cancel button only if status is not already 'cancelled'
        if ($payment->status->value !== PaymentStatusEnum::Cancelled->value) {
            $buttons .= '
            <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-reject-form" data-payment-id="' . $payment->id . '">
                ' . csrf_field() . '
                ' . method_field('PUT') . '
                <input type="hidden" name="status" value="cancelled">
                <button type="button" class="btn btn-sm btn-danger quick-reject-btn" title="Quick Reject">
                    <i class="fas fa-times"></i>
                </button>
            </form>';
        }

        return $buttons;
    }

    private function getUpdateAmountButton($payment): string
    {
        return '
        <button type="button"
                class="btn btn-sm btn-warning ml-1 update-amount-btn"
                title="Update Amount"
                data-payment-id="' . $payment->id . '"
                data-current-amount="' . $payment->amount_confirmed . '"
                data-expected-amount="' . $payment->amount_after_coupon . '">
            <i class="fas fa-edit"></i>
        </button>';
    }

    private function getUpdateCouponButton($payment): string
    {
        return '
        <button type="button"
                class="btn btn-sm btn-info ml-1 update-coupon-btn"
                title="Update Coupon"
                data-payment-id="' . $payment->id . '"
                data-current-coupon-id="' . ($payment->coupon_id ?? '') . '"
                data-current-coupon-code="' . (optional($payment->coupon)->code ?? 'No Coupon') . '"
                data-amount-before-coupon="' . $payment->amount_before_coupon . '">
            <i class="fas fa-tag"></i>
        </button>';
    }

    private function getUpdatePaidAtButton($payment): string
    {
        return '
        <button type="button"
                class="btn btn-sm btn-secondary ml-1 update-paid-at-btn"
                title="Update Paid At"
                data-payment-id="' . $payment->id . '"
                data-current-paid-at="' . ($payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '') . '">
            <i class="fas fa-calendar-alt"></i>
        </button>';
    }
}
