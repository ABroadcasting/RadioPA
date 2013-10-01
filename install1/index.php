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
	ob_start();
	
    include('include.php');
	include_once('Install.class.php');
	$timezone_identifier = TIMEZONE_IDENIFIER;
    date_default_timezone_set($timezone_identifier);
	$request = Request::create();
	$ins = Install::create();

	$hag_install = "0 (Getting music path)";
	$hag = 0;
	if (!empty($_GET['hag'])) {
		if ($_GET['hag'] == 1) {$hag_install = "1 (Checking files and libraries)"; }
		if ($_GET['hag'] == 2) {$hag_install = "2 (Configuring the database)"; }
		if ($_GET['hag'] == 3) {$hag_install = "3 (Configuring the main data)"; }
		if ($_GET['hag'] == 4) {$hag_install = "4 (Configuring paths)"; }
		if ($_GET['hag'] == 5) {$hag_install = "5 (Password setup)"; }
		if ($_GET['hag'] == 6) {$hag_install = "6 (End of the installation process)"; }
	}

	$action = "?hag=$hag";
?>
<html>
	<head>
		<link rel="stylesheet" href="../templates/default/css/style.css" type="text/css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<style> form {margin:0;} </style>
	<title>Installation <?=ORCP_TITLE?></title>

	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="2" align="right"><img border="0" src="../templates/default/images/navi_01.jpg" width="1" height="122"></td>
				<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="324">
								<img border="0" src="../templates/default/images/navi_02.jpg" width="588" height="38"></td>
								<td background="../templates/default/images/navi_03.jpg" valign="top"><div class="navi_text"><?=IP?></a> | <?=date("H:i")?> | <a href="http://openrcp.ru/">Exit</a><br>Installing <?=ORCP_TITLE." "?><?=ORCP_VERSION?></div></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td background="../templates/default/images/navi_16.jpg"><img border="0" src="../templates/default/images/navi_05.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84"><img border="0" src="../templates/default/images/navi_07.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84"><img border="0" src="../templates/default/images/navi_09.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84"><img border="0" src="../templates/default/images/navi_11.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84"><img border="0" src="../templates/default/images/navi_17.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84"><img border="0" src="../templates/default/images/navi_13.jpg" width="100" height="84"><img border="0" src="../templates/default/images/navi_06.jpg" width="1" height="84">
						</tr>
				</table>
				</td>
				<td width="2" align="left"><img border="0" src="../templates/default/images/navi_04.jpg" width="1" height="122"></td>
			</tr>
		</table>

		<div class="body">
		<div class="title"><?php echo 'Installation: Step '.$hag_install;?></div>
		<div class="border">
		<form method="POST" action="<?php echo $action; ?>">
		
<?php
	if ($hag == 0) {
?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="15%" valign="top">Music Path:</td>
					<td width="75%" valign="top">
						<input type="text" name="cf_music" size="55" value="<?=$request->hasPostVar('music_path') ? $request->getPostVar('music_path') : MUSIC_PATH ?>"><br>
						<div class="podpis">full music folder path</div>
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag0")) {
			echo $ins->ifHag0();
		}
?>
			<p>
				<input class="button" type="button" value="Previous" name="B1" onClick="location.href='?hag=0'">
				<input class="button" type="submit" name="hag1" value="Next">
			</p>
<?php
	}
?>

<!-- ///////// 3 /////////////////////////////////////////////////////////////////// 3 ////////// -->
<?php
	if ($hag == 3) {
?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="15%" valign="top">IP-adress:<br>
					<div class="podpis">for ssh connection</div></td>
					<td width="75%" valign="top">
						<input type="text" name="ip" size="35" value="<?=$request->hasPostVar('ip') ? $request->getPostVar('ip') : IP ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">WEB-adress:<br>
					<div class="podpis">full site URL without / at the end</div></td>
					<td valign="top">
						<input type="text" name="url" size="35" value="<?=$request->hasPostVar('url') ? $request->getPostVar('url') : URL ?>">
					</td>
				</tr>
					<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Port:<br>
					<div class="podpis">stream port</div></td>
					<td valign="top">
						<input type="text" name="port" size="35" value="<?=$request->hasPostVar('port') ? $request->getPostVar('port') : PORT ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">SSH login:<br>
					<div class="podpis">root login</div></td>
					<td valign="top">
						<input type="text" name="ssh_user" size="35" value="<?=$request->hasPostVar('ssh_user') ? $request->getPostVar('ssh_user') : SSH_USER ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">SSH password:<br>
					<div class="podpis">root password</div></td>
					<td valign="top">
						<input type="password" name="ssh_pass" size="35" value="<?=$request->hasPostVar('ssh_pass') ? $request->getPostVar('ssh_pass') : SSH_PASS ?>">
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag3")) {
			echo $ins->ifHag3();
		}
?>
			<p>
				<input class="button" type="button" value="Previous" name=B1 onClick="location.href='install.php?hag=2'">
	 			<input class="button" type="submit" value="Next" name="hag3">
	 		</p>
<?php
	}
?>

<!-- ///////// 4 /////////////////////////////////////////////////////////////////// 4 ////////// -->

<?php
	if ($hag == 4) {
?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="15%" valign="top">IceCast configuration:</td>
					<td width="75%" valign="top">
						<input type="text" name="cf_icecast" size="55" value="<?=$request->hasPostVar('cf_icecast') ? $request->getPostVar('cf_icecast') : CF_ICECAST ?>"><br>
						<div class="podpis">full Icecast configuration path</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Ezstream configuration:</td>
					<td valign="top">
						<input type="text" name="cf_ezstream" size="55" value="<?=$request->hasPostVar('cf_ezstream') ? $request->getPostVar('cf_ezstream') : CF_EZSTREAM ?>"><br>
						<div class="podpis">full ezstream configuration path</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Playlist address:</td>
					<td valign="top">
						<input type="text" name="playlist" size="55" value="<?=$request->hasPostVar('playlist') ? $request->getPostVar('playlist') : PLAYLIST ?>"><br>
						<div class="podpis">full playlist path</div>
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag4")) {
			echo $ins->ifHag4();
		}
?>
			<p>
				<input class="button" type="button" value="Previous" name="B1" onClick="location.href='?hag=3'">
				<input class="button" type="submit" name="hag4" value=Продолжить>
			</p>
<?php
	}
?>

<!-- ///////// 5 /////////////////////////////////////////////////////////////////// 5 ////////// -->

<?php
	if ($hag == 5) {
?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="15%" valign="top">Login:</td>
					<td width="75%" valign="top">
						<input type="text" name="user" size="55" value="<?=USER?>"><br>
						<div class="podpis">used for the authorisation</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Password:</td>
					<td valign="top">
						<input type="password" name="password" size="55" value="<?=PASSWORD?>"><br>
						<div class="podpis">used for the authorisation</div>
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag5")) {
			echo $ins->ifHag5();
		}
?>
			<p>
				<input class="button" type="button" value="Previous" name="B1" onClick="location.href='?hag=4'">
				<input class="button" type="submit" name="hag5" value="Next">
			</p>
<?php
	}
?>

<!-- ///////// 2 /////////////////////////////////////////////////////////////////// 2 ////////// -->

<?php
	if ($hag == 2) {
?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="150" valign="top"><span lang="en-us">Server:</span><br>
					<div class="podpis">usually localhost</div></td>
					<td valign="top">
						<input type="text" name="db_host" size="35" value="<?=$request->hasPostVar('db_host') ? $request->getPostVar('db_host') : DB_HOST?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><span lang="en-us">Login:</span><br>
					<div class="podpis">enter the login</div></td>
					<td valign="top">
						<input type="text" name="db_login" size="35" value="<?=$request->hasPostVar('db_login') ? $request->getPostVar('db_login') : DB_LOGIN?>">
					</td>
					</tr>
					<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><span lang="en-us">Password:</span><br>
					<div class="podpis">enter the password</div></td>
					<td valign="top">
						<input type="password" name="db_password" size="35" value="<?=$request->hasPostVar('db_password') ? $request->getPostVar('db_password') : DB_PASSWORD?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Database:<br>
					<div class="podpis">SQL Database</div></td>
					<td valign="top">
						<input type="text" name="db_name" size="35" value="<?=$request->hasPostVar('db_name') ? $request->getPostVar('db_name') : DB_NAME?>">
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag2")) {
			echo $ins->ifHag2();
		}
?>
			<p>
				<input class="button" type="button" value="Previous" name="B1" onClick="location.href='?hag=1'">
	 			<input class="button" type="submit" value="Next" name="hag2">
	 		</p>
<?php
	}
?>


<?php
	if ($hag == 6) {
		$ins->addStatistic();
?>
			Congratulations! You are successfully installed <?=ORCP_TITLE?>.
			To finish the installation completely add current command to the cron (every 3 minutes):<br><br>
			<div class="border">
				<?=$ins->getWgetCron();?>
			</div>
			<br>
			Another variant of the command:<br><br>
			<div class="border">
				<?=$ins->getPhpCron();?>
			</div>
			<br>
			Please delete the install directory for the security reasons.
			<br><br>
			<input class="button" type="button" value="Admin panel" name="B1" onClick="location.href='index.php'">

<?php
	}
?>

<!-- ///////// 1 /////////////////////////////////////////////////////////////////// 1 ////////// -->

<?php
	if ($hag == 1) {
?>
			<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
				<tr>
					<td width="20%" valign="top">Description</td>
					<td width="15%" valign="top">Current status</td>
					<td width="65%" valign="top">Must be</td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Permissions of <b>music</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getPerms($request->getMusicPath())?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">writeable</span></td>
				</tr>
				<tr>
					<td valign="top">Permissions of <b>config.php</b></td>
					<td valign="top"><?=$ins->getPerms($request->getRadioPath()."../conf/config.php")?></td>
					<td valign="top"><span class="green">writeable</span></td>
				</tr>
				<tr>
					<td valign="top">Permissions of <b>system.php</b></td>
					<td valign="top"><b><?=$ins->getPerms($request->getRadioPath()."../conf/system.php")?></b></td>
					<td valign="top"><span class="green">writeable</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Variable <b>open_basedir</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getBaseDir()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">/ or no_value</span></td>
				</tr>
				<tr>
					<td valign="top">Library <b>libssh2</b></td>
					<td valign="top"><?=$ins->getSsh2()?></td>
					<td valign="top"><span class="green">installed</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Library <b>curl</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getCurl()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">installed</span></td>
				</tr>
				<tr>
                    <td valign="top">Library <b>SimpleXML</b></td>
                    <td valign="top"><?=$ins->getXML()?></td>
                    <td valign="top"><span class="green">installed</span></td>
                </tr>
				<tr>
					<td valign="top">Library <b>iconv</b></td>
					<td valign="top"><?=$ins->getIconv()?></b></td>
					<td valign="top"><span class="green">installed</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Library <b>gd2</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getGd()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">installed</span></td>
				</tr>
			</table>
	<br>
<?php
	if ($ins->ifHag1()) {
?>
			<input class="button" type="button" value="Next" name="B1" onClick="location.href='?hag=2'">
<?php
	} else {
?>
			Fix all the problems to continue the installation.
<?php
	}

}
?>
			</div>
		</div>
	</body>
<html>