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
	include '_config.php';
	#Get POST
	$namefile = $_FILES['music_file']['name'];
	$mail = $_POST['music_mail'];

	$filename = $namefile."_".$mail;
	#Making filename
	$filename = str_replace(".mp3", "", $filename);
	$filename = str_replace(".MP3", "", $filename);
	$filename = $filename.".mp3";

	#Deleting extra symbols
	$filename = htmlspecialchars($filename, ENT_QUOTES, "utf-8");

	$filename = $_SERVER["DOCUMENT_ROOT"]."/music/".TEMP_UPLOAD."/".$filename;

	#Saving file
	if (move_uploaded_file($_FILES['music_file']['tmp_name'], $filename)) {
		print "<h1>Файл загружен</h1><h4>Сейчас вы будите перемещены обратно</h4>";
	} else {
    	print "<h4>Загрузить файл не удалось</h4>";
	}


	#Redirecting back to
	$URL = "http://".$_SERVER['HTTP_HOST'];
    if (isset($_GET['back'])) {
        $URL = $_GET['back'];
    }
    if (isset($_POST['back'])) {
        $URL = $_POST['back'];
    }
?>
	<head>
		<link rel="stylesheet" href="/style.css" type="text/css" />
		<link rel="stylesheet" href="/element.css" type="text/css" />
		<meta http-equiv="Refresh" content="2; URL=<?php echo $URL; ?>">
	</head>