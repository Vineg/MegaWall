$(document).ready(function(){
	$(".delete").click(function(){
		pid=$(this).parent().parent().attr("pid") 
		rem(pid, 2, this);
	});
});