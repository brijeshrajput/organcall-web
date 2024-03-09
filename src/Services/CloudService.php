<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class CloudService
{
    private $database;
    private $notificationSender;

    public function __construct()
    {
        $dbCon = new DbCon();
        $this->database = $dbCon->getDatabase();

        $this->notificationSender = new NotificationSender();
    }

    //Leaderboard function
    public function getLeaderboardData()
    {
        $users = [];

        // Retrieve data from the '/Users/' node
        $dataSnapshot = $this->database->getReference('/Users/')->getValue();

        // Iterate through each user
        foreach ($dataSnapshot as $userId => $userData) {
            $ldbResult = [
                'fullName' => $userData['fullName'],
                'id' => $userData['id'],
                'xp' => $userData['xp']
            ];
            $users[] = $ldbResult;
        }

        // Sort users based on XP
        usort($users, function ($a, $b) {
            return $b['xp'] - $a['xp'];
        });

        return $users;
    }

    //GetPosts function
    public function getPosts()
    {
        $posts = [];

        // Retrieve data from the '/Posts/' node
        $dataSnapshot = $this->database->getReference('/Posts/')->getValue();

        if ($dataSnapshot === null) {
            return $posts;
        }

        // Iterate through each post
        foreach ($dataSnapshot as $postId => $postData) {
            $posts[] = $postData;
        }

        // Sort posts based on dateStamp
        usort($posts, function ($a, $b) {
            return strtotime($a['dateStamp']) - strtotime($b['dateStamp']);
        });

        return $posts;
    }

    //EventAdded function
    public function handleEventAdded($eventId)
    {
        if ($eventId == null) {
            return false; // Return false if eventId is null
        }
        // Reference to the newly created event
        $eventRef = $this->database->getReference('/Events/' . $eventId);

        // Get event data
        $eventData = $eventRef->getValue();

        if ($eventData == null) {
            return false; // Return false if event data is null
        }

        // Determine message condition
        $messageCondition = '\'events\' in topics';

        // Construct message
        $message = [
            'data' => [
                'event' => json_encode($eventData)
            ],
            'condition' => $messageCondition
        ];

        // Send message
        $this->sendMessage($message);
        dump('Event added');
        return true; // Return true if handling is successful
    }

    //AlertAdded function
    public function handleAlertAdded($alertId)
    {
        // Reference to the newly created alert
        $alertRef = $this->database->getReference('/Alerts/' . $alertId);

        // Get alert data
        $alertData = $alertRef->getValue();

        // Reference to the hospital corresponding to the alert
        $hospitalRef = $this->database->getReference('/Hospitals/' . $alertData['owner']);

        // Get hospital data
        $hospitalData = $hospitalRef->getValue();

        if ($alertData == null || $hospitalData == null) {
            return false; // Return false if alert or hospital data is null
        }

        // Eyes, Kidneys, Lungs, Heart
        // Determine message condition based on blood type
        $bloodType = str_replace(['+', '-'], ['pos', 'neg'], $alertData['bloodType']);
        switch ($bloodType) {
            case 'Eyespos':
                $messageCondition = '\'Eyespos\' in topics';
                break;
            case 'Eyesneg':
                $messageCondition = '\'Eyesneg\' in topics';
                break;
            case 'Kidneyspos':
                $messageCondition = '\'Kidneyspos\' in topics';
                break;
            case 'Kidneysneg':
                $messageCondition = '\'Kidneysneg\' in topics';
                break;
            case 'Lungspos':
                $messageCondition = '\'Lungspos\' in topics';
                break;
            case 'Lungsneg':
                $messageCondition = '\'Lungsneg\' in topics';
                break;
            case 'Heartpos':
                $messageCondition = '\'Heartpos\' in topics';
                break;
            case 'Heartneg':
                $messageCondition = '\'Heartneg\' in topics';
                break;
            default:
                $messageCondition = ''; // Default condition
                break;
        }

        // Construct message
        $message = [
            'data' => [
                'alert' => json_encode($alertData),
                'hospital' => json_encode($hospitalData)
            ],
            'condition' => $messageCondition
        ];

        // Send message
        $this->sendMessage($message);
        dump('Alert added');
        return true; // Return true if handling is successful
    }

    //checking event dates and deleting expired events
    public function scheduledEventDateCheck()
    {
        // Get current server timestamp
        $currentTime = time();
        echo 'Server timestamp: ' . $currentTime . PHP_EOL;

        // Reference to the '/Events/' node
        $eventsRef = $this->database->getReference('/Events/');

        // Get all events
        $eventsSnapshot = $eventsRef->getValue();

        // Iterate through each event
        foreach ($eventsSnapshot as $eventId => $eventData) {
            $eventTime = strtotime($eventData['date'] . ' 20:00:00'); // Convert event date to timestamp

            if ($eventTime < $currentTime) {
                // Delete expired event
                $delRef = $this->database->getReference('/Events/' . $eventId);
                $delRef->remove()->getValue(); // Trigger the removal operation
                echo 'Deletion Succeeded for event with ID: ' . $eventId . PHP_EOL;
            } else {
                echo 'Event time: ' . $eventTime . PHP_EOL;
            }
        }

        return true; // Return true if the process is successful
    }

    //Delete all Alerts
    public function deleteAlerts()
    {
        // Reference to the '/Alerts/' node
        $delRef = $this->database->getReference('/Alerts/');

        // Remove all data under the '/Alerts/' node
        $delRef->remove()
            ->getValue(); // This is to trigger the removal operation

        return true; // Return true if deletion is successful
    }

    public function getAlerts()
    {
        $alerts = [];

        // Retrieve data from the '/Alerts/' node
        $dataSnapshot = $this->database->getReference('/Alerts/')->getValue();

        // Iterate through each alert
        foreach ($dataSnapshot as $alertId => $alertData) {
            $alerts[] = $alertData;
        }

        // Sort alerts based on dateCreated
        usort($alerts, function ($a, $b) {
            return strtotime($a['dateCreated']) - strtotime($b['dateCreated']);
        });

        return $alerts;
    }

    private function sendMessage($message)
    {
        $response = $this->notificationSender->sendMessage($message);
        
        if ($response == true){
            return true;
        }
        return $response;
    }

    private function sendNotification($title, $body, $topic = null, $messageCondition=null)
    {
        if(!$topic == null)
        {
            // Send notification with condition
            return $this->notificationSender->sendNotification($title, $body, $topic);
        } 
        else if(!$messageCondition == null)
        {
            // Send notification with topic
            return $this->notificationSender->sendNotification($title, $body, $messageCondition);
        } else {
            // Send notification with topic and condition
            //return $this->notificationSender->sendNotification($title, $body, $topic, $messageCondition);
            return false;
        }
        
    }
}
