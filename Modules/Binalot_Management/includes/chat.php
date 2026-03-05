<?php
include '../../../init.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";

$function = new BINALOTFunctions;

$cluster = $_SESSION['binalot_cluster'];
$branch = $_SESSION['binalot_branch'];
$acctname = $_SESSION['binalot_appnameuser'];
?>
<style>


.chat-container {
    width: 95%;
    max-width: 1200px;
    margin: auto;
    margin-top: 20px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.people-status {
    height: 500px;
    overflow-y: auto;
    border-right: 2px solid #ddd;
    padding: 15px;
    padding-top: 0px;
    position: relative;
}

.people-status h5 {
    position: sticky;
    top: 0;
    background: white;
    padding: 10px;
    border-bottom: 2px solid #ddd;
    z-index: 10;
}

.chat-box {
    height: 500px;
    display: flex;
    flex-direction: column;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    border-bottom: 2px solid #ddd;
}

.chat-messages p {
    font-size: 12px; /* Mas maliit na font */
    line-height: 1.2; /* Mas compact spacing */
}

.chat-messages small {
    font-size: 10px; /* Mas maliit na timestamp */
    color: gray;
}

.chat-input {
    display: flex;
    padding: 10px;
    background: #f1f1f1;
    border-radius: 0 0 10px 10px;
}

.chat-input input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.chat-input button {
    margin-left: 10px;
    padding: 10px 15px;
}

/* Responsiveness */
@media screen and (max-width: 768px) {
    .chat-container {
        padding: 10px;
    }

    .row {
        display: flex;
        flex-direction: column;
    }

    .people-status {
        height: auto;
        border-right: none;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
    }

    .chat-box {
        height: auto;
    }

    .chat-messages {
        height: 300px;
    }
}

</style>

<div class="chat-container">
    <div class="row">
        <div class="col-md-4 people-status">
            <h5>Online Users</h5>
            <ul class="list-group">
                
                
			<?php

				$sqlOnlineUsers = "SELECT acctname,branch FROM tbl_system_user WHERE binalot_online = 1";
				$resultOnlineUsers = $db->query($sqlOnlineUsers);
							
				if ($resultOnlineUsers->num_rows > 0) {
				    while ($row = $resultOnlineUsers->fetch_assoc()) {
				    	$uname = $row['acctname'];
						$branch =	$row['branch'];
						
				        echo "<li title='{$branch}' class='list-group-item' style='cursor:pointer'>{$uname} <i class='fa fa-circle' style='color:green' aria-hidden='true'></i></li>";
				    }
				} else {
				    echo "<li class='list-group-item'>Walay online users.</li>";
				}
			?>


                
            </ul>
        </div>

        <!-- Right Side: Chat Box -->
        <div class="col-md-8 chat-box">
            <h5><i class="fa fa-comments-o" aria-hidden="true"></i> Binalot Public Chat</h5>
            <div class="chat-messages" id="chatbox">

                <?php

                $sql = "SELECT sender, message, timestamp FROM binalot_chat_data ORDER BY timestamp ASC";	
                $result = $db->query($sql);		
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                    	
						$sender = $row['sender'];
						$message = $row['message'];
						$timestamp = $row['timestamp'];
						
                        echo "<p style='font-size: 12px; line-height: 1.2;'>
							        <strong title={$branch} style='cursor:pointer'>{$sender}:</strong> {$message} 
							        <small style='font-size: 10px; color: gray;'>({$timestamp})</small>
					    	</p>";

                    }
                } else {
                    echo "<p>No messages yet.</p>";
                }
                ?>                
            </div>
            
            <div id="chattextdata" class="chat-input">
                <input type="hidden" id="sender" value="<?php echo $acctname ?>">
                <input type="text" id="message" placeholder="Type a message..." autocomplete="off">
                <button class="btn btn-primary" id="send">Send</button>
            </div>
            <div id="offlinechat" class="chat-input" style="display:none">
            	<p style="text-align:right; color:red">CHAT SERVER IS OFFLINE</p>
            </div>
            
        </div>
    </div>
</div>

<script>

$(function(){
	
	if (!connbinalot || connbinalot.readyState !== WebSocket.OPEN) {
        $("#chattextdata").hide()
        $("#offlinechat").show()
    }
});


document.getElementById("message").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("send").click();
    }
});

//###--> Send nig message ###//
function sendMessage(message) {
    if (!connbinalot || connbinalot.readyState !== WebSocket.OPEN) {
        console.error("WB Dili pa open. Dili ka send message.");
        return;
    }
    connbinalot.send(message);
}

document.getElementById("send").addEventListener("click", function () {
    const sender = document.getElementById("sender").value;
    const messageInput = document.getElementById("message");
    const message = messageInput.value.trim();
    var tablename = "binalot_chat_data";
    
    console.log(sender+' ====');

    if (message !== "") {
        try {
//            console.log("Sending message:", message);

            //###--> 1. Ipakita dayon ang message sa sender ###//
            displayMessage(sender, message, new Date().toLocaleTimeString());

            //### Ipadala ang message ngadto sa WB ###//
            sendMessage(JSON.stringify({
                sender: sender, 
                message: message,
                tablename: tablename
            }));

            messageInput.value = ""; //###--> Clear input human mag-send ###//
        } catch (error) {
            console.error("Error sending message:", error);
            alert("Naay error sa pag-send sa message. Tan-awa ang console.");
        }
    } else {
        console.warn("Message is empty, not sending.");
    }
});

//###--> Function para magpakita og message sa sender ###//
function displayMessage(sender, message, timestamp) {
    const messageElement = document.createElement("p");
    messageElement.innerHTML = `<strong>${sender}:</strong> ${message} <small>(${timestamp})</small>`;

    document.getElementById("chatbox").appendChild(messageElement);
    document.getElementById("chatbox").scrollTop = document.getElementById("chatbox").scrollHeight;
}



connbinalot.onmessage = function (event) {
    try {
        const data = JSON.parse(event.data);

        if (data.sender && data.message) {
            console.log("Received message:", data);

            //###--> maghimo ug bag-o message element ###//
            const messageElement = document.createElement("p");
            messageElement.innerHTML = `<strong>${data.sender}:</strong> ${data.message} <small>(${data.timestamp})</small>`;

            //###--> isulod ang bag-ong message sa chatbox ###//
            document.getElementById("chatbox").appendChild(messageElement);

            //###--> Auto-scroll pababa para makita ang latest chat ###//
            document.getElementById("chatbox").scrollTop = document.getElementById("chatbox").scrollHeight;
        }
    } catch (error) {
        console.error("Error parsing WB message:", error);
    }
};



</script>
