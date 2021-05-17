<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Notification;
use App\Models\NotificationPreference;

class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle( $event )
    {
        if( isset ( $event->notification_array )  )
        {
            /**
             * If you are not sure about the parameters in notification_array,
             * @see App\Events\SendNotification constructor
             */
            $notification_array = $event->notification_array;

            // Get user notification preferences 
            if(isset($notification_array['user_id'])){
                $notification_preferences = NotificationPreference::where([
                    'user_id'   => $notification_array[ 'user_id' ],
                    'type'      => $notification_array[ 'type' ]
                ])->first();
                // Send Email notifications if:
                if( isset(( $notification_array[ 'force_email' ] )) && ( $notification_array[ 'force_email' ] == true ) || 
                    $notification_preferences->preference == 'notification_email' ) // or if user has set notification preference on for email
                    {
                        // Handle send email notification to the user here
                        if( isset( $notification_array[ 'email_content' ] ) && $notification_array[ 'email_template' ] )
                        {
                            sendMail( $notification_array[ 'email_content' ], $notification_array[ 'email_template' ] );
                        }
                    }
            }
            if(isset($notification_array['setter_id'])){
                $notification_preferences = NotificationPreference::where([
                    'user_id'   => $notification_array[ 'setter_id' ],
                    'type'      => $notification_array[ 'type' ]
                ])->first();
                if( isset(( $notification_array[ 'force_email' ] )) && ( $notification_array[ 'force_email' ] == true ) || 
                    $notification_preferences->preference == 'notification_email' ) // or if user has set notification preference on for email
                    {
                        // Handle send email notification to the user here
                        if( isset( $notification_array[ 'email_content' ] ) && $notification_array[ 'email_template' ] )
                        {
                            sendMail( $notification_array[ 'email_content' ], $notification_array[ 'email_template' ] );
                        }
                    }
            }
            if(isset($notification_array['sdr'])){
                $notification_preferences = NotificationPreference::where([
                    'user_id'   => $notification_array[ 'sdr' ],
                    'type'      => $notification_array[ 'type' ]
                ])->first();
                if( isset(( $notification_array[ 'force_email' ] )) && ( $notification_array[ 'force_email' ] == true ) || 
                    $notification_preferences->preference == 'notification_email' ) // or if user has set notification preference on for email
                    {
                        // Handle send email notification to the user here
                        if( isset( $notification_array[ 'email_content' ] ) && $notification_array[ 'email_template' ] )
                        {
                            sendMail( $notification_array[ 'email_content' ], $notification_array[ 'email_template' ] );
                        }
                    }
            }

            // Create a notification
            if(isset($notification_array['user_id'])){
                Notification::create([
                    'user_id'       => $notification_array[ 'user_id' ],
                    'content'       => $notification_array[ 'content' ],
                    'model_name'    => $notification_array[ 'model_name' ],
                    'model_id'      => $notification_array[ 'model_id' ],
                ]);
            }
            
            if(isset($notification_array['setter_id'])){
                
                Notification::create([
                    'user_id'       => $notification_array[ 'setter_id' ],
                    'content'       => $notification_array[ 'content' ],
                    'model_name'    => $notification_array[ 'model_name' ],
                    'model_id'      => $notification_array[ 'model_id' ],
                ]);
            }
            if(isset($notification_array['sdr'])){
                Notification::create([
                    'user_id'       => $notification_array[ 'sdr' ],
                    'content'       => $notification_array[ 'content' ],
                    'model_name'    => $notification_array[ 'model_name' ],
                    'model_id'      => $notification_array[ 'model_id' ],
                ]);
            }
            
        }
    }
}
