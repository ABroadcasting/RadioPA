<?php
	include('top.php');
	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$song = Song::create();
	$song->handler();
?>
	<div class="body">
		<div class="navi_white"><a href="playlist.php">Плейлисты</a></div>
		<div class="navi"><a href="playlist_edit.php">Создать плейлист</a></div>
		<div class="navi"><a href="playlist_order.php">Заказы</a></div>
		<div class="navi"><a href="playlist_checks.php">Проверки</a></div>
		<br><br>
		<form method="POST" action="">
			<div class="title">Редактирование песни</div>
			<div class="border">

<?php
	$line = $song->getSong($request->getGetVar('edit_song'));
	$player_filename = $song->getPlayerPath($line['filename']);
?>

				<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
					<tr>
					    <td width="15%">
					    	Плеер<br>
					    	<div class="podpis">Dewplayer Classic 1.9</div>
					    </td>
				        <td width="85%">
				        	<object type="application/x-shockwave-flash" data="files/dewplayer.swf?mp3=<?=$player_filename?>&amp;showtime=1" width="200" height="20">
				        		<param name="wmode" value="transparent" />
				        		<param name="movie" value="files/dewplayer.swf?mp3=<?=$player_filename?>&amp;showtime=1" />
				        	</object>
				        </td>
				    </tr>
					<tr>
					    <td>
					    	Название<br>
					    	<div class="podpis">ID3-тег MP3-файла</div>
					    </td>
				        <td>
				        	<input maxlength="90" size="60" type="text" name="title" value="<?=htmlspecialchars($line['title'])?>">
				        </td>
				    </tr>
					<tr>
					    <td>
					    	Исполнитель<br>
					    	<div class="podpis">ID3-тег MP3-файла</div>
					    </td>
				        <td>
				        	<input maxlength="90" size="60" type="text" name="artist" value="<?=htmlspecialchars($line['artist'])?>">
				        </td>
				    </tr>
					<tr>
					    <td>
					    	Альбом<br>
					    	<div class="podpis">ID3-тег MP3-файла</div>
					    </td>
				        <td>
				        	<input maxlength="90" size="60" type="text" name="album" value="<?=htmlspecialchars($line['album'])?>">
				        </td>
				    </tr>
				    <tr>
					    <td>
					    	Заказы<br>
					    	<div class="podpis">Количество заказов</div>
					    </td>
				        <td>
				        	<input size="40" type="text" name="zakazano" value="<?=$line['zakazano']?>">
				        </td>
				    </tr>
				    <tr>
					    <td>
					    	Сортировка<br>
					    	<div class="podpis">Порядок сортировки</div>
					    </td>
				        <td>
				        	<input size="40" type="text" name="sort" value="<?=$line['sort']?>">
				        </td>
				    </tr>
				    <tr>
					    <td>
					    	Переместить в<br>
					    	<div class="podpis">Переместить в другой плейлист\папку</div>
					    </td>
				        <td>
				        	<select size="1" name="position">
<?php
		 foreach ($song->getPlaylistList() as $playlist) {
?>
								<option <?=$playlist['id']==$line['id']? 'selected':''?> value="<?=$playlist['id']?>">
			 						<?=$playlist['name']?>
								</option>
<?php
		 }
?>
							</select>
							&nbsp;&nbsp;&nbsp;
        					<select size="1" name="folder">
<?php

		foreach ($song->getFolderList() as $folder) {

?>
								<option value="<?=$folder?>" <?=$song->getFolder($line['filename'])==$folder?'selected':''?>>
									<?=$folder?>
								</option>
<?php
		}
?>
        					</select>
						</td>
					</tr>
		    		<tr>
				   		<td>
				   			Имя файла<br>
				   			<div class="podpis">Измените</div>
				   		</td>
			        	<td>
			        		<input size="40" type="text" name="filename" value="<?=$song->getFilename($line['filename'])?>">
						</td>
				    </tr>
				    <tr>
					    <td>
					    	Идентификатор<br>
					    	<div class="podpis">Только чтение</div>
					    </td>
				        <td>
				        	<input readonly  size="40" type="text" name="idsong" value="<?=$line['idsong']?>">
				        </td>
				    </tr>
				</table>
				<br><br>
<?php
	if ($request->getGetVar('playlist_id') == "povtor") {
?>
				<input class="button" type="button" value="Назад" name="back" onClick="location.href='playlist_checks.php?povtor=yes'" />
<?php
	} else {
?>
				<input class="button" type="button" value="Назад" name="back" onClick="location.href='playlist_view.php?playlist_id=<?=$request->getGetVar('playlist_id')?>&sort=<?=$request->getGetVar('sort')?>&start=<?=$request->getGetVar('start')?>&search=<?=$request->getGetVar('search')?>'" />
<?php
	}
?>
				<input class="button" value="Сохранить" name="submit" type="submit"> <input class="button" value="Сохранить и назад" name="submit_and_save" type="submit">
			</div>
		</form>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	