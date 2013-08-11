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
	include('top.php') ;

	$file = FileManager::create();
	$file->handler();
	$ssh = Ssh::create();
	$setting = Setting::create();
	$setting->handler();
?>

	<div class="body">
		<div class="title">Добро пожаловать</div>
		<div class="border">
<?php

	if ($user['admin'] == 0) {?>
			Вы зашли как <i>DJ</i>. Вам открыт доступ к модулям 'Стастистика' и частично к  'Ваши DJ' и 'Статус'.
<?php
    } else {
?>
    		Вы зашли как <i>Администратор</i>. Вам открыт доступ ко всем модулям.
<?php
    }
?>
	Пожалуйста, используйте главное меню для работы с доступными сервисами.
	<br><br>
	Система: <b>RadioCMS</b><br>
	Версия: <b><?=RADIOCMS_VERSION?></b>
	<br>
<?php
	$count = $file->getCountTempFiles();
	$pokazat = "";
	if ($count >= 1 and TEMP_UPLOAD != "") {		$pokazat = " — <a href='/radio/manager.php?fold=".$request->getMusicPath().TEMP_UPLOAD."'>Посмотреть</a>";
	} else {		$count = 0;
	}
?>
			Файлов во временной папке Upload: <b><?=$count?></b><?=$pokazat?>
<?php
?>
	<br>
<?php
    if (!$ssh->checkEzstreamCompatibility()) {
?>
            <div><span class="red">Установлен ezstream не с сайта RadioCMS, существуют ограничения на длинну id3-тегов</span></div>
<?php
    }   
?>
<?php
	if (
		DIR_SHOW == "on" and
		DIR_NAME != "" and
		DIR_URL != "" and
		DIR_STREAM != "" and
		DIR_DESCRIPTION != "" and
		DIR_GENRE != ""
	) {
?>
			<div>Ваше радио <span class="green">отображается</span> в каталоге RadioCMS</div>
<?php
	} else {?>			<div>Ваше радио <span class="red">не отображается</span> в каталоге RadioCMS — <a href='setting_dir.php'>Исправить</a></div>
<?php	}
?>
<?php
	if ( file_exists("install.php")) {
?>
			<div><span class="red">install.php не удалён</span> — <a href="?del_install=1">Удалить</a></div>
<?php
	}
?>
<?php
			include('tpl/error.tpl');
?>
			<br><br>
			<img style="position: absolute; margin-top: -1px;" src="images/go.png" border="0"> <a style="position: absolute; margin-left: 17px;" href="http://radiocms.ru" target="_blank">Официальный сайт</a>
			<br>
			<br>
			<form method="POST" action="">
				<textarea name="main_text" style="width: 500px; height: 100px;"><?=$setting->getDescription()?></textarea>
				<p>
					<input class="button" type="submit" value="Сохранить">
				</p>
			</form>
		</div>
	</div>
<?php
    include('tpl/footer.tpl');
?>  	