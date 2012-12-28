<?php
$cont=htmlspecialchars($_POST['text'], ENT_QUOTES);

print <<<EOQ
<form action="" method=POST>
<textarea name="text"></textarea>
<input type="submit"></input>
</form>
<br />
<br />
<noscript>$cont</noscript>
</input>
EOQ;
?>