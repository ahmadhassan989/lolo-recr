<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $jobTitle = $job->title ?? 'the position';
        $name = trim(($notifiable->first_name ?? '').' '.($notifiable->last_name ?? ''));

        return (new MailMessage)
            ->subject('Application received — '.$jobTitle)
            ->greeting('Hello '.($name !== '' ? $name : 'there').',')
            ->line('We received your application for **'.$jobTitle.'** at '.config('app.name').'.')
            ->line('Our team will review your profile and get back to you if there is a match.')
            ->line('Application reference: #'.$this->application->id);
    }
}
