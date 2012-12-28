function share(obj){
//	var jobj=$(obj);
//	var post=jobj.closest(".Post");
//	var content=$(".PostContent", post).text();
//	content=content.substr(0, Math.max(500, content.length));
//	var link=jobj.attr("href");
//	//link+="&description="+encodeURIComponent(content);
//	jobj.attr("href", link);
//	alert(link);
//	var width = 554;
//	var height = 349;
//	var left = (screen.width - width) / 2;
//	var top = (screen.height - height) / 2;
//	var url = this._base_domain + 'share.php';
//	var popupParams = 'scrollbars=0, resizable=1, menubar=0, left=' + left + ', top=' + top + ', width=' + width + ', height=' + height + ', toolbar=0, status=0';
//	windows.open(link, );
	return VK.Share.click(0, obj);
}

function getShareButton(pid, title, uri){
	var content=$(".PostContent",$("#p"+pid));
	var description=content.clone();
	$(".open", description).remove();
	$(".close",description).remove();
	$(".noshare",description).remove();
	description=description.text();
	
	description=replaceAll(description, "\n","");
	description=replaceAll(description, "\"","\\\"");
	var img=$("img", content).attr("src");
	//description=replaceAll(description, "\r","\\r");
	//title=title.replace("\n","\\n");
	var script="<script type=\"text/javascript\">document.write(VK.Share.button({url:\""+uri+"\", noparse:\"1\", title:\""+title+"\", description:\""+description+"\", image:\""+img+"\"},{custom:\"1\", type: \"link_noicon\", text: \"<span class=\\\"link shadow\\\" style=\\\"font-family: Georgia,'Times New Roman',Times,Serif;font-size: 11px;\\\" class=\\\"shadow control\\\">Опубликовать Вконтакте</span>\"}));</script>";
	return script;
}