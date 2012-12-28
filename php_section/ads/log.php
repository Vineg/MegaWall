<?php
fp("log.txt", $_SERVER[REMOTE_ADDR]." ".$_SERVER['HTTP_REFERER']."\n", true);