<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationSender
{
    private $messaging;

    public function __construct()
    {
        $dbCon = new DbCon();
        $this->messaging = $dbCon->getMessaging();
    }

    public function sendNotification($title, $body, $topic = null)
    {
        try {
            // Construct the notification message
            $notification = Notification::create($title, $body);

            // Construct the message
            $messageBuilder = CloudMessage::new();
            $messageBuilder->withNotification($notification);
            
            $message = CloudMessage::new();

            if ($topic !== null) {
                $messageBuilder->withTopic($topic);
            }

            // Send the message
            $this->messaging->send($messageBuilder->build());

            // Return a success response
            return 'Notification sent successfully';

        } catch (Exception $e) {
            // Log the error
            error_log('Error sending notification: ' . $e->getMessage());
            // Return an error response
            return 'Error sending notification: ' . $e->getMessage();
        }
    }

    public function sendDataMessage(array $data, $topic = null)
    {
        try {
            // Construct the message
            $messageBuilder = CloudMessage::new();

            foreach ($data as $key => $value) {
                $messageBuilder->withData($key, $value);
            }

            if ($topic !== null) {
                $messageBuilder->withTopic($topic);
            }

            // Send the message
            $this->messaging->send($messageBuilder->build());

            // Return a success response
            return 'Data message sent successfully';
        } catch (Exception $e) {
            // Log the error
            error_log('Error sending data message: ' . $e->getMessage());
            // Return an error response
            return 'Error sending data message: ' . $e->getMessage();
        }
    }

    public function sendNotification_old($title, $body, $topic = null)
    {
        try {
            // Construct the notification message
            $notificationData = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];

            if ($topic !== null) {
                $notificationData['topic'] = $topic;
            }

            $notification = CloudMessage::fromArray($notificationData);

            // Send the notification
            $this->messaging->send($notification);

            // Return a success response
            return 'Notification sent successfully';
        } catch (Exception $e) {
            // Log the error
            error_log('Error sending notification: ' . $e->getMessage());
            // Return an error response
            return 'Error sending notification: ' . $e->getMessage();
        }
        
    }
}
