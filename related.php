<?php
$id = isset($_GET['v']) ? $_GET['v'] : '';
if ($id) {
	include 'key.php';
	$query = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=12&key=$key&relatedToVideoId=$id&prettyPrint=false&fields=nextPageToken,items(id(videoId),snippet(title,thumbnails(medium)))";
	if (isset($_GET['t'])) $query .= '&pageToken='.$_GET['t'];
	$response = json_decode(@file_get_contents($query), true);
	if (isset($response['items'][0])) {
		$token = isset($response['nextPageToken']) ? $response['nextPageToken'] : '';
		foreach ($response['items'] as $item) {
			$id = $item['id']['videoId'];
			$title = $item['snippet']['title'];
			$image = $item['snippet']['thumbnails']['medium']['url'];
?>
			<div class="four columns">
				<div class="row thumb">
					<a href="?id=<?= $id; ?>">
						<img class="scale" src="<?= $image; ?>" alt="<?= $title; ?>">
						<div class="caption">
							<p class="ellipsis"><?= $title; ?></p>
						</div>
					</a>
				</div>
			</div>
<?php } if ($token) { ?>
			<div class="sixteen columns add-bottom">
				<button class="more-videos half-bottom" value="<?= "v=$id&amp;t=$token"; ?>"><i class="fa fa-refresh"></i> More Videos</button>
			</div>
<?php } } else { ?>
			<div class="sixteen columns add-bottom">
				<p>There are no more related videos.</p>
			</div>
<?php } } ?>
