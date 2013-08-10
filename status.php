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
	$status = Status::create();
	$status->handler();
	$status->update();

	# Access to the module
	if ($request->hasPostVar('off_x') and $user['admin']!=1) {
		$error = "<i>Вы не можете выключить радио.</i><br>";
	}
?>
	<div class="body">
		<div class="title">Статус серверов</div>
		<div class="border">
			<?=!empty($error) ? $error: ''?>
			<div class="status">Для работы необходимы запущенные сервера.<br><br>Текущий статус:
<?php
	if(!$status->isIcecastRunned() and !$status->isEzstreamRunned()) {
?>
				<img src="images/status_off.jpg" border="0" width="100" height="30">
<?php
	}
?>
<?php
	if ($status->isIcecastRunned() and !$status->isEzstreamRunned()) {?>				<img src="images/status_on_air.jpg" border="0" width="100" height="30">
<?php
	}
?>
<?php
	if ($status->isIcecastRunned() and $status->isEzstreamRunned()) {?>				<img src="images/status_on.jpg" border="0" width="100" height="30">
<?php
	}
?>
			&nbsp;&nbsp;&nbsp;
		</div>
		<div>Используйте кнопки ниже что бы запустить или остановить сервера.</div>
		<br>
		<form method="POST" action="">
			<input type=image src="images/off1.jpg" width="180" height="70" name="off">
			<input type="hidden" name="off" value="off">
			<input type=image src="images/on2.jpg" width="180" height="70" name="on_air">
			<input type="hidden" name="on_air" value="on_air">
			<input type=image src="images/on3.jpg" width="180" height="70" name="on">
			<input type="hidden" name="on" value="on">
			<br>
			<input type=image src="images/next_track.jpg" width="170" height="30" name="next">
			<input type="hidden" name="next" value="next">
		</form>
	</div>
<?php
	include 'tracklist.php';
	if ($status->isIcecastRunned()) {

?>
		<div class="title">Точки монтирования</div>
		<div class="border">
			<style>
				.bgt1 td { background-color:#F5F4F7;}
			</style>
			<table width="97%" cellspacing="0" cellpadding="2" border="0">
				<tr>
					<td>Точка монтирования</td>
					<td>Играет</td>
					<td>Слушают</td>
					<td>Слушать</td>
				</tr>
<?php
		$bg = 0;
		foreach ($status->getStreams() as $stream) {
			if ($bg==0) {
				$bgt='bgt1';
			} else {
				$bgt='';
			}
?>
				<tr class='<?=$bgt?>'>
					<td>/<?=$stream['tochka']?></td>
					<td><?=$stream['cur_song']?></td>
					<td><?=$stream['listeners']?></td>
					<td>
						<a href='<?=$stream['link']?>'>
							<img src='images/winamp.gif' border='0' width='16' height='16'>
						</a>
					</td>
				</tr>
<?php
			if ($bg==0) {				$bg=1;
			} else {				$bg=0;
			}
 		}
?>
			</table>
		</div>
<?php
	}
?>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	