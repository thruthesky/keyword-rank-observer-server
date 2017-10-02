<?php

$db_user = 'keywordobserver';
$db_name = 'keyword_rank_observer';
$db_password = 'Mon3:56PM';



include_once "ezSQL/shared/ez_sql_core.php";
include_once "ezSQL/mysqli/ez_sql_mysqli.php";


$db = new ezSQL_mysqli( $db_user, $db_password, $db_name,'localhost');
