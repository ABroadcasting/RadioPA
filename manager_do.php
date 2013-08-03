<?php
	ob_start();
	require_once('include.php');

	$auth = Autentification::create();
	$user = $auth->getUser();
	$security = Security::create();

	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$manager = Meneger::create();
	$manager->handler();

	Header ("Location: manager.php?fold=".$manager->getFolder()."&start=".$manager->getStart()."&search=".$manager->getSearch());
?>

