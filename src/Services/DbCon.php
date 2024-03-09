<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class DbCon
{
    private $database;
    private $firebase;
    private $auth;

    public function __construct()
    {
        try {
            $serviceAccount = SERVICE_ACCOUNT;
            //$serviceAccount = ROOT_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'serviceAccount.json';
            //$serviceAccount = '../config/serviceAccountKey.json'; // Path to your service account key JSON file
            $databaseUri = 'https://organ-call-9d948-default-rtdb.firebaseio.com/'; // Your Firebase Realtime Database URL
    
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->withDatabaseUri($databaseUri);

            $this->firebase = $firebase;
            $this->database = $firebase->createDatabase();
            $this->auth = $firebase->createAuth();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }

    public function getFirebase()
    {
        return $this->firebase;
    }
    
    public function getDatabase()
    {
        return $this->database;
    }

    public function getAuth()
    {
        return $this->auth;
    }
}
