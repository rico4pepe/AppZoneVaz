<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class SubscriptionReminderSms extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['twilio'];
    }

    public function toTwilio($notifiable)
    {
        $message = "Hello {$this->user->name}, your {$this->user->plan} subscription expires on {$this->user->expires_at->format('M d, Y')}. Renew now!";

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio_number = env('TWILIO_PHONE_NUMBER');

        $client = new Client($sid, $token);
        $client->messages->create(
            $notifiable->phone_number, // User's phone number
            [
                'from' => $twilio_number,
                'body' => $message
            ]
        );
    }
}
