$(document).ready(function(){
	$(".regForm").submit(function(){
		checkForm($(this).serialize(), this);
		return false;
	});
	$(".reloadCaptcha").click(
		function(){
			$(".captcha", $(this).parent())[0].src="/files/captcha.php?r="+Math.random();
		}	
	);
});

function checkForm(ar, obj){
	//alert($.serializeArray(ar));
	$.post("/func/checkf.php", ar,
		function(data){
			if(data==1){obj.submit();}else{
				data=(data=="")?"Ошибка":data;
				co=$(".err", obj);
				co.html(data);
				co.slideDown("middle");
			}
	});
}

function sinit(obj){
	$(".reloadCaptcha", obj).click(
			function(){
				$(".captcha", $(this).parent())[0].src="/files/captcha.php?r="+Math.random();
			}	
	);
	return;
}