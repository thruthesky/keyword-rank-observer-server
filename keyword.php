<?php

include 'lib.php';

if ( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'submit' ) {
	config_set( 'desktop', regen_keyword($_REQUEST['desktop']) );
	config_set( 'mobile', regen_keyword($_REQUEST['mobile']) );
}



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
	<h1 class="display-3">집계 할 키워드 선택</h1>
	<p class="lead">
		키워드 통계, 실시간 모니터링에 사용할 키워드를 입력하세요.
	</p>
	<hr class="my-4">

	<p>메뉴를 선택하세요.</p>
	<p class="lead">
		<a class="btn btn-danger btn-lg" href="query.php?mode=monitoring" role="button">실시간 모니터링</a>
		<a class="btn btn-primary btn-lg" href="query.php" role="button">통계</a>
		<a class="btn btn-secondary btn-lg" href="keyword.php" role="button">집계 키워드 관리</a>
	</p>
</div>

<div class="m-5">
	<form>
		<input type="hidden" name="mode" value="submit">
		<div class="form-group">
			<label for="exampleInputEmail1">데스크톱 키워드</label>
			<input name="desktop" value="<?php echo config_get('desktop')?>" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="데스크톱 집계 할 키워드 입력. 콤마로 여러개 입력.">
			<small id="emailHelp" class="form-text text-muted">콤마로 구분하여 여러개를 입력 할 수 있습니다. 예) 화상영어, 전화영어</small>
		</div>
		<div class="form-group">
			<label for="exampleInputPassword1">모바일 키워드</label>
			<input name="mobile" value="<?php echo config_get('mobile')?>" type="text" class="form-control" id="exampleInputPassword1" placeholder="모바일 집계 할 키워드 입력. 콤마로 여러개 입력.">
			<small id="emailHelp" class="form-text text-muted">콤마로 구분하여 여러개를 입력 할 수 있습니다. 예) 화상영어, 전화영어</small>
		</div>
		<button type="submit" class="btn btn-primary">저장하기</button>
	</form>
</div>

</body>
</html>