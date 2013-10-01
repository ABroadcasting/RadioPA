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
	include_once 'conf/config.php';
	include_once 'classes/class/RequestFilter.class.php';
	include_once 'classes/class/Filter.class.php';
	include_once 'classes/class/Autentification.class.php';
	include_once 'classes/class/Request.class.php';
	include_once 'classes/class/DateTime.class.php';
	include_once 'classes/class/MySql.class.php';
	include_once 'classes/class/Setting.class.php';
	include_once 'classes/class/FileManager.class.php';
	include_once 'classes/class/Ssh.class.php';
	include_once 'classes/class/Order.class.php';
	include_once 'classes/class/Security.class.php';
	include_once 'classes/class/Repeat.class.php';
	include_once 'classes/class/Statistic.class.php';
	include_once 'classes/class/Dj.class.php';
	include_once 'classes/class/Status.class.php';
	include_once 'classes/class/Tracklist.class.php';
	include_once 'classes/class/Nowplay.class.php';
	include_once 'classes/class/Playlist.class.php';
	include_once 'classes/class/Song.class.php';
	include_once 'classes/class/PlaylistAll.class.php';
	include_once 'classes/class/PlaylistEdit.class.php';
	include_once 'classes/class/Manager.class.php';
	include_once 'classes/class/AddTracks.class.php';
	include_once 'classes/class/Event.class.php';
?>