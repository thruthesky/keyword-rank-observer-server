<?php

?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>키워드 모니터링</title>


	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

	<style>
		body {
			font-family: "Malgun Gothic", "Gulim", sans-serif;
		}
	</style>
</head>
<body>

<div class="jumbotron m-5">
	<h1 class="display-3">키워드 모니터링 툴</h1>
	<p class="lead">
		키워드를 순위를 집계하고 통계를 볼 수 있으며, 실시간 모니터링을 할 수 있습니다.
	</p>
	<hr class="my-4">
	<p>메뉴를 선택하세요.</p>
	<p class="lead">
		<a class="btn btn-danger btn-lg" href="query.php?mode=monitoring" role="button">실시간 모니터링</a>
		<a class="btn btn-primary btn-lg" href="query.php" role="button">통계</a>
		<a class="btn btn-secondary btn-lg" href="keyword.php" role="button">집계 키워드 관리</a>
	</p>
</div>
</body>
</html>