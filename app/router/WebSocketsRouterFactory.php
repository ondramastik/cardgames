<?php
namespace App;

use IPub\WebSockets\Router\Route;
use IPub\WebSockets\Router\RouteList;

class WebSocketsRouterFactory
{
	public static function createRouter() : RouteList
	{
		$router = new RouteList;
		
		$router[] = new Route('/communication/prsi/play/<gameId>', 'Prsi:');
		
		$router[] = new Route('/communication/chat/<lobbyId>', 'Chat:');
		
		return $router;
	}
}