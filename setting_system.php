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
	include('top.php');
	# Access to the module
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}
	$setting = Setting::create();
	$setting->handler();

	# Denying cache around
	if ($request->hasPostVar('request')) {
		Header("Location: setting_system.php");
	}
?>
	<div class="body">
		<div class="navi"><a href="setting.php">Настройки радио</a></div>
		<div class="navi_white"><a href="setting_system.php">Настройки системы</a></div>
		<div class="navi"><a href="setting_dir.php">Каталог</a></div>
		<br><br>
		<div class="title">Настройки системы</div>
		<form method="POST" action="setting_system.php">
			<div class="border">
				<table border="0" width="97%" cellpadding="0" class="paddingtable">
					<tr>
						<td width="104" valign="top">
							IP-адрес:
						</td>
						<td valign="top">
							<input type="text" name="ip" size="35" value="<?=IP?>"><br>
							<div class="podpis">для соеденения ssh</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							WEB-адрес:
						</td>
						<td valign="top">
							<input type="text" name="url" size="35" value="<?=URL?>"><br>
							<div class="podpis">полный адрес сайта без / на конце</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Порт:
						</td>
						<td valign="top">
							<input type="text" name="port" size="35" value="<?=PORT?>"><br>
							<div class="podpis">порт потока</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
<?php
	if ($user['dj'] == USER)  {?>
					<tr>
						<td width="104" valign="top">
							Логин:
						</td>
						<td valign="top">
							<input type="text" name="setting_user" size="35" value="<?=USER?>"><br>
							<div class="podpis">для входа в админку</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Пароль:
						</td>
						<td valign="top">
							<input type="password" name="setting_password" size="35" value="<?=PASSWORD?>"><br>
							<div class="podpis">введите пароль</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
<?php
	}
?>
					<tr>
						<td width="104" valign="top">Конфигурация IceCast:</td>
						<td valign="top">
							<input type="text" name="cf_icecast" size="55" value="<?=CF_ICECAST?>"><br>
							<div class="podpis">полный путь до файла с конфигурацией</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Конфигурация<br>ezstream:
						</td>
						<td valign="top">
							<input type="text" name="cf_ezstream" size="55" value="<?=CF_EZSTREAM?>"><br>
							<div class="podpis">полный путь до файла с конфигурацией</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Адрес<br>плейлистa:
						</td>
						<td valign="top">
							<input type="text" name="playlist" size="55" value="<?=PLAYLIST?>"><br>
							<div class="podpis">полный путь до файла плейлиста</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">Папка для<br>загрузки:</td>
						<td valign="top">
							<input type="text" name="temp_upload" size="55" value="<?=TEMP_UPLOAD?>"><br>
							<div class="podpis">в каталоге music (без полного пути)</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
				</table>
				Будьте очень внимательны при заполнении!
				<br><br>
				<input class="button" type="submit" value="Сохранить" name="request">
			</div>
		</form>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	