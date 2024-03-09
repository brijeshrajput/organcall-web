<?php

namespace App\Controllers;

use App\Services\CloudService;
use App\Services\MyCloudService;
use Illuminate\View\Factory as ViewFactory;

class CloudController extends Controller
{
    protected $cloudService;

    public function __construct(ViewFactory $viewFactory)
    {
        parent::__construct($viewFactory);
        $this->cloudService = new CloudService();
    }

    public function index()
    {
        //$data = $this->cloudService->;
        return $this->view('welcome', ['name' => 'Brijesh']);
    }

    public function getLeaderboardData()
    {
        $leaderboardData = $this->cloudService->getLeaderboardData();
        return $leaderboardData;
    }

    public function getPosts()
    {
        $posts = $this->cloudService->getPosts();
        return $posts;
    }

    public function handleEventAdded($eventId)
    {
        $this->cloudService->handleEventAdded($eventId);
    }

    public function handleAlertAdded($alertId)
    {
        $this->cloudService->handleAlertAdded($alertId);
    }

    public function scheduledEventDateCheck()
    {
        $this->cloudService->scheduledEventDateCheck();
    }

    public function deleteAlerts()
    {
        $this->cloudService->deleteAlerts();
    }

    public function getAlerts()
    {
        $alerts = $this->cloudService->getAlerts();
        return $alerts;
    }

}
