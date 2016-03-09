<?php

require_once 'DB/dbmanager.php';
require_once 'DB/DAO/UsersDAO.php';

$dbmanager = new DBManager();
$userDAO = new UsersDAO($dbmanager);

$dbmanager->openConnection();

if(!empty($_GET['name']) && !empty($_GET['surname'])
&& !empty($_GET['email'])&& !empty($_GET['password']) ){
		
	$userDAO->insertUser($_GET);
	$dbmanager->closeConnection();
}
//header("Refresh:1; listUsers.php");
header('Location: listUsers.php');
?>