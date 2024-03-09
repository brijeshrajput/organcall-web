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


    public function getUserFromDB($userId)
    {
        try {
            // Retrieve user data from the database
            $snapshot = $this->database->getReference('/Users/' . $userId)->getSnapshot();

            if (!$snapshot->exists()) {
                // Return null if user data does not exist
                return [];
            }

            // Convert snapshot data to an array and return it
            $userData = $snapshot->getValue();

            // Prepare the user array with the same structure as the database
            $userArray = [
                'id' => $userData['id'],
                'fullName' => $userData['fullName'],
                'email' => $userData['email'],
                'bloodType' => $userData['bloodType'],
                'xp' => $userData['xp'],
                // Add other fields as needed
            ];

            return $userArray;
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error getting user data: ' . $e->getMessage();
            error_log($error);
            return null;
        }
    }

    public function getAllUsersFromDB()
    {
        try {
            // Retrieve all users data from the database
            $snapshot = $this->database->getReference('/Users')->getSnapshot();

            // Initialize an array to hold all users
            $allUsers = [];

            if (!$snapshot->exists()) {
                // Return null if user data does not exist
                return [];
            }

            // Loop through each user data
            foreach ($snapshot->getValue() as $userId => $userData) {
                // Prepare user array with the same structure as the database
                $userArray = [
                    'id' => $userId,
                    'fullName' => $userData['fullName'],
                    'email' => $userData['email'],
                    'bloodType' => $userData['bloodType'],
                    'xp' => $userData['xp'],
                    // Add other fields as needed
                ];

                // Add the user array to the array of all users
                $allUsers[] = $userArray;
            }

            return $allUsers;
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error getting all users data: ' . $e->getMessage();
            error_log($error);
            return null;
        }
    }


    public function getAllHospitalsFromDB()
    {
        try {
            // Retrieve all hospitals data from the database
            $snapshot = $this->database->getReference('/Hospitals')->getSnapshot();

            // Initialize an array to hold all hospitals
            $allHospitals = [];

            if (!$snapshot->exists()) {
                // Return null if user data does not exist
                return [];
            }

            // Loop through each user data
            foreach ($snapshot->getValue() as $userId => $userData) {
                // Prepare user array with the same structure as the database
                $userArray = [
                    'id' => $userId,
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'address' => $userData['address'],
                    'serviced' => $userData['serviced'],
                    // Add other fields as needed
                ];

                // Add the user array to the array of all users
                $allHospitals[] = $userArray;
            }

            return $allHospitals;
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error getting all hospitals data: ' . $e->getMessage();
            error_log($error);
            return null;
        }
    }


    public function getNotificationTokensFromDB()
    {
        try {
            // Retrieve all notification tokens data from the database
            $snapshot = $this->database->getReference('/notificationTokens')->getSnapshot();

            // Initialize an array to hold all notification tokens
            $notificationTokens = [];

            if (!$snapshot->exists()) {
                // Return null if user data does not exist
                return [];
            }

            // Loop through each user's notification token data
            foreach ($snapshot->getValue() as $userId => $token) {
                // Add the user ID and notification token to the array
                $notificationTokens[$userId] = $token;
            }

            return $notificationTokens;
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error getting notification tokens data: ' . $e->getMessage();
            error_log($error);
            return null;
        }
    }

    public function getUserTokenFromDB($userId)
    {
        try {
            // Retrieve the notification token for the specified user ID from the database
            $snapshot = $this->database->getReference('/notificationTokens/' . $userId)->getSnapshot();

            if (!$snapshot->exists()) {
                return null;
            }

            // Return the notification token
            return $snapshot->getValue();
        } catch (\Exception $e) {
            // Handle errors
            $error = 'Error getting notification token for user ID ' . $userId . ': ' . $e->getMessage();
            error_log($error);
            return null;
        }
    }

    public function sendNotification($title, $body, $topic = null)
    {
        return $this->notificationSender->sendNotification_old($title, $body, $topic);
    }

}