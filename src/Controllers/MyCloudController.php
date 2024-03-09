<?php

namespace App\Controllers;

use App\Services\MyCloudService;
use Illuminate\View\Factory as ViewFactory;

class MyCloudController extends Controller
{
    protected $cloudService;

    public function __construct(ViewFactory $viewFactory)
    {
        parent::__construct($viewFactory);
        $this->cloudService = new MyCloudService();
    }

    public function index()
    {
        //$data = $this->cloudService->;
        return $this->view('welcome', ['name' => 'Brijesh']);
    }

    public function getAllUsers()
    {
        $users = $this->cloudService->getAllUsers();
        return $users;
    }
    
    public function getLeaderboardData()
    {
        $leaderboardData = $this->cloudService->getLeaderboardData();
        return $leaderboardData;
    }

}
