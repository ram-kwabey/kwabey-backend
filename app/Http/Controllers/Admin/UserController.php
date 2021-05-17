<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Method to get current user details
     * 
     * @return User - json (Authenticated User) 
     */
    public function editUserProfile()
    {
        $id = Auth::id();
        return User::where( 'id', '=', $id )
                ->with('roles')
                ->first();
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        if ($requestData['updateFor'] === 'password') {
                
                $data = $request->validate([
                    'password' => ['required', 'min:8'],
                    'confirmPassword' => 'same:password',
                ]);
                $data['password'] = Hash::make($request->password);
                $user = User::findOrFail($id);
                $user->update($data);

        }else{
                $data = $request->validate([
                    'first_name' => 'required|alpha',
                    'last_name' => 'required|alpha',
                    'username' => 'required',
                    'status' => 'sometimes',
                    // 'role' => 'required',
                    'email' => 'required|email|unique:users,email,'.$id,
                ]);
                if ( isset($requestData['image']) && !empty($requestData['image']) ) {
                    $image = $this->storeUserProfilePicture($requestData['image']);
                    $data['avatar'] = $image;
                    
                }
                $data['name'] = $data['first_name'].' '.$data['last_name'];
                unset($data['role']);
                $user = User::findOrFail($id);
                $user->update($data);
                // $user->syncRoles([$request->role]);
        }
    }

    
    /**
     * Stores user profile picture and replaces existing
     */
    public function storeUserProfilePicture($file,$id=null){

        $base64String = $file;
        list($type, $base64String) = explode(';', $base64String);
        $typeArr = explode(':', $type);
        $typeStr = $typeArr[1];
        $imgExt = explode('/', $typeStr);

        list(, $base64String)      = explode(',', $base64String);
        $base64String = base64_decode($base64String);

        $croppedImgName = 'image'.time().'.'.$imgExt[1];

    if(!empty($id)){
        $UserData = User::find($id);
        if(!empty($UserData->profile_picture)){
            $exists = Storage::disk('profile')->exists($UserData->profile_picture);
            if($exists == 1){
                Storage::disk('profile')->delete($UserData->profile_picture);
            }
        }
    }

    // $StoreFileName = Storage::disk('profile')->putFile('', $fileArr);
    Storage::disk('profile')->put($croppedImgName , $base64String);

    return $croppedImgName; 
    }

    public function handleUsersCsv(Request $request){
        $users = User::all();
        if($users->count()){
            return (new UsersExport($request->type))->download('users.csv');
         }
         else{
            return response()->json(['error' => 'Empty'],422);
         }
        
    }

}
