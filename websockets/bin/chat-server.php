<?php
use Ratchet\Server\IoServer;
use MyApp\Chat;

    require dirname(__DIR__) . '/vendor/autoload.php';

    /*$server = IoServer::factory(
        new Chat(),
        8090
    );

    $server->run();*/
	
	// Run the server application through the WebSocket protocol on port 8080
    $app = new Ratchet\App('localhost', 8090);
    $app->route('/chat', new Chat);
    $app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
    $app->run();