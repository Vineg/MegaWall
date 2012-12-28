$(document).ready(function(){
	$(".delete", ".Post").click(function(){
		id=$(this).parent().attr("sid");
		el=this;
		$.post("/func/rem.php", {id:id, from:"safe_sites"},
			function(data){
			vmsg=$(".vmsg", $(el).parent());
			vmsg.removeClass("err ok");
			cont="";
			if(data=="1"){cont="Ок"; vmsg.addClass("ok");del=400;}else{
				cont="Ошибка";vmsg.addClass("err"); del=800;
			}
			vmsg.text(cont);
			if(vmsg.css("opacity")=="0"){vmsg.stop().animate({ opacity: "1" }, 100).delay(del).animate({ opacity: "0" }, 300);}
		});
	});
});