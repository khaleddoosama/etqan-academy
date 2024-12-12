<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentApprovedNotification extends Notification
{
    use Queueable, NotificationToArray;

    private $course_slug;
    private $cours_title;
    private $payment;

    public function __construct(string $course_slug, string $cours_title, $payment)
    {
        $this->course_slug = $course_slug;
        $this->cours_title = $cours_title;
        $this->payment = $payment;
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
        $pdf = Pdf::loadView('invoice.payment_approved', ['payment' => $this->payment]);

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
