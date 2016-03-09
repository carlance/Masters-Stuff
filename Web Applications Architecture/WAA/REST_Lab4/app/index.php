<?php
session_start ();

require_once "../Slim/Slim.php";
Slim\Slim::registerAutoloader ();
$app = new \Slim\Slim (); // slim run-time object
require_once "conf/config.inc.php"; // include configuration file

if (empty ( $_SESSION ["localUserList"] ))
	$_SESSION ["localUserList"] = array (); // initialitation of users container

$app->map ( "/users(/:id)", function ($elementID = null) use($app) {
	$body = $app->request->getBody (); // get the body of the HTTP request (from client)	
	$decBody = json_decode ( $body, true ); // this transform the string into an associative array
	var_dump($decBody);
	
	$httpMethod = $app->request->getMethod ();
	var_dump($httpMethod);
	// initialisations
	$responseBody = null;
	$responseCode = null;
	
	switch ($httpMethod) {
		case "GET" :
			if (empty ( $_SESSION )) {
				$respondeCode = HTTPSTATUS_NOCONTENT;
			} else {
				if ($elementID != null) {
					
					if (empty ( $_SESSION ["localUserList"] [$elementID] )) {
						$respondeCode = HTTPSTATUS_NOTFOUND;
					} else {
						$tmp = array();
						$tmp[$elementID] = $_SESSION ["localUserList"] [$elementID];
						
						$responseBody = $tmp;
						$respondeCode = HTTPSTATUS_OK;
					}
				} else {
					$responseBody = $_SESSION ["localUserList"];
					$respondeCode = HTTPSTATUS_OK;
				}
			}
			break;
		case "POST" :
			if (! empty ( $decBody ["name"] )) {
				$newName = $decBody ["name"];
				$newAlhanumericalID;
				do{
					$newAlhanumericalID = "i" . rand(0,100);
				}
				while(isset($_SESSION ["localUserList"] [$newAlhanumericalID]));
				
				$_SESSION ["localUserList"] [$newAlhanumericalID] = $newName; // push new element into array
				$respondeCode = HTTPSTATUS_OK;
			} else {
				$respondeCode = HTTPSTATUS_BADREQUEST;
			}
			break;
		case "PUT" :
			if ($elementID != null) {
				
				if (empty ( $_SESSION ["localUserList"] [$elementID] ))
					$respondeCode = HTTPSTATUS_NOTFOUND;
				else {
					$updatedName = $decBody ["name"];
					$_SESSION ["localUserList"][$elementID] = $updatedName; // update element into array
					$respondeCode = HTTPSTATUS_OK;
				}
			} else
				$respondeCode = HTTPSTATUS_BADREQUEST;
			break;
		case "DELETE" :
			if ($elementID != null) {
				if (empty ( $_SESSION ["localUserList"] [$elementID] ))
					$respondeCode = HTTPSTATUS_NOTFOUND;
				else {
					unset ( $_SESSION ["localUserList"] [$elementID] );
					$respondeCode = HTTPSTATUS_OK;
				}
			} else
				$respondeCode = HTTPSTATUS_BADREQUEST;
			break;
	}
	
	// return response to client
	
	if ($responseBody != null)
		$app->response->write ( json_encode ( $responseBody ) ); // this is the body of the response
			                                                         
	// TODO:we need to write also the response codes in the headers to send back to the client
	$app->response->status ( $respondeCode );
} )->via ( "GET", "POST", "PUT", "DELETE" );

$app->run ();
	
?>