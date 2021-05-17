<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RolesRequest;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index(){
        return view('admin.pages.roles');
    }
    public function store(RolesRequest $request){
        $role = Role::create($request->validated());
        return response()->json(['success' => 'Role created successfully!']);
    }

    public function show_permissions(){
        return view('admin.pages.permissions');
    }
    
    public function store_permission(Request $request){

        $request->validate([
            'name' => ['required'],
        ]);
        $permission_name = trim($request->name, ' ');
        $permission_name = Str::slug($permission_name,'_');
        $permission = Permission::create(['name' => $permission_name]);
        $role = Role::where('name','Admin')->first();
        $permission->assignRole($role);
        return response()->json(['success' => 'Permission created successfully!']);
        
    } 

    public function getPermissions(){
        return Permission::all();
    }

    public function checkedPermissions(){
        return Role::with('permissions')->get();
        
    }
    public function updatePermissions(Request $request){
        $role = Role::where('name',$request->role)->first();
        $permissions = Permission::whereIn('name',$request->checkedPermissions)->get();
        $role->syncPermissions($permissions);
        return response()->json(['success' => 'Permissions updated successfully!']);
    }
}
