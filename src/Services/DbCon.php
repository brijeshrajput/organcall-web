<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class DbCon
{
    private $factory;
    private $database;
    private $firebase;
    private $auth;
    private $messaging;

    public function __construct()
    {
        try {
            $serviceAccount = SERVICE_ACCOUNT;
            //$serviceAccount = ROOT_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'serviceAccount.json';
            //$serviceAccount = '../config/serviceAccountKey.json'; // Path to your service account key JSON file
            $databaseUri = 'https://organ-call-9d948-default-rtdb.firebaseio.com/'; // Your Firebase Realtime Database URL
                

            $this->factory = (new Factory)->withServiceAccount($serviceAccount);

            $this->firebase = $this->factory->withDatabaseUri($databaseUri);
            $this->database = $this->firebase->createDatabase();
            $this->auth = $this->factory->createAuth();
            $this->messaging = $this->factory->createMessaging();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }
    
    public function getDatabase()
    {
        return $this->database;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function getMessaging()
    {
        return $this->messaging;
    }
}
