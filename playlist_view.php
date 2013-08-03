<?php
	include('top.php');

	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$playlist = Playlist::create();
	$notices = $playlist->handler();
	$playlistId = $playlist->getPlaylistId();

	$vsego_pesen = $playlist->getCountSongs($playlistId);
    $vsego_time = $playlist->getSongsDuration($playlistId);
    $poryadok = $playlist->isSortShow($playlistId);
    $search = $playlist->getSearch($playlistId);

	$search = $playlist->getSearch();
	$sort = $playlist->getSortArray();
	$start = $playlist->getStart();
	$limit = $playlist->getLimit();
?>
	<div class="body">
		<div class="navi_white"><a href="playlist.php">Плейлисты</a></div>
		<div class="navi"><a href="playlist_edit.php">Создать плейлист</a></div>
		<div class="navi"><a href="playlist_order.php">Заказы</a></div>
		<div class="navi"><a href="playlist_checks.php">Проверки</a></div>
		<br><br>
		<form method="POST" action="">
			<div class="title">
				Просмотр плейлиста «<?=$playlist->getTitle($playlistId)?>»
			</div>
			<div class="border">
<?php
	if (!empty($notices)) {
		foreach ($notices as $notice) {
?>
			<p><?=$notice?></p>
<?php
		}
	}
?>
				<table border=0 cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
					    <td width="3%">
					    	Ред.
					    </td>
						<td width="18%">
							<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=($sort['string']=='title')?'!title':'title'?>&search=<?=$search?>&start=<?=$start?>">
								Название
							</a>
						</td>
				        <td width="15%">
				        	<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=($sort['string']=='artist')?'!artist':'artist'?>&search=<?=$search?>&start=<?=$start?>">
				        		Исполнитель
				        	</a>
				        </td>
				        <td width="12%">
				        	<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=$playlistId?>&sort=<?=($sort['string']=='album')?'!album':'album'?>&search=<?=$search?>&start=<?=$start?>">
				        		Альбом
				        	</a>
				        </td>
				        <td width="5%">
				        	<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=$playlistId?>&sort=<?=($sort['string']=='zakazano')?'!zakazano':'zakazano'?>&search=<?=$search?>&start=<?=$start?>">
				        		Заказы
				        	</a>
				        </td>
				        <td width="4%">
				        	Время
				        </td>
				        <td width="5%">
				        	В&nbsp;эфир
				        </td>
				        <td width="35%">
				        	<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=$playlistId?>&sort=<?=($sort['string']=='filename')?'!filename':'filename'?>&search=<?=$search?>&start=<?=$start?>">
				        		Имя файла
				        	</a>
				        </td>
<?php
	if ($poryadok) {
?>
				        <td width="3%">
				        	<a href="playlist_view.php?playlist_id=<?=$playlistId?>&sort=<?=$playlistId?>&sort=<?=($sort['string']=='sort')?'!sort':'sort'?>&search=<?=$search?>&start=<?=$start?>">
				        		Сорт.
				        	</a>
				        </td>
<?php
	}
?>
				        <td width="2%"></td>
				        <td width="3%"></td>
				    </tr>
<?php
	$i = 0;
    foreach ($playlist->getSongs($playlistId) as $line) {
		$color = ($i == 1) ? 'bgcolor=#F5F4F7' : '';
?>
					<tr>
						<td <?=$color?>>
							<a href="edit_song.php?playlist_id=<?=$line['id']?>&edit_song=<?=$line['idsong']?>&start=<?=$start?>&sort=<?=$sort['string']?>&search=<?=$search?>">
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
				        	<?=$line['album']?>
				        </td>
				        <td <?=$color?>>
				        	<?=$line['zakazano']?>
				        </td>
				        <td <?=$color?>>
				        	<?=$playlist->getDuration($line['duration'])?>
				        </td>
				        <td align=center <?=$color?>>
				        	<a href="playlist_view.php?playlist_id=<?=$line['id']?>&start=<?=$start?>&sort=<?=$sort['string']?>&search=<?=$search;?>&play=<?=$line['idsong']?>">
				        		<img src="images/play.gif" border="0" title="Воспроизвести">
				        	</a>
				        </td>
				        <td <?=$color?>>
				        	<?=$playlist->getSongLocalPath($line['filename'], 30)?>
				        </td>
<?php
		if ($poryadok) {
?>
				        <td <?=$color?>>
				        	<input size="2" type="text" name="song_sort[<?=$line['idsong']?>]" value="<?=$line['sort']?>">
				        </td>
<?php
        }
?>
				        <td <?=$color?>>
				        	<a href="playlist_view.php?playlist_id=<?=$line['id']?>&delete_song=<?=$line['idsong']?>&start=<?=$start?>&sort=<?=$sort['string']?>&search=<?=$search?>">
				        		<img src="images/delete.gif" border="0" title="Удалить песню">
				        	</a>
				        </td>
				        <td <?=$color?>>
				        	<a href="playlist_view.php?playlist_id=<?=$line['id']?>&delete_song_2=<?=$line['idsong']?>&start=<?=$start?>&sort=<?=$sort['string']?>&search=<?=$search?>">
				        		<img src="images/delete2.gif" border="0" title="Удалить песню из всех плейлистов">
				        	</a>
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
				<br>
				<table border=0 cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
						<td width="60%">
							<a href="playlist_view.php?playlist_id=<?=$playlistId?>">Все песни</a>&nbsp;&nbsp;&nbsp;
<?php
	$seychas = $start+$limit;
    $sort_string = ($request->hasGetVar('sort')) ? "&sort=".$sort['string'] : "";
    
	if ($vsego_pesen < $seychas) {
?>
							Показано: <?=$start+1?>-<?=$vsego_pesen?>&nbsp;&nbsp;&nbsp;
<?php
	} else {
?>
							Показано: <?=$start+1?>-<?=$seychas?>&nbsp;&nbsp;&nbsp;
<?php
	}

	if ($limit <= $start) {
		$pokaz = $start-$limit;
?>
							<a href="playlist_view.php?playlist_id=<?=$playlistId?>&start=<?=$pokaz?>&limit=<?=$limit?><?=$sort_string?>&search=<?=$search?>">Назад</a>
<?php
	}

	if (($limit <= $start) and ($vsego_pesen > $seychas)) {
		echo " | ";
	}

	$pokaz = $start+$limit;
	if ($vsego_pesen > $seychas) {
?>
							<a href="playlist_view.php?playlist_id=<?=$playlistId?>&start=<?=$pokaz?>&limit=<?=$limit?><?=$sort_string?>&search=<?=$search?>">Дальше</a>
<?php
	}
?>
						</td>
						<td align="right">
							Всего песен: <?=$vsego_pesen?>&nbsp;(<?=$vsego_time?>)&nbsp;&nbsp;
							<a href="playlist_view.php?del_all=1&playlist_id=<?=$playlistId?>">Удалить всё</a>
						</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				</table>
				<br>
				<input class="button" type="button" value="Назад" name="back" onClick="location.href='playlist.php'" />
<?php
	if ($poryadok) {?>
				<input class="button" value="Сохранить" name="submit" type="submit">
<?php
	}
?>
				<input class="button" value="Добавить треки" name="14" type="button"  onClick="location.href='manager.php?playlist_id=<?=$playlistId?>'" />
			</form>
			<br><br>
			<form method="POST" action="playlist_view.php?playlist_id=<?=$playlistId;?>">
			Поиск <input type="text" name="search" size="20" value="<?=$playlist->getSearchString()?>">
			<input type="submit" value="Найти" name="b1">
			</form>
		</div>
		<br><br>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	