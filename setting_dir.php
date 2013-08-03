<?php
	include('top.php');
	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$statistic = Statistic::create();
	$setting = Setting::create();
	$setting->handler();

	// обходим кеш строной
	if ($request->hasPostVar('request')) {
	    if ($request->getPostVar('dir_show') == "on") {
		  $statistic->updateDirectory();
        }    
		Header("Location: setting_dir.php");
	}
?>
	<div class="body">
		<div class="navi"><a href="setting.php">Настройки радио</a></div>
		<div class="navi"><a href="setting_system.php">Настройки системы</a></div>
		<div class="navi_white"><a href="setting_dir.php">Каталог</a></div>
		<br><br>
		<div class="title">Каталог RadioCMS</div>
			<form method="POST" action="setting_dir.php">
				<div class="border">
					<table border="0" width="97%" cellpadding="0" class="paddingtable">
						<tr>
							<td width="150" valign="top">
								Название станции:<br>
							</td>
							<td valign="top">
								<input maxlength="50" size="35" name="dir_name" type="text" value="<?=DIR_NAME?>"><br>
								<div class="podpis">кратко</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Сайт:<br>
							</td>
							<td valign="top">
								<input maxlength="60" size="35" name="dir_url" type="text" value="<?=DIR_URL?>"><br>
								<div class="podpis">с http://</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Адрес потока:<br>
							</td>
							<td valign="top">
								<input maxlength="80" size="35" name="dir_stream" type="text" value="<?=DIR_STREAM?>"><br>
								<div class="podpis">с http://</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Описание:<br>
							</td>
							<td valign="top">
								<input maxlength="80" size="65" name="dir_description" type="text" value="<?=DIR_DESCRIPTION?>"><br>
								<div class="podpis">кратко</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Жанр:<br>
							</td>
							<td valign="top">
								<input maxlength="10" size="35" name="dir_genre" type="text" value="<?=DIR_GENRE?>"><br>
								<div class="podpis">одно слово</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">
								Битрейт:<br>
							</td>
							<td valign="top">
								<select size="1" name="dir_bitrate" style="width:100px;">
									<option <?=(DIR_BITRATE=='64')? 'selected':''?> value="64">64 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='96')? 'selected':''?> value="96">96 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='128')? 'selected':''?> value="128">128 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='192')? 'selected':''?> value="192">192 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='256')? 'selected':''?> value="256">256 Кбит\сек</option>
									<option <?=(DIR_BITRATE=='VBR')? 'selected':''?> value="VBR">VBR</option>
								</select>
								<br><div class="podpis">VBR - переменный битрейт</div>
							</td>
						</tr>
						<tr>
							<td width="150" valign="top">&nbsp;</td>
							<td valign="top">&nbsp;</td>
						</tr>
						<tr>
							<td width="150" valign="top">Отображать в каталоге:<br></td>
							<td valign="top">
								<select size="1" name="dir_show" style="width:60px;">
									<option <?=(DIR_SHOW=='off')?'selected':''?> value="off">Нет</option>
									<option <?=(DIR_SHOW=='on')?'selected':''?> value="on">Да</option>
								</select>
								<br><div class="podpis">будет отображаться только если заполнены все поля</div>
							</td>
						</tr>
					</table>
					<input type="text" name="request" size="1" value="request" style="visibility: hidden;"><br>
					<input class="button" type="submit" value="Сохранить" name="B1">
				</div>
			</form>
		</div>
		<br>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	