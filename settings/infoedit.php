<?php
if(!User::get_c_id()){l404();}
$lk=User::get_url();
User::get_url();
header("location:$lk?edit");