<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private $model;

    public function __construct(User $model)
    {
        //
        $this->model = $model;
    }
    public function register($params)
    {
        return $this->model->create($params);
    }
}
