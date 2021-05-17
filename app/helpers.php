<?php
use App\Models\Lead;
use App\Models\Task;
use App\Jobs\SendEmailJobs;
use App\Models\EmailTemplate;
use App\Events\SendNotification;
use App\LeadStatusSuboutcome;

if(!function_exists('sendMail')){
    function sendMail($lead,$email_template){
        if($email_template->alias == 'lead_email'){
            $lead = Lead::where('id',$lead->id)->with('assigned','creator')->first();
            $data = [
                'lead' => $lead,
                'email_template' => $email_template,
                'email' => $lead->assigned->email
            ];
        }
        if($email_template->alias == 'task_email'){
            $task = Task::where('id',$lead->id)->with('getstatus','assignedTo')->first();
            $data = [
                'lead_id' => $task->lead_id,
                'email_template' => $email_template,
                'email' => $task->assignedTo->email,
                'name' => $task->assignedTo->name,
                'status' => $task->getstatus->status
            ];
        }
        if($email_template->alias == 'outcome'){
            $lead = Lead::where('id',$lead->id)->with('assigned','creator','outcomeStatus')->first();
            
            $data = [
                'lead' => $lead,
                'email_template' => $email_template,
                'email' => $lead->assigned->email,
                'status' => $lead->outcomeStatus->name
            ];
        }
        
        SendEmailJobs::dispatch($data)
                ->delay(now()->addSeconds(5));
    }
}
if(!function_exists('sendNotification')){
    function sendNotification($task){
        $task1 = Task::where('id',$task->id)->with('assignedTo','leadId')->first();
        $email_template = EmailTemplate::where('alias','task_email')->first();
                
            $notification_array = [
                'model_name'    => 'Task',
                'model_id'      => $task1->id ,
                'type'          => 'tasks',
                'content'       => $task1->leadId->outcomeStatus->name.' task created!',
                'user_id'       => $task1->assigned_to,
                'email_content' => $task,
                'email_template'=> $email_template
            ];
            event( new SendNotification( $notification_array ) );
    }
}