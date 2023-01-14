<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function getAll($user_id);

    public function create($params);

    public function update($id, $params);

    public function delete($id);

}
