<?php
namespace App\Repository;

use App\Repository\RepositoryInterface;
use App\Models\SubCategory;

class SubCategoryRepository implements RepositoryInterface
{
    private $model;

    public function __construct(SubCategory $SubCategory)
    {
        $this->model = $SubCategory;
    }

    //To create and update data
    public function createUpdateData($condition, $parameters)
    {
        return $resultSet = $this->model->updateOrCreate($condition, $parameters);
    }

    //To create data
    public function createData($data){
        return $resultSet = $this->model->create($data);
    }

    //To fetch data
    public function getData($conditions, $method, $withArr = [],$toArray)
    {
        $query = $this->model->whereNotNull('id');
        if(!empty($method)&& ($method == 'restore')){
                $query =$query->onlyTrashed();
        }
        if (!empty($conditions['id'])) {
            $query->where('id', $conditions['id']);
        }
        
        $resultSet = $query->$method();

        if (!empty($resultSet) && $toArray) {
            $resultSet = $resultSet->toArray();
        }
        return $resultSet;
    }

    //To fetch data with pagination
    public function getDataWithPagination($parameters)
    {      
        $searchValue = (!empty($parameters['search'])?$parameters['search']:'');
        $orderBy = (!empty($parameters['column'])?$parameters['column']:'id');
        $orderBydir = (!empty($parameters['dir'])?$parameters['dir']:'desc');
        $length = (!empty($parameters['length'])?$parameters['length']:env('PAGINATION_LENGTH'));
        $type = (!empty($parameters['type'])?$parameters['type']:'All');
        $query = $this->model;
        if(isset($searchValue) && !empty($searchValue)){
            $query = $query->where("name", "LIKE", "%$searchValue%");
        }
        if(isset($type) && $type == 'All'){
            $query = $query->where("deleted_at",NULL);
        }
        if(isset($type) && $type == 'Archived'){
            $query = $query->onlyTrashed();
        }
        if(isset($orderBy) && !empty($orderBy)&& !empty($orderBydir)){
            $query = $query->orderBy($orderBy,$orderBydir);
        }        
        $resultSet = $query->paginate($length);
        return $resultSet;
    }
}