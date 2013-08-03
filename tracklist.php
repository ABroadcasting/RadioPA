<?php
	require_once('Include.php');

	$statistic = Statistic::create();
	$statistic->updateMain();
	$tracklist = Tracklist::create();
    
    /* отображать live вместо play (если есть подключение к live) */
    $tracklist->infoFromPoint('live');
	$tracklist->update();

	$status_listeners = $statistic->getListeners();

	$lastSongs = array();
	foreach ($tracklist->getLastTrackList(9) as $title) {
	    if (defined('EXTERNAL_CHARSET')) {
	        $title = @iconv('utf-8', EXTERNAL_CHARSET, $title);
	    } 		$lastSongs[] = $title;
	}

	$trackplay9 = isset($lastSongs[8]) ? $lastSongs[8] : "";
	$trackplay8 = isset($lastSongs[7]) ? $lastSongs[7] : "";
	$trackplay7 = isset($lastSongs[6]) ? $lastSongs[6] : "";
	$trackplay6 = isset($lastSongs[5]) ? $lastSongs[5] : "";
	$trackplay5 = isset($lastSongs[4]) ? $lastSongs[4] : "";
	$trackplay4 = isset($lastSongs[3]) ? $lastSongs[3] : "";
	$trackplay3 = isset($lastSongs[2]) ? $lastSongs[2] : "";
	$trackplay2 = isset($lastSongs[1]) ? $lastSongs[1] : ""; // то что выше - играло раньше
	$trackplay1 = isset($lastSongs[0]) ? $lastSongs[0] : ""; // сейчас играет
?>
