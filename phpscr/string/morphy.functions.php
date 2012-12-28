<?php

/**
 * 
 * Описание файла здесь: http://valera.ws/2007.09.05~morpho_search_in_mysql/
 * 
 */

/**
 * Подсветка слов поискового запроса
 *
 * @param string $whereText
 * @param string $whatText
 * @return string
 */
function Highlight($whereText, $whatText)
{

	$highlightWords = $highlightWordsRepl = array();
	$highlightWordsT = $this->Words2AllForms($_REQUEST['text']);
	
	foreach ( $highlightWordsT as $k => $v )
		if ( !$v )
		{
			$highlightWords[]  = "#\b($k)\b#isU";
			$highlightWordsRepl[] = '[highlight]\\1[/highlight]';
		}
		else
			foreach ( $v as $v1 )
			{
				$highlightWords[]  = "#\b($v1)\b#isU";
				$highlightWordsRepl[] = '[highlight]\\1[/highlight]';
			}
	
	return $message['message_text'] = preg_replace(array_reverse($highlightWords), '[highlight]$1[/highlight]', $whereText);
}
/**
 * Возвращает все словоформы слов поискового запроса
 *
 * @param string $text
 * @return array
 */
function Words2AllForms($text)
{
	require_once('phpmorphy/src/common.php');

	// set some options
	$opts = array(
		// storage type, follow types supported
		// PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
		// PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
		// PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
		'storage' => PHPMORPHY_STORAGE_MEM,
		// Extend graminfo for getAllFormsWithGramInfo method call
		'with_gramtab' => false,
		// Enable prediction by suffix
		'predict_by_suffix' => true, 
		// Enable prediction by prefix
		'predict_by_db' => true
	);
	
	$dir = 'phpmorphy/dicts';
	
	// Create descriptor for dictionary located in $dir directory with russian language
	$dict_bundle = new phpMorphy_FilesBundle($dir, 'rus');
	
	// Create phpMorphy instance
	$morphy = new phpMorphy($dict_bundle, $opts);
	
	// All words in dictionary in UPPER CASE, so don`t forget set proper locale
	// Supported dicts and locales:
	//  *------------------------------*
	//  | Dict. language | Locale name |
	//  |------------------------------|
	//  | Russian        | cp1251      |
	//  |------------------------------|
	//  | English        | cp1250      |
	//  |------------------------------|
	//  | German         | cp1252      |
	//  *------------------------------*
	// $codepage = $morphy->getCodepage();
	// setlocale(LC_CTYPE, array('ru_RU.CP1251', 'Russian_Russia.1251'));
	
	$words = preg_split('#\s|[,.:;!?"\'()]#', $text, -1, PREG_SPLIT_NO_EMPTY);
	
	$bulk_words = array();
	foreach ( $words as $v )
		if ( strlen($v) > 3 )
			$bulk_words[] = strtoupper($v);

	return $morphy->getAllForms($bulk_words);
}

/**
 * Возвращает начальные словоформы слов поискового запроса
 *
 * @param string $text
 * @return string
 */
function Words2BaseForm($text)
{
	require_once('phpmorphy/src/common.php');

	// set some options
	$opts = array(
		// storage type, follow types supported
		// PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
		// PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
		// PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
		'storage' => PHPMORPHY_STORAGE_MEM,
		// Extend graminfo for getAllFormsWithGramInfo method call
		'with_gramtab' => false,
		// Enable prediction by suffix
		'predict_by_suffix' => true, 
		// Enable prediction by prefix
		'predict_by_db' => true
	);
	
	$dir = 'phpmorphy/dicts';
	
	// Create descriptor for dictionary located in $dir directory with russian language
	$dict_bundle = new phpMorphy_FilesBundle($dir, 'rus');
	
	// Create phpMorphy instance
	$morphy = new phpMorphy($dict_bundle, $opts);
	
	// All words in dictionary in UPPER CASE, so don`t forget set proper locale
	// Supported dicts and locales:
	//  *------------------------------*
	//  | Dict. language | Locale name |
	//  |------------------------------|
	//  | Russian        | cp1251      |
	//  |------------------------------|
	//  | English        | cp1250      |
	//  |------------------------------|
	//  | German         | cp1252      |
	//  *------------------------------*
	// $codepage = $morphy->getCodepage();
	// setlocale(LC_CTYPE, array('ru_RU.CP1251', 'Russian_Russia.1251'));
	
	$words = preg_replace('#\[.*\]#isU', '', $text);
	$words = preg_split('#\s|[,.:;!?"\'()]#', $words, -1, PREG_SPLIT_NO_EMPTY);
	
	$bulk_words = array();
	foreach ( $words as $v )
		if ( strlen($v) > 3 )
			$bulk_words[] = strtoupper($v);
	
	$base_form = $morphy->getBaseForm($bulk_words);
	
	$fullList = array();
	if ( is_array($base_form) && count($base_form) )
		foreach ( $base_form as $k => $v )
			if ( is_array($v) )
				foreach ( $v as $v1 )
					if ( strlen($v1) > 3 )
						$fullList[$v1] = 1;
	
	$words = join(' ', array_keys($fullList));
	
	return $words;
}

?>