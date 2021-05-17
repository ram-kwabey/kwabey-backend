<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
	use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'team_lead_id'
    ];

    public function teamLead()
    {
        return $this->hasOne('App\User', 'id', 'team_lead_id');
    }

    public static function getTeamMembersIds($teamID)
    {
        $teamMembers = array();

        $teamsLead = json_decode(Team::where('id','=',$teamID)->pluck('team_lead_id'));

        
        $leadMembers = json_decode(TeamMembers::where('team_id',$teamID)
                    ->select('team_lead_id')
                    ->pluck('team_lead_id'));
       
        $setters = json_decode(TeamMembers::where('team_id',$teamID)
                    ->select('setter_id')
                    ->pluck('setter_id'));

        $teamMembers = array_merge($teamsLead,$leadMembers,$setters);
        
        return $teamMembers;
    }
}