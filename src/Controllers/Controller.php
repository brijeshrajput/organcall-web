<?php

namespace App\Controllers;

use Illuminate\View\Factory as ViewFactory;

class Controller
{
    protected $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    protected function view($viewName, $data = [])
    {
        return $this->viewFactory->make($viewName, $data);
    }
    
}
