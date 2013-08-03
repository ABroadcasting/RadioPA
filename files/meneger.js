
	function iprClick(ipr, cvet) {
		if (document.getElementById(ipr).checked) {
			document.getElementById('t_'+ipr).style.backgroundColor = '#E8E8FF';
			document.getElementById('t2_'+ipr).style.backgroundColor = '#E8E8FF';
		} else {
			document.getElementById('t_'+ipr).style.backgroundColor = cvet;
			document.getElementById('t2_'+ipr).style.backgroundColor = cvet;
		}
	}

	function s_all(obj) {
		var k = 0;
		var cvet = '';
  		var check = document.getElementsByName('fl[]');
   		for (var i=0; i<check.length; i++) {
   			var t = 't_'+i;
   			var t2 = 't2_'+i;
      		check[i].checked = obj.checked;
      		if (k > 2) { cvet = 'white';} else {cvet = '#F5F4F7';}
      		if (check[i].checked==true)	{
      			document.getElementById(t).style.backgroundColor = '#E8E8FF';
      			document.getElementById(t2).style.backgroundColor = '#E8E8FF';
      		} else {
      			document.getElementById(t).style.backgroundColor = cvet;
      			document.getElementById(t2).style.backgroundColor = cvet;
      		}
      		if (k > 4) { k = 0; } else  {k = k+1;}
      	}
	}

	function playmedia(ipr, fl) {
		var pl = "play_"+ipr;
		document.getElementById(pl).innerHTML = '<object type="application/x-shockwave-flash" data="files/dewplayer.swf?mp3='+fl+'&amp;showtime=1&amp;autostart=1" width="200" height="20"><param name="wmode" value="transparent" /><param name="movie" value="files/dewplayer.swf?mp3='+fl+'&amp;showtime=1&amp;autostart=1" /></object>'
	}