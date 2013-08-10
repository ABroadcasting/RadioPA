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
	include_once('include.php');

	$playlist = Playlist::create();
    $request = Request::create();
	$playlistAll = PlaylistAll::create();

	$notice = $playlistAll->handler();
    $sort = $playlistAll->getSort();
	$start = $playlistAll->getStart();
	$limit = $playlistAll->getLimit();
	$search = $playlistAll->getSearch();
	$letter = $playlistAll->getLetter();

    # Playlist path
	$url_start = $playlistAll->getUrlStart();

	if (!empty($notice['zakaz'])) {		foreach ($notice['zakaz'] as $message) {			if (defined('EXTERNAL_CHARSET')) {
        		$message = iconv('utf-8', EXTERNAL_CHARSET, $message);
    		}?>
			<p><?=$message?></p>
<?php		}
	}
?>
	<a href="<?=$url_start?>?limit=<?=$limit?>">Все</a>
	<a href="<?=$url_start?>?letter=0-9&limit=<?=$limit?>">0 - 9</a>,
<?php
	foreach (range("A","Z") as $word) {
?>
		<a href="<?=$url_start?>?letter=<?=strtolower($word)?>&limit=<?=$limit?>"><?=$word?></a><?=$word!="Z"?',':''?>
<?php
	}
?>
	<form method="GET" action="">
		<p>
			Поиск <input type="text" name="search" size="20" value="<?=$playlistAll->getSearchString()?>">
			<input type="hidden" name="limit" size="20" value="<?=$limit;?>">
			<input type="submit" value="Найти">
		</p>
	</form>
	<table border="0"  cellspacing="0" cellpadding="0" width="100%"  class="table1">
		<tr>
			<td width="250">
				Название
				<span>
					<a href="<?=$url_start?>?start=<?=$start?>&limit=<?=$limit?>&sort=title&letter=<?=$letter?>&search=<?=$search?>"><img src="/radio/images/up.png" border="0"></a>
					<a href="<?=$url_start?>?start=<?=$start?>&limit=<?=$limit?>&sort=!title&letter=<?=$letter?>&search=<?=$search?>"><img src="/radio/images/down.png" border="0"></a>
				</span>
			</td>
			<td width="210">
				Исполнитель
				<span>
					<a href="<?=$url_start?>?start=<?=$start?>&limit=<?=$limit?>&sort=artist&letter=<?=$letter?>&search=<?=$search?>"><img src="/radio/images/up.png" border="0"></a>
					<a href="<?=$url_start?>?start=<?=$start?>&limit=<?=$limit?>&sort=!artist&letter=<?=$letter?>&search=<?=$search?>"><img src="/radio/images/down.png" border="0"></a>
				</span>
			</td>
			<td align=center>
				Заказать
			</td>
			<td align=center>
				Время
			</td>
		</tr>
		<form method="POST" action="">
<?php
	$zakaz_i = 0;
	$i = 0;
	foreach ($playlistAll->getSongList() as $line) {?>
		<tr <?=($i != 1) ? 'bgcolor=#F5F4F7':''?>>
			<td width="250">
				<?=defined('EXTERNAL_CHARSET') ? @iconv('utf-8', EXTERNAL_CHARSET, $line['title']) : $line['title']?>
			</td>
			<td width="210">
				<?=defined('EXTERNAL_CHARSET') ? @iconv('utf-8', EXTERNAL_CHARSET, $line['artist']) : $line['artist']?>
<?php
		if (!empty($line['album']) and $line['album']!= " ") {?>
				(<?=defined('EXTERNAL_CHARSET') ? @iconv('utf-8', EXTERNAL_CHARSET, $line['album']) : $line['album']?>)
<?php
		}
?>
			</td>
			<td align="center">
				<input type=image src="/radio/images/headphones.png" width="32" height="32" name="zakaz_<?=$zakaz_i?>">
				<input type="hidden" name="zakaz_<?=$zakaz_i ?>" value="<?=$line['idsong']?>">
<?php
			$zakaz_i = $zakaz_i+1;
?>
			</td>
			<td align="center">
				<?=$playlist->getDuration($line['duration'])?>
			</td>
		</tr>
<?php
     	if ($i == 1) {     		$i = 0;
     	} else {     		$i = $i+1;
     	}
	}
?>
		</form>
	</table>
	<p>
<?php
	$seychas = $start+$limit;
	$sort_string = ($request->hasGetVar('sort')) ? "&sort=$sort" : "";

	if ($limit <= $start) {
		$pokaz = $start-$limit;
?>
			<img border="0" src="/radio/images/prev.gif" width="9" height="10">
			<a href="<?=$url_start?>?start=<?=$pokaz?>&limit=<?=$limit?><?=$sort_string?>&letter=<?=$letter?>&search=<?=$search?>">Назад</a>
<?php
	}

	$vsego_pesen = $playlistAll->getVsegoPesen();

	if (($limit <= $start) and ($vsego_pesen > $seychas)) {			echo " или ";
	}

	$pokaz = $start+$limit;
	if ($vsego_pesen > $seychas) {
?>
			<a href="<?=$url_start?>?start=<?=$pokaz?>&limit=<?=$limit?><?=$sort_string?>&letter=<?=$letter?>&search=<?=$search?>">Дальше</a>
			<img border="0" src="/radio/images/next.gif" width="9" height="10">
<?php
	}

?>
	</p>
	<form method="GET" action="">
		Выводить по
		<select size="1" name="limit">
			<option<?php if ($limit==5) echo " selected"; ?>>5</option>
			<option<?php if ($limit==10) echo " selected"; ?>>10</option>
			<option<?php if ($limit==25) echo " selected"; ?>>25</option>
			<option<?php if ($limit==50) echo " selected"; ?>>50</option>
		</select> песен
		<input type="hidden" name="letter" value="<?=$letter?>">
		<input type="hidden" name="search" value="<?=$search?>">
		<input type="hidden" name="sort" value="<?=$sort?>">
		<input type="hidden" name="start" value="<?=$start?>">
		<input type="submit" value="Ок">
	</form>
	<p>
		<i>
			Working on <a href="http://open-rcp/" target="_blank">OpenRCP</a>
		</i>
	</p>