<?php

use App\Controllers\CloudController;
use App\Controllers\DeveloperController;
use App\Controllers\DummyController;
use App\Controllers\MyCloudController;
use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', [DummyController::class, 'index']);

    $r->addGroup('/cloud', function (RouteCollector $r) {

        // Define routes within the cloud section
        $r->addRoute('GET', '', [CloudController::class, 'index']);
        $r->addRoute('GET', '/leaderboard', [CloudController::class, 'getLeaderboardData']);
        $r->addRoute('GET', '/posts', [CloudController::class, 'getPosts']);
        $r->addRoute('GET', '/event/handle', [CloudController::class, 'handleEventAdded']);
        $r->addRoute('GET', '/event/check', [CloudController::class, 'scheduledEventDateCheck']);
        $r->addRoute('GET', '/alerts', [CloudController::class, 'getAlerts']);
        $r->addRoute('GET', '/alerts/handle', [CloudController::class, 'handleAlertAdded']);
        $r->addRoute('GET', '/alerts/delete', [CloudController::class, 'deleteAlerts']);


        $r->addRoute('GET', '/users', [MyCloudController::class, 'getAllUsers']);

    });

    // Define routes for the admin section
    $r->addGroup('/admin', function (RouteCollector $r) {
        // Define middleware for the admin section, if needed
        //$r->addMiddleware(...);

        // Define routes within the admin section
        $r->addRoute('GET', '', [DummyController::class, 'index']);
        $r->addRoute('GET', '/abc', [DummyController::class, 'index']);
    });

    $r->addRoute('GET', '/clear', [DeveloperController::class, 'clearViewsCache']);
};
