<?php
	require_once('include.php');

	$nowplay = Nowplay::create();

	$seychasigraet = $nowplay->getCurrentPlaylist();
	$budetigrat = $nowplay->getNextPlaylist();
	$visual_playlist = $nowplay->getVisualPlaylist();
	$dinamika  = $nowplay->getDinamika();
    $musicLoadForm = $nowplay->getMusicLoadForm();
    
    if (defined('EXTERNAL_CHARSET')) {
        $seychasigraet = @iconv('utf-8', EXTERNAL_CHARSET, $seychasigraet);
    }
    
    if (defined('EXTERNAL_CHARSET')) {
        $budetigrat = @iconv('utf-8', EXTERNAL_CHARSET, $budetigrat);
    }
    
    if (defined('EXTERNAL_CHARSET')) {
        $visual_playlist = @iconv('utf-8', EXTERNAL_CHARSET, $visual_playlist);
    }
    
    if (defined('EXTERNAL_CHARSET')) {
        $musicLoadForm = @iconv('utf-8', EXTERNAL_CHARSET, $musicLoadForm);
    }
?>