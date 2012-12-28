<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<title></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" href="/design/style/style.css" type="text/css" media="screen, projection" />
    <script type="text/javascript" src="/js/jquery/jquery.js?v=1"></script>
    <script type="text/javascript" src="/js/jquery/jquery.corners.min.js"></script>
    <script>
	function change_main_sheet()
	{
		var v = document.getElementById('pageSheet').value;
		switch (v)
		{
			case '1':
				document.getElementById('mainsheetother').style.display = 'none';
				document.getElementById('mainsheetweather').style.display = 'block';
			break;
			case '2':
				document.getElementById('mainsheetweather').style.display = 'none';
				document.getElementById('mainsheetother').style.display = 'block';
			break;
		}
	}
	</script>
</head>

<body>

<div id="wrapper">

	<?php include("test/mould.php");
		$M = new Mould();
		$M->Head(); ?>
        
	<section id="middle">

		<div id="container">
			<div id="content">
            
            	<select id="pageSheet" onChange="change_main_sheet(this.value);">
                	<option selected value="1">Погода</option>
                    <option value="2">Прочее</option>
                </select>
                <table id="mainsheetother" style="display:none">
                 <tr>
                  <td align="center">
                    <?php $text = file_get_contents('content/informers/calend.ru.html');
					print($text); ?>
                  </td>
                  <td>  
                    <?php $text = file_get_contents('content/informers/calend.ru-today.html');
					print($text); ?>
                  </td>
                  <td>
                   <!-- Информер RedDay.RU (Санкт-Петербург)--><a href="" target="_new"><img src="http://redday.ru/informer/i_moon/297/wt.png" width="150" height="190" border="0" alt="Фазы Луны на RedDay.ru (Санкт-Петербург)" /></a><!-- / Информер RedDay.RU-->
                  </td>                  
                 </tr>
                </table>
                <table id="mainsheetweather">
                 <tr>
                  <td>
                   <table cellpadding=0 cellspacing=0 width=160 style="border:solid 1px #29aaff;font-family:Arial;font-size:12px;background-color:#ffffff"><tr><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=8 height=30 background="http://rp5.ru/informer/htmlinfa/topshl.png"  bgcolor=#29aaff> </td><td width=* align=center background="http://rp5.ru/informer/htmlinfa/topsh.png" bgcolor=#29aaff><a style="color:#ffffff; font-family:Arial;font-size: 12px;" href="http://rp5.ru/7285/ru"><b>Санкт-Петербург</b></a></td><td width=8 height=30 background="http://rp5.ru/informer/htmlinfa/topshr.png" bgcolor=#29aaff> </td></tr></table></td></tr><tr><td valign=top style="padding:0;"><iframe src="http://rp5.ru/htmla.php?id=7285&lang=ru&um=00000&bg=%23ffffff&ft=%23ffffff&fc=%2329aaff&c=%23000000&f=Arial&s=12&sc=4" width=100% height=364 frameborder=0 scrolling=no style="margin:0;"></iframe></td></tr></table>
                  </td>
                  <td>
                   <a href="http://www.gismeteo.ru/towns/26063.htm"><img src="http://informer.gismeteo.ru/G26063-W.GIF" alt="GISMETEO: Погода по г. Санкт-Петербург" title="GISMETEO: Погода по г. Санкт-Петербург" border=0></a>
                  </td>
                  <td>
                   <script type="text/javascript" src="http://pogoda.mail.ru/informer/weather.js?city=1301&view=2&encoding=utf"></script>
                  </td>
                  <td>
                   <a href="http://clck.yandex.ru/redir/dtype=stred/pid=7/cid=1228/*http://pogoda.yandex.ru/saint-petersburg"><img src="http://info.weather.yandex.net/saint-petersburg/1.png" border="0" alt="Яндекс.Погода"/><img width="1" height="1" src="http://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*http://img.yandex.ru/i/pix.gif" alt="" border="0"/></a>
                  </td>
                 </tr>
                </table>  
			</div><!-- #content-->
		</div><!-- #container-->
		<?php  $M->Left(); ?>

	</section><!-- #middle-->

</div><!-- #wrapper -->

	<?php $M->Foot(); ?>
    
</body>
</html>