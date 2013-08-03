<?php
	include('top.php');
	$file = FileManager::create();
	$nowplay = Nowplay::create();
	$statistic = Statistic::create();
	$statistic->updateAll();
    
    $tracklist = Tracklist::create();
    $tracklist->update();
?>
	<div class="body">
		<div class="navi_white"><a href="statistic.php">Общая</a></div>
		<div class="navi"><a href="statistic_client.php">По слушателям</a></div>
		<br><br>
		<div class="title">Статистика радио</div>
		<div class="border">
			Сейчас слушают: <?=$statistic->getListeners()?> (потоков: <?=$statistic->getStreamCount()?>), динамика за последние 24 часа:<br><br>
            <?=$nowplay->getDinamika();?>
   			<br><br><br>
<?php
	$disk = $file->getDiskInfo();
?>
			Размер дискового пространства:<br><br>
			<table border="0" width="400" cellspacing="0" cellpadding="0">
				<tr>
					<td class="graph_g2_1" width="<?=$disk['zan']['proc']?>%" align="center"></td>
					<td width="1">
						<img src="images/blank.gif" border="0">
					</td>
					<td class="graph_g2_2" width="<?=$disk['free']['proc']?>%" align="center"></td>
				</tr>
			</table>
			<table border="0" width="400" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<div class="minitext" style="position:relative; top:-19px; left:0px;">
							Занято: <?=$disk['zan']['mb']?> \ Свободно: <?=$disk['free']['mb']?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<br>
		<div>
		<div class="title">Последние песени</div>
		<div class="border">
			<table width="97%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="15%">Время</td>
				<td width="85%">Песня</td>
			</tr>
<?php
	$i = 0;
?>
<?php
	foreach ($statistic->getLastSongs() as $line) {
		$time = date("H:i:s (d.m)", $line['time']);
?>
			<tr>
        		<td <?=($i!=1) ? 'bgcolor=#F5F4F7' : ''?>>
        			<?php echo $time ?>
        		</td>
				<td <?=($i!=1) ? 'bgcolor=#F5F4F7' : ''?>>
					<?=($line['title']== " - ") ? "Нет данных" : $line['title']?>
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
	<br><br>
	</div>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	