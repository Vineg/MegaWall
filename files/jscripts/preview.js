function submit(but, handler){
	but.disabled=true;
	ar="lpost=1";
	$.post("/func/"+handler+".php", ar,
			function(data){
		but.disabled=false;
		data=(data=="")?"Ошибка":data;
		form=$(but).closest(".form");
		co=$(".err", $(form));
		co.html(data);
		co.slideDown("middle");
	});
}