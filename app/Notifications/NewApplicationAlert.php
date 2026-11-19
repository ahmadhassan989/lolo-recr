<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationAlert extends Notification
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $candidate = $this->application->candidate;
        $name = trim(($candidate->first_name ?? '').' '.($candidate->last_name ?? ''));
        $jobTitle = $this->application->job->title ?? 'a position';

        return (new MailMessage)
            ->subject('New application — '.$jobTitle)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('**'.$name.'** applied for **'.$jobTitle.'**.')
            ->line('Email: '.$candidate->email)
            ->action('Review application', url('/applications/'.$this->application->id));
    }
}
