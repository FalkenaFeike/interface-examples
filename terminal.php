<!DOCTYPE HTML>
<html>
<style>
* {
    margin: 0px;
    padding: 0px;
}

body {
    background-color: rgb(0, 0, 0);
}

#background {
    position: fixed;
    top: 35px;
    bottom: 0px;
    left: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    background-color: black;
    margin: 0px;
    padding: 0px;
}

#console {
    margin: 0px;
    padding: 0px;
}

#consoletext {
    color: rgb(255, 255, 255);
    font-family: Monospace;
    margin: 10px 0px 0px 10px;
}

#textinput {
    resize: none;
    margin: 0px 0px 10px 10px;
    border: none;
    outline: none;
    background-color: rgb(0, 0, 0);
    color: rgb(255, 255, 255);
    font-family: Monospace;
    width: calc(100% - 20px);
    overflow: hidden;
}

.deleteButton
{
   margin-right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
}

textarea {
   caret-color: rgb(0, 200, 0);
}
</style>
   <head>
	
      <script type="text/javascript">
         
         //websocket object
         var ws, username;

         // start a websocket client connecting to our local server
         function launchWebsocketClient()
         {
            if ("WebSocket" in window)
            {
               if(document.getElementById("username").value == "")
               {
                 alert("enter username");
               }
               else {
                  username = document.getElementById("username").value;
              

                     //open websocket on port 49152
                     ws = new WebSocket("ws://77.162.30.112:49152");
                     
                     ws.onopen = function()
                     {
                        document.getElementById("connectionState").innerHTML="Connected";
                        document.getElementById("connectionState").style.color="green";
                        //logText("Connection to server with url : " + ws.url);
                        document.getElementById("launch").disabled = true;

                        document.getElementById("username").disabled = true;
                        ws.send(JSON.stringify({"name": username, "type": "join"}));
                     };
                  
                     ws.onmessage = function (evt) 
                     { 
                        if(isJson(evt.data))
                        {
                           var messageData = JSON.parse(evt.data);
                           if(messageData.type == "delete")
                           {
                              deleteResponse(messageData.id);
                           }
                           else 
                           {
                              logResponse(messageData);
                           }
                        }
                        else 
                        {
                           addLine(evt.data);
                        }
                     };
                  
                     ws.onclose = function()
                     { 
                        ws.close();
                        document.getElementById("connectionState").innerHTML="Disconnected";
                        document.getElementById("connectionState").style.color="red";
                        document.getElementById("launch").disabled = false;
                        document.getElementById("username").disabled = false;

                        logText("Connection has been closed");
                     };

                  }
               }
            else
            {
               alert("WebSocket is NOT supported by your Browser!");
            }
         }

         //send data to server
         function sendDataToServer(message)
         {
            var serverSend = {"name": username, "message":message, "type": "message"};
            ws.send(JSON.stringify(serverSend));
         }

         //close websocket if exists
         function closeWebsocketClient()
         {
            if (typeof(ws)!="undefined")
               ws.close();
         }

         function checkInput() {
            var event = window.event || event.which;

            if (event.keyCode == 13) {
               event.preventDefault();
               // addLine(document.getElementById("textinput").value);
               sendDataToServer(document.getElementById("textinput").value);
               document.getElementById("textinput").value = "";
            }

            document.getElementById("textinput").style.height = (document.getElementById("textinput").scrollHeight) + "px";
         }

         function addLine(line) {

            var textNode = document.createTextNode(line);
            var linebreak = document.createElement('br');

            document.getElementById("consoletext").appendChild(textNode);
            document.getElementById("consoletext").appendChild(linebreak);
         }

         function logResponse(response)
         {
            var linebreak = document.createElement('br');
            var textContainer = document.createElement("response");

            if(response.username != null)
            {
               var textNode = document.createTextNode(response.username + ": " + response.message);
            }
            else 
            {
               var textNode = document.createTextNode(response.message);
            }
            textContainer.id = response.id;
            textContainer.style.color = response.color;

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "🗑️"; 
            deleteButton.classList.add("deleteButton");
            deleteButton.setAttribute("onclick", "javascript:deleteMessage("+response.id+")");

            textContainer.appendChild(deleteButton);
            textContainer.appendChild(textNode);
            textContainer.appendChild(linebreak);
            

            document.getElementById("consoletext").appendChild(textContainer);
         }

         function isJson(item) {
            item = typeof item !== "string"
               ? JSON.stringify(item)
               : item;

            try {
               item = JSON.parse(item);
            } catch (e) {
               return false;
            }

            if (typeof item === "object" && item !== null) {
               return true;
            }

            return false;
         }
         //display something in body with log id
         function logText(text)
         {
            var currentdate = new Date(); 
                  var datetime = "[" 
                   + currentdate.getHours() + ":"  
                   + currentdate.getMinutes() + ":" 
                   + currentdate.getSeconds() + "] ";
               addLine(datetime + text);
         }

         //clear all log
         function clearLog()
         {
            document.getElementById("consoletext").innerHTML="";
         }

         function deleteMessage(messageID)
         {
            var serverSend = {"id": messageID, "type": "delete"};
            ws.send(JSON.stringify(serverSend));
         }

         function deleteResponse(messageID)
         {
            document.getElementById(messageID).remove();
         }
        // launchWebsocketClient();
      </script>
	
   </head>
	
      <div id="main">
         <input type="button" onClick="javascript:launchWebsocketClient();" value="Launch websocket client" id="launch"/>
         <input type="button" onClick="javascript:closeWebsocketClient();" value="Close websocket client" />
         <input type="button" onClick="javascript:clearLog();" value="Clear log" />
         <input type="text" id="username" />

         <p>
            <span id="connectionState" style='color:red; margin: 10px;'>Disconnected</span> 
         </p>
      </div>

<div id="background">
    <div id="console">
        <p id="consoletext"></p>
        <textarea rows="1" id="textinput" onkeydown="checkInput();"></textarea>
    </div>
</div>