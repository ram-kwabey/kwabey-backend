<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements WithHeadings,FromQuery,WithMapping
{
    use Exportable;

    protected $type;

    function __construct($type){
        $this->type = $type;
    }
    
    public function headings(): array
    {
        return [
            'id','source', 'product_id', 'pitch', 'revenue', 'cash_collected', 'deposit', 'date_closed', 'appointment_date','created_at','updated_at','client_id','created_by','outcome','assigned_to'
        ];
    }

    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {

        $currentUserId = Auth::id();
        $data= Lead::query()->with('outcomeStatus','sourceData','product','client','creator','assigned');
        
        if(!Auth::user()->hasRole('Admin')){
            if(Auth::user()->hasRole('Team lead')){
                if($this->type == 'Archived'){
                   $data = $data->onlyTrashed();
                }
                else{
                    $data = $data->whereIn("assigned_to",Auth::user()->getTeamMembersIds());
                }
            }
            else if(Auth::user()->hasRole('Setter')){
                if($this->type == 'Archived'){
                    $data = $data->onlyTrashed();
                }
                else{
                    $data = $data->whereIn("assigned_to", Auth::user()->getTeamLeadIds());
                }
                
            }else{
                if($this->type == 'Archived'){
                    $data = $data->onlyTrashed();
                }
                else{
                    $data = $data->where("created_by", "=", $currentUserId)
                            ->orWhere("assigned_to", "=", $currentUserId);
                }
                
            }
        }
        else{
            if($this->type == 'Archived'){
                $data = $data->onlyTrashed();
             }
             
        }
        return $data;
    }

    public function map($lead): array
    {
        return[
            $lead->id,
            $lead->sourceData ? $lead->sourceData->name : '',
            $lead->product ? $lead->product->name : '',
            $lead->pitch,
            $lead->revenue,
            $lead->cash_collected,
            $lead->deposit,
            $lead->date_closed,
            $lead->appointment_date,
            $lead->created_at,
            $lead->updated_at,
            $lead->client ? $lead->client->name : '',
            $lead->creator ? $lead->creator->name : '',
            $lead->outcomeStatus ? $lead->outcomeStatus->name : '',
            $lead->assigned ? $lead->assigned->name : ''
        ];
    }
}
