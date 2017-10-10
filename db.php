<?php

$db_user = 'keywordobserver';
$db_name = 'keyword_rank_observer';
$db_password = 'Mon3:56PM';



include_once "ezSQL/shared/ez_sql_core.php";
include_once "ezSQL/mysqli/ez_sql_mysqli.php";



$db = new ezSQL_mysqli( $db_user, $db_password, $db_name,'www.sonub.com');

$db->query("SET CHARACTER SET utf8");
$db->query("set session character_set_connection=utf8;");
$db->query("set session character_set_results=utf8;");
$db->query("set session character_set_client=utf8;");


