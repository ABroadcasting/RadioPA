<?php
	include_once('Include.php');

	$playlist = Playlist::create();
    $request = Request::create();
	$playlistAll = PlaylistAll::create();

	$notice = $playlistAll->handler();
    $sort = $playlistAll->getSort();
	$start = $playlistAll->getStart();
	$limit = $playlistAll->getLimit();
	$search = $playlistAll->getSearch();
	$letter = $playlistAll->getLetter();

    //Пропишите путь до плейлиста
	$url_start = $playlistAll->getUrlStart();

	if (!empty($notice['zakaz'])) {		foreach ($notice['zakaz'] as $message) {?>
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
				<?=$line['title']?>
			</td>
			<td width="210">
				<?=$line['artist']?>
<?php
		if (!empty($line['album']) and $line['album']!= " ") {?>
				(<?=$line['album']?>)
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
	<!-- Не убирайте ссылку на сайт radiocms.ru, а если удалили - ставьте в другом месте -->
	<p>
		<i>
			Работает на основе <a href="http://radiocms.ru/" target="_blank">RadioCMS</a>
		</i>
	</p>	