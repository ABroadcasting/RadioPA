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

	$statistic = Statistic::create();
	$setting = Setting::create();
	$setting->handler();

	# Denying cache around
	if ($request->hasPostVar('request')) {
	    if ($request->getPostVar('dir_show') == "on") {
		  $statistic->updateDirectory();
        }    
		Header("Location: setting_dir.php");
	}
?>
	<div class="body">
		<div class="navi"><a href="setting.php">Настройки радио</a></div>
		<div class="navi"><a href="setting_system.php">Настройки системы</a></div>
		<div class="navi_white"><a href="setting_dir.php">Каталог</a></div>
		<br><br>
		<div class="title">Каталог RadioCMS</div>
			<form method="POST" action="setting_dir.php">
				<div class="border">
					<table border="0" width="97%" cellpadding="0" class="paddingtable">
						<tr>
							<td width="150" valign="top">
								Название станции:<br>
							</td>
							<td valign="top">
								<input maxlength="50" size="35" name="dir_name" type="text" value="<?=DIR_NAME?>"><br>
								<div class="podpis">кратко</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Сайт:<br>
							</td>
							<td valign="top">
								<input maxlength="60" size="35" name="dir_url" type="text" value="<?=DIR_URL?>"><br>
								<div class="podpis">с http://</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Адрес потока:<br>
							</td>
							<td valign="top">
								<input maxlength="80" size="35" name="dir_stream" type="text" value="<?=DIR_STREAM?>"><br>
								<div class="podpis">с http://</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Описание:<br>
							</td>
							<td valign="top">
								<input maxlength="80" size="65" name="dir_description" type="text" value="<?=DIR_DESCRIPTION?>"><br>
								<div class="podpis">кратко</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Жанр:<br>
							</td>
							<td valign="top">
								<input maxlength="10" size="35" name="dir_genre" type="text" value="<?=DIR_GENRE?>"><br>
								<div class="podpis">одно слово</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Битрейт:<br>
							</td>
							<td valign="top">
								<select size="1" name="dir_bitrate" style="width:100px;">
									<option <?=(DIR_BITRATE=='64')? 'selected':''?> value="64">64 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='96')? 'selected':''?> value="96">96 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='128')? 'selected':''?> value="128">128 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='192')? 'selected':''?> value="192">192 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='256')? 'selected':''?> value="256">256 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='VBR')? 'selected':''?> value="VBR">VBR</option>
								</select>
								<br><div class="podpis">VBR - переменный битрейт</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">Отображать в каталоге:<br></td>
							<td valign="top">
								<select size="1" name="dir_show" style="width:60px;">
									<option <?=(DIR_SHOW=='off')?'selected':''?> value="off">Нет</option>
									<option <?=(DIR_SHOW=='on')?'selected':''?> value="on">Да</option>
								</select>
								<br><div class="podpis">будет отображаться только если заполнены все поля</div>
							</td>
						</tr>
					</table>
					<input type="text" name="request" size="1" value="request" style="visibility: hidden;"><br>
					<input class="button" type="submit" value="Сохранить" name="B1">
				</div>
			</form>
		</div>
		<br>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	