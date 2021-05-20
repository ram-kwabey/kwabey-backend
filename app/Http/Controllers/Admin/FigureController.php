<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Figure;
use App\Repository\FigureRepository;
use App\Http\Requests\StoreFigureRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class FigureController extends Controller
{

    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
        
    }

    /**
     * Display a listing of the resource with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $figureData = $this->figureRepository->getDataWithPagination($request->all());
        return new DataTableCollectionResource($figureData);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpdateFigure(StoreFigureRequest $request)
    {
        try {
            $request['slug'] = str_replace(' ','_', $request->input('title'));
            
            $figureData = $this->figureRepository->createUpdateData(['id'=> $request->id],$request->all());
            
            $this->statusCode = 200;
            $this->msg = 'Figure updated successfully.';
        }
        catch (\Exception $e) {
            $this->statusCode = 400;
            $this->msg = $e->getMessage();
        }
        return response()->json(['message' => $this->msg], $this->statusCode);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            $this->figureRepository->getData(['id' => $id],'delete',[],0);
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
            $this->figureRepository->getData(['id' => $id],'restore',[],0);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }
}
