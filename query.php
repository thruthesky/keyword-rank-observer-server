<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
</head>
<body>
<?php

include 'db.php';
$rows = $db->get_results("SELECT * FROM keyword_ranks");
print_r($rows);

?>
</body>
</html>
