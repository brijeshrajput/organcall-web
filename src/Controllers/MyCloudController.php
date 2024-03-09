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

    public function welcome($request)
    {
        $name = $request['name'];
        return $this->view('welcome', ['name' => $name]);
    }

    public function getAllUsers()
    {
        $users = $this->cloudService->getAllUsers();
        return $users;
    }
    
    public function getAllTokens()
    {
        $tokensData = $this->cloudService->getNotificationTokensFromDB();
        return $tokensData;
    }

    public function getUsersFromDB()
    {
        $users = $this->cloudService->getAllUsersFromDB();
        return $users;
    }

    public function getHospitalsFromDB()
    {
        $hospitals = $this->cloudService->getAllHospitalsFromDB();
        return $hospitals;
    }

    public function sendNotification()
    {
        $title = 'Test Notification';
        $body = 'This is a test notification';
        $topic = 'hospital'; // user, hospital

        $response = $this->cloudService->sendNotification($title, $body, $topic);
        return $response;
    }

    public function sendNotificationToTopic($request)
    {
        
        $topicv = $request['topic'];
        
        $title = 'Test Notification';
        $body = 'This is a test notification';
        $topic = 'hospital'; // user, hospital

        $response = $this->cloudService->sendNotification($title, $body, $topic);
        return $response;
    }

}
