<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
<?php
echo $_SERVER['HTTP_REFERER'];
log_msg($_SERVER['HTTP_REFERER'],"c2");
//if($_SERVER['HTTP_REFERER']=="http://"+Request::get_host()+"/files/c1.php"):
?>
<!-- <script language=javascript>
document.write("<img style=\"visibility:hidden\" width=1 height=1 src=http://lc.jetswap.net/lc?u=2083991&p=PDrxn81&r=" + Math.random() + ">");
</script>-->

<a href="<?php print $_GET[l]?>" id=nav>a1</a>
<?php //endif;?>
</body>
</html>