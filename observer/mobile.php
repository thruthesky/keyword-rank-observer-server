<?php
include '../lib.php';


$keywords = get_keywords();

foreach( $keywords as $k ) {
	crawl_mobile_keyword($k);
}

function crawl_mobile_keyword( $keyword ) {
	$k = urlencode( $keyword );
	$url = "https://m.search.naver.com/search.naver?query=$k&where=m&sm=mtp_hty";
	_log("keyword: $keyword, begin at " . date('r'));
	$data = [ 'time' => time(), 'platform' => 'mobile', 'keyword' => $keyword ];
	$html = crawl_naver( $url, 'https://m.naver.com/' );
	$ranks = parse_naver_mobile_first_page_html( $html );
	print_r($ranks);
	$data['count'] = count($ranks);
	for ( $i = 0; $i < count($ranks); $i ++ ) {
		if ( isset($ranks['type']) && $ranks['type'] == 'blog' ) continue;
		$html = crawl_naver( $ranks[$i]['href'], $url );
		$names = parse_naver_mobile_kin_page_html( $html );
		$ranks[$i]['names'] = $names;
	}
	$data['rank'] = $ranks;
	print_r($data);
	save_ranks($data);
	_log("finished: $keyword");
}
