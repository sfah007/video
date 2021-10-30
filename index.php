<?php
if (isset($_COOKIE['mv']) || $_POST['pw'] == 'mesvideo') { if (!isset($_COOKIE['mv'])) setcookie('mv', true);
include('functions.php');
$title = 'VideoTube';
$description = 'Watch ad-free YouTube videos.';
$grins = ['alt', 'beam', 'hearts', 'squint', 'stars'];
$ok = $channel = null;
$cat = !empty($_GET['c']) ? $_GET['c'] : null;
$user = !empty($_GET['u']) ? $_GET['u'] : null;
$order = !empty($_GET['o']) ? $_GET['o'] : null;
$query = !empty($_GET['q']) ? $_GET['q'] : null;
$length = !empty($_GET['l']) ? ($_GET['l'] == '✓' ? null : $_GET['l']) : null;
$date = !empty($_GET['d']) ? ($_GET['d'] == '✓' ? null : $_GET['d']) : null;
$hd = isset($_GET['hd']) ? 1 : null;
$d3 = isset($_GET['3d']) ? 1 : null;
$live = isset($_GET['le']) ? 1 : null;
$caption = isset($_GET['cc']) ? 1 : null;
$playlist = isset($_GET['p']) ? 1 : null;
$history = isset($_GET['h']) ? 1 : null;
$video = !empty($_GET['v']) ? $_GET['v'] : null;
if ($video) {
	$pm += [
		'regionCode' => $country,
		'id' => $video,
		'hl' => $language,
		'part' => 'snippet,contentDetails,statistics',
		'fields' => 'items(snippet(publishedAt,channelId,channelTitle,tags,categoryId,localized(title,description)),statistics(viewCount,likeCount,dislikeCount,commentCount),contentDetails(duration,definition))'
	];
	$ch = curl_init($api.'videos?'.http_build_query($pm));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	if (!empty($json['items'])) {
		$ok = true;
		$snippet = $json['items'][0]['snippet'];
		$publish = new DateTime($snippet['publishedAt']);
		$elapsed = elapsed($snippet['publishedAt']);
		$stats = $json['items'][0]['statistics'];
		$content = $json['items'][0]['contentDetails'];
	}
}
elseif ($playlist) {
	if (!empty($_COOKIE['playlist'])) $ok = true;
}
elseif ($history) {
	if (!empty($_COOKIE['history'])) $ok = true;
}
else {
	$pm += [
		'regionCode' => $country,
		'maxResults' => 50,
		'videoCategoryId' => $cat && in_array($cat, [1, 2, 10, 15, 17, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29]) ? $cat : null,
		'pageToken' => !empty($_GET['t']) ? $_GET['t'] : null,
		'part' => 'snippet',
		'fields' => 'nextPageToken,prevPageToken,items(id)'
	];
	$random = isset($_GET['r']) ? 1 : null;
	$search = $random || $query || $length || $date || $hd || $d3 || $live || $caption || $cat && in_array($cat, [19, 22, 25, 27, 29]) || $user || $country == 'WS' ? 1 : null;
	if ($search) {
		$pm += ['type' => 'video', 'relevanceLanguage' => $language];
		if ($random) $pm['relatedToVideoId'] = !empty($_COOKIE['random']) ? $_COOKIE['random'] : '7LJIcrJKDI0';
		else {
			$pm += ['videoEmbeddable' => 'true', 'safeSearch' => $safe];
			if ($user) $pm['channelId'] = $user;
			if ($order) $pm['order'] = in_array($order, ['viewCount', 'date', 'rating', 'title']) ? $order : 'relevance';
			if ($query) $pm['q'] = $query;
			if ($length) $pm['videoDuration'] = in_array($length, ['long', 'short', 'medium']) ? $length : 'any';
			if ($date) {
				$pst = new DateTime('-1 '.(in_array($date, ['hour', 'day', 'week', 'month', 'year']) ? $date : 'year'), new DateTimeZone('America/Los_Angeles'));
				$pm['publishedAfter'] = $pst->format('Y-m-d\TH:i:s\Z');
			}
			if ($hd) $pm['videoDefinition'] = 'high';
			if ($d3) $pm['videoDimension'] = '3d';
			if ($live) $pm['eventType'] = 'live';
			if ($caption) $pm['videoCaption'] = 'closedCaption';
		}
	}
	else $pm['chart'] = 'mostPopular';
	$ch = curl_init($api.($search ? 'search?' : 'videos?').http_build_query($pm));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	if ($random) {
		header('Location: ?v='.$json['items'][mt_rand(0, count($json['items']) - 1)]['id']['videoId']);
		exit;
	}
	if (!empty($json['items'])) {
		$ok = true;
		$next = !empty($json['nextPageToken']) ? $json['nextPageToken'] : null;
		$prev = !empty($json['prevPageToken']) ? $json['prevPageToken'] : null;
		foreach ($json['items'] as $item) $ids[] = $search ? $item['id']['videoId'] : $item['id'];
		$pms += ['id' => implode(',', $ids), 'part' => 'snippet,contentDetails', 'fields' => 'items(id,snippet(title,channelTitle),contentDetails(duration))'];
		$ch = curl_init($api.'videos?'.http_build_query($pms));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		$json = json_decode($result, true);
		if ($user) $channel = $json['items'][0]['snippet']['channelTitle'];
	}
}
?>
<!DOCTYPE html>
<html <?php if (in_array($language, ['ar', 'fa', 'iw', 'ur'])) echo 'dir="rtl" '; ?>lang="<?= $language; ?>">
	<head>
		<meta charset="utf-8"/>
		<meta name="robots" content="noindex"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<title><?php titlize(); ?></title>
		<meta name="description" content="<?php descize(); ?>"/>
		<meta property="og:title" content="<?php titlize(); ?>"/>
		<meta property="og:description" content="<?php descize(); ?>"/>
		<meta property="og:image" content="<?php if ($video && $ok) echo "https://i.ytimg.com/vi/$video/mqdefault.jpg"; else echo 'https://lh3.googleusercontent.com/-mYhxE-bTeLM/XXaKBWjrxbI/AAAAAAAACSc/nhuhiYCecU0kdkT6ZZ_MFFEwF1SlJI9EQCDMYAw/image.png'; ?>"/>
		<meta property="og:url" content="<?= $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>"/>
		<meta name="twitter:card" content="summary"/>
		<meta name="theme-color" content="#282828"/>
		<link rel="icon" href="favicon.ico"/>
		<link rel="manifest" href="manifest.json"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700|Cinzel:700&amp;subset=latin-ext"/>
		<link rel="stylesheet" href="style.css"/>
	</head>
	<body>
		<header>
			<h1><a href="./" rel="bookmark">VIDE<i class="fas fa-grin-<?= $grins[array_rand($grins)]; ?>"></i>TUBE</a></h1>
			<ul>
				<li><a id="menu"><i class="fas fa-folder-open"></i><span><?= $l10n['categories']; ?></span></a></li>
				<li><a id="search"><i class="fas fa-search"></i><span><?= $l10n['search']; ?></span></a></li>
				<li><a id="settings"><i class="fas fa-cog"></i><span><?= $l10n['settings']; ?></span></a></li>
				<li><a href="./?p" rel="bookmark"><i class="fas fa-play-circle"></i><span><?= $l10n['playlist']; ?></span></a></li>
				<li><a href="./?r" rel="nofollow"><i class="fas fa-random"></i><span><?= $l10n['random']; ?></span></a></li>
			</ul>
		</header>
		<nav class="menu">
			<a href="./?c=2" rel="bookmark"<?php if ($cat == 2) echo ' class="on"'; ?>><?= $l10n['autos']; ?></a>
			<a href="./?c=23" rel="bookmark"<?php if ($cat == 23) echo ' class="on"'; ?>><?= $l10n['comedy']; ?></a>
			<a href="./?c=27" rel="bookmark"<?php if ($cat == 27) echo ' class="on"'; ?>><?= $l10n['education']; ?></a>
			<a href="./?c=24" rel="bookmark"<?php if ($cat == 24) echo ' class="on"'; ?>><?= $l10n['entertainment']; ?></a>
			<a href="./?c=1" rel="bookmark"<?php if ($cat == 1) echo ' class="on"'; ?>><?= $l10n['film']; ?></a>
			<a href="./?c=20" rel="bookmark"<?php if ($cat == 20) echo ' class="on"'; ?>><?= $l10n['gaming']; ?></a>
			<a href="./?c=26" rel="bookmark"<?php if ($cat == 26) echo ' class="on"'; ?>><?= $l10n['howto']; ?></a>
			<a href="./?c=10" rel="bookmark"<?php if ($cat == 10) echo ' class="on"'; ?>><?= $l10n['music']; ?></a>
			<a href="./?c=25" rel="bookmark"<?php if ($cat == 25) echo ' class="on"'; ?>><?= $l10n['news']; ?></a>
			<a href="./?c=29" rel="bookmark"<?php if ($cat == 29) echo ' class="on"'; ?>><?= $l10n['activism']; ?></a>
			<a href="./?c=22" rel="bookmark"<?php if ($cat == 22) echo ' class="on"'; ?>><?= $l10n['people']; ?></a>
			<a href="./?c=15" rel="bookmark"<?php if ($cat == 15) echo ' class="on"'; ?>><?= $l10n['animals']; ?></a>
			<a href="./?c=28" rel="bookmark"<?php if ($cat == 28) echo ' class="on"'; ?>><?= $l10n['science']; ?></a>
			<a href="./?c=17" rel="bookmark"<?php if ($cat == 17) echo ' class="on"'; ?>><?= $l10n['sports']; ?></a>
			<a href="./?c=19" rel="bookmark"<?php if ($cat == 19) echo ' class="on"'; ?>><?= $l10n['travel']; ?></a>
		</nav>
		<nav class="search">
			<form>
				<div>
					<label for="q"><?= $l10n['query']; ?>:</label>
					<input type="search" name="q" id="q" value="<?= htmlspecialchars($query); ?>"/>
					<button type="submit"><i class="fas fa-search"></i></button>
				</div>
				<div>
					<label><?= $l10n['length']; ?>:</label>
					<select name="l">
						<option value="&#x2713;"><?= $l10n['any']; ?></option>
						<option value="long"<?php if ($length == 'long') echo ' selected'; ?>><?= $l10n['long']; ?></option>
						<option value="short"<?php if ($length == 'short') echo ' selected'; ?>><?= $l10n['short']; ?></option>
						<option value="medium"<?php if ($length == 'medium') echo ' selected'; ?>><?= $l10n['medium']; ?></option>
					</select>
				</div>
				<div>
					<label><?= $l10n['date']; ?>:</label>
					<select name="d">
						<option value="&#x2713;"><?= $l10n['any']; ?></option>
						<option value="hour"<?php if ($date == 'hour') echo ' selected'; ?>><?= $l10n['last_hour']; ?></option>
						<option value="day"<?php if ($date == 'day') echo ' selected'; ?>><?= $l10n['today']; ?></option>
						<option value="week"<?php if ($date == 'week') echo ' selected'; ?>><?= $l10n['this_week']; ?></option>
						<option value="month"<?php if ($date == 'month') echo ' selected'; ?>><?= $l10n['this_month']; ?></option>
						<option value="year"<?php if ($date == 'year') echo ' selected'; ?>><?= $l10n['this_year']; ?></option>
					</select>
				</div>
				<div>
					<label><input type="checkbox" name="c" value="<?= $cat; ?>"<?php if (!$cat) echo ' disabled'; ?>/><?= $l10n['current_category']; ?></label>
				</div>
				<div>
					<label><input type="checkbox" name="u" value="<?= $user ?? $snippet['channelId']; ?>"<?php if (!($user || $video)) echo ' disabled'; ?>/><?= $l10n['current_channel']; ?></label>
				</div>
				<div>
					<label><input type="checkbox" name="hd" value="&#x2713;"<?php if ($hd) echo ' checked'; ?>/><?= $l10n['hd']; ?></label>
				</div>
				<div>
					<label><input type="checkbox" name="3d" value="&#x2713;"<?php if ($d3) echo ' checked'; ?>/><?= $l10n['3d']; ?></label>
				</div>
				<div>
					<label><input type="checkbox" name="le" value="&#x2713;"<?php if ($live) echo ' checked'; ?>/><?= $l10n['live']; ?></label>
				</div>
				<div>
					<label><input type="checkbox" name="cc" value="&#x2713;"<?php if ($caption) echo ' checked'; ?>/><?= $l10n['caption']; ?></label>
				</div>
			</form>
		</nav>
		<nav class="settings">
			<div>
				<label><input type="checkbox" name="auto_start"/><?= $l10n['auto_start']; ?></label>
				<p><?= $l10n['auto_start_desc']; ?>.</p>
			</div>
			<div>
				<label><input type="checkbox" name="infinite_playback"/><?= $l10n['infinite_playback']; ?></label>
				<p><?= $l10n['infinite_playback_desc']; ?>.</p>
			</div>
			<div>
				<label><input type="checkbox" name="safe_search"/><?= $l10n['safe_search']; ?></label>
				<p><?= $l10n['safe_search_desc']; ?>.</p>
			</div>
			<div>
				<label><input type="checkbox" name="save_history"/><?= $l10n['save_history']; ?></label>
				<p><?= $l10n['save_history_desc']; ?>.</p>
			</div>
			<div>
				<label><?= $l10n['country']; ?>:</label>
				<select name="country" rel="alternate">
					<option value="WS"><?= $l10n['worldwide']; ?> &#x1F5FA;</option>
					<option value="AR">Argentina</option>
					<option value="AU">Australia</option>
					<option value="AZ">Azərbaycan</option>
					<option value="BE">België</option>
					<option value="BO">Bolivia</option>
					<option value="BA">Bosna i Hercegovina</option>
					<option value="BR">Brasil</option>
					<option value="CA">Canada</option>
					<option value="CL">Chile</option>
					<option value="CO">Colombia</option>
					<option value="CH">Confoederatio Helvetica</option>
					<option value="CR">Costa Rica</option>
					<option value="CZ">Česko</option>
					<option value="DK">Danmark</option>
					<option value="DE">Deutschland</option>
					<option value="EC">Ecuador</option>
					<option value="EE">Eesti</option>
					<option value="SV">El Salvador</option>
					<option value="ES">España</option>
					<option value="FI">Finland</option>
					<option value="FR">France</option>
					<option value="GH">Ghana</option>
					<option value="GT">Guatemala</option>
					<option value="HN">Honduras</option>
					<option value="HR">Hrvatska</option>
					<option value="ID">Indonesia</option>
					<option value="IE">Ireland</option>
					<option value="IT">Italy</option>
					<option value="IS">Ísland</option>
					<option value="JM">Jamaica</option>
					<option value="KE">Kenya</option>
					<option value="LV">Latvija</option>
					<option value="LI">Liechtenstein</option>
					<option value="LT">Lietuva</option>
					<option value="LU">Luxembourg</option>
					<option value="HU">Magyarország</option>
					<option value="MY">Malaysia</option>
					<option value="MT">Malta</option>
					<option value="MX">México</option>
					<option value="NL">Nederland</option>
					<option value="NZ">New Zealand</option>
					<option value="NI">Nicaragua</option>
					<option value="NG">Nigeria</option>
					<option value="NO">Norge</option>
					<option value="AT">Österreich</option>
					<option value="PA">Panamá</option>
					<option value="PG">Papua New Guinea</option>
					<option value="PY">Paraguay</option>
					<option value="PE">Perú</option>
					<option value="PH">Pilipinas</option>
					<option value="PL">Polska</option>
					<option value="PT">Portugal</option>
					<option value="PR">Puerto Rico</option>
					<option value="DO">República Dominicana</option>
					<option value="RO">România</option>
					<option value="SN">Sénégal</option>
					<option value="SG">Singapore</option>
					<option value="SI">Slovenija</option>
					<option value="SK">Slovensko</option>
					<option value="ZA">South Africa</option>
					<option value="SE">Sverige</option>
					<option value="TZ">Tanzania</option>
					<option value="TR">Türkiye</option>
					<option value="UG">Uganda</option>
					<option value="GB">United Kingdom</option>
					<option value="US">United States</option>
					<option value="UY">Uruguay</option>
					<option value="VE">Venezuela</option>
					<option value="VN">Việt Nam</option>
					<option value="ZW">Zimbabwe</option>
					<option value="BG">България</option>
					<option value="BY">Беларусь</option>
					<option value="KZ">Қазақстан</option>
					<option value="ME">Црна Гора</option>
					<option value="MK">Северна Македонија</option>
					<option value="RS">Србија</option>
					<option value="RU">Россия</option>
					<option value="UA">Україна</option>
					<option value="CY">Κύπρος</option>
					<option value="GR">Ελλάδα</option>
					<option value="GE">საქართველო</option>
					<option value="IL">ישראל</option>
					<option value="PK">پاکستان</option>
					<option value="AE">الإمارات العربية المتحدة</option>
					<option value="BH">البحرين</option>
					<option value="DZ">الجزائر</option>
					<option value="EG">مصر</option>
					<option value="IQ">العراق</option>
					<option value="JO">الأردن</option>
					<option value="KW">الكويت</option>
					<option value="LB">لبنان</option>
					<option value="LY">ليبيا</option>
					<option value="MA">المغرب</option>
					<option value="OM">سلطنة عمان</option>
					<option value="QA">دولة قطر</option>
					<option value="SA">المملكة العربية السعودية</option>
					<option value="TN">تونس</option>
					<option value="YE">اليمن</option>
					<option value="BD">বাংলাদেশ</option>
					<option value="IN">भारत</option>
					<option value="NP">नेपाल</option>
					<option value="LK">ශ්‍රී ලංකාව</option>
					<option value="TH">ประเทศไทย</option>
					<option value="KR">한국</option>
					<option value="JP">日本</option>
					<option value="TW">台灣</option>
					<option value="HK">香港</option>
					<option value="CN">中国</option>
				</select>
			</div>
			<div>
				<label><?= $l10n['language']; ?>:</label>
				<select name="language" rel="alternate">
					<option value="af">Afrikaans</option>
					<option value="az">Azərbaycan</option>
					<option value="bs">Bosanski</option>
					<option value="ca">Català</option>
					<option value="cs">Čeština</option>
					<option value="da">Dansk</option>
					<option value="de">Deutsch</option>
					<option value="et">Eesti</option>
					<option value="en">English</option>
					<option value="es">Español</option>
					<option value="eu">Euskara</option>
					<option value="fil">Filipino</option>
					<option value="fr">Français</option>
					<option value="gl">Galego</option>
					<option value="hr">Hrvatski</option>
					<option value="id">Indonesia</option>
					<option value="zu">IsiZulu</option>
					<option value="it">Italiano</option>
					<option value="is">Íslenska</option>
					<option value="sw">Kiswahili</option>
					<option value="lv">Latviešu</option>
					<option value="lt">Lietuvių</option>
					<option value="hu">Magyar</option>
					<option value="ms">Malaysia</option>
					<option value="nl">Nederlands</option>
					<option value="no">Norsk</option>
					<option value="uz">O&#8217;zbek</option>
					<option value="pl">Polski</option>
					<option value="pt">Português</option>
					<option value="ro">Română</option>
					<option value="sq">Shqip</option>
					<option value="sk">Slovenčina</option>
					<option value="sl">Slovenščina</option>
					<option value="fi">Suomi</option>
					<option value="sv">Svenska</option>
					<option value="vi">Tiếng Việt</option>
					<option value="tr">Türkçe</option>
					<option value="be">Беларуская</option>
					<option value="bg">Български</option>
					<option value="ky">Кыргызча</option>
					<option value="kk">Қазақ</option>
					<option value="mk">Македонски</option>
					<option value="mn">Монгол</option>
					<option value="ru">Русский</option>
					<option value="sr">Српски</option>
					<option value="uk">Українська</option>
					<option value="el">Ελληνικά</option>
					<option value="ka">ქართული</option>
					<option value="hy">Հայերեն</option>
					<option value="iw">עברית</option>
					<option value="ar">العربية</option>
					<option value="fa">فارسی</option>
					<option value="ur">اردو</option>
					<option value="hi">हिन्दी</option>
					<option value="bn">বাংলা</option>
					<option value="mr">मराठी</option>
					<option value="te">తెలుగు</option>
					<option value="ta">தமிழ்</option>
					<option value="gu">ગુજરાતી</option>
					<option value="ml">മലയാളം</option>
					<option value="kn">ಕನ್ನಡ</option>
					<option value="or">ଓଡ଼ିଆ</option>
					<option value="pa">ਪੰਜਾਬੀ</option>
					<option value="ne">नेपाली</option>
					<option value="si">සිංහල</option>
					<option value="th">ภาษาไทย</option>
					<option value="lo">ລາວ</option>
					<option value="my">ဗမာ</option>
					<option value="km">ខ្មែរ</option>
					<option value="am">አማርኛ</option>
					<option value="ko">한국어</option>
					<option value="ja">日本語</option>
					<option value="zh-CN">简体中文</option>
					<option value="zh-TW">繁體中文</option>
				</select>
			</div>
		</nav>
		<main>
			<div id="content" data-title="<?php titlize(); ?>" data-page="<?= $_SERVER['REQUEST_URI']; ?>">
<?php if ($ok) { if ($video) { ?>
				<figure>
					<div id="player"></div>
					<figcaption></figcaption>
				</figure>
				<ol>
					<li><b id="info" class="on"><i class="fas fa-bullhorn"></i></b></li>
					<li><b id="comments"<?php if (empty($stats['commentCount'])) echo ' class="off"'; ?>><i class="fas fa-comment<?= empty($stats['commentCount']) ? '-slash' : 's' ?>"></i></b></li>
					<li><b id="related"><i class="fas fa-video"></i></b></li>
					<li><b id="channel"><i class="fas fa-user"></i></b></li>
				</ol>
				<section class="info">
					<h4><?= $snippet['localized']['title']; ?></h4>
					<h5><a href="./?u=<?= $snippet['channelId']; ?>" rel="bookmark"><?= $snippet['channelTitle']; ?></a><b id="<?= $video; ?>"><i class="fas fa-plus-circle"></i></b></h5>
<?php if ($content['duration'] != 'P0D') { ?>
					<div>
						<span><a href="api.php?t=v&amp;v=<?= $video; ?>" rel="external" target="_blank"><i class="far fa-file-video"></i><?= $l10n['video']; ?></a></span>
						<span><a href="api.php?t=w&amp;v=<?= $video; ?>" rel="external" target="_blank"><i class="far fa-file-audio"></i><?= $l10n['audio']; ?></a></span>
						<span><a href="https://www.youtube-nocookie.com/embed/<?= $video; ?>" rel="external" target="_blank"><i class="fab fa-youtube"></i>Embed</a></span>
<?php if ($snippet['categoryId'] == '10') { ?>
						<span><a href="https://music.youtube.com/watch?v=<?= $video; ?>" rel="external" target="_blank"><i class="fab fa-youtube"></i><?= $l10n['music']; ?></a></span>
<?php } ?>
					</div>
<?php } ?>
					<div>
						<span><a href="./?c=<?= $snippet['categoryId']; ?>" rel="bookmark"><i class="far fa-folder-open"></i><?= category($snippet['categoryId']); ?></a></span>
						<span><i class="far fa-clock"></i><time datetime="<?= $publish->format('Y-m-d H:i'); ?>" data-elapsed="<?= $elapsed; ?>"><?= $elapsed; ?></time></span>
						<span><i class="far fa-hourglass"></i><?= duration($content['duration']); ?></span>
					</div>
<?php if (!empty($stats['viewCount'])) { ?>
					<div>
						<span><i class="far fa-eye"></i><?= number_format($stats['viewCount']); ?></span>
<?php if (!empty($stats['likeCount'])) { ?>
						<span><i class="far fa-thumbs-up"></i><?= number_format($stats['likeCount']); ?></span>
						<span><i class="far fa-thumbs-down"></i><?= number_format($stats['dislikeCount']); ?></span>
						<progress value="<?= rating($stats['likeCount'], $stats['dislikeCount']); ?>"></progress>
<?php } ?>
					</div>
<?php } if (!empty($snippet['localized']['description'])) { ?>
					<p><?= nl2br($snippet['localized']['description']); ?></p>
<?php } if (!empty($snippet['tags'])) { ?>
					<p><?php foreach ($snippet['tags'] as $tag) { ?><a href="./?q=<?= urlencode($tag); ?>" rel="tag"><?= $tag; ?></a><?php } ?></p>
<?php } ?>
				</section>
				<section class="comments" data-id="<?= $video; ?>"></section>
				<section class="related" data-id="<?= $video; ?>"></section>
				<section class="channel" data-id="<?= $snippet['channelId']; ?>"></section>
<?php } elseif ($playlist || $history) { if ($history) { ?>
				<h2><?= $l10n['history']; ?></h2>
<?php } else { ?>
				<figure>
					<div id="player"></div>
					<figcaption></figcaption>
				</figure>
				<ol>
					<li><em class="backward"><i class="fas fa-step-backward"></i></em></li>
					<li><em class="shuffle"><i class="fas fa-random"></i></em></li>
					<li><em class="loop"><i class="fas fa-undo-alt"></i></em></li>
					<li><em class="forward"><i class="fas fa-step-forward"></i></em></li>
				</ol>
<?php } ?>
				<section class="<?= $history ? 'history' : 'playlist' ?>">
					<aside>
						<a data-yes="<?= $l10n['yes']; ?>" data-no="<?= $l10n['no']; ?>"><i class="fas fa-trash"></i> <?= $l10n['clear']; ?></a>
					</aside>
				</section>
<?php } else { if ($cat || $user || $query) { ?>
				<h2><?php
					if ($cat) echo category($cat);
					if ($user) echo $channel;
					if ($query && ($cat || $user)) echo ': ';
					if ($query) echo $query;
				?></h2>
<?php } if ($search) { ?>
				<ol>
					<li><a href="<?= order(); ?>" rel="nofollow"<?php if (!$order) echo ' class="on"'; ?>><i class="fas fa-star"></i></a></li>
					<li><a href="<?= order('viewCount'); ?>" rel="nofollow"<?php if ($order == 'viewCount') echo ' class="on"'; ?>><i class="fas fa-heart"></i></a></li>
					<li><a href="<?= order('date'); ?>" rel="nofollow"<?php if ($order == 'date') echo ' class="on"'; ?>><i class="fas fa-fire-alt"></i></a></li>
					<li><a href="<?= order('rating'); ?>" rel="nofollow"<?php if ($order == 'rating') echo ' class="on"'; ?>><i class="fas fa-thumbs-up"></i></a></li>
				</ol>
<?php } ?>
				<section>
<?php foreach ($json['items'] as $item) { ?>
					<article>
						<a href="./?v=<?= $item['id']; ?>" rel="bookmark">
							<small><?= $item['snippet']['channelTitle']; ?></small>
							<span><?= $item['snippet']['title']; ?></span>
							<img src="https://i.ytimg.com/vi/<?= $item['id']; ?>/mqdefault.jpg" alt="<?= htmlspecialchars($item['snippet']['title']); ?>"/>
						</a>
						<b id="<?= $item['id']; ?>"><?= duration($item['contentDetails']['duration']); ?> <i class="fas fa-plus-circle"></i></b>
					</article>
<?php } if ($prev || $next) { ?>
					<aside>
<?php if ($prev) { ?>
						<a <?php if ($next) echo 'class="prev" '; ?>href="<?= paginate($prev); ?>" rel="prev"><i class="fas fa-chevron-left"></i> <?= $l10n['prev']; ?></a>
<?php } if ($next) { ?>
						<a <?php if ($prev) echo 'class="next" '; ?>href="<?= paginate($next); ?>" rel="next"><?= $l10n['next']; ?> <i class="fas fa-chevron-right"></i></a>
<?php } ?>
					</aside>
<?php } ?>
				</section>
<?php } } else { ?>
				<h3><?php
					echo $l10n['no_videos'].'.';
					if ($cat || $query) {
						echo '<div>';
						if ($cat) echo category($cat);
						if ($cat && $query) echo ': ';
						if ($query) echo $query;
						echo '</div>';
					}
				?></h3>
<?php } ?>
				<img class="counter" alt="<?= $_SERVER['HTTP_REFERER']; ?>"/>
			</div>
		</main>
		<footer>
			<div><i class="fas fa-keyboard"></i></div>
			<div><i class="fas fa-chevron-up"></i></div>
			<div><a href="mailto:memres@protonmail.com"><i class="fas fa-envelope"></i></a></div>
		</footer>
		<kbd>
			<i class="fas fa-times-circle fa-2x"></i>
			<img/>
			<ul>
				<li><b>Enter</b> Random Video</li>
				<li><b>Space</b> Play // Pause</li>
				<li><b>M</b> Mute</li>
				<li><b>L</b> Loop</li>
				<li><b>S</b> Shuffle</li>
				<li><b>F</b> Fullscreen</li>
				<li><b>N</b> Next Page // Video</li>
				<li><b>P</b> Previous Page // Video</li>
				<li><b><i class="fas fa-arrow-left"></i></b> Seek Backward 5 Seconds</li>
				<li><b><i class="fas fa-arrow-right"></i></b> Seek Forward 5 Seconds</li>
				<li><b><i class="fas fa-minus"></i></b> Decrease Volume by 5%</li>
				<li><b><i class="fas fa-plus"></i></b> Increase Volume by 5%</li>
				<li><b>0...9</b> Jump Over Video Sections</li>
				<li><b>Esc</b> Exit Modal</li>
			</ul>
		</kbd>
		<script src="https://www.youtube.com/iframe_api" async></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.1/js.cookie.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
		<script src="script.js"></script>
<?php if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) { ?>
		<script>var sc_invisible=1,sc_project=5408945,sc_security="f75ba4c3";window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)},ga.l=+new Date,ga("create","UA-28085788-1","auto"),ga("send","pageview");</script>
		<script src="https://statcounter.com/counter/counter.js"></script>
		<script src="https://www.google-analytics.com/analytics.js" async></script>
<?php } ?>
	</body>
</html>
<?php } else echo '<form method="post"><input type="password" name="pw"/></form>'; ?>