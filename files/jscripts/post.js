function linit(obj){
	$(".delete", obj).click(function(){
		pid=$(this).parent().parent().attr("pid");
		r=$(this).parent().parent().attr("rem");
		if(!r){r=4;}
		rem(pid, r, this);
	});
	$(".reloadCaptcha", obj).click(
			function(){
				$(".captcha", $(this).parent())[0].src="/files/scaptcha.php?r="+Math.random();
			}	
	);
}