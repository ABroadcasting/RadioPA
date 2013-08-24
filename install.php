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
	
	$product = "Radio Panel Alpha";
	$vers = "0.1.0";
    
    include('include.php');
    date_default_timezone_set($timezone_identifier);
	$request = Request::create();
	$ins = Install::create();

	$hag_install = "Установка: Шаг 1 (Проверка библиотек и файлов)";
	$hag = 1;
	if (!empty($_GET['hag'])) {
		if ($_GET['hag'] == 2) { $hag = 2; $hag_install = "Установка: Шаг 2 (Настройка базы данных)"; }
		if ($_GET['hag'] == 3) { $hag = 3; $hag_install = "Установка: Шаг 3 (Ввод основных данных)"; }
		if ($_GET['hag'] == 4) { $hag = 4; $hag_install = "Установка: Шаг 4 (Настройка путей)"; }
		if ($_GET['hag'] == 5) { $hag = 5; $hag_install = "Установка: Шаг 5 (Установка пароля панели управления)"; }
		if ($_GET['hag'] == 6) { $hag = 6; $hag_install = "Установка: Шаг 6 (Завершение установки)"; }
	}

	$action = "install.php?hag=$hag";
?>
<html>
	<head>
		<link rel="stylesheet" href="files/admin_style.css" type="text/css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<style> form {margin:0;} </style>
	<title>Установка <?php print $product;?></title>

	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="2" align="right"><img border="0" src="images/navi_01.jpg" width="1" height="122"></td>
				<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="324">
								<img border="0" src="images/navi_02.jpg" width="588" height="38"></td>
								<td background="images/navi_03.jpg" valign="top"><div class="navi_text"><?=IP?></a> | <?=date("H:i")?> | <a href="http://radiocms.ru/">Выход</a><br>Установка <?=$product." ".$vers?></div></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td background="images/navi_16.jpg"><img border="0" src="images/navi_05.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"><img border="0" src="images/navi_07.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"><img border="0" src="images/navi_09.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"><img border="0" src="images/navi_11.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"><img border="0" src="images/navi_17.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"><img border="0" src="images/navi_13.jpg" width="100" height="84"><img border="0" src="images/navi_06.jpg" width="1" height="84"></tr>
				</table>
				</td>
				<td width="2" align="left"><img border="0" src="images/navi_04.jpg" width="1" height="122"></td>
			</tr>
		</table>

		<div class="body">
		<div class="title"><?=$hag_install?></div>
		<div class="border">
		<form method="POST" action="<?php echo $action; ?>">
<!-- ///////// 3 /////////////////////////////////////////////////////////////////// 3 ////////// -->
<?php
	if ($hag == 3) {?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="15%" valign="top">IP-адрес:<br>
					<div class="podpis">для соеденения ssh</div></td>
					<td width="75%" valign="top">
						<input type="text" name="ip" size="35" value="<?=$request->hasPostVar('ip') ? $request->getPostVar('ip') : IP ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">WEB-адрес:<br>
					<div class="podpis">полный адрес сайта без / на конце</div></td>
					<td valign="top">
						<input type="text" name="url" size="35" value="<?=$request->hasPostVar('url') ? $request->getPostVar('url') : URL ?>">
					</td>
				</tr>
					<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Порт:<br>
					<div class="podpis">порт потока</div></td>
					<td valign="top">
						<input type="text" name="port" size="35" value="<?=$request->hasPostVar('port') ? $request->getPostVar('port') : PORT ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">SSH логин:<br>
					<div class="podpis">root логин</div></td>
					<td valign="top">
						<input type="text" name="ssh_user" size="35" value="<?=$request->hasPostVar('ssh_user') ? $request->getPostVar('ssh_user') : SSH_USER ?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">SSH пароль:<br>
					<div class="podpis">root пароль</div></td>
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
				<input class="button" type="button" value="Назад" name=B1 onClick="location.href='install.php?hag=2'">
	 			<input class="button" type="submit" value="Продолжить" name="hag3">
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
					<td width="15%" valign="top">Конфигурация IceCast:</td>
					<td width="75%" valign="top">
						<input type="text" name="cf_icecast" size="55" value="<?=$request->hasPostVar('cf_icecast') ? $request->getPostVar('cf_icecast') : CF_ICECAST ?>"><br>
						<div class="podpis">полный путь до файла с конфигурацией</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Конфигурация ezstream:</td>
					<td valign="top">
						<input type="text" name="cf_ezstream" size="55" value="<?=$request->hasPostVar('cf_ezstream') ? $request->getPostVar('cf_ezstream') : CF_EZSTREAM ?>"><br>
						<div class="podpis">полный путь до файла с конфигурацией</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Адрес плейлистa:</td>
					<td valign="top">
						<input type="text" name="playlist" size="55" value="<?=$request->hasPostVar('playlist') ? $request->getPostVar('playlist') : PLAYLIST ?>"><br>
						<div class="podpis">полный путь до файла плейлиста</div>
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag4")) {
			echo $ins->ifHag4();
		}
?>
			<p>
				<input class="button" type="button" value="Назад" name="B1" onClick="location.href='?hag=3'">
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
					<td width="15%" valign="top">Логин:</td>
					<td width="75%" valign="top">
						<input type="text" name="user" size="55" value="<?=USER?>"><br>
						<div class="podpis">используется для входа</div>
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Пароль:</td>
					<td valign="top">
						<input type="text" name="password" size="55" value="<?=PASSWORD?>"><br>
						<div class="podpis">используется для входа</div>
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag5")) {
			echo $ins->ifHag5();
		}
?>
			<p>
				<input class="button" type="button" value="Назад" name="B1" onClick="location.href='?hag=4'">
				<input class="button" type="submit" name="hag5" value="Продолжить">
			</p>
<?php
	}
?>

<!-- ///////// 2 /////////////////////////////////////////////////////////////////// 2 ////////// -->

<?php
	if ($hag == 2) {?>
			<table border="0" width="97%" cellpadding="0" class="paddingtable">
				<tr>
					<td width="150" valign="top"><span lang="en-us">Сервер:</span><br>
					<div class="podpis">обычно localhost</div></td>
					<td valign="top">
						<input type="text" name="db_host" size="35" value="<?=$request->hasPostVar('db_host') ? $request->getPostVar('db_host') : DB_HOST?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><span lang="en-us">Логин:</span><br>
					<div class="podpis">укажите логин</div></td>
					<td valign="top">
						<input type="text" name="db_login" size="35" value="<?=$request->hasPostVar('db_login') ? $request->getPostVar('db_login') : DB_LOGIN?>">
					</td>
					</tr>
					<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><span lang="en-us">Пароль:</span><br>
					<div class="podpis">укажите пароль</div></td>
					<td valign="top">
						<input type="text" name="db_password" size="35" value="<?=$request->hasPostVar('db_password') ? $request->getPostVar('db_password') : DB_PASSWORD?>">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">База:<br>
					<div class="podpis">база данных</div></td>
					<td valign="top">
						<input type="text" name="db_name" size="35" value="<?=$request->hasPostVar('db_name') ? $request->getPostVar('db_name') : DB_NAME?>">
					</td>
				</tr>
			</table>
<?php
		if ($request->hasPostVar("hag2")) {			echo $ins->ifHag2();		}
?>
			<p>
				<input class="button" type="button" value="Назад" name="B1" onClick="location.href='?hag=1'">
	 			<input class="button" type="submit" value="Продолжить" name="hag2">
	 		</p>
<?php
	}
?>


<?php
	if ($hag == 6) {
		$ins->addStatistic();
?>
			Поздравляем! Вы успешно установили RadioCMS.
			Для завершения установки добавьте в cron (через каждые 3 минуты) следующую команду:<br><br>
			<div class="border">
				<?=$ins->getWgetCron();?>
			</div>
			<br>
			Ещё один вариант команды:<br><br>
			<div class="border">
				<?=$ins->getPhpCron();?>
			</div>
			<br>
			В целях безопасности настоятельно рекомендуем удалить файл install.php.
			<br><br>
			<input class="button" type="button" value="Перейти в админку" name="B1" onClick="location.href='index.php'">

<?php
	}
?>

<!-- ///////// 1 /////////////////////////////////////////////////////////////////// 1 ////////// -->

<?php
	if ($hag == 1) {
?>
			<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
				<tr>
					<td width="20%" valign="top">Описание</td>
					<td width="15%" valign="top">Текущее</td>
					<td width="65%" valign="top">Необходимо</td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Права на папку <b>music</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getPerms($request->getMusicPath())?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">доступен для записи</span></td>
				</tr>
				<tr>
					<td valign="top">Права на файл <b>_config.php</b></td>
					<td valign="top"><?=$ins->getPerms($request->getRadioPath()."_config.php")?></td>
					<td valign="top"><span class="green">доступен для записи</span></td>
				</tr>
				<tr>
					<td valign="top">Права на файл <b>_system.php</b></td>
					<td valign="top"><b><?=$ins->getPerms($request->getRadioPath()."_system.php")?></b></td>
					<td valign="top"><span class="green">доступен для записи</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Параметр <b>open_basedir</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getBaseDir()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">/ или no_value</span></td>
				</tr>
				<tr>
					<td valign="top">Библиотека <b>libssh2</b></td>
					<td valign="top"><?=$ins->getSsh2()?></td>
					<td valign="top"><span class="green">установлена</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Библиотека <b>curl</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getCurl()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">установлена</span></td>
				</tr>
				<tr>
                    <td valign="top">Библиотека <b>SimpleXML</b></td>
                    <td valign="top"><?=$ins->getXML()?></td>
                    <td valign="top"><span class="green">установлена</span></td>
                </tr>
				<tr>
					<td valign="top">Библиотека <b>iconv</b></td>
					<td valign="top"><?=$ins->getIconv()?></b></td>
					<td valign="top"><span class="green">установлена</span></td>
				</tr>
				<tr>
					<td bgcolor="#F5F4F7" valign="top">Библиотека <b>gd2</b></td>
					<td bgcolor="#F5F4F7" valign="top"><?=$ins->getGd()?></td>
					<td bgcolor="#F5F4F7" valign="top"><span class="green">установлена</span></td>
				</tr>
			</table>
	<br>
<?php
	if ($ins->ifHag1()) {?>
			<input class="button" type="button" value="Продолжить" name="B1" onClick="location.href='?hag=2'">
<?php
	} else {
?>			Устраните проблемы чтобы продолжить установку.
<?php
	}

}
?>
			</div>
		</div>
	</body>
<html>