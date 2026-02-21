<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\OTPNotification;
use App\Notifications\ActivityNotification;
use App\Services\Notification\TwilioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_notification_uses_sms_channel()
    {
        Notification::fake();

        $user = User::factory()->create(['phone' => '+1234567890']);
        $user->notify(new OTPNotification('123456'));

        Notification::assertSentTo(
            $user,
            OTPNotification::class,
            function ($notification, $channels) {
                return in_array('App\Channels\SMSChannel', $channels);
            }
        );
    }

    public function test_activity_notification_uses_whatsapp_channel()
    {
        Notification::fake();

        $user = User::factory()->create(['phone' => '+1234567890']);
        $user->notify(new ActivityNotification('Logged in from new device'));

        Notification::assertSentTo(
            $user,
            ActivityNotification::class,
            function ($notification, $channels) {
                return in_array('App\Channels\WhatsAppChannel', $channels);
            }
        );
    }

    public function test_twilio_service_sends_sms()
    {
        $this->mock(TwilioService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendSMS')
                ->once()
                ->with('+1234567890', 'Your OTP code is: 123456. Do not share this with anyone.');
        });

        $user = User::factory()->create(['phone' => '+1234567890']);
        $user->notify(new OTPNotification('123456'));
    }

    public function test_twilio_service_sends_whatsapp()
    {
        $this->mock(TwilioService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendWhatsApp')
                ->once()
                ->with('+1234567890', 'Activity Update: Account password changed');
        });

        $user = User::factory()->create(['phone' => '+1234567890']);
        $user->notify(new ActivityNotification('Account password changed'));
    }
}
