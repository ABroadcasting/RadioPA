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

	$nowplay = Nowplay::create();
	$playlist = Playlist::create();
	$playlist->handler();
?>
	<div class="body">
		<div class="navi_white"><a href="playlist.php">Плейлисты</a></div>
		<div class="navi"><a href="playlist_edit.php">Создать плейлист</a></div>
		<div class="navi"><a href="playlist_order.php">Заказы</a></div>
		<div class="navi"><a href="playlist_checks.php">Проверки</a></div>
		<br><br>	
		<div class="title">Визуальный плейлист</div>
			<div class="border">
				<?=$nowplay->getVisualPlaylist()?>
			</div>
			<br>
			<div class="title">Список плейлистов</div>	
<?php 
	if ($playlist->noNowCheck()) {
?>	
				<p style="padding-left: 5px;"><span class="red"><i>Нет запущенного плейлиста. Запустите плейлист, указав ему ближайшее время запуска.</i></span></p>
<?php 
	}
?>			
			<form method="POST" action="">
				<div class="border">				
<?php
            $vsego_time = $playlist->getAllSongsDuration();
            $vsego_pesen = $playlist->getCountAllSongs();
?>
				<table border=0 cellspacing="0" cellpadding="0" width="97%" class="table1">
<?php
	$i = 0;
?>
<?php
    		foreach ($playlist->getList() as $line) {
    			$color = ($i != 1) ? 'bgcolor=#F5F4F7' : '';?>
					<tr>
				        <td width="17%" <?=$color?>>
				        	<a href="playlist_view.php?playlist_id=<?=$line['id']?>"><?=$line['name']?></a>
				        	<br>
				        	<?=$playlist->getPlaymode($line['playmode'])?>
							<br>
							<a href="playlist_edit.php?playlist_id=<?php echo $line["id"]?>"><img src="images/edit.gif" width="16" height="16" border="0" title="Редактировать плейлист"></a>&nbsp;&nbsp;
				        	<a href="manager.php?playlist_id=<?php echo $line['id']?>"><img src="images/plus.gif" width="16" height="16" border="0" title="Добавить треки в плейлист"></a>&nbsp;&nbsp;
				        	<a href="playlist.php?delete_playlist=<?php echo $line['id']?>"><img src="images/delete2.gif" width="16" height="16" border="0" title="Удалить плейлист"></a>
				        </td>
				        <td width="51%" <?=$color?>>
				        	<?=$playlist->getTimes($line)?>
						</td>
						<td width="10%" <?=$color?>>
							<?=$playlist->getCountSongs($line['id'])." песен";?>
<?php
				if ($line['now'] == '1') {?>
							<br>в эфире
<?php
				}
?>
						</td>
				        <td width="6%" <?=$color?>>
<?php
				if ($line['enable'] == '1') {?>
						<img src="images/online.gif" width="36" height="29" border="0" title="Плейлист в ротации">
<?php
				} else {?>
						<img src="images/offline.gif" width="36" height="29" border="0" title="Этот плейлист отключён">
<?php
				}
?>
						</td>
				        <td width="6%" <?=$color?>>
<?php
				if ($line['allow_zakaz'] == '1') {?>
							<img src="images/zakaz.gif" width="29" height="29" border="0" title="Заказы разрешены">
<?php
				} else {?>
							<img src="images/zakaz2.gif" width="29" height="29" border="0" title="Заказы запрещены">
<?php
				}
?>
						</td>
				        <td width="5%" <?=$color?>>
<?php
				if ($line['show'] == '1') {?>
					<img src="images/magnifier.gif" width="29" height="29" border="0" title="Показывать на главной">
<?php
				} else {?>
					<img src="images/magnifier2.gif" width="29" height="29" border="0" title="Не показывать на главной">
<?php
				}
?>
						</td>
						<td width="7%" <?=$color?>>
							<input size="2" type="text" name="playlist_sort[<?=$line['id']?>]" value="<?=$line['sort']?>">
						</td>
					</tr>
 <?php
 		if ($i == 1) { 			$i = 0;
 		} else { 			$i = $i+1;
 		}
 	}
 ?>
				</table>
				<br>
				<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
						<td width="60%">
							<input class="button" value="Сохранить" name="submit" type="submit">
						</td>
						<td align="right" valign="top">
							Песен на главной: <?php echo $vsego_pesen; ?> (<?php echo $vsego_time; ?>)
						</td>
					</tr>
				</table>
			</div>
		</form>
		<br><br>
	</div>
<?php
    include('tpl/footer.tpl');
?>  