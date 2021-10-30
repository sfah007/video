<?php
$country = !empty($_COOKIE['country']) ? $_COOKIE['country'] : country();
$language = !empty($_COOKIE['language']) ? $_COOKIE['language'] : language($country);
include("l10n/$language.php");
$api = 'https://www.googleapis.com/youtube/v3/';
$pm = $pms = ['prettyPrint' => 'false', 'key' => 'AIzaSyA-dlBUjVQeuc4a6ZN4RkNUYDFddrVLxrA'];
$safe = isset($_COOKIE['safe_search']) ? 'strict' : 'none';
function country() {
	$gl = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : 'WS';
	if (!in_array($gl, ['WS', 'AE', 'AR', 'AU', 'AZ', 'AT', 'BA', 'BD', 'BE', 'BG', 'BH', 'BO', 'BR', 'BY', 'CA', 'CH', 'CL', 'CN', 'CO', 'CR', 'CY', 'CZ', 'DE', 'DK', 'DO', 'DZ', 'EC', 'EE', 'EG', 'ES', 'FI', 'FR', 'GB', 'GE', 'GH', 'GR', 'GT', 'HK', 'HN', 'HR', 'HU', 'ID', 'IE', 'IL', 'IN', 'IQ', 'IS', 'IT', 'JO', 'JM', 'JP', 'KE', 'KR', 'KW', 'KZ', 'LB', 'LI', 'LK', 'LT', 'LU', 'LV', 'LY', 'OM', 'MA', 'ME', 'MK', 'MT', 'MX', 'MY', 'NG', 'NI', 'NL', 'NO', 'NP', 'NZ', 'PA', 'PE', 'PG', 'PH', 'PK', 'PL', 'PR', 'PT', 'PY', 'QA', 'RO', 'RS', 'SA', 'SE', 'SG', 'SI', 'SK', 'SN', 'SV', 'RU', 'TH', 'TN', 'TR', 'TW', 'TZ', 'UA', 'UG', 'US', 'UY', 'VE', 'VN', 'YE', 'ZA', 'ZW'])) $gl = 'WS';
	setcookie('country', $gl, time() + 86400 * 365);
	return $gl;
}
function language($gl) {
	$hl = 'en';
	// <option value="as">অসমীয়া</option> Assamese -> Assamca tercümesi bekleniyor
	if (in_array($gl, ['AZ', 'BG', 'ES', 'FI', 'HR', 'ID', 'IT', 'IS', 'LV', 'LT', 'HU', 'MK', 'NO', 'PL', 'RO', 'RU', 'SK', 'TH', 'TR'])) $hl = strtolower($gl);
	if (in_array($gl, ['AR', 'BO', 'CL', 'CO', 'CR', 'DO', 'EC', 'GT', 'HN', 'MX', 'NI', 'PA', 'PE', 'PR', 'PY', 'SV', 'UY', 'VE'])) $hl = 'es';#İspanyolca
	if (in_array($gl, ['AE', 'BH', 'DZ', 'EG', 'IQ', 'JO', 'KW', 'LB', 'LY', 'MA', 'OM', 'QA', 'SA', 'TN', 'YE'])) $hl = 'ar';#Arapça
	// if (in_array($gl, ['au', 'ca', 'gb', 'gh', 'ie', 'jm', 'mt', 'ng', 'nz', 'pg', 'sg', 'us', 'ws'])) $hl = 'en';#İngilizce
	if (in_array($gl, ['AT', 'CH', 'DE', 'LI'])) $hl = 'de';#Almanca
	if (in_array($gl, ['FR', 'LU', 'SN'])) $hl = 'fr';#Fransızca
	if (in_array($gl, ['KE', 'TZ', 'UG'])) $hl = 'sw';#Svahili
	if (in_array($gl, ['BR', 'PT'])) $hl = 'pt';#Portekizce
	if (in_array($gl, ['BE', 'NL'])) $hl = 'nl';#Flemenkçe
	if (in_array($gl, ['CN', 'HK'])) $hl = 'zh-CN';#Çince
	if (in_array($gl, ['CY', 'GR'])) $hl = 'el';#Yunanca
	if (in_array($gl, ['ME', 'RS'])) $hl = 'sr';#Sırpça
	if ($gl == 'BA') $hl = 'bs';#Boşnakça
	if ($gl == 'CZ') $hl = 'cs';#Çekçe
	if ($gl == 'DK') $hl = 'da';#Danca
	if ($gl == 'EE') $hl = 'et';#Estonca
	if ($gl == 'PH') $hl = 'fil';#Filipince
	if ($gl == 'ZA') $hl = 'zu';#Zuluca
	if ($gl == 'SE') $hl = 'sv';#İsveççe
	if ($gl == 'VN') $hl = 'vi';#Vietnamca
	if ($gl == 'BY') $hl = 'be';#Belarusça
	if ($gl == 'KZ') $hl = 'kk';#Kazakça
	if ($gl == 'UA') $hl = 'uk';#Ukraynaca
	if ($gl == 'GE') $hl = 'ka';#Gürcüce
	if ($gl == 'IL') $hl = 'iw';#İbranice
	if ($gl == 'PK') $hl = 'ur';#Urduca
	if ($gl == 'IN') $hl = 'hi';#Hintçe
	if ($gl == 'NP') $hl = 'ne';#Nepalce
	if ($gl == 'BD') $hl = 'bn';#Bengalce
	if ($gl == 'LK') $hl = 'si';#Seylanca
	if ($gl == 'KR') $hl = 'ko';#Korece
	if ($gl == 'JP') $hl = 'ja';#Japonca
	if ($gl == 'TW') $hl = 'zh-TW';#Geleneksel Çince
	setcookie('language', $hl, time() + 86400 * 365);
	return $hl;
}
function titlize() {
	global $video, $ok, $snippet, $playlist, $history, $l10n, $cat, $channel, $query, $title;
	if ($video && $ok) echo htmlspecialchars($snippet['localized']['title']);
	if ($playlist) echo $l10n['playlist'];
	if ($history) echo $l10n['history'];
	if ($cat) echo category($cat);
	if ($channel) echo $channel;
	if ($query && ($cat || $channel)) echo ': ';
	if ($query) echo htmlspecialchars($query);
	if (($video && $ok) || $playlist || $history || $cat || $channel || $query) echo ' &#x2726; ';
	echo $title;
}
function descize() {
	global $video, $ok, $snippet, $description;
	if ($video && $ok) echo str_replace("\n", ' ', htmlspecialchars($snippet['localized']['description']));
	else echo $description;
}
function category($n) {
	global $l10n;
	$cat = 'Category';
	if ($n == 1) $cat = $l10n['film'];
	if ($n == 2) $cat = $l10n['autos'];
	if ($n == 10) $cat = $l10n['music'];
	if ($n == 15) $cat = $l10n['animals'];
	if ($n == 17) $cat = $l10n['sports'];
	if ($n == 19) $cat = $l10n['travel'];
	if ($n == 20) $cat = $l10n['gaming'];
	if ($n == 22) $cat = $l10n['people'];
	if ($n == 23) $cat = $l10n['comedy'];
	if ($n == 24) $cat = $l10n['entertainment'];
	if ($n == 25) $cat = $l10n['news'];
	if ($n == 26) $cat = $l10n['howto'];
	if ($n == 27) $cat = $l10n['education'];
	if ($n == 28) $cat = $l10n['science'];
	if ($n == 29) $cat = $l10n['activism'];
	return $cat;
}
function elapsed($dt) {
	global $l10n, $language;
	$now = new DateTime;
	$ago = new DateTime($dt);
	$diff = $now->diff($ago);
	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;
	$string = ['y' => $l10n['year'], 'm' => $l10n['month'], 'w' => $l10n['week'], 'd' => $l10n['day'], 'h' => $l10n['hour'], 'i' => $l10n['minute'], 's' => $l10n['second']];
	foreach ($string as $key => &$val) {
		if ($diff->$key) $val = $diff->$key.' '.$val.($language == 'en' && $diff->$key > 1 ? 's' : '');
		else unset($string[$key]);
	}
	$string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string).' '.$l10n['ago'] : $ago->format('Y-m-d H:i');
}
function duration($rfc) {
	global $l10n;
	if ($rfc == 'P0D') return $l10n['live'];
	else {
		$drt = new DateInterval($rfc);
		if (preg_match('/W|D|H/', $rfc)) {
			preg_match('/\d[W]/', $rfc, $weeks);
			preg_match('/\d[D]/', $rfc, $days);
			$drt->h += ($weeks ? str_replace('W', '', $weeks[0]) * 7 * 24 : 0) + ($days ? str_replace('D', '', $days[0]) * 24 : 0);
			return $drt->format('%h:%I:%S');
		}
		else return $drt->format('%i:%S');
	}
}
function rating($pos, $n) {
	$n += $pos;
	$z = 1.96;
	$phat = 1.0 * $pos / $n;
	return ($phat + $z * $z / (2 * $n) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $n)) / $n)) / (1 + $z * $z / $n);
}
function order($by = null) {
	global $cat, $user, $query, $length, $date, $hd, $d3, $live, $caption;
	$loc = './?';
	if ($cat) $loc .= "c=$cat&amp;";
	if ($user) $loc .= "u=$user&amp;";
	$loc .= 'o';
	if ($by) $loc .= "=$by";
	if ($query) $loc .= '&amp;q='.urlencode($query);
	if ($length) $loc .= "&amp;l=$length";
	if ($date) $loc .= "&amp;d=$date";
	if ($hd) $loc .= '&amp;hd';
	if ($d3) $loc .= '&amp;3d';
	if ($live) $loc .= '&amp;le';
	if ($caption) $loc .= '&amp;cc';
	return $loc;
}
function paginate($t) {
	global $cat, $user, $order, $query, $length, $date, $hd, $d3, $live, $caption;
	$loc = './?';
	if ($cat) $loc .= "c=$cat&amp;";
	if ($user) $loc .= "u=$user&amp;";
	if ($order) $loc .= "o=$order&amp;";
	if ($query) $loc .= 'q='.urlencode($query).'&amp;';
	if ($length) $loc .= "l=$length&amp;";
	if ($date) $loc .= "d=$date&amp;";
	if ($hd) $loc .= 'hd&amp;';
	if ($d3) $loc .= '3d&amp;';
	if ($live) $loc .= 'le&amp;';
	if ($caption) $loc .= 'cc&amp;';
	$loc .= "t=$t";
	return $loc;
}
?>