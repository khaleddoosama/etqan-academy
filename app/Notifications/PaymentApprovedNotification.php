<?php

namespace App\Notifications;

use App\Enums\PaymentType;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Services\CourseInstallmentService;
use App\Services\StudentInstallmentService;
use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class PaymentApprovedNotification extends Notification
{
    use Queueable, NotificationToArray;
    private Payment $payment;
    private Collection $payment_items;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->payment_items = $payment->paymentItems;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formatterDecimal = new \NumberFormatter('ar', \NumberFormatter::DECIMAL);
        $mailMessage = (new MailMessage)
            ->subject('Access Approved')
            ->line('Congratulations! Your payment has been approved.');

        Log::info("From Notificatioin: " . $this->payment->amount);

        foreach ($this->payment_items as $payment_item) {
            Log::info("payment_item: " . $payment_item);
            $remaining = 0;

            // Check and calculate remaining installments
            if ($payment_item->payment_type == PaymentType::INSTALLMENT) {
                $studentInstallmentService = new StudentInstallmentService(new CourseInstallmentService());
                $installmentPaidCount = $studentInstallmentService->getNumberOfInstallmentsPaid(
                    $this->payment->user_id,
                    $payment_item->course_installment_id
                );

                $courseInstallment = $payment_item->courseInstallment;
                $remaining_installments = $courseInstallment->number_of_installments - $installmentPaidCount;
                $installmentsAmount = $courseInstallment->installment_amounts;

                for ($i = 0; $i < $remaining_installments; $i++) {
                    $remaining += $installmentsAmount[$courseInstallment->number_of_installments - $i - 1];
                }
            }

            $date = Carbon::parse(now())->locale('ar')->translatedFormat('d F Y');
            $day = Carbon::parse(now())->locale('ar')->translatedFormat('l');

            $include = '';
            $value = '';
            $course = $payment_item->course()->first();

            if ($course && $course->title == "السوبر جرافيك") {
                $include = "كورس الجرافيك ديزاين - كورس المونتاج - كورس الموشن جرافيك - كورس العمل الحر - كورس التسويق - مكتبة أورا للجرافيك";
                $value = "الأشتراك فـي دبلومة السوبر جرافيك";
            } elseif ($course && $course->title == "الميني جرافيك") {
                $include = "كورس الجرافيك ديزاين - كورس العمل الحر - مكتبة أورا للجرافيك";
                $value = "الأشتراك فـي دبلومة الميني جرافيك";
            } elseif ($payment_item->package_plan_id) {
                $include = "البرامج المذكورة على الموقع";
                $value = "تفعيل حساب " . $payment_item->packagePlan->title . " لمدة " . $payment_item->packagePlan->duration_text;
            }

            $data = [
                'date' => $date,
                'day' => $day,
                'branch' => '٦ اكتوبر',
                'name' => $this->payment->user->name,
                'phone' => $this->payment->user->phone,
                'amount' => $formatterDecimal->format($payment_item->amount),
                'remaining' => $formatterDecimal->format($remaining),
                'value' => $value,
                'include' => $include,
                'method' => trans('attributes.' . $this->payment->payment_method, [], 'ar'),
                'type' => trans('attributes.' . $payment_item->payment_type->value, [], 'ar'),
                'admin_name' => 'دينا موسى القاضي',
            ];

            $pdf = LaravelMpdf::loadView('invoice.payment_approved', $data);
            $mailMessage->attachData($pdf->output(), 'invoice_' . $payment_item->id . '.pdf');

            if ($course) {
                $mailMessage->line("Course: {$course->title}")
                    ->action('View Course', env('FRONTEND_URL') . 'courses/' . $course->slug);
            }

            $mailMessage->line("Included: " . $value);
        }

        $mailMessage->line('We hope you enjoy the learning experience!')
            ->line('Your invoices are attached.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
