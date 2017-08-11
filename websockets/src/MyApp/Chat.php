<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

date_default_timezone_set('America/Bogota');

class Chat implements MessageComponentInterface {
    protected $clients;	

    public function __construct() {
        $this->clients = new \SplObjectStorage;		
    }

    public function onOpen(ConnectionInterface $conn) {        
        $this->clients->attach($conn);
		
        echo "New connection! ({$conn->resourceId})\n";	        
		
		$msg = new \stdClass;
		$msg->type = "id";
		$msg->id = $conn->resourceId;		
		$conn->send(json_encode($msg));	
    }
	
    private function hacerMensajeListaUsuarios() {
		$userListMsg = new \stdClass;
		$userListMsg->type = "userlist";
		$userListMsg->users = array();		
		
		foreach ($this->clients as $client) {
			$userListMsg->users[] = $client->username;
		}
		
		return $userListMsg;
	}
	
	private function enviarListaUsuariosTodos() {
		$userListMsg = $this->hacerMensajeListaUsuarios();
		foreach ($this->clients as $client) {
			$client->send(json_encode($userListMsg));
		}
	}

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
			
		$msg_json = json_decode($msg);
		
		switch($msg_json->type) {
			case "message":
			    $msg_json->name = $from->username;
				//TODO: falta sanitizar el texto del mensaje
				break;			
			case "username": 
			    //TODO: falta sanitizar el nombre del usuario
				$from->username = $msg_json->name;
				$this->enviarListaUsuariosTodos();
			    break;
		}

        foreach ($this->clients as $client) {            
			$client->send(json_encode($msg_json));            
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
		
		$this->enviarListaUsuariosTodos();
		
		$msg = new \stdClass;
		$msg->type = "desconexion";
		$msg->name = $conn->username;
        $msg->date = date('Y-m-d H:i:s');		
						
        foreach ($this->clients as $client) {
            //if ($conn !== $client) {}
			$client->send(json_encode($msg));
        }		
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}