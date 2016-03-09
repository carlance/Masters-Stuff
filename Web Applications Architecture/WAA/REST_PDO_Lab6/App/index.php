<?php
require_once 'DB/pdoDbmanager.php';
require_once 'DB/DAO/UsersDAO.php';

require_once "../Slim/Slim.php";
Slim\Slim::registerAutoloader ();
$app = new \Slim\Slim (); // slim run-time object
require_once "conf/config.inc.php"; // include configuration file

$app->map ( "/users(/:id)", function ($elementID = null) use($app) {
	$body = $app->request->getBody (); // get the body of the HTTP request (from client)
	$decBody = json_decode ( $body, true ); // this transform the string into an associative array
	var_dump ( $decBody );
	
	$pdoDbmanager = new pdoDbManager ();
	$usersDAO = new UsersDAO ( $pdoDbmanager );
	
	$httpMethod = $app->request->getMethod ();
	var_dump ( $httpMethod );
	
	// initialisations
	$responseBody = null;
	$responseCode = null;
	
	
	switch ($httpMethod) {
		case "GET" :
			$pdoDbmanager->openConnection ();
			$result = $usersDAO->getUsers ();
			$responseCode = HTTPSTATUS_OK;
			// echo '{"name": '. json_encode($usersDAO) . '}' ;
			foreach ( $result as $key => $value ) {
				echo "<li>" . $value ['name'] . " " . $value ['surname'] . " " . $value ['email'] . "</li>";
			}
			$pdoDbmanager->closeConnection ();
			
			break;
		case "POST" :
			$pdoDbmanager->openConnection ();
			/*$name = $decBody ["name"];
			$surname = $decBody ["surname"];
			$email = $decBody ["email"];
			$password = $decBody ["password"];
			
			$result = $usersDAO->insertUser($name,$surname,$email,$password);*/
			
			$result = $usersDAO->insertUser($decBody);
			
			$pdoDbmanager->closeConnection();
			
			
			
			break;
		case "PUT" :
			
			break;
		case "DELETE" :
			
			break;
	}
	
	
	// return response to client
	
	if ($responseBody != null)
		$app->response->write ( json_encode ( $responseBody ) ); // this is the body of the response
			                                                         
	// TODO:we need to write also the response codes in the headers to send back to the client
	$app->response->status ( $responseCode );
} )->via ( "GET", "POST", "PUT", "DELETE" );

$app->run ();
?>