<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Faker\Factory as FakerFactory;

class MyCloudService
{
    private $database;
    private $auth;
    private $notificationSender;

    public function __construct()
    {
        $dbCon = new DbCon();
        $this->database = $dbCon->getDatabase();
        $this->auth = $dbCon->getAuth();
        $this->notificationSender = new NotificationSender();
    }


    //Get all users from Auth
    public function getAllUsers()
    {
        $users = array();

        try {
            // List all users
            $userRecords = $this->auth->listUsers();

            // Iterate through user records
            foreach ($userRecords as $userRecord) {
                $userData = array(
                    'uid' => $userRecord->uid,
                    'email' => $userRecord->email,
                    'displayName' => $userRecord->displayName
                    // You can include additional user data as needed
                );

                // Add user data to the array
                $users[] = $userData;
            }

            // Return the user data
            return $users;
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error listing users: ' . $e->getMessage();
            error_log($error);
            return $error;
        }
    }

    public function generateDummyUsers()
    {
        $numUsers = 1000; // Number of dummy users to generate

        try {
            // Initialize an array to hold promises
            $userPromises = [];

            // Generate dummy users asynchronously
            for ($i = 0; $i < $numUsers; $i++) {
                $faker = FakerFactory::create();
                $email = $faker->email();
                $password = $faker->password();
                $displayName = $faker->name();

                // Register dummy user and store the promise
                $userPromises[] = $this->auth->createUser([
                    'email' => $email,
                    'password' => $password,
                    'displayName' => $displayName,
                ]);
            }

            // Wait for all promises to settle
            $results = Promise\settle($userPromises)->wait();

            // Check for errors in results
            foreach ($results as $result) {
                if ($result['state'] === 'rejected') {
                    // Handle rejected promises (errors)
                    throw new \Exception('Error generating dummy user: ' . $result['reason']->getMessage());
                }
            }

            // Return success message
            return "Successfully generated $numUsers dummy users.";
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error generating dummy users: ' . $e->getMessage();
            error_log($error);
            return $error;
        }
    }


}