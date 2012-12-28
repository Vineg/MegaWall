function linit(obj){
	//alert($('[name="encoding"]')[0]);
	$("form", obj).submit(function(){
		var enc = $('[name="encoding"]', this);
		if(!enc.attr("value")){
			alert("Введите кодировку сайта.");
			return false;
		}
		return true;
	});
}