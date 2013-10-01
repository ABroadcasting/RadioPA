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

	$repeat = Repeat::create();
	$repeat->handler();
?>
	<div class="body">
		<div class="navi"><a href="playlist.php">Плейлисты</a></div>
		<div class="navi"><a href="playlist_edit.php">Создать плейлист</a></div>
		<div class="navi"><a href="playlist_order.php">Заказы</a></div>
		<div class="navi_white"><a href="playlist_checks.php">Проверки</a></div>
		<br><br>
		<div class="title">Эти файлы не существуют</div>
		<div class="border">
<?php
	if ($request->hasGetVar('povtor')) {		$povtor_yes = 'yes';
	}
?>
		<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width="3%">Ред.</td>
				<td width="20%">Название</td>
   				<td width="17%">Исполнитель</td>
        		<td width="10%">Плейлист</td>
        		<td width="47%">Имя файла</td>
				<td width="3%"></td>
			</tr>
<?php
	$i = 0;
	foreach ($repeat->getNotExisting() as $line) {		$style = ($i != 1) ? "bgcolor=#F5F4F7" : '';
?>
			<tr>
				<td <?=$style?>>
					<a href="edit_song.php?playlist_id=povtor&edit_song=<?=$line['idsong']?>">
						<img src="images/edit_song.gif" border="0" title="Редактировать песню">
					</a>
				</td>
				<td <?=$style?>>
					<?=$line['title']?>
				</td>
        		<td <?=$style?>>
        			<?=$line['artist']?>
        		</td>
        		<td <?=$style?>>
        			<?=$line['playlistName']?>
        		</td>
        		<td <?=$style?>>
        			<?=$line['filename']?>
        		</td>
				<td <?=$style?>>
					<a href="playlist_checks.php?delete_song3=<?=$line['idsong']?>">
						<img src="images/delete2.gif" border="0" title="Удалить песню из плейлиста">
					</a>
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
	<br>
	<div class="title">Повторяющиеся id3</div>
	<div class="border">
<?php
	if (empty($povtor_yes)) {
?>
		<div>
			Поворяющие "Название" и "Исполнитель" приводит к частичной не работоспособности системы.<br>
			Используйте эту функцию после добавления новых песен.
		</div>
<?php
	}
?>

<?php
	if (!empty($povtor_yes)) {?>
		<table border=0 cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width="3%">Ред.</td>
				<td width="20%">Название</td>
       			<td width="20%">Исполнитель</td>
        		<td width="10%">Плейлист</td>
				<td width="10%">Время</td>
				<td width="32%">Имя файла</td>
				<td width="3%"></td>
			</tr>
<?php
		$i = 0;
		foreach ($repeat->getRepeat() as $line) {
			$color = ($i!=1) ? 'bgcolor=#F5F4F7' : '';
?>
		<tr>
			<td <?=$color?>>
				<a href="edit_song.php?playlist_id=povtor&edit_song=<?=$line['idsong']?>">
					<img src="images/edit_song.gif" border="0" title="Редактировать песню">
				</a>
			</td>
			<td <?=$color?>>
				<?=$line['title']?>
			</td>
			<td <?=$color?>>
				<?=$line['artist']?>
			</td>
        	<td <?=$color?>>
        		<?=$line2['name']?>
        	</td>
         	<td <?=$color?>>
         		<?=$line['duration']?>
        	</td>
        	<td <?=$color?>>
        		<?=$line['filename']?>
        	</td>
        	<td <?=$color?>>
        		<a href="playlist_checks.php?povtor=yes&delete_song=<?=$line['idsong']?>">
        			<img src="images/delete.gif" border="0" title="Удалить песню из этого списка">
        		</a>
        	</td>
			<td <?=$color?>>
				<a href="playlist_checks.php?povtor=yes&delete_song2=<?=$line['idsong']?>">
					<img src="images/delete2.gif" border="0" title="Удалить песню из всех плейлистов и жётского диска">
				</a>
			</td>
		</tr>
<?php
			if ($i == 1) {				$i = 0;
			} else {				$i = $i+1;
			}
		}
?>
	</table>
	<br><br>
	<div class="bborder"><a href="?povtor_start=yes">Ещё разок</a></div>
<?php
	} else {
?>
	<br>
 	<div class="bborder"><a href="?povtor_start=yes">Найти повторы</a></div>
<?php
	}
?>
	</div>
	</div>
<?php
    include('tpl/footer.tpl');
?>  	