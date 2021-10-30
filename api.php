<?php
if (isset($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $_SERVER['SERVER_NAME'] && !empty($_GET['t']) && !empty($_GET['v'])) {
	$ch = curl_init('https://dev.ytapi.com/'.($t = $_GET['t']).'/'.($v = $_GET['v']));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1)');
	$htm = curl_exec($ch);
	curl_close($ch);
	$dom = @DOMDocument::loadHTML($htm);
	$url = '//invidious.snopyta.org/latest_version?local=true&itag=';
	if ($t == 'v') {
		if ($dom->getElementsByTagName('video')->item(0)) {
			$a = explode("vjs.src([{src:'", str_replace(["\n", ' '], '', $dom->textContent));
			$b = explode("',type", $a[1]);
			$url = $b[0];
		}
		else $url .= "18&id=$v";
	}
	if ($t == 'w') {
		if ($dom->getElementsByTagName('a')->item(5)) $url = $dom->getElementsByTagName('a')->item(5)->attributes->getNamedItem('href')->value;
		else $url .= "251&id=$v";
	}
	header('Location: https:'.preg_replace('/^https:/', '', $url));
}