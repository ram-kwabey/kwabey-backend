<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Repository\CategoryRepository;
use App\Http\Requests\StoreCategoryRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class CategoryController extends Controller
{

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $categoryData = $this->categoryRepository->getDataWithPagination($request->all());
        return new DataTableCollectionResource($categoryData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpdateCategory(StoreCategoryRequest $request)   
    {
        try {
            $request['slug'] = str_replace(' ','_', $request->input('name'));
            
            $categoryData = $this->categoryRepository->createUpdateData(['id'=> $request->id],$request->all());
            
            $this->statusCode = 200;
            $this->msg = 'Category updated successfully.';
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
            $this->categoryRepository->getData(['id' => $id],'delete',[],0);
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
    public function restoreCategory($id)
    { 
        try {
            $this->categoryRepository->getData(['id' => $id],'restore',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }

    public function geCategoryData(){
        try {
            $categoryData = $this->categoryRepository->getData([],'get',[],0);
            return response()->json(['status' => 'success','categoryData'=>$categoryData]);
        } catch (ModelNotFoundException $e) {            
            return response()->json(['status' => 'error',]);
        }
    }
}
