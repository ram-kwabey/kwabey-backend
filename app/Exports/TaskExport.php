<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements WithHeadings,FromQuery,WithMapping
{
    use Exportable;

    protected $type;

    function __construct($type)
    {
        $this->type = $type;
    }
    
    public function headings(): array
    {
        return [
            'id','name','status','assigned_to','lead_id','description','due_date'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        if(!Auth::user()->hasRole('Admin')){
            if($this->type == 'Archived'){
                return Task::onlyTrashed()->with('getstatus','leadId','assignedTo');
            }
            else{
                return Task::query()->where('assigned_to',Auth::id())->with('getstatus','leadId','assignedTo');
            }
           
        }
        else{
            if($this->type == 'Archived'){
                return Task::onlyTrashed()->with('getstatus','leadId','assignedTo');
            }
            else{
                return Task::query()->with('getstatus','leadId','assignedTo');
            }
            
        }
        
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->name,
            $task->getstatus ? $task->getstatus->status : '',
            $task->assignedTo ? $task->assignedTo->name : '',
            $task->lead_id,
            $task->description ? $this->convertJsonToString($task->description)  : '',
            $task->due_date,
        ];
    }

    private function convertJsonToString($arr){
        if($arr){
            $result = '';
            foreach($arr as $key => $value){
                if(strlen($key)){
                    $result .= $key.' : '.$value ." ";
                }else{
                    $result .= $value." ";
                } 
            }
            return $result;
        }
        return '';
    }
}
