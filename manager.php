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

	$song = Song::create();
	$manager = Meneger::create();
	$start = $manager->getStart();
	$playlist_id_get = $manager->getPlaylistId();
	$fold = $manager->getFold();
	$search = $manager->getSearch();

	$in = 0;
	$i = 0;
	$ipr = 0;
	$ipk = 0;
	$ips = 0;

	$dirct = $manager->getDirct();
	$dirct2 = $manager->getDirct2();
    $back = $manager->getBack();
    $begin = $manager->getBegin();

	$dirct_f = str_replace(" ", "%20", $dirct);
?>
	<div class="body">
		<br>
		<div class="title">
<?php
	if (!empty($playlist_id_get)) {
?>
		Добавление файлов в <?=$manager->getPlaylistName($playlist_id_get)?> (<?=$dirct2?>)
<?php	} else {
?>
		Файловый менеджер <?=$dirct2?>
<?php
	}
?>
		</div>
	<div class="border">
		<form name='fman' action='manager_query.php?folder=<?=$dirct_f?>&start=<?=$start?>&search=<?=$search?>' method='POST'>
		<table border=0 cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width=25>Выб.</td>
				<td>Имя файла</td>
				<td width=80% align="right">
<?php
	if ($dirct!=$begin) {
?>
					<b><a href="manager.php?fold=<?=$back?>&playlist_id=<?=$playlist_id_get?>">Вернуться назад</a></b>
<?php
	}
?>
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
<?php
	$list = $manager->getList();
	foreach ($list['list'] as $k) {

    	if ($i == 0) {    		$bg = " bgcolor='#F5F4F7'";
   	 	} else {    		$bg = "";
    	}

    	if ($in == 0) {?>
			<tr <?=$bg?>>
<?php
    	}

		$full = $dirct."/".$k;
		$k_size = $manager->getFilesize($full);

    	if (is_dir($full) === true) {
?>
				<td width="25" valign="top">
					<span style='display: none;' id=t_"<?=$ipr?>"></span>
					<span style='display: none;' id=t2_"<?=$ipr?>"></span>
<?php
    	$kp = urlencode($k);
    	if (!empty($playlist_id_get)) {?>
					<a href="add_tracks.php?start=<?=$start?>&search=<?=$search?>&add_directory=<?=urlencode($full)?>&playlist_id=<?=$playlist_id_get?>">
    					<img src="images/plus.gif" width="16" height="16" border="0" title="Добавить папку в плейлист">
    				</a>
<?php
    	} else {?>
					<input id="<?=$ipr?>" name="fl[]" value="<?=$kp?>" type="checkbox">
<?php
    	}

?>
				</td>
<?php
    	} else {
	   		if ($ipk > 2) {	    		$cvet = "white";
	    	} else {	    		$cvet = "#F5F4F7";
	    	}?>
				<td width="25" valign="top" id="t2_<?=$ipr?>">
<?php

	    	$kp = urlencode($k);
	        if ($dirct2 == "/music") {
	            if ($manager->isMp3($k)) {
	    			if (!empty($playlist_id_get)) {
?>
	    				<img src="images/delete2.gif" width="16" height="16" border="0" title="Для того бы добавить эту песню в плейлист необходимо перенести её в папку">
<?php
	    			} else {
?>
						<input id="<?=$ipr?>" name="fl[]" value="<?=$kp?>" type="checkbox" onclick="iprClick('<?=$ipr?>', '<?=$cvet?>')">
<?php
	    			}
	    		} else {
	                if (!empty($playlist_id_get)) {
?>
	    				<img src="images/delete2.gif" width="16" height="16" border="0" title="Это не mp3-файл">
<?php
	    			} else {
?>
	    				<input id="<?=$ipr?>" name="fl[]" value="<?=$kp?>" type="checkbox" onclick="iprClick('<?=$ipr?>', '<?=$cvet?>')">
<?php
	    			}
	    		}
	        } else {
	        	if ($manager->isMp3($k)) {
	    			if (!empty($playlist_id_get)) {
?>
						<a href="add_tracks.php?start=<?=$start?>&search=<?=$search?>&filename=<?=urlencode($full)?>&playlist_id=<?=$playlist_id_get?>">
	    					<img src="images/plus.gif" width="16" height="16" border="0" title="Добавить песню в плейлист">
	    				</a>
<?php
	    			} else {
?>
						<input id="<?=$ipr?>" name="fl[]" value="<?=$kp?>" type="checkbox" onclick="iprClick('<?=$ipr?>', '<?=$cvet?>')">
<?php
	    			}	        	} else {
	            	if (!empty($playlist_id_get)) {
?>
	    				<img src="images/delete2.gif" width="16" height="16" border="0" title="Это не mp3-файл">
<?php
	    			} else {
?>
						<input id="<?=$ipr?>" name="fl[]" value="<?=$kp?>" type="checkbox" onclick="iprClick('<?=$ipr?>', '<?=$cvet?>')">
<?php
	    			}
	    		}
	    	}
?>
				</td>
<?php
		}

  		$playlist_name = $manager->getUseIn($full);

    	$old_k = $k;
   		$k = wordwrap($k, 30, "\n", 1);
    	if (is_dir($full) === true) {
       		$full = str_replace(" ", "%20", $full);?>
				<td width=31% valign=top>
<?php
			if (!empty($playlist_id_get)) {
?>
					<img src="images/m_folder.gif" border="0" width="13" height="11">
					<a href="manager.php?fold=<?=$full?>&playlist_id=<?=$playlist_id_get?>"><b><?=$k?></b></a>
					<br><div class="podpis">Папка номер <?=$ips?></div>
<?php
			} else {?>
					<img src="images/m_folder.gif" border="0" width="13" height="11">
					<a href="manager.php?fold=<?=$full?>"><b><?=$k?></b></a>
					<br><div class="podpis">Папка номер <?=$ips+1?></div><?php
			}
?>
				</td>
<?php
     	} else {
?>
				<td id="t_<?=$ipr?>" width="31%" valign="top">
					<label for="<?=$ipr?>"><div><img src="images/m_file.png" border="0" width="9" height="12"> <?=$k?></div><div class=podpis>Файл номер <?=$ips+1?> (<?=$k_size?>)</div></label>
<?php
				if ($manager->isTempUpload($full)) {
					$afl = $song->getPlayerPath($full);
                    if ($manager->isMp3($old_k)) {
?>
						<div class="podpis">
							<div style="height: 20px; margin-top: 3px;">
	      						<span id="play_<?=$ipr?>">
	      							<a href="javascript: playmedia(<?=$ipr?>,'<?=$afl?>');"><img width="16" height="16" border="0" src="/radio/images/play.gif"></a>&nbsp;
	      							<span onclick="playmedia(<?=$ipr?>,'<?=$afl?>');" style="cursor: pointer;position: absolute;margin-top: 2px;">
	      								cлушать
	      							</span>&nbsp;
	      						</span>
	      					</div>
	      				</div>
<?php
					}
      			} else {

					if (!$playlist_name) {?>          	  		<div class="podpis"><font color="#993333">Не используется!</font></div>
<?php
					} else {?>
          	  		<div class="podpis"><font color="#669999">Используется в <i><?=$playlist_name?></i></font></div>
<?php
					}
				}
?>
				</td>
<?php
     	}
        $ipr = $ipr+1;
        $ips = $ips+1;
    	if ($i == 1) {    		$i = 0;
    	} else {
    		$i++;
    	}
    	if ($in == 2) {
?>    		</tr>
<?php
    	}
		if ($in == 2) {    	 	$in = 0;
    	} else {
    		$in++;
    	}
		if ($ipk == 5) {			$ipk = 0;
		} else {			$ipk++;;
		}
	}
?>
			<tr>
				<td width=25></td><td width=31%></td>
				<td width=25></td><td width=31%></td>
				<td width=25></td><td width=31% align=right></td>
			</tr>
		</table>
 		<br>
		<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width="50%">
<?php
	$fold = str_replace(" ","%20",$fold);
	$a2ostalos = $list['vsego']-$list['start'];
	$posl = (int)($list['vsego']/$list['limit']);
	$posl = $posl*$list['limit'];

	if (empty($playlist_id_get)) {
?>
					<label for="se_all">Выбрать всё</label>&nbsp;&nbsp;<input type="checkbox" name="se_all" id="se_all" onClick="s_all(this);">&nbsp;&nbsp;&nbsp;
<?php
	}
?>
<?php
	if (!empty($search) ) {
?>
					<a href="?start=0&fold=<?=$fold?>&playlist_id=<?=$playlist_id_get?>">Сбросить поиск</a>
<?php
	}

   	if ( !empty($search) and (($list['vsego'] > $list['limit']) and ($list['start'] != 0) or ($list['vsego'] > $list['limit']) and ($a2ostalos >= $list['limit'])) ) {
   		echo " | ";
   	}

	if ( ($list['vsego'] > $list['limit']) and ($list['start'] != 0) ) {
    	$a2prev = $list['start']-$list['limit'];
?>
					<a href="?start=<?=$a2prev?>&search=<?=$search?>&fold=<?=$fold?>&playlist_id=<?=$playlist_id_get?>">Назад</a>
<?php
	}


	if ( ($list['vsego'] > $list['limit']) and ($a2ostalos >= $list['limit']) and ($list['vsego'] > $list['limit']) and ($list['start'] != 0) ) {
		echo " | ";
	}

	if ( ($list['vsego'] > $list['limit']) and ($a2ostalos >= $list['limit']) ) {    	$a2next = $list['start']+$list['limit'];?>
					<a href="?start=<?=$a2next?>&search=<?=$search?>&fold=<?=$fold?>&playlist_id=<?=$playlist_id_get?>">Дальше</a>
<?php	}

	if ($a2ostalos <= $list['limit']) {
		$list['end'] = $list['start']+$a2ostalos;
	}
	$list['start'] = $list['start']+1;
?>
				</td>
				<td width="50%" valign="top" align="right">
					Показаны: <b><?=$list['start']?>-<?=$list['end']?></b>. Всего файлов: <b><?=$list['vsego']?></b>.
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width="100">
<?php
	if (!empty($playlist_id_get)) {
?>
					<input class="button" type="button" value="Завершить добавление" name="back" onClick="location.href='playlist_view.php?playlist_id=<?=$playlist_id_get?>'">
<?php
	} else {?>
					<input type=image src="images/m_new_folder.png" width="32" height="32" title="Создать папку" name="md">
					<input type="hidden" name="md" value="md">
				</td>
				<td width="100">
					<input type=image src="images/m_copy_file.png" title="Копировать" width="32" height="32" name="copy">
					<input type="hidden" name="copy" value="copy">
				</td>
				<td width="100">
					<input type=image src="images/m_move.png" title="Переместить" width="32" height="32" name="move">
					<input type="hidden" name="move" value="move">
				</td>
				<td width="100">
					<input type=image src="images/m_rename.png" title="Переименовать" width="32" height="32" name="ren">
					<input type="hidden" name="ren" value="ren">
				</td>
				<td width="100">
					<input type=image src="images/m_del.png" title="Удалить" width="32" height="32" name="udal">
					<input type="hidden" name="udal" value="udal">
<?php
	}
?>
				</td>
				<td width="80%" valign="top" align="right">
				</td>
			</tr>
		</table>
		</form>
		<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
			<tr>
				<td width="20%">&nbsp;</td>
				<td width="80%" valign="top" align="right">
					<div class="searcht">
						<form action='manager_query.php?folder=<?=$dirct_f?>&start=<?=$start?>&playlist_id=<?=$playlist_id_get?>' method='post'>
							Поиск в этой папке <input type="text" name="search" size="20" value="<?=$search?>">
							<input type="submit" value="Найти" name="search_button">
						</form>
					</div>
				</td>
			</tr>
		</table>
	</div>
	</div>
<?php
    include('tpl/footer.tpl');
?>  	