<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Repository\SubCategoryRepository;
use App\Http\Requests\StoreSubCategoryRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class SubCategoryController extends Controller
{

    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $subCategoryData = $this->subCategoryRepository->getDataWithPagination($request->all());
        return new DataTableCollectionResource($subCategoryData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpdateSubCategory(StoreSubCategoryRequest $request)   
    {
        try {
            $request['slug'] = str_replace(' ','_', $request->input('name'));
            
            $subCategoryData = $this->subCategoryRepository->createUpdateData(['id'=> $request->id],$request->all());
            
            $this->statusCode = 200;
            $this->msg = 'Sub Category updated successfully.';
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
            $this->subCategoryRepository->getData(['id' => $id],'delete',[],0);
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
    public function restoreSubCategory($id)
    { 
        try {
            $this->subCategoryRepository->getData(['id' => $id],'restore',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }
}
