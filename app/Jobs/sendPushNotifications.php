<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendPushNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $commonHelper;
    protected $pushNotificationData;

    public function __construct($commonHelper,$pushNotificationData)
    {
        $this->pushNotificationData = $pushNotificationData;
        $this->commonHelper = $commonHelper;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $users = User::all()->except($this->pushNotificationData['send_by']);
        if(!empty($user)){
            foreach($users as $user){
                $title = $this->pushNotificationData['title'];
                $message = $this->pushNotificationData['message'];
                $type = $this->pushNotificationData['type'];
                $notification_payload = $this->pushNotificationData['notification_payload'];
                $icon_type = $this->pushNotificationData['icon_type'];
                $send_by = $this->pushNotificationData['send_by'];
                $user = User::where('id', $user->id)->first();
                $this->commonHelper->sendNotification($title, $message, $type,$notification_payload, $icon_type, $send_by, $user);
            }
        }
    }
}
