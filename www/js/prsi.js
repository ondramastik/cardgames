$(function () {
    var webSocket = IPub.WebSockets.WAMP.initialize('ws://' + {$serverIp} + ':8080');
    var globalSession = null;

    webSocket.on('socket/connect', function(session) {
        console.log('Successfully Connected!');

        globalSession = session;

        globalSession.subscribe('communication/prsi/play/{$game->getId()}', function(uri, payload) {
            console.log("Prisla zprava", payload);
            $.nette.ajax({
                url: {link play},
                off: ['publish']
            });
        });
    });

    webSocket.on('socket/disconnect', function(error){
        console.log('Disconnected for ' + error.reason + ' with code ' + error.code);
    });

    $.nette.ext('publish', {
        success: function () {
            if(globalSession) {
                console.log("Publishing");
                globalSession.publish('communication/prsi/play/{$game->getId()}', "played");
            }
        }
    });
})