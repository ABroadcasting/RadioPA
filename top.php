<?php
    ob_start();
	require_once('Include.php');

	$requestFilter = RequestFilter::create();
	$auth = Autentification::create();
	$request = Request::create();
	$dateTime = Date::create();
	$security = Security::create();
	$filter = Filter::create();
	$ssh = Ssh::create();

	/* --------------------------------------- */

	$auth->handler();
	$user = $auth->getUser();

	if (empty($user)) {
		include('Tpl/login.tpl.html');
		exit;	}

	/* Вы зашли как */
    if ($user['admin'] == 0) {    	$prava = "DJ";
    } else {    	$prava = "администратор";
    }

    include('Tpl/header.tpl.html');
?>