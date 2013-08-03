	function prog_rasp() {
		if (document.getElementById('chasi').style.display == 'block') {
			document.getElementById('rasp').style.display = 'block';
			document.getElementById('chasi').style.display = 'none';
			document.getElementById('i_rasp').checked = 'false';
		}
	}

	function rasp_chasi() {
		if (document.getElementById('i_program').checked != true) {
			if (document.getElementById('chasi').style.display == 'block' ) {
				document.getElementById('chasi').style.display = 'none';
				document.getElementById('rasp').style.display = 'block';
			} else {
				document.getElementById('chasi').style.display = 'block';
				document.getElementById('rasp').style.display = 'none';
			}
		} else {
			alert('Программа может быть запущена только по расписанию');
			document.getElementById('i_rasp').checked = 'false';
		}
	}

	function pokazat(eventName, id) {
		blockId = eventName+'_timeblock_'+id;
		shiftId = id-1;
		buttonId = eventName+'_button_'+shiftId;

		if (document.getElementById(blockId).style.display == 'none') {
			document.getElementById(blockId).style.display = 'block';
			document.getElementById(buttonId).value = '-';
		} else {
			document.getElementById(blockId).style.display = 'none';
			if (id == 1) {
				document.getElementById(eventName+'_timeblock_2').style.display = 'none';
				document.getElementById(eventName+'_button_1').value = '+';
			}
			document.getElementById(buttonId).value = '+';
		}
	}