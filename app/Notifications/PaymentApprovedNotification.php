<?php

namespace App\Notifications;

use App\Enums\PaymentType;
use App\Services\StudentInstallmentService;
use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Attachment;
use Carbon\Carbon;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class PaymentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;

    private $course_slug;
    private $cours_title;
    private $payment;
    private $courseInstallment;

    public function __construct(string $course_slug, string $cours_title, $payment)
    {
        $this->course_slug = $course_slug;
        $this->cours_title = $cours_title;
        $this->payment = $payment;
        $this->courseInstallment = $payment->courseInstallment;
        $this->queue = 'high';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $formatterDecimal = new \NumberFormatter('ar', \NumberFormatter::DECIMAL);

        // Calculate the remaining amount
        $remaining = 0;
        if ($this->payment->payment_type == PaymentType::INSTALLMENT) {
            // Get the number of paid installments
            $studentInstallmentService = new StudentInstallmentService();
            $installmentPaidCount = $studentInstallmentService->getNumberOfInstallmentsPaid($this->payment->user_id, $this->payment->course_installment_id);

            // Calculate remaining installments
            $remaining_installments = $this->courseInstallment->number_of_installments - $installmentPaidCount;
            $installmentsAmount = $this->courseInstallment->installment_amounts;


            for ($i = 0; $i < $remaining_installments; $i++) {
                $remaining += $installmentsAmount[$this->courseInstallment->number_of_installments - $i - 1];
            }
        }


        $date = Carbon::parse($this->payment->approved_at)->locale('ar')->translatedFormat('d F Y');
        // $date = Carbon::parse($this->payment->approved_at)->locale('ar')->translatedFormat('Y-m-d');
        $day = Carbon::parse($this->payment->approved_at)->locale('ar')->translatedFormat('l');

        $include = '';
        $value = '';
        if ($this->courseInstallment->course->title == "السوبر جرافيك") {
            $include = "كورس الجرافيك ديزاين - كورس المونتاج - كورس الموشن جرافيك - كورس العمل الحر - كورس التسويق - مكتبة إتقان للجرافيك";
            $value = "الأشتراك فـي دبلومة السوبر جرافيك ( الدبلومة الشاملة للتصميم )";
        } elseif ($this->courseInstallment->course->title == "الميني جرافيك") {
            $include = "كورس الجرافيك ديزاين - كورس العمل الحر - مكتبة اتقان للجرافيك";
            $value = "الأشتراك فـي دبلومة الميني جرافيك ( الدبلومة المصغرة للتصميم )";
        }

        $data = [
            'date' => $date,
            'day' => $day,
            'branch' => '٦ اكتوبر',

            'name' => $this->payment->user->name,
            'phone' => $this->payment->user->phone,

            'amount' => $formatterDecimal->format($this->payment->amount),

            'remaining' => $formatterDecimal->format($remaining),

            'value' => $value,

            'include' => $include,

            'method' => trans('attributes.' . $this->payment->payment_method->value, [], 'ar'),
            'type' => trans('attributes.' . $this->payment->payment_type->value, [], 'ar'),

            'admin_name' => 'دينا موسى القاضي',
        ];
        $pdf = LaravelMpdf::loadView('invoice.payment_approved', $data);

        return (new MailMessage)
            ->subject('Access Approved: ' . $this->cours_title)
            ->line('Congratulations! You have been approved to access the course: ' . $this->cours_title)
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->course_slug)
            ->line('We hope you enjoy the learning experience!')
            ->line('Your invoice is attached below.')
            ->attachData($pdf->output(), 'invoice.pdf');
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
