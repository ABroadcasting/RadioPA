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
    include('tpl/footer.tpl');
?>  	