<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param notification_array 
     * 
     * Sample Notification Array below:
     * $notification_array = [
     *      'model_name'    => 'Task',                                  // String: model name example: Task/Lead
     *      'model_id'      => '1',                                     // Int: id corresponding to the model_name 
     *      'type'          => 'tasks',                                 // String: This should be same as type of notification preferences
     *      'content'       => 'Hello! You have been assigned a task',  // String: The content of notification
     *      'user_id'       => 1,                                       // Int: Notification Receiver (User ID) here
     *      'email_content' => 'Sample Content',                        // Email Notification Content to pass in sendMail function
     *      'email_template'=> $emailTemplate,                          // Email Template which is to be used in sendMail function  
     *      'force_email'   => true,                                    // Boolean: If you want to send email notification regardless of the user notification preference
     *   ];
     * 
     * @return void
     */
    public function __construct( $notification_array )
    {
        $this->notification_array = $notification_array;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
