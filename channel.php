<?php
if (isset($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $_SERVER['SERVER_NAME'] && !empty($_GET['id'])) {
	include('functions.php');
	$pm += array(
		'channelId' => $_GET['id'],
		'relevanceLanguage' => $language,
		'safeSearch' => $safe,
		'maxResults' => 20,
		'part' => 'snippet',
		'type' => 'video',
		'order' => 'date',
		'fields' => 'nextPageToken,items(id(videoId))',
		'pageToken' => !empty($_GET['token']) ? $_GET['token'] : null
	);
	$ch = curl_init($api.'search?'.http_build_query($pm));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	if (empty($json['items'])) echo "\n";
	else {
		foreach ($json['items'] as $item) $ids[] = $item['id']['videoId'];
		$pms += array(
			'id' => implode(',', $ids),
			'part' => 'snippet,contentDetails',
			'fields' => 'items(id,snippet(title,channelTitle),contentDetails(duration))'
		);
		$ch = curl_init($api.'videos?'.http_build_query($pms));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		$json2 = json_decode($result, true);
		foreach ($json2['items'] as $item) {
?>
					<article>
						<a href="?v=<?= $item['id']; ?>">
							<small><?= $item['snippet']['channelTitle']; ?></small>
							<span><?= $item['snippet']['title']; ?></span>
							<img src="https://i.ytimg.com/vi/<?= $item['id']; ?>/mqdefault.jpg" alt="<?= htmlspecialchars($item['snippet']['title'], ENT_QUOTES); ?>"/>
						</a>
						<b id="<?= $item['id']; ?>"><?= duration($item['contentDetails']['duration']); ?> <i class="fas fa-plus-circle"></i></b>
					</article>
<?php } if (!empty($json['nextPageToken'])) { ?>
					<aside id="<?= $json['nextPageToken']; ?>">
						<a class="more"><i class="fas fa-sync-alt"></i> <?= $l10n['more']; ?></a>
					</aside>
<?php } } } ?>