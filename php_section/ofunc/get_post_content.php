<?php
$pid=h2s($_GET[pid]);
print Post::get_content(pid2id($pid));