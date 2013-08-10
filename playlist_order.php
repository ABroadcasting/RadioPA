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

	$order = Order::create();
	$order->handler();
?>
	<div class="body">
		<div class="navi"><a href="playlist.php">Плейлисты</a></div>
		<div class="navi"><a href="playlist_edit.php">Создать плейлист</a></div>
		<div class="navi_white"><a href="playlist_order.php">Заказы</a></div>
		<div class="navi"><a href="playlist_checks.php">Проверки</a></div>
		<br><br>
		<div class="polovina1">
			<div class="title">Последние заказы</div>
			<div class="border">
				<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
						<td width="26%">Время</td>
						<td width="54%">Трек</td>
						<td width="20%">Плейлист</td>
					</tr>
<?php
   $i = 0;
	foreach ($order->getLastOrders(25) as $line) {
		$playlist = $order->getPlaylistBySong($line['id']);
		$dateTime->setTime($line['time']);
?>
					<tr>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$dateTime->toFormatString("H:i:s (d.m)")?>
						</td>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$line['track']?>
						</td>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$playlist['name']?>
						</td>
					</tr>
<?php
		if ($i == 1) {			$i = 0;
		} else {			$i = $i+1;
		}
  	}
?>
				</table>
			</div>
			<br><br>
		</div>
		<div class="polovina2">
			<div class="title">Топ заказов</div>
			<div class="border">
				<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
						<td width="6%">Заказов</td>
						<td width="74%">Трек</td>
						<td width="20%">Плейлист</td>
					</tr>
<?php
   $i = 0;
	foreach ($order->getTopOrders(25) as $line) {
		$playlist = $order->getPlaylistBySong($line['id']);
?>
					<tr>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$line['zakazano']?>
						</td>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$line['artist']." - ".$line['title']?>
						</td>
						<td <?=($i!=1) ? "bgcolor=#F5F4F7" : ''?>>
							<?=$playlist['name']?>
						</td>
					</tr>
<?php
		if ($i == 1) {
			$i = 0;
		} else {
			$i = $i+1;
		}
  	}
?>
			</table>
		</div>
		<br>
		<div class="bborder">
			<a style="color: #333333;" href="?clear_zakaz=yes">Обнулить счётчик заказов</a></div>
			<br><br>
		</div>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	