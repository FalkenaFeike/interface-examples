<?php

class Socket
{
        private $port;
        private $host;
        private $socketHost;
        private $error = [];

        function __construct($port, $host)
        {
                $this->port = $port;
                $this->host = $host;
        }

        function create() {
                if($this->socketHost = socket_create(AF_INET, SOCK_STREAM, 6))
                {
                        $this->connect();
                        return true;
                }
                else 
                {
                        $this->error .= "Could not create socket \n";
                        return false;
                }
        }

        function connect() 
        {
                if(!$this->socketClient = socket_connect($this->socketHost, $this->host, $this->port))  $this->error .= "Could not connect to socket \n";  
        }

        function sendToBot($bot, $command) 
        {
                if($response = $this->write(json_encode(["BOT" => $bot, "CMD" => $command]))) return $response->server;
        }

        function sendToAll($command)
        {
                if($response = $this->write(json_encode(["BOT" => "ALL", "CMD" => $command]))) return $response->server;
        }

        function getBotList()
        {
                if($response = $this->write(json_encode(["CMD" => "BOTLIST"]))) return $response;
        }

        function startSPS()
        {
                if($response = $this->write(json_encode(["CMD" => "START_SPS"]))) return $response;
        }

        function customCommand($command)
        {
                if($response = $this->write(json_encode(["CMD" => $command]))) return $response;
        }

        function write($message)
        {
                if($this->create())
                {
                        if(socket_write($this->socketHost, $message . "\n", strlen($message) + 1))
                        {
                                if($response = socket_read($this->socketHost, 10000, PHP_NORMAL_READ))
                                {
                                        return json_decode($response);
                                }
                        }
                }

                return false;
        }

}

$socket = new Socket(49153, "77.162.30.112");

if(isset($_POST['sendButton']))
{
        if(isset($_POST['number']))
        {
        }
}
var_dump($socket->startSPS());

?>

<form method="POST">
        <input type="number" name="number">
        <input type="submit" name="sendButton">
</form>
<!DOCTYPE HTML>