<?php

namespace App\Traits;

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
        $buttons = '<a href="' . route('admin.payment_details.show', $payment->id) . '" target="_blank" class="btn btn-sm btn-success ml-2">
            <i class="fas fa-eye"></i>
        </a>';

        // Add quick action buttons for pending Instapay payments
        if ($payment->gateway === 'instapay' && $payment->status->value === 'pending') {
            $buttons .= $this->getQuickActionButtons($payment);
        }

        return $buttons;
    }

    private function getQuickActionButtons($payment): string
    {
        return '
        <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-approve-form" data-payment-id="' . $payment->id . '">
            ' . csrf_field() . '
            ' . method_field('PUT') . '
            <input type="hidden" name="status" value="paid">
            <button type="button" class="btn btn-sm btn-success quick-approve-btn" title="Quick Approve">
                <i class="fas fa-check"></i>
            </button>
        </form>
        <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-reject-form" data-payment-id="' . $payment->id . '">
            ' . csrf_field() . '
            ' . method_field('PUT') . '
            <input type="hidden" name="status" value="cancelled">
            <button type="button" class="btn btn-sm btn-danger quick-reject-btn" title="Quick Reject">
                <i class="fas fa-times"></i>
            </button>
        </form>';
    }
}
