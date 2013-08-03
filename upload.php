<?php
	include '_config.php';
	//Получаем POST
	$namefile = $_FILES['music_file']['name'];
	$mail = $_POST['music_mail'];

	$filename = $namefile."_".$mail;
	//Формируем новое имя файла
	$filename = str_replace(".mp3", "", $filename);
	$filename = str_replace(".MP3", "", $filename);
	$filename = $filename.".mp3";

	//Удаляем лишние символы
	$filename = htmlspecialchars($filename, ENT_QUOTES, "utf-8");

	$filename = $_SERVER["DOCUMENT_ROOT"]."/music/".TEMP_UPLOAD."/".$filename;

	//Сохраняем файл
	if (move_uploaded_file($_FILES['music_file']['tmp_name'], $filename)) {
		print "<h1>Файл загружен</h1><h4>Сейчас вы будите перемещены обратно</h4>";
	} else {
    	print "<h4>Загрузить файл не удалось</h4>";
	}


	//Редиректим обратно
	$URL = "http://".$_SERVER['HTTP_HOST'];
    if (isset($_GET['back'])) {
        $URL = $_GET['back'];
    }
    if (isset($_POST['back'])) {
        $URL = $_POST['back'];
    }
?>
	<head>
		<link rel="stylesheet" href="/style.css" type="text/css" />
		<link rel="stylesheet" href="/element.css" type="text/css" />
		<meta http-equiv="Refresh" content="2; URL=<?php echo $URL; ?>">
	</head>