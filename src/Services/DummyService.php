<?php

namespace App\Services;

use App\Models\DummyModel;

class DummyService
{
    protected $dummyModel;

    public function __construct()
    {
        $this->dummyModel = new DummyModel();
    }

    public function getData()
    {
        return $this->dummyModel->getData();
    }
}
