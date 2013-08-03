<?php

	include('include.php');

	$status = Status::create();

	$status->updateSetting(1);
    if (!$status->isIcecastRunned()) {
        $status->startIcecast();
    }
    if (!$status->isEzstreamRunned()) {
       	$status->startEzstream();
    }
?>
