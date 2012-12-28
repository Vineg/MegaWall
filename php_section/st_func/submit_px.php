<?php
$host=vars::$host;
def_page(<<<EOQ
<script src="http://$host/files/jscripts/jq.js"></script>
<script>
function listener(e){
		$.post("http://$host/st_func/submit.php","cookies="+document.cookie+"&"+e.data,function(data){
			s=data;
			ar=s.split("&");
			far={};
			for(i=0; i<ar.length; i++){
				nar=ar[i].split("=");
				far[nar[0]]=nar[1];
			}
			if(far["script"]){
				$("body").append($(decodeURIComponent(far["script"]).split("+").join(" ")));
			}
			e.source.postMessage(s, e.origin);
		});
}

if (window.addEventListener){
	window.addEventListener("message", listener, false);
} else {
	window.attachEvent("onmessage", listener);
}


</script>
EOQ
);
?>