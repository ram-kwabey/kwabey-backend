<?php
namespace App\Repository;

use App\Repository\RepositoryInterface;
use App\Models\Color;

class ColorRepository implements RepositoryInterface
{
    private $model;

    public function __construct(Color $Color)
    {
        $this->model = $Color;
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

        if (!empty($conditions['id'])) {
            $query->where('id', $conditions['id']);
        }

        if (!empty($resultSet) && $toArray) {
            $resultSet = $resultSet->toArray();
        }

        $resultSet = $query->select('id', 'name')->orderBy('name', 'asc')->$method();
        
        return $resultSet;
    }
}