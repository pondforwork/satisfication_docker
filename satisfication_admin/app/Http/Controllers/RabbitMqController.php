<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqController extends Controller
{
    private $connection;
    private $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('platform.buu.in.th', 8020, 'user', 'Pddd1245');
        // Open a channel
        $this->channel = $this->connection->channel();
    }

    public function sendFanoutMessage($messageBody)
    {
        // Declare a fanout exchange with the name 'my_fanout_exchange'
        $exchangeName = 'my_fanout_exchange';
        $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);

        // Create a new message
        $msg = new AMQPMessage($messageBody);

        // Publish the message to the fanout exchange
        $this->channel->basic_publish($msg, $exchangeName);

        echo " [x] Sent '{$messageBody}' to {$exchangeName}\n";

        // Optional: Close the channel and connection if you don't need them anymore
        $this->channel->close();
        $this->connection->close();
    }

    public function sendCheckUpdatetoClient()
    {
        try {
            $exchangeName = 'my_fanout_exchange';
            $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);
            $msg = new AMQPMessage("Update");
            $this->channel->basic_publish($msg, $exchangeName);
            echo " [x] Update Command sent to {$exchangeName}\n";
        } catch (\Exception $e) {
            // Handle the exception and log the error message
            echo " [!] Error: " . $e->getMessage() . "\n";
        } finally {
            // Ensure the channel and connection are closed, whether an error occurred or not
            if ($this->channel !== null) {
                $this->channel->close();
            }
            if ($this->connection !== null) {
                $this->connection->close();
            }
        }
    }

}
