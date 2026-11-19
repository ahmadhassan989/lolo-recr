<?php

namespace App\Notifications;

use App\Models\JobOffer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobOfferSent extends Notification
{
    use Queueable;

    public function __construct(public JobOffer $offer) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->offer->loadMissing(['candidate', 'project', 'creator']);
        $name = trim(($notifiable->first_name ?? '').' '.($notifiable->last_name ?? ''));

        $message = (new MailMessage)
            ->subject('Job offer — '.$this->offer->position_title.' at '.config('app.name'))
            ->greeting('Hello '.($name !== '' ? $name : 'there').',')
            ->line('Congratulations — we are pleased to offer you the position of **'.$this->offer->position_title.'**.');

        if ($this->offer->salary) {
            $message->line('**Salary:** '.number_format((float) $this->offer->salary, 2).' '.$this->offer->currency);
        }
        if ($this->offer->start_date) {
            $message->line('**Start date:** '.$this->offer->start_date->format('F j, Y'));
        }
        if ($this->offer->contract_duration) {
            $message->line('**Contract duration:** '.$this->offer->contract_duration);
        }

        $message->line('Your official offer letter is attached to this email.');

        try {
            $pdf = Pdf::loadView('pdf.job_offer', ['offer' => $this->offer]);
            $message->attachData($pdf->output(), 'JobOffer-'.$this->offer->id.'.pdf', ['mime' => 'application/pdf']);
        } catch (\Throwable $e) {
            report($e);
        }

        return $message;
    }
}
