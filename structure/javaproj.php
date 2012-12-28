<?php
function process_request(){
		$core = new Include_core("proj/javaproj");
		$core->filedef=array("about");
		$core->fileset=array("changelog", "changepass", "infoedit");
		$core->file2file=array("lpreview"=>"post_preview", "chat"=>"shortcuts/chat", "update"=>"sys/userupdate", "yandex_4546a87d27a25d8f.txt"=>"files/dummy");
		$core->fold2file=array("functions"=>"get_function", "packages"=>"packages", "posts");
		$core->process_request();
}