<?php

namespace App\Console\Commands;

use App\Mail\ProfilePhotoReminder;
use App\User;
use Mail;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEmailsProfilePhoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:profile_photo_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send photo upload reminder to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('active', 1)->get();
        foreach ($users as $user) {
            if ($user->profile()->getImageCount() > 0) {
                continue;
            }

            try {
                Mail::to($user)->send(new ProfilePhotoReminder($user));
                Log::info("Sent profile photo reminder to user " . $user->dataid);
            } catch (\Exception $e) {
                Log::info("Could not send reminder to user " . $user->dataid . ". Error: " . $e->getMessage());
            }
        }
    }
}
