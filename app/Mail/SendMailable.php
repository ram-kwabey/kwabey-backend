<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        Log::info(print_r($this->data, true));
        $this->subject = $data['email_template']['subject'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->data['email_template']['alias'] == 'welcome_email'){
            
            $a = array('{first_name}','{email}','{password}');
            $b = array($this->data['name'],$this->data['email'],$this->data['password']);
        }
        if($this->data['email_template']['alias'] == 'lead_email'){
            
            $a = array('{assigned_to}','{lead_id}','{created_by}','{client_id}');
            $b = array($this->data['lead']['assigned']['name'],$this->data['lead']['id'],$this->data['lead']['creator']['name'],$this->data['lead']['client_id']);
        }
        if($this->data['email_template']['alias'] == 'task_email'){
            
            $a = array('{assigned_to}','{lead_id}','{status}');
            $b = array($this->data['name'],$this->data['lead_id'],$this->data['status']);
        }
        if($this->data['email_template']['alias'] == 'outcome'){
            
            $a = array('{assigned_to}','{lead_id}','{outcome}');
            // $b = array('tk','12','noshow');
            $b = array($this->data['lead']['assigned']['name'],$this->data['lead']['id'],$this->data['status']);
        }
        $this->data =  str_replace($a,$b,$this->data['email_template']['content']);
        return $this->html($this->data)->subject($this->subject);
    }
}
