<?php
function getElementsByClassNameb(DOMDocument $DOMDocument, $ClassName) {
	$Elements = $DOMDocument->getElementsByTagName("*");
	$Matched = array();

	for($i=0;$i<$Elements->length;$i++) {

		if($Elements->item($i)->attributes->getNamedItem('class')->nodeValue == $ClassName) {
			$Matched[]=$Elements->item($i);
		}
	}
	return $Matched;
}

function get_attributes($node){
	foreach ($node->attributes as $attr)
	{
		$array[$attr->nodeName] = $attr->nodeValue;
	}
	return  $array;
}

function resize_elements($xml, $el, $maxwidth){
	$elements = $xml->getElementsByTagName($el);
	for($i=0; $i<$elements->length; $i++){
		$element=$elements->item($i);
		$sizes=get_sizes($element);
		$height0=$sizes[height];
		$width0=$sizes[width];
		if($width0>$maxwidth){
			$width=$maxwidth;
			$height=floor($height0*$width/$width0);
		}else{
			$width=$width0;
			$height=$height0;
		}
		$element->setAttribute("height", "$height");
		$element->setAttribute("width", "$width");
	}

	//cho htmlspecialchars(rxml($xml->saveHTML()));
	return $xml;
}

function img_onerror($xml){
	$elements = $xml->getElementsByTagName("img");
	for($i=0; $i<$elements->length; $i++){
		$element=$elements->item($i);
		$element->setAttribute("onerror", "brokenimage(this);");
	}

	//cho htmlspecialchars(rxml($xml->saveHTML()));
	return $xml;
}

function param_name_tolower($xml){
	$elements = $xml->getElementsByTagName("param");
	for($i=0; $i<$elements->length; $i++){
		$element=$elements->item($i);
		$val=strtolower($element->getAttribute("name"));
		$element->setAttribute("name", "$val");
	}

	//cho htmlspecialchars(rxml($xml->saveHTML()));
	return $xml;
}

function hide($xml, $el, $hideall){
	$elements = $xml->getElementsByTagName("$el");
	$i = $elements->length - 1;
	while ($i > -1) {
		$element = $elements->item($i);
		$element->setAttribute("wmode", "opaque");
		$sizes=get_sizes($element);
		$ihtml=rxml(innerHTML($xml, $element));
		//cho htmlspecialchars($ihtml);
		if($el=="object"){
			$ihtml=str_replace("<br />", " ", $ihtml);
			$ihtml=str_replace("<br/>", " ", $ihtml);
			$ihtml=str_replace("<br>", " ", $ihtml);
			$sizes=get_sizes($element->getElementsByTagName("embed")->item(0));
		}
		if($sizes[height]&&$sizes[width]){
			$ihtml="<div style=\"height:$sizes[height]px; width:$sizes[width]px;\">$ihtml</div>";
		}
		if($sizes[width]>st_vars::$max_width){$topbox=" topbox";}
		if($topbox||$hideall){
			$post=new Post();
			$post->text=$ihtml;
			$post->submit();
			$ncid=$post->pid;
			$ihtml=s2u($ihtml);
			$nhtml=<<<EOQ
		<div class="olist">
	        <a class="open">Открыть приложение</a>
	        <a class="close hidden">Скрыть приложение</a>
	        <div class="db"></div>
	        <div class="openobj hidden ars$topbox" mw_load="data:$ihtml" mw_pid="$ncid">
	        <div class="db"></div>
	        </div>
	    </div>
EOQ;
		}else{
			$nhtml=$ihtml;
		}
		$tmp = $xml->createDocumentFragment();
		$tmp->appendXML($nhtml);
		$element->parentNode->replaceChild($tmp, $element);
		$i--;
	}

}

function addembedparent($xml){
	$elements = $xml->getElementsByTagName("embed");
	for($i=0; $i<$elements->length; $i++){
		$element = $elements->item($i);
		//$element->addAttribute("wmode", "opaque");
		if($element->parentNode->tagName=="object"){continue;}
		$ihtml=innerHTML($xml, $element);

		$ihtml=str_replace("<br />", " ", $ihtml);
		$ihtml=str_replace("<br/>", " ", $ihtml);
		$ihtml=str_replace("<br>", " ", $ihtml);
		$ihtml=rxml($ihtml);
		$nhtml=<<<EOQ
		<object>
		$ihtml
			<param name="wmode" value="opaque"/>
        </object>
EOQ;
		$tmp = $xml->createDocumentFragment();
		$tmp->appendXML($nhtml);
		$element->parentNode->replaceChild($tmp, $element);
	}
	return $xml;
}

function rxml($s){
	$res=preg_replace("/<\?(.*)\?>/", "", $s);
	$res=preg_replace("/<xml([^>]*)>/", "", $res);
	$res=str_replace("</xml>", "", $res);
	$res=str_replace("<root>", "", $res);
	$res=str_replace("</root>", "", $res);
	return $res;

}

function get_sizes($element){
	$attrs=get_attributes($element);
	$height=$attrs[height];
	$width=$attrs[width];
	$style=$attrs[style];
	$stylear=s2ar($style, ";", ":");
	if(!$height){$height=$stylear[height];}
	if(!$width){$width=$stylear[width];}
	return array("width"=>h2i($width), "height"=>h2i($height));
}

function post_xml_edit($s, $params=array()){
	$xml=parse_xml($s);
	//$xml=resize_elements($xml, "embed", st_vars::$max_width);
	$xml=resize_elements($xml, "iframe", st_vars::$max_width);
	$xml=img_onerror($xml);
	$xml=param_name_tolower($xml);
	$xml=hide_blocks($xml,!$params[nohide]);
	$s=rxml($s);
	return (rxml($xml->saveXML()));
}

function parse_xml($s){
	$s=rxml($s);
	$s="<?xml version='1.0' encoding='UTF-8'?><root>".$s."</root>";
	//cho "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
	$xml = new DOMDocument;
	$xml->loadXML($s);
	return $xml;
}
?>