<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ClientExport implements WithHeadings,FromQuery,WithMapping
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
            'id','name','email','phone'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $currentUserId = Auth::id();
        
        
        if(!Auth::user()->hasRole('Admin')){
            $data = Client::select(array(
                            'clients.id',
                            'clients.name',
                            'clients.email',
                            'clients.phone',
                        ))
                        ->leftJoin('leads', 'leads.client_id', '=', 'clients.id')
                        ->groupBy('clients.id');

            if(Auth::user()->hasRole('Team lead')){
                if($this->type == 'Archived'){
                    $data = $data->onlyTrashed();
                }
                else{
                    $data = $data->whereIn("leads.assigned_to",Auth::user()->getTeamMembersIds());
                }
                
                
            }else if(Auth::user()->hasRole('Setter')){
                if($this->type == 'Archived'){
                    $data = $data->onlyTrashed();
                }
                else{
                    $data->whereIn("leads.assigned_to", Auth::user()->getTeamLeadIds());
                }
                
                
            }else{
                if($this->type == 'Archived'){
                    $data = $data->onlyTrashed();
                }
                else{
                    $data = $data->where("leads.created_by", "=", $currentUserId)
                        ->orWhere("leads.assigned_to", "=", $currentUserId);
                }
                
                
            }
            
        }
        else{
            $data = Client::query();

            if($this->type == 'Archived'){
                $data = $data->onlyTrashed();
            }
            
        }
        return $data;
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->email,
            $client->phone
        ];
    }
}
