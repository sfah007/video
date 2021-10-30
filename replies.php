<?php
if (isset($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $_SERVER['SERVER_NAME'] && !empty($_GET['id'])) {
	include('functions.php');
	$pm += array(
		'parentId' => $_GET['id'],
		'maxResults' => 10,
		'part' => 'snippet',
		'textFormat' => 'plainText',
		'fields' => 'nextPageToken,items(id,snippet(authorDisplayName,authorProfileImageUrl,authorChannelId,textDisplay,likeCount,publishedAt))',
		'pageToken' => !empty($_GET['token']) ? $_GET['token'] : null
	);
	$ch = curl_init($api.'comments?'.http_build_query($pm));
	curl_setopt($ch, CURLOPT_REFERER, $ref);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	if (!empty($json['items'])) {
		foreach (array_reverse($json['items']) as $item) {
			$comment = $item['snippet'];
			$publish = new DateTime($comment['publishedAt']);
?>
						<div id="<?= $item['id']; ?>">
							<img src="<?= str_replace('s48-', 's40-', $comment['authorProfileImageUrl']); ?>" alt="<?= $comment['authorDisplayName']; ?>"/>
							<a href="?u=<?= $comment['authorChannelId']['value']; ?>"><?= $comment['authorDisplayName']; ?></a>
							<p><?= nl2br($comment['textDisplay']); ?></p>
							<blockquote>
								<span><i class="far fa-clock"></i><time datetime="<?= $publish->format('Y-m-d H:i'); ?>" data-elapsed="<?= elapsed($comment['publishedAt']); ?>"><?= elapsed($comment['publishedAt']); ?></time></span>
<?php if (!empty($comment['likeCount'])) { ?>
								<span><i class="far fa-thumbs-up"></i><?= number_format($comment['likeCount']); ?></span>
<?php } ?>
							</blockquote>
						</div>
<?php } if (!empty($json['nextPageToken'])) { ?>
						<b id="<?= $json['nextPageToken']; ?>"><i class="fas fa-sync-alt"></i> <?= $l10n['more']; ?></b>
<?php } } } ?>