<?php 
     define('_SAPE_USER', 'f90b71e880c245fe3afba2974de89e65');
     require_once($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php'); 
     $sape_articles = new SAPE_articles();
     print $sape_articles->process_request();
?>
