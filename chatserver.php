<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require 'vendor/autoload.php';
require 'init.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $db;
    protected $users = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if ($this->db->connect_error) {
            die("Database Connection Failed: " . $this->db->connect_error);
        } else {
            echo "CHAT SERVER RUNNING, DO NOT CLOSE!\n";
        }

        $this->db->autocommit(true);
        
    	$this->db->query("UPDATE tbl_system_user SET dbc_online = 0, dbc_seasonal_online = 0, binalot_online = 0, fds_online = 0");
        
    }


	public function onOpen(ConnectionInterface $conn) {
	    $this->clients->attach($conn);
//	    echo "New connection! ({$conn->resourceId}) - Total Clients: " . count($this->clients) . "\n";
	
	    $queryString = $conn->httpRequest->getUri()->getQuery();
	    parse_str($queryString, $queryParams);
	
	    $allowedColumns = ['dbc_online', 'dbc_seasonal_online', 'binalot_online', 'fds_online'];
	
	    if (!empty($queryParams['username']) && !empty($queryParams['columnonline'])) {
	        $username = $this->db->real_escape_string($queryParams['username']);
	        $columnonline = $queryParams['columnonline'];
	
	        if (!in_array($columnonline, $allowedColumns)) {
	            echo "Invalid column name: $columnonline\n";
	            return;
	        }
	
	        $this->users[$conn->resourceId] = [
	            'username' => $username,
	            'columnonline' => $columnonline
	        ];
	
	        $sql = "UPDATE tbl_system_user SET `$columnonline` = 1 WHERE acctname = '$username'";
	
	        if ($this->db->query($sql)) {
//	            echo "$username-->$columnonline-->acive\n";
	        } else {
	            echo "Query Error: " . $this->db->error . "\n";
	        }
	    } else {
	        echo "Missing username or columnonline parameter.\n";
	    }
	}




    public function onMessage(ConnectionInterface $from, $msg) {
//        echo "Received raw message: $msg\n"; 

        $data = json_decode($msg, true);

        if (!empty($data['sender']) && !empty($data['message']) && !empty($data['tablename'])) {
        $sender = htmlspecialchars($data['sender']);
        $message = htmlspecialchars($data['message']);
        $tablename = htmlspecialchars($data['tablename']);
        $timestamp = date('Y-m-d H:i:s');

        // validation para iwas injection
        $allowedTables = ['dbc_chat_data', 'binalot_chat_data', 'fds_chat_data', 'dbc_seasonal_chat_data']; 
        if (!in_array($tablename, $allowedTables)) {
            echo "Invalid table name.\n";
            return;
        }
        
            $stmt = $this->db->prepare("INSERT INTO $tablename (sender, message, timestamp) VALUES (?, ?, ?)");

            if (!$stmt) {
                echo "SQL Prepare Failed: " . $this->db->error . "\n";
                return;
            }

            $stmt->bind_param("sss", $sender, $message, $timestamp);

            if ($stmt->execute()) {
//                echo "Message nasave na sa database: $sender - $message\n";

                // **Broadcast tanan clients except lang sa sender**
                $response = json_encode([
                    'sender' => $sender,
                    'message' => $message,
                    'tablename' => $tablename,
                    'timestamp' => $timestamp
                ]);

                $clientCount = 0;
                foreach ($this->clients as $client) {
                    if ($from !== $client) { // Prevent sa sender nga maka dawat ug duplicate
                        try {
                            $client->send($response);
                            $clientCount++;
                        } catch (Exception $e) {
                            echo "Failed to send message to client: " . $e->getMessage() . "\n";
                        }
                    }
                }
//                echo "Message broadcasted to $clientCount clients.\n";

            } else {
                echo "DB Insert Failed: " . $stmt->error . "\n";
            }

            $stmt->close();
        } else {
            echo "Invalid JSON format received.\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
    
    
    	if (isset($this->users[$conn->resourceId])) {
		    $username = $this->users[$conn->resourceId]['username'];
		    $columnonline = $this->users[$conn->resourceId]['columnonline'];
		
//		    var_dump($username);
//		    var_dump($columnonline);
		
		    $sql = "UPDATE tbl_system_user SET `$columnonline` = 0 WHERE acctname = '$username'";
		    
		    if ($this->db->query($sql)) {
//		        echo "$username is off $columnonline \n";
		    } else {
		        echo "Logout Query Error: " . $this->db->error . "\n";
		    }
		
		    unset($this->users[$conn->resourceId]);
		}
	}


    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(new WsServer(new Chat())),
    8080
);

$server->run();
