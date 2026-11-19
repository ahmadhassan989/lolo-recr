<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduled extends Notification
{
    use Queueable;

    public function __construct(public Interview $interview) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $type = ucfirst($this->interview->type).' interview';
        $name = trim(($notifiable->first_name ?? '').' '.($notifiable->last_name ?? ''));
        $message = (new MailMessage)
            ->subject('Interview invitation — '.config('app.name'))
            ->greeting('Hello '.($name !== '' ? $name : 'there').',')
            ->line('You are invited to a **'.$type.'** with '.config('app.name').'.')
            ->line('**Date & time:** '.$this->interview->interview_date->format('l, F j, Y \a\t g:i A'));

        if ($this->interview->interviewer) {
            $message->line('**Interviewer:** '.$this->interview->interviewer);
        }
        if ($this->interview->notes) {
            $message->line('**Notes:** '.$this->interview->notes);
        }

        return $message->line('Please reply to this email to confirm your attendance.');
    }
}
