//$(document).ready(function(){
////	$(".submit").click(function(){
////		formL(this);
////		return false;
////	});
//	$(".reloadCaptcha").click(
//		function(){
//			$(".captcha", $(this).parent())[0].src="/files/scaptcha.php?r="+Math.random();
//		}	
//	);
//});

//function formL(but){
//		but.disabled=true;
//		form=$(but).closest("form");
//		req=$(form).serialize();
//		if($(but).attr("name")=="prev"){req+="&preview=1";}
//		sendForm(req, but);
//		form.submit(function(){return false;});
//		return false;
//}

//function sendForm(ar, but){
//	//alert($.serializeArray(ar));
//	$.post("/func/new/new.php", ar,
//		function(data){
//			but.disabled=false;
//			data=(data=="")?"Ошибка":data;
//			form=$(but).closest("form");
//			co=$(".err", form);
//			co.html(data);
//			co.slideDown("middle");
//	});
//}

function sinit(obj){
	$(".reloadCaptcha", obj).click(
			function(){
				$(".captcha", $(this).parent())[0].src="/files/scaptcha.php?r="+Math.random();
			}	
	);
}