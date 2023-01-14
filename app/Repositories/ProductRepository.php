<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private $model;

    public function __construct(Product $model){
        $this->model = $model;
    }

    public function getAll($user_id){
        return $this->model->where('user_id',$user_id)->select('name as producto','price as precio','amount as cantidad')->get()->toArray();
    }

    public function create($params){
        return $this->model->create($params);
    }

    public function update($id, $params){
        return $this->model->where('user_id',$id)->update($params);
    }

    public function delete($id){
        return $this->model->where('id',$id)->delete();
    }
}
