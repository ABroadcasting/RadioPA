<?php
	ob_start();
	require_once('Include.php');

	$auth = Autentification::create();
	$user = $auth->getUser();
	$security = Security::create();

	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$meneger = Meneger::create();
	$meneger->handler();

	Header ("Location: meneger.php?fold=".$meneger->getFolder()."&start=".$meneger->getStart()."&search=".$meneger->getSearch());
?>

