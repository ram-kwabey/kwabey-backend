<?php

namespace App\Exports;

use App\Models\TeamMembers;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class TeamMembersExport implements WithHeadings,FromQuery,WithMapping
{
    use Exportable;

    protected $id;
    
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function headings(): array
    {
        return [
            'id','closer','setter'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return TeamMembers::query()->with('teamLead','setter')->where('team_id', '=', $this->id);
    }

    public function map($team): array
    {
        return [
            $team->id,
            $team->teamLead->name,
            $team->setter->name
        ];
    }
}
