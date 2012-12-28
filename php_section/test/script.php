<head>
	<script src=/files/jscripts/jq.js></script>
	<script>
	$.getScript('/files/jscripts/Script/main.js?r='+Math.random(), function() {
	    //alert('Load was performed.');
	});
	</script>
</head>
<form onsubmit='processCommand($("#mainInput").val()); return false;'>
<input id="mainInput"/>
</form>