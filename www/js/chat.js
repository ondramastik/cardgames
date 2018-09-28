function generateMessageHTML(message) {
    return '<div class="message' + (message.isOtherSender ? ' other-sender' : '') + '">'
        + message.sender + ': ' + message.text + '</div>';
}

function insertMessage(message) {
    let messages = $('#chat-window .messages');

    messages.append(
        generateMessageHTML(message)
    );

    messages.animate({ scrollTop: messages.height() }, 0);
}

$(function () {
    var webSocket = IPub.WebSockets.WAMP.initialize('ws://' + {$serverIp} + ':8080');

    webSocket.on('socket/connect', function(session) {
        console.log('Successfully connected to chat!');

        session.subscribe('communication/chat/' + {$lobbyId}, function(uri, payload) {
            insertMessage(payload);
        });

        $('#chat-window .input-field .send-message').on('click', function() {
            let messageField = $('#chat-window .input-field textarea');

            session.publish('communication/chat/' + {$lobbyId}, messageField.val());
            messageField.val('');

            messageField.trigger('focus');
        });

        $('#chat-window .input-field textarea').on('keypress', function(event) {
            if(event.which === 13) {
                event.preventDefault();
                $('#chat-window .input-field .send-message').trigger('click');
            }
        });
    });

    webSocket.on('socket/disconnect', function(error){
        console.log('Disconnected for ' + error.reason + ' with code ' + error.code);
    });
})