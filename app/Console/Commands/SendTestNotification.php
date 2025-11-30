<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {email : User email to send notification to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test push notification to a user';

    /**
     * Execute the console command.
     */
    public function handle(PushNotificationService $service): int
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found: {$email}");
            return Command::FAILURE;
        }

        $deviceCount = $user->deviceTokens()->active()->count();
        
        if ($deviceCount === 0) {
            $this->warn("User has no active device tokens registered.");
            $this->info("The mobile app needs to register a device token first.");
            return Command::FAILURE;
        }

        $this->info("Found {$deviceCount} active device(s) for {$user->name}");
        $this->info("Sending test notification...");

        $result = $service->sendToUser(
            $user,
            '🎉 Test Notification',
            'This is a test notification from Kiasi Daily!',
            ['type' => 'test']
        );

        if ($result['success']) {
            $this->info("✅ Notification sent successfully!");
            $this->info("Sent: {$result['sent_count']}, Failed: " . ($result['failed_count'] ?? 0));
        } else {
            $this->error("❌ Failed to send notification: " . ($result['message'] ?? 'Unknown error'));
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
