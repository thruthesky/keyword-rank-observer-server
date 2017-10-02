<?php


include 'db.php';
$rows = $db->get_results("SELECT * FROM keyword_ranks");
print_r($rows);

