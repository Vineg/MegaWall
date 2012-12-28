$(document).ready(function(){
	$(".delete").click(function(){
		pid=$(this).parent().parent().attr("tcid");
		remc(cid, 3, this);
	});
});

//function linit(obj){
//	$(".voteup", obj).click(function(){
//		cid=$(this).parent().parent().attr("cid");
//		votec(cid, 1, this);
//	});
//
//	$(".votedn", obj).click(function(){
//		cid=$(this).parent().parent().attr("cid");
//		votec(cid, -1, this);
//	});
//};