<?php
	include('top.php');
	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}
	$setting = Setting::create();
	$setting->handler();

	// обходим кеш строной
	if ($request->hasPostVar('request')) {		Header("Location: setting.php");
	}
?>
	<div class="body">
		<div class="navi_white"><a href="setting.php">Настройки радио</a></div>
		<div class="navi"><a href="setting_system.php">Настройки системы</a></div>
		<div class="navi"><a href="setting_dir.php">Каталог</a></div>
		<br><br>
		<div class="title">Настройки радио</div>
		<form method="POST" action="setting.php">
			<div class="border">
				<table border="0" width="97%" cellpadding="0" class="tablepadding">
					<tr>
						<td width="331" valign="top">
							Системные символы:<br>
							<div class="podpis">песни содержащие эти символы не будут показаны в списке последних песен</div>
						</td>
						<td valign="top">
							<input type="text" name="system_symvol" value="<?=SYSTEM_SYMVOL?>" style="width: 200px;"><br>
						</td>
					</tr>
					<tr>
						<td><div class="podpis"></div></td><td></td>
					</tr>
					<tr>
						<td width="331" valign="top">
							Ваши потоки:<br>
							<div class="podpis">укажите через запятую, например: <br>play, play32, live</div>
						</td>
						<td valign="top">
							<input type="text" name="system_stream" value="<?=$setting->getSystemStream()?>" style="width: 200px;"><br>
						</td>
					</tr>
					<tr>
						<td><div class="podpis"></div></td><td></td>
					</tr>
					<tr>
						<td width="331" valign="top">
							Нет повторам:<br>
							<div class="podpis">гарантирует исключение повторов в эфире в течение установленого числа песен
							<span class="red"><?=$setting->checkNetPovtorov()?></span>
							</div>
						</td>
						<td valign="top">
							<select size="1" name="net_povtorov" style="width:100px;">
								<option <?=(NO_REPEAT=='1')? 'selected' : ''?>>1</option>
								<option <?=(NO_REPEAT=='10')? 'selected' : ''?>>10</option>
								<option <?=(NO_REPEAT=='30')? 'selected' : ''?>>30</option>
								<option <?=(NO_REPEAT=='50')? 'selected' : ''?>>50</option>
								<option <?=(NO_REPEAT=='80')? 'selected' : ''?>>80</option>
								<option <?=(NO_REPEAT=='100')? 'selected' : ''?>>100</option>
								<option <?=(NO_REPEAT=='120')? 'selected' : ''?>>120</option>
								<option <?=(NO_REPEAT=='150')? 'selected' : ''?>>150</option>
								<option <?=(NO_REPEAT=='170')? 'selected' : ''?>>170</option>
								<option <?=(NO_REPEAT=='200')? 'selected' : ''?>>200</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div class="podpis"></div></td><td></td>
					</tr>
					<tr>
						<td width="331" valign="top">
							Ограничение песен:<br>
							<div class="podpis">ограничение количества песен добовляемых в<br>playlist.txt</div>
						</td>
						<td valign="top">
							<select size="1" name="limit_event" style="width:100px;">
								<option <?=(LIMIT_EVENT=='200') ? 'selected' : ''?>>200</option>
								<option <?=(LIMIT_EVENT=='500') ? 'selected' : ''?>>500</option>
								<option <?=(LIMIT_EVENT=='1000') ? 'selected' : ''?>>1000</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div class="podpis"></div></td><td></td>
					</tr>
					<tr>
						<td width="331" valign="top">
							Лимит заказов:<br>
							<div class="podpis">ограничение количества заказов находящихся в обработке за один раз</div>
						</td>
						<td valign="top">
							<select size="1" name="limit_zakazov" style="width:100px;">
								<option <?=(LIMIT_ZAKAZOV=='2') ? 'selected':''?>>2</option>
								<option <?=(LIMIT_ZAKAZOV=='3') ? 'selected':''?>>3</option>
								<option <?=(LIMIT_ZAKAZOV=='4') ? 'selected':''?>>4</option>
								<option <?=(LIMIT_ZAKAZOV=='5') ? 'selected':''?>>5</option>
								<option <?=(LIMIT_ZAKAZOV=='6') ? 'selected':''?>>6</option>
							</select>
						</td>
					</tr>
				 	 <tr>
						<td><div class="podpis"></div></td><td></td>
					</tr>
					<tr>
						<td width="331" valign="top">
							Транслит:<br>
							<div class="podpis">Все русские id3-теги будут переведены в транслит</div>
						</td>
						<td valign="top">
							<select size="1" name="translit" style="width:100px;">
								<option <?=(TRANSLIT=='on') ? 'selected':''?> value="on">Да</option>
								<option <?=(TRANSLIT=='off') ? 'selected':''?> value="off">Нет</option>
							</select>
						</td>
					</tr>
				</table>
				<br>
				<input class="button" type="submit" value="Сохранить" name="request">
			</div>
		</form>
		<br>
		<br>
	</div>
<?php
    include('Tpl/footer.tpl.html');
?>  	