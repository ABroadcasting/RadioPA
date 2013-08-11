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
	ob_start();
	include('top.php');

	# Access to the module
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$manager = Meneger::create();
	$manager->zaprosHandler();

    if ($manager->request->hasPostVar('fl')) {
	   $fl = $manager->request->getPostVar('fl');
    } else {
        $fl = array();
    }    
	$fold = $manager->getFold();
	$folder = $manager->getFolder();
	$start = $manager->getStart();
	$search = $manager->getSearch();
	$root_path = $request->getMusicPath();
?>

	<div class="body">
		<br>
		<div class="title">Действие с файлом</div>
		<div class="border">
			<form action="manager_do.php?folder=<?=$folder?>&start=<?=$start?>&search=<?=$search?>" method="post">
				<table border="0" cellspacing="0" cellpadding="0" width="97%" class="table1">
<?php
	if ($manager->isUdal()) {
?>
					<tr>
						<td width="95%">Удалить файлы?<br></td>
					</tr>
					<tr>
						<td>
<?php
		foreach ($fl as $k=>$i) {
			if (is_dir($folder."/".$i)) {	  			$ds = " (папка)";
	  		} else {	  			$ds = "";
	  		}
?>
							<input type="hidden" name="fl[]" value="<?=$i?>"><b><?=urldecode($i)?></b> <?=$ds?> из папки <?=$folder?><br>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<td>
							<input class="button" type="submit" value="Отмена" name="ot"> <input class="button" type="submit" value="Удалить" name="udal_x"><br>
						</td>
<?php
	}

	if ($manager->isCopy()) {
		$begin = $root_path;
		$begin = substr($begin, 0, -1);
?>
					<tr>
						<td width=95%><b>Куда копировать файлы?</b></td>
					</tr>
					<tr>
						<td width=95%>
<?php
		foreach ($fl as $i) {			if (is_dir($folder."/".urldecode($i))) {				$ds = " (папка)";
			} else {				$ds = "";
			}
?>
							<input type="hidden" name="fl[]" value="<?=$i?>">&nbsp;<?=urldecode($i)?> <?=$ds?><br>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<td width="95%"><b>Выберите папку для копирования:</b></td>
					</tr>
					<tr>
						<td width="95%">
<?php
		foreach ($manager->getTree($begin) as $fllnm2=>$fllnm) {?>							<input id="<?=$fllnm2?>" name="rd" type="radio" value="<?=$fllnm?>">&nbsp;
							<label for="<?=$fllnm2?>"><?=$fllnm2?></label><br><?php
		}
?>
<?php
		if ($begin != $folder) {
			$begin2 = "/";
?>
							<input id="<?=$begin2?>" name="rd" type="radio" value="<?=$begin?>">&nbsp;
							<label for="<?=$begin2?>"><?=$begin2?></label><br>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<td width="95%">
							<input class="button" type="submit" value="Отмена" name="ot">
							<input class="button" type="submit" value="Копировать" name="copy_x">
						</td>
					</tr>
<?php
	}

	if ($manager->isMove()) {
		$begin = $root_path;
		$begin = substr($begin, 0, -1);
?>
					<tr>
						<td width=95%><b>Куда переместить файлы?</b></td>
					</tr>
					<tr>
						<td width=95%>
<?php
		foreach ($fl as $i)	{
			if (is_dir($folder."/".$i)) {				$ds = " (папка)";
			} else {				$ds = "";
			}?>
			<input type="hidden" name="fl[]" value="<?=$i?>">&nbsp;<?=urldecode($i)?> <?=$ds?><br>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<td width=95%><b>Выберите папку для перемещения:</b></td>
					</tr>
					<tr>
						<td width=95%>
<?php
		foreach ($manager->getTree($begin) as $fllnm2=>$fllnm) {
?>
							<input id="<?=$fllnm2?>" name="rd" type="radio" value="<?=$fllnm?>">&nbsp;
							<label for="<?=$fllnm2?>"><?=$fllnm2?></label><br>
<?php
		}
?>
<?php
		if ($begin!=$folder) {
			$begin2 = "/";
?>
							<input id="<?=$begin2?>" name="rd" type="radio" value="<?=$begin?>">&nbsp;
							<label for="<?=$begin2?>"><?=$begin2?></label><br>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<td width="95%">
							<input class="button" type="submit" value="Отмена" name="ot">
							<input class="button" type="submit" value="Переместить" name="move_x">
						</td>
					</tr>
<?php
	}

	if ($manager->isRename()) {
		foreach ($fl as $i) {
?>
					<tr>
						<td width="200">
							Старое имя:<br><div class="podpis">Текущее имя файла</div>
						</td>
						<td width="80%" valign=top>
							<input readonly type="hidden" size="30" name="afl[]" value="<?=$i?>" style='color:#888888'>
							<?=urldecode($i)?>
						</td>
					</tr>
					<tr>
						<td>
							Новое имя:<br><div class=podpis>Имя для сохранения</div>
						</td>
						<td valign="top">
							<input type="text" size="30" name="rfl[]" value="<?=urldecode($i)?>">
						</td>
					</tr>
<?php
		}
?>
					<tr>
						<td>
							<input class="button" type="submit" value="Отмена" name="ot">
							<input class="button" type="submit" value="Сохранить" name="ren_x">
						</td>
						<td>
							<!-- nothing -->
						</td>
					</tr>
<?php
	}

	if ($manager->isMakeDir()) {
?>
					<tr>
						<td width="200">
							Введите имя папки:<br>
							<div class="podpis">Имя для сохранения</div><br><br>
							<input class="button" type="submit" value="Отмена" name="ot">
							<input class="button" type="submit" value="Создать" name="md_x">
						</td>
						<td width="80%" valign="top">
							<input type="text" size="30" name="newname">
						</td>
					</tr>
<?php
	 }
?>
				</table>
			</form>
		</div>
	</div>
<?php
    include('tpl/footer.tpl');
?>  