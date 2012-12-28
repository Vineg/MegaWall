$.getScript("/files/jscripts/Script/ext.js");

var Command={
}

eval("function test(){alert(\"test\");}");
function processCommand(commandStr){
	var commandEnd=commandStr.regexIndexOf(/[^a-zA-Z]/g);
	//var patt=/[^a-zA-Z]/g;
	var param = commandStr.substr(commandEnd);
	command = commandStr.substr(0,commandEnd);
	if(!Command[command]){
		loadCommand(cmmand);
	}
	//var commandAr = command.split(" ");
	
}

function load(uri,onComplete){
	$.ajax({
		url: uri,
		success: onComplete
	});
}

function loadCommand(command){
	$.getScript('/files/jscripts/Script/main.js?r='+Math.random(), function() {
	    //alert('Load was performed.');
	});
}

$(document).ready(function(){
	$("#mainInput")[0].focus();
});