<?php
# Radio Panel Alpha - is an OpenSource Radio Panel.
# Radio Panel Alpha will be base part of the complete Radio Streaming Administration tool (Open Radio Control Panel)
#
# Copyright (C) 2010-2013 by James Heinrich - http://www.getid3.org
# Copyright (C) 2010-2013 by Max S Alyohin - http://radiocms.ru
# Copyright (C) 2013 by OpenRCP - http://open-rcp.ru
#
#
# The contents of this file are subject to the Mozilla Public License
# Version 1.1 (the "License"); you may not use this file except in
# compliance with the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS"
# basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
# License for the specific language governing rights and limitations
# under the License.
#
# The Original Code is "RadioCMS".
#
# The Initial Developer of the Original Code is Max S Alyohin.
# Portions created by Initial Developer are Copyright (C) 2010-2013
# by Max S Alyohin. All Rights Reserved.
#
# Product contains getID3 project code are Copyright (C) 2010-2013 by
# James Heinrich. All Rights Reserved.
#
# Portions created by the OpenRCP Development Team (C) 2013 by
# Open Radio Control Panel. All Rights Reserved.
#
# The OpenRCP Home Page is:
#
#    http://open-rcp.ru
#
	require_once('include.php');

	$statistic = Statistic::create();
	$statistic->updateMain();
	$tracklist = Tracklist::create();
    
    /* Print live exept play (if we have connection to live) */
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
	$trackplay2 = isset($lastSongs[1]) ? $lastSongs[1] : ""; // as higher - as more time ago
	$trackplay1 = isset($lastSongs[0]) ? $lastSongs[0] : ""; // Now playing
?>
