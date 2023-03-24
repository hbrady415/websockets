<?php
/*
Simple demo of websockets

To run open 2 browser tabs:
In 1 tab go to:          http://localhost/hunter/websockets/websockets.php
In the other tab go to:  http://localhost/hunter/websockets/websockets.html

You will see the websocket will repeatedly send back a timestamp of current time, this happens in the while loop below

To view the messages open inspector > network
Headers tab will contain Status Code: 101 Switching Protocols which is where make the connection

Because we are running 
*/
// Port 443 = SSL

$servaddress = "74.208.203.29";
$address = '0.0.0.0';
$port = 80;

// Create WebSocket.
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, $servaddress, $port);
socket_listen($server);
$client = socket_accept($server);

// Send WebSocket handshake headers.
$request = socket_read($client, 5000);
preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
$key = base64_encode(pack(
    'H*',
    sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
));
$headers = "HTTP/1.1 101 Switching Protocols\r\n";
$headers .= "Upgrade: websocket\r\n";
$headers .= "Connection: Upgrade\r\n";
$headers .= "Sec-WebSocket-Version: 13\r\n";
$headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
socket_write($client, $headers, strlen($headers));

// Send messages into WebSocket in a loop.
$cnt = 0;
while (true) {
    //if($cnt > 20) exit("Time elapsed");
    sleep(1);
    $content = 'Now: ' . time();
    $response = chr(129) . chr(strlen($content)) . $content;
    socket_write($client, $response);
    $cnt++;
}
?>