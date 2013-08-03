<?php
	include('top.php');
	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}
	$setting = Setting::create();
	$setting->handler();

	// обходим кеш строной
	if ($request->hasPostVar('request')) {
		Header("Location: setting_system.php");
	}
?>
	<div class="body">
		<div class="navi"><a href="setting.php">Настройки радио</a></div>
		<div class="navi_white"><a href="setting_system.php">Настройки системы</a></div>
		<div class="navi"><a href="setting_dir.php">Каталог</a></div>
		<br><br>
		<div class="title">Настройки системы</div>
		<form method="POST" action="setting_system.php">
			<div class="border">
				<table border="0" width="97%" cellpadding="0" class="paddingtable">
					<tr>
						<td width="104" valign="top">
							IP-адрес:
						</td>
						<td valign="top">
							<input type="text" name="ip" size="35" value="<?=IP?>"><br>
							<div class="podpis">для соеденения ssh</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							WEB-адрес:
						</td>
						<td valign="top">
							<input type="text" name="url" size="35" value="<?=URL?>"><br>
							<div class="podpis">полный адрес сайта без / на конце</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Порт:
						</td>
						<td valign="top">
							<input type="text" name="port" size="35" value="<?=PORT?>"><br>
							<div class="podpis">порт потока</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
<?php
	if ($user['dj'] == USER)  {?>
					<tr>
						<td width="104" valign="top">
							Логин:
						</td>
						<td valign="top">
							<input type="text" name="setting_user" size="35" value="<?=USER?>"><br>
							<div class="podpis">для входа в админку</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Пароль:
						</td>
						<td valign="top">
							<input type="password" name="setting_password" size="35" value="<?=PASSWORD?>"><br>
							<div class="podpis">введите пароль</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
<?php
	}
?>
					<tr>
						<td width="104" valign="top">Конфигурация IceCast:</td>
						<td valign="top">
							<input type="text" name="cf_icecast" size="55" value="<?=CF_ICECAST?>"><br>
							<div class="podpis">полный путь до файла с конфигурацией</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Конфигурация<br>ezstream:
						</td>
						<td valign="top">
							<input type="text" name="cf_ezstream" size="55" value="<?=CF_EZSTREAM?>"><br>
							<div class="podpis">полный путь до файла с конфигурацией</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">
							Адрес<br>плейлистa:
						</td>
						<td valign="top">
							<input type="text" name="playlist" size="55" value="<?=PLAYLIST?>"><br>
							<div class="podpis">полный путь до файла плейлиста</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="104" valign="top">Папка для<br>загрузки:</td>
						<td valign="top">
							<input type="text" name="temp_upload" size="55" value="<?=TEMP_UPLOAD?>"><br>
							<div class="podpis">в каталоге music (без полного пути)</div>
						</td>
					</tr>
					<tr>
						<td width="104" valign="top">&nbsp;</td>
						<td valign="top">&nbsp;</td>
					</tr>
				</table>
				Будьте очень внимательны при заполнении!
				<br><br>
				<input class="button" type="submit" value="Сохранить" name="request">
			</div>
		</form>
	</div>
<?php
    include('tpl/footer.tpl.html');
?>  	