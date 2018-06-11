var socket = require('socket.io');
var express = require('express');
var http = require('http');
var winston = require('winston');

var logger = new(winston.Logger)({
    transports: [
        new(winston.transports.Console)({'timestamp': true, 'colorize': true}),
        new(winston.transports.File)({filename: 'socket.log'})
    ]
});

var app = express();
var router = express.Router();
var server = http.createServer( app );

var enviroments = [{'socketID':'', 'enviromentID':''}];

var io = socket.listen( server );

function findIndex(search){
	var found;
	enviroments.forEach( function(value, i){
		if(value.enviromentID == search){
			found = i;
			return;
		}
	})

	return found;
} 

io.sockets.on( 'connection', function(client){
	client.on('history', function(data){
		if(data.type == 'registerEnviroment'){
			enviroments.splice(findIndex(data.enviromentID), 1);
			enviroments.push({'socketID':client.id, 'enviromentID':data.enviromentID});
			logger.info('New user connected');
			return;		
		}

		logger.info('New history');
		index = findIndex(data.details.enviromentID);
		if(index !== undefined){
			io.sockets.sockets[enviroments[index].socketID].emit('history', data);
		}
	});

	client.on('disconnect', function() {
		logger.info('Someone disconected');
		id = findIndex(client.id)
		if(id !== undefined){
			enviroments.splice(id, 1);
		}

    });
});

server.listen( 3002 );