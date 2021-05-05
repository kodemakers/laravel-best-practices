<?php

namespace App\Traits;

use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Throwable;

trait SendFcmNotification
{
    private function sendNotification($title, $message, $click_action, $tokens, $extra = [])
    {
        $logger = new Logger('Laravel-FCM');
        $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-fcm.log')));

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)
            ->setClickAction('FLUTTER_NOTIFICATION_CLICK')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();

        $default_values = ['title' => $title, 'body' => $message, 'click_action' => $click_action];
        $dataBuilder->addData(['data' => array_merge($default_values, $extra)]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $logger->info('notification send to ' . count($tokens) . ' devices' . PHP_EOL);

        try {
            if (count($tokens) > 0) {
                $res = FCM::sendTo($tokens, $option, $notification, $data);
              
                $logMessage = 'success: ' . $res->numberSuccess() . PHP_EOL;
                $logMessage .= 'failures: ' . $res->numberFailure() . PHP_EOL;
                $logMessage .= 'number of modified token : ' . $res->numberModification() . PHP_EOL;

                $logger->info($logMessage);
            }
          
            return true;
        } catch (\Throwable $th) {
            $logger->error($th);
            return true;
        }
    }

    public function sendTopicNotification($title, $message, $topic_title)
    {
        try {
            $logger = new Logger('Laravel-FCM');
            $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-fcm.log')));
          
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($message)
                ->setClickAction('FLUTTER_NOTIFICATION_CLICK')
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();

            $default_values = ['title' => $title, 'body' => $message, 'click_action' => 'GENERAL'];
            $dataBuilder->addData(['data' => array_merge($default_values)]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $topic = new Topics();
            $topic->topic($topic_title);

            $topicResponse = FCM::sendToTopic($topic, $option, $notification, $data);

            $logMessage = 'success: ' . $topicResponse->isSuccess() . PHP_EOL;
            $logMessage .= 'failures: ' . $topicResponse->error() . PHP_EOL;

            $logger->info($logMessage);
            
            return true;
        } catch (Throwable $th) {
            $logger->error($th);
            return true;
        }
    }
}
