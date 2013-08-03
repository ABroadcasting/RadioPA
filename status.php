<?php
	include('top.php');
	$status = Status::create();
	$status->handler();
	$status->update();

	/* Доступ к модулю */
	if ($request->hasPostVar('off_x') and $user['admin']!=1) {
		$error = "<i>Вы не можете выключить радио.</i><br>";
	}
?>
	<div class="body">
		<div class="title">Статус серверов</div>
		<div class="border">
			<?=!empty($error) ? $error: ''?>
			<div class="status">Для работы необходимы запущенные сервера.<br><br>Текущий статус:
<?php
	if(!$status->isIcecastRunned() and !$status->isEzstreamRunned()) {
?>
				<img src="images/status_off.jpg" border="0" width="100" height="30">
<?php
	}
?>
<?php
	if ($status->isIcecastRunned() and !$status->isEzstreamRunned()) {?>				<img src="images/status_on_air.jpg" border="0" width="100" height="30">
<?php
	}
?>
<?php
	if ($status->isIcecastRunned() and $status->isEzstreamRunned()) {?>				<img src="images/status_on.jpg" border="0" width="100" height="30">
<?php
	}
?>
			&nbsp;&nbsp;&nbsp;
		</div>
		<div>Используйте кнопки ниже что бы запустить или остановить сервера.</div>
		<br>
		<form method="POST" action="">
			<input type=image src="images/off1.jpg" width="180" height="70" name="off">
			<input type="hidden" name="off" value="off">
			<input type=image src="images/on2.jpg" width="180" height="70" name="on_air">
			<input type="hidden" name="on_air" value="on_air">
			<input type=image src="images/on3.jpg" width="180" height="70" name="on">
			<input type="hidden" name="on" value="on">
			<br>
			<input type=image src="images/next_track.jpg" width="170" height="30" name="next">
			<input type="hidden" name="next" value="next">
		</form>
	</div>
<?php
	include 'tracklist.php';
	if ($status->isIcecastRunned()) {

?>
		<div class="title">Точки монтирования</div>
		<div class="border">
			<style>
				.bgt1 td { background-color:#F5F4F7;}
			</style>
			<table width="97%" cellspacing="0" cellpadding="2" border="0">
				<tr>
					<td>Точка монтирования</td>
					<td>Играет</td>
					<td>Слушают</td>
					<td>Слушать</td>
				</tr>
<?php
		$bg = 0;
		foreach ($status->getStreams() as $stream) {
			if ($bg==0) {
				$bgt='bgt1';
			} else {
				$bgt='';
			}
?>
				<tr class='<?=$bgt?>'>
					<td>/<?=$stream['tochka']?></td>
					<td><?=$stream['cur_song']?></td>
					<td><?=$stream['listeners']?></td>
					<td>
						<a href='<?=$stream['link']?>'>
							<img src='images/winamp.gif' border='0' width='16' height='16'>
						</a>
					</td>
				</tr>
<?php
			if ($bg==0) {				$bg=1;
			} else {				$bg=0;
			}
 		}
?>
			</table>
		</div>
<?php
	}
?>
	</div>
<?php
    include('Tpl/footer.tpl.html');
?>  	