<?php

namespace App\Exports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements WithHeadings,FromQuery,WithMapping
{
    use Exportable;
    
    public function headings(): array
    {
        return [
            'id','team_name','team_lead'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return Team::query()->with('teamLead');
    }

    public function map($team): array
    {
        return [
            $team->id,
            $team->name,
            $team->teamLead->name
        ];
    }
}
