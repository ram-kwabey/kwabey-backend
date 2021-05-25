<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Repository\MaterialRepository;
use App\Http\Requests\StoreMaterialRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class MaterialController extends Controller
{

    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $materialData = $this->materialRepository->getDataWithPagination($request->all());
        return new DataTableCollectionResource($materialData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpdateMaterial(StoreMaterialRequest $request)
    {
        try {
            $request['slug'] = str_replace(' ','_', $request->input('title'));
            
            $materialData = $this->materialRepository->createUpdateData(['id'=> $request->id],$request->all());
            
            $this->statusCode = 200;
            $this->msg = 'Material updated successfully.';
        }
        catch (\Exception $e) {
            $this->statusCode = 400;
            $this->msg = $e->getMessage();
        }
        return response()->json(['message' => $this->msg], $this->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->materialRepository->getData(['id' => $id],'delete',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }

    /**
     * Get the specified trashed resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreUser($id)
    { 
        try {
            $this->materialRepository->getData(['id' => $id],'restore',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }
}
