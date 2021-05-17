<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function getUsersCount(Request $request){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $filterBy = $request->input('filterBy');

        if(isset($filterBy)){
            switch($filterBy){
                case 'month':
                    return response()->json(['results' => User::whereMonth('created_at', Carbon::now()->month)->count()]);
                    break;
                case 'week':
                    return response()->json(['results' => User::whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->count()]);
                    break;
                case 'today':
                    return response()->json(['results' => User::whereDate('created_at', Carbon::today())->count()]);
                    break;
                default:
                    return response()->json(['results' => User::count()]);
                    break;
            }
        }else{
            return response()->json(['results' => User::count()]);
        }
    }

    public function getAllLeads(Request $request){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

    	$currentUserId = Auth::user()->getId();
        $filterBy = $request->input('filterBy');

    	if(!Auth::user()->hasRole('Admin')){
            if(Auth::user()->hasRole('Team lead')){
                $data = Lead::whereIn("assigned_to",Auth::user()->getTeamMembersIds());
            }
            else if(Auth::user()->hasRole('Setter')){
                $data = Lead::whereIn("assigned_to", Auth::user()->getTeamLeadIds());
            }else{
                $data = Lead::where("created_by", "=", $currentUserId)
                        ->orWhere("assigned_to", "=", $currentUserId);
            }

            if(isset($filterBy)){
                switch($filterBy){
                    case 'month':
                        $data = $data->whereMonth('appointment_date', Carbon::now()->month);
                        break;
                    case 'week':
                        $data = $data->whereBetween('appointment_date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                        break;
                    case 'today':
                         $data = $data->whereDate('appointment_date', Carbon::today());
                        break;
                    default:
                        break;
                }
            }
            return response()->json(['results' => $data->get()]);
        }else{
            if(isset($filterBy)){
                switch($filterBy){
                    case 'month':
                        $data = Lead::whereMonth('appointment_date', Carbon::now()->month);
                        break;
                    case 'week':
                        $data = Lead::whereBetween('appointment_date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                        break;
                    case 'today':
                        $data = Lead::whereDate('appointment_date', Carbon::today());
                        break;
                    default:
                        return response()->json(['results' => Lead::get()]);
                        break;
                }
                return response()->json(['results' => $data->get()]);
            }else{
                return response()->json(['results' => Lead::get()]);
            }
        }
    }

    public function getClientsCount(Request $request){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $currentUserId = Auth::user()->getId();
        $filterBy = $request->input('filterBy');

        $data = Client::select(array(
                            'clients.id',
                            'clients.name',
                            'clients.email',
                            'clients.phone',
                        ))
                        ->leftJoin('leads', 'leads.client_id', '=', 'clients.id')
                        ->groupBy('clients.id');

        if(!Auth::user()->hasRole('Admin')){

            if(Auth::user()->hasRole('Team lead')){
                $data = $data->whereIn("leads.assigned_to",Auth::user()->getTeamMembersIds());
            }else if(Auth::user()->hasRole('Setter')){
                $data->whereIn("leads.assigned_to", Auth::user()->getTeamLeadIds());
            }else{
                $data = $data->where("leads.created_by", "=", $currentUserId)
                        ->orWhere("leads.assigned_to", "=", $currentUserId);
            }
        }

        if(isset($filterBy)){
            switch($filterBy){
                case 'month':
                    $data = $data->whereMonth('leads.appointment_date', Carbon::now()->month);
                    break;
                case 'week':
                    $data = $data->whereBetween('leads.appointment_date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
                case 'today':
                    $data = $data->whereDate('leads.appointment_date', Carbon::today());
                    break;
                default:
                    break;
            }
        }

        $data = $data->get()->count();

        return response()->json(['results' => $data]);
    }

    public function getAllTask(Request $request){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $currentUserId = Auth::user()->getId();
        $filterBy = $request->input('filterBy');
        
        $data = Task::with( 'assignedTo','getstatus' )->select('status','assigned_to','id');
        
        if(!Auth::user()->hasRole('Admin')){
            if(Auth::user()->hasRole('Team lead')){
                $data = $data->whereIn("assigned_to",Auth::user()->getTeamMembersIds(true));
            }else{
                $data = $data->where("assigned_to",$currentUserId);
            }
        }

        switch($filterBy){
            case 'month':
                $data = $data->whereMonth('due_date', Carbon::now()->month);
                break;
            case 'week':
                $data = $data->whereBetween('due_date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                break;
            case 'today':
                $data = $data->whereDate('due_date', Carbon::today());
                break;
            default:
                break;
        }
            
        return response()->json(['results' => $data->get()]);
    }
}
