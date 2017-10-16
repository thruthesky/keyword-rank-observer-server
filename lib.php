<?php

require 'observer/vendor/autoload.php';
use \Symfony\Component\DomCrawler\Crawler;

include 'db.php';


function _log($msg) {
	echo "$msg\n";
}

/**
 * 네이버 페이지를 긁어서 리턴한다.
 *
 * @note 최대한 웹 브라우저 처럼 보이게 하기 위해서 referer 및 기타 정보를 입력한다.
 *
 * @param $url
 * @param string $referer
 *
 * @return null|string
 */
function crawl_naver( $url, $referer = 'https://www.naver.com' ) {


	$headers = [
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
		"Accept-Encoding: gzip, deflate",
		"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
	];
	$o = [
		CURLOPT_URL => $url,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_HEADER => 0, // 결과 값에 HEADER 정보 출력 여부
		CURLOPT_FRESH_CONNECT => 1, // 캐시 사용 0, 새로 연결 1
		CURLOPT_RETURNTRANSFER => 1, // 리턴되는 결과 처리 방식. 1을 변수 저장. 2는 출력.
		CURLOPT_SSL_VERIFYPEER => 0, // HTTPS 검사 여부
		CURLOPT_REFERER => $referer,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
	];
	$ch = curl_init();
	curl_setopt_array( $ch, $o );
	$res = null;
	$html = null;
	try {
		$res = curl_exec( $ch );
	}
	catch ( Exception $e ) {
		echo "Exeption: " . $e->getMessage() . "\n";
	}
	if ( $res ) {
		try {
			$html = gzdecode ( $res );
		}
		catch( Exception $e ) {
			echo $res;
		}
	}
	else $html = null;
	curl_close( $ch );

//	echo $html;
	return $html;
}


/**
 * 네이버 데스크톱 통합 검색 페이지 중, 지식인 부분을 파싱해서, 1순위 부터 5순위 까지 제목과 URL 을 배열로 리턴한다.
 * @param $html
 *
 * @return array
 */
function parse_naver_desktop_first_page_html( $html ) {
	$c = new Crawler( $html );
	$lis = $c->filter('._kinBase > ul > li');
	$count = $lis->count();
	_log("result: $count");
	$rank = [];
	for ( $i = 0; $i < $count; $i ++ ) {
		$a = $lis->eq($i)->filter('.question')->filter('a')->first();
		$data = [];
		$data['title'] = $a->text();
		$data['href'] = $a->attr('href');
		$rank[] = $data;
	}
	return $rank;
}
function parse_naver_mobile_first_page_html( $html ) {
	$c = new Crawler( $html );
	$lis = $c->filter('.sc.sp_ntotal > .api_subject_bx > ul.lst_total > li.bx');
	$count = $lis->count();
	_log("result: $count");
	$rank = [];
	for ( $i = 0; $i < $count; $i ++ ) {
		$li = $lis->eq($i);
		$a = $li->filter('a');
		$data = [];
		$data['title'] = $a->filter('.total_tit')->first()->text();
		$data['href'] = $a->attr('href');
		$data['type'] = 'blog';

		if ( strpos( $data['href'], 'blog.') === false && strpos( $data['href'], 'post.') === false ) $data['type'] = 'kin';


		if ($data['type'] == 'blog') {
			$data['name'] = $li->filter('.sub_name')->first()->text();
		}
		else {
			$data['title'] = str_replace('질문', '', $data['title']);
		}
		$rank[] = $data;
	}
	return $rank;
}

/**
 * 네이버 지식인 페이지의 HTML 을 받아서, 답변자 이름을 배열로 리턴한다.
 * @note 지식인 페이지 HTML 이다. 통합검색 HTML 페이지가 아닌다.
 *
 * @param $html
 *
 * @return array
 */
function parse_naver_mobile_kin_page_html( $html ) {

	if ( empty($html) ) return;

	$names = [];
	$c = new Crawler( $html );
	$answers = $c->filter("a.button_friend");

	for ($i = 0; $i < $answers->count(); $i++) {
		$name = $answers->eq($i);
		$names[] = $name->attr('data-friend-view-name');
	}

	return $names;
}

function parse_naver_desktop_kin_page_html( $html ) {

	if ( empty($html) ) return;

	$names = [];
	$c = new Crawler( $html );
	$answers = $c->filter("h3 > em");

	for ($i = 0; $i < $answers->count(); $i++) {
		$name = $answers->eq($i);
		$names[] = $name->text();
	}

	return $names;
}


function save_ranks( $data ) {
	global $db;
	if ( empty($data) ) _log('no data');
	if ( ! is_array($data) ) _log('data is not array');


	$keyword = $data['keyword'];
	$platform = $data['platform'];


	for ( $j = 0; $j < count($data['rank']); $j ++ ) {
		$e = $data['rank'][$j];
		$rank = $j + 1;
		if ( isset($e['type'] ) ) $type = $e['type'];
		else $type = '';
		if ( $platform == 'desktop' || ( $platform == 'mobile' && $type == 'kin') ) {
			if ( !isset($e['names']) || empty($e['names']) || ! count($e['names']) ) {
				_log('no names');
			}
		}
		else if ( $platform == 'mobile' && $type == 'blog' ) {
			if ( !isset($e['name']) || empty($e['name']) ) _log('no name on blog');
			$e['names'] = [ $e['name'] ];
		}
		else _log('no platform');



		$date = date('Ymd');
		$time = date('Hi');
		$title = $db->escape($e['title']);
		$href = $db->escape($e['href']);


		$names = $e['names'];
		$db->query("START TRANSACTION");
		for ( $i = 0; $i < count($names); $i ++ ) {
			$name = $db->escape($names[$i]);
			$q = "
						INSERT INTO keyword_ranks
								(platform, keyword, `date`, `time`, `name`, `rank`, href, title, `type`)
						VALUES
								('$platform', '$keyword', '$date', '$time', '$name', '$rank', '$href', '$title', '$type')
					";
//			dog($q);
			$db->query($q);
		}
		$db->query("COMMIT");


	}


}



function code_exists( $code ) {
	global $db;
	return $db->get_var("SELECT idx FROM config WHERE `code`='$code'");
}
function config_set( $code, $data ) {
	if ( code_exists($code) ) config_update( $code, $data );
	else config_insert( $code, $data );
}
function config_get( $code ) {
	if ( code_exists($code) ) {
		global $db;
		return $db->get_var("SELECT `data` FROM config WHERE `code`='$code' ");
	}
	else return null;
}

function config_insert( $code, $data ) {
	global $db;
	$time = time();
	$q = "INSERT INTO config ( `code`, `data`, stamp ) VALUES ( '$code', '$data', $time)";
	$db->query($q);
}
function config_update( $code, $data ) {
	global $db;
	$db->query("UPDATE config SET `data`='$data' WHERE `code`='$code'");
}


function make_array( $str ) {
	$str = preg_replace('/\s*,\s*/', ',', $str);
	$arr = explode(',', $str);
	if ( empty($arr) ) return [];
	$ret = [];
	foreach( $arr as $a ) {
		if ( empty($a) ) continue;
		$ret[] = $a;
	}
	return $ret;
}
function regen_keyword( $str ) {
	return implode(',', make_array($str) );
}

/**
 * @return string
 */
function get_selectable_keywords() {
	return implode(',', get_keywords());
}
function get_keywords() {

	$desktop = make_array(config_get('desktop'));
	$mobile = make_array(config_get('mobile'));
//	$re = array_merge( $desktop, $mobile );

	$unique_array = [];
	foreach( $desktop as $e ) {
		$unique_array[$e] = true;
	}
	foreach( $mobile as $e ) {
		$unique_array[$e] = true;
	}
	$re = array_keys($unique_array);
	return $re;
}