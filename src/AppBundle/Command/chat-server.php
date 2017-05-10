<?php
use Ratchet\Server\IoServer;
use AppBundle\Websocket\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

//TODO:PRZENIŚĆ TEN SKRYPT DO AppBundle/Command/ServerCommand.php

/*
 *  Chat Script
 *
 *  Create I/O (Input/Output) server class.
 *  It stores all the established connections,
 *  mediates data sent between each client and our Chat application, and catches errors.
 *
 *  The new instance of Chat class then wraps the I/O Server class.
 *
 *  Finally, we tell the server to enter an event loop, listening for any incoming requests on port 8080.
 */


require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();