<?php
	ob_start();
	include('top.php');

	/* Доступ к модулю */
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
    include('tpl/footer.tpl.html');
?>  