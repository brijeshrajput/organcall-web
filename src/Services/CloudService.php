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
        // Reference to the newly created event
        $eventRef = $this->database->getReference('/Events/' . $eventId);

        // Get event data
        $eventData = $eventRef->getValue();

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

        // Determine message condition based on blood type
        $bloodType = str_replace(['+', '-'], ['pos', 'neg'], $alertData['bloodType']);
        switch ($bloodType) {
            case 'Apos':
                $messageCondition = '\'Apos\' in topics || \'Aneg\' in topics || \'Oneg\' in topics || \'Opos\' in topics';
                break;
            case 'Opos':
                $messageCondition = '\'Oneg\' in topics || \'Opos\' in topics';
                break;
            case 'Bpos':
                $messageCondition = '\'Bpos\' in topics || \'Bneg\' in topics || \'Oneg\' in topics || \'Opos\' in topics';
                break;
            case 'ABpos':
                $messageCondition = '\'Apos\' in topics || \'ABpos\' in topics || \'Opos\' in topics || \'Bpos\' in topics || \'Oneg\' in topics';
                break;
            case 'Aneg':
                $messageCondition = '\'Aneg\' in topics || \'Oneg\' in topics';
                break;
            case 'Oneg':
                $messageCondition = '\'Oneg\' in topics';
                break;
            case 'Bneg':
                $messageCondition = '\'Bneg\' in topics || \'Oneg\' in topics';
                break;
            case 'ABneg':
                $messageCondition = '\'ABneg\' in topics || \'Aneg\' in topics || \'Oneg\' in topics || \'Bneg\' in topics';
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

        return true; // Return true if handling is successful
    }

    private function sendMessage($message)
    {
        // Implement your logic to send message here
        // Example: use Firebase Cloud Messaging (FCM) or any other messaging service
        echo 'Message sent:', PHP_EOL;
        var_dump($message);
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
}
