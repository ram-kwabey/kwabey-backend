<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use App\Repository\SizeRepository;
use App\Http\Requests\StoreSizeRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class SizeController extends Controller
{

    public function __construct(SizeRepository $sizeRepository)
    {
        $this->sizeRepository = $sizeRepository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $colorData = $this->sizeRepository->getDataWithPagination($request->all());
       return new DataTableCollectionResource($colorData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpdateSize(StoreSizeRequest $request)
    {
        try {
            $request['slug'] = str_replace(' ','_', $request->input('title'));
            
            $sizeData = $this->sizeRepository->createUpdateData(['id'=> $request->id],$request->all());
            
            $this->statusCode = 200;
            $this->msg = 'Size updated successfully.';
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
            $this->sizeRepository->getData(['id' => $id],'delete',[],0);
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
            $this->sizeRepository->getData(['id' => $id],'restore',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }
}
