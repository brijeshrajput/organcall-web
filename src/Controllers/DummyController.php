<?php

namespace App\Controllers;

use App\Services\DummyService;
use Illuminate\View\Factory as ViewFactory;

class DummyController extends Controller
{
    protected $dummyService;

    public function __construct(ViewFactory $viewFactory)
    {
        parent::__construct($viewFactory);
        $this->dummyService = new DummyService();
    }

    public function index()
    {
        $data = $this->dummyService->getData();

        //return $this->view('home', ['name' => 'Brijesh']);
        return $this->view('cloud.dummy', ['name' => 'Brijesh']);
    }
}
