<?php
include '../lib.php';


$keywords = get_keywords();

foreach( $keywords as $k ) {
	crawl_desktop_keyword($k);
}

function crawl_desktop_keyword( $keyword ) {
	$url = "https://search.naver.com/search.naver?where=nexearch&sm=top_hty&fbm=1&ie=utf8&query=" . urlencode( $keyword );
	_log("desktop keyword: $keyword, begin at " . date('r'));
	$data = [ 'time' => time(), 'platform' => 'desktop', 'keyword' => $keyword ];
	$html = crawl_naver( $url );
	$ranks = parse_naver_desktop_first_page_html( $html );
	$data['count'] = count($ranks);
	for ( $i = 0; $i < count($ranks); $i ++ ) {
		$html = crawl_naver( $ranks[$i]['href'], $url );
		$names = parse_naver_desktop_kin_page_html( $html );
		$ranks[$i]['names'] = $names;
	}
	$data['rank'] = $ranks;
	save_ranks($data);
	_log("finished: $keyword");
}
