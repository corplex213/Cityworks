<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test notification for the first user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found in the database.');
            return 1;
        }
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'project_created',
            'title' => 'Test Notification',
            'message' => 'This is a test notification created via artisan command.',
            'link' => route('projects'),
            'read' => false,
        ]);
        
        $this->info('Test notification created successfully for user: ' . $user->name);
        $this->info('Notification ID: ' . $notification->id);
        
        return 0;
    }
} 