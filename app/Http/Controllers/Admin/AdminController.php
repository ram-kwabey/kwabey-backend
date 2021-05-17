<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Team;
use App\Jobs\SendEmailJobs;
use App\Models\TeamMembers;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class AdminController extends Controller
{
    public function index(){
        
        return view('admin.pages.users');
    }

    public function users_list(Request $request){
        $searchValue = $request->input('search');
        $orderBy = $request->input('column');
        $orderBydir = $request->input("dir");
        $length = $request->input('length');
        $type = $request->input('type');

        $filterRole = $request->input('role');

        $roles = Role::all();

        $data = User::with('roles');

        if(isset($type) && $type == 'All'){
            $data = $data->where("deleted_at",NULL);
        }
        if(isset($type) && $type == 'Archived'){
            $data = $data->onlyTrashed();
        }
        
        if($filterRole){
            $data = $data->whereHas('roles', function ($query) use ($filterRole){
                $query->whereIn('id', [$filterRole]);
            });
        }

        $data = $data->where("name", "LIKE", "%$searchValue%")
            ->Where("email", "LIKE", "%$searchValue%")
            ->orderBy($orderBy, $orderBydir)
            ->paginate($length);
        
        return new DataTableCollectionResource($data);
    }

    public function store(StoreUserRequest $request){

        $email_template = EmailTemplate::where('alias','welcome_email')->first();
        $data = [
            'email'   => $request->input('email'), 
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'username' => $request->input('username'),
            'email_template' => $email_template,
            'password' => $request->password
        ];
        $data['name'] = $request->input('first_name').' '.$request->input('last_name');
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        $user->assignRole([$request->role]);
        $user->update(['email_verified_at' => date("Y-m-d h:i:s")]);

        $email_template = EmailTemplate::where('alias','welcome_email')->first();
        $data = [
            'email'   => $request->input('email'), 
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'username' => $request->input('username'),
            'email_template' => $email_template,
            'password' => $request->password
        ];
        $data['name'] = $request->input('first_name').' '.$request->input('last_name');
        //SendEmailJobs::dispatch($data)->delay(now()->addSeconds(5));
        return response()->json(['user' => $user]);
    }

    public function getRoles(){
        return response()->json(['results' => Role::where('name', '!=','Admin')->get()]);
    }
    public function deleteUser($id){
        try {
            User::findOrFail($id)->delete();
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }   

    public function updateUser(UpdateUserRequest $request, User $user){
        $user->update($request->validated());
        $user = $user->syncRoles([$request->role]);
        return response()->json(['success' => 'User updated successfully!']);
    }

    public function getAllUsers(){
        return response()->json(['results' => User::get()]);
    }

    public function restore_user($id)
    { 
        try {
            User::onlyTrashed()->find($id)->restore();
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }
}
