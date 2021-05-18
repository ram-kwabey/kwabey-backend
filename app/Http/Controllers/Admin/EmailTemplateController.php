<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class EmailTemplateController extends Controller
{

    public function template_list(Request $request)
    {
        $searchValue = $request->input('search');
        $orderBy = $request->input('column');
        $orderBydir = $request->input("dir");
        $length = $request->input('length');
 
        $data = EmailTemplate::where("name", "LIKE", "%$searchValue%")
            ->orderBy($orderBy, $orderBydir)
            ->paginate($length);
        
        return new DataTableCollectionResource($data);
    }

    public function store(Request $request)
    {
        $requestData = $request->all();

        $data = $request->validate([
            'name' => 'required|min:3',
            'subject' => 'required|min:3',
            'content' => 'required|min:3',
        ]);

        $data['alias'] =  preg_replace('/[^a-zA-Z0-9_]/', '_',strtolower($data['name']));
        if(isset($requestData['variables'])){
            $data['variables'] = $requestData['variables'];
        }

        $EmailTemplate = EmailTemplate::create($data);
        return $EmailTemplate;
    }

    public function update(Request $request,$id)
    {
        $requestData = $request->all();

        $data = $request->validate([
            'name' => 'required|min:3',
            'subject' => 'required|min:3',
            'content' => 'required|min:3',
        ]);

        if(isset($requestData['variables'])){
            $data['variables'] = $requestData['variables'];
        }

        $EmailTemplate = EmailTemplate::where('id',$id);
        $EmailTemplate->update($data);

        return $requestData;
    }
}
