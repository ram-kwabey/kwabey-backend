<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class UsersExport implements WithHeadings,FromQuery,WithMapping
{
    use Exportable;

    protected $type;

    function __construct($type) {
            $this->type = $type;
    }
    
    public function headings(): array
    {
        return [
            'id','name','email'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        if($this->type == 'Archived'){
            return User::onlyTrashed();
        }
        else{
            return User::query();
        }
        
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email
        ];
    }
}
