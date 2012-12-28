<?php
require("GTranslate.php");
error_reporting(E_ALL);
ini_set('display_error',1);

/**
* Example using RequestHTTP
*/
$translate_string = "Das ist wunderschön";
 try{
       $gt = new Gtranslate;
	//cho "[HTTP] Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."\n";

	/**
	* Lets switch the request type to CURL
	*/
	$gt->setRequestType('curl');
	//cho "[CURL] Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."\n";
	$translate_string	=	'hello';
	//cho "[CURL] Translating [$translate_string] English to German=> ".$gt->english_to_german($translate_string)."\n";
	//cho "[CURL] Translating [$translate_string] English to Portuguese=> ".$gt->english_to_portuguese($translate_string)."\n";
	//cho "[CURL] Translating [$translate_string] English to Italian=> ".$gt->english_to_italian($translate_string)."\n";
} catch (GTranslateException $ge)
 {
       //cho $ge->getMessage();
 }

?>
