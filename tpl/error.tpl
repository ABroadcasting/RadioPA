<?php
	$errors = array();

	if(!$ssh->isConnected()) {
		$errors[] = "Соеденение ssh не установлено";
	}

	if (!function_exists("ssh2_connect")) {
		$errors[] = "Библиотека libssh2 не установлена. Работа с системой невозможна.";
	}

	$doc_file = $request->getRadioPath();

	if (is_readable("_config.php") and !is_writeable("_config.php") ) {
		$ssh->sshExec("chmod 777 ".$doc_file."_config.php");
	}

	if (
		is_readable("_config.php") and
		(!is_writeable("_system.php") or !is_readable("_system.php"))
	) {
		$ssh->sshExec("chmod 777 ".$doc_file."_system.php");
	}

	if (
		is_readable("_config.php") and
		(!is_writeable(PLAYLIST) or !is_readable(PLAYLIST))
	) {
		$ssh->sshExec("chmod 777 ".PLAYLIST);
	}
	
	if (!is_writeable(PLAYLIST) or !is_readable(PLAYLIST)) {
		$errors[] = "playlist.txt недоступен для чтения или записи (не правильно настроен open_basedir?)";
	}
	
	if (!is_writeable("_system.php") or !is_readable("_system.php")) {
		$errors[] = "Файл _system.php недоступен для чтения или записи.";
	}
	
	if (!is_writeable("_config.php") or !is_readable("_config.php")) {
		$errors[] = "Файл _config.php недоступен для чтения или записи.";
	}
?>

<?php
	if (!empty($errors)) {
		foreach ($errors as $error) {
?>
			<div><span class="red"><?=$error?></span></div>
<?php
		}
	}
?>