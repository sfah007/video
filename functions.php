<?php

// HTTPS Redirection
function is_https() {
	if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 1) return true;
	elseif (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') return true;
	else return false;
}
if (!is_https()) {
    header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit();
}

// Site Description
$site_title = 'VidyoSite';
$site_description = 'Watch ad-free YouTube videos, even if it is blocked at your location.';
$site_image = 'https://lh3.googleusercontent.com/gtxYXbakPzEBM8_T3iWrD1THk775MyvIPjyiIQQh-VXCPt-ZWtN2HI3ztfOO9-9cWjjRNEKNEiN6fUKvFw';

// Results Per Page
$perpage = 24;

// Current URL
$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// Active Menu Item
$active = 'class="active" ';

// Basic Variables
$id = isset($_GET['id']) ? $_GET['id'] : '';
$random = isset($_GET['random']) ? true : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';
$channel = isset($_GET['channel']) ? $_GET['channel'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Search Variables
$term = isset($_GET['term']) ? $_GET['term'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : '';
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';
$hd = isset($_GET['hd']) ? true : '';
$d3 = isset($_GET['3d']) ? true : '';
$live = isset($_GET['live']) ? true : '';
$caption = isset($_GET['caption']) ? true : '';
$search = ($term || $duration || $hd || $d3 || $live || $caption) ? true : '';

// Include API Key
include 'key.php';

// SSL Options
$opts = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false));

// Video Page
if ($id) {
	$query = "https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&id=$id&key=$key&prettyPrint=false&fields=items(snippet(publishedAt,channelId,title,description,thumbnails(medium),channelTitle,tags),contentDetails(duration),statistics(viewCount,likeCount,dislikeCount))";
	$response = json_decode(@file_get_contents($query, false, stream_context_create($opts)), true);
	$video = $response['items'][0];
	$video_image = $video['snippet']['thumbnails']['medium']['url'];
	$video_title = $video['snippet']['title'];
	$video_description = strlen(str_replace('\n', '', $video['snippet']['description'])) > 3 ? $video['snippet']['description'] : '';
	$video_datetime = $video['snippet']['publishedAt'];
	$arr = explode('T', $video_datetime);
	$video_date = $arr[0];
	$video_tags = isset($video['snippet']['tags']) ? $video['snippet']['tags'] : '';
	$video_views = $video['statistics']['viewCount'];
	$video_likes = isset($video['statistics']['likeCount']) ? $video['statistics']['likeCount'] : 0;
	$video_dislikes = isset($video['statistics']['dislikeCount']) ? $video['statistics']['dislikeCount'] : 0;
	$video_channel = $video['snippet']['channelTitle'];
	$video_channel_id = $video['snippet']['channelId'];
	$rpc = str_replace(array('PT', 'H', 'M', 'S'), array('', ':', ':', ''), $video['contentDetails']['duration']);
	$arr = explode(':', $rpc);
	if (count($arr) == 1) $video_duration = '00:00:'.sprintf('%02d', $rpc);
	elseif (count($arr) == 2) $video_duration = '00:'.sprintf('%02d', $arr[0]).':'.sprintf('%02d', $arr[1]);
	elseif (count($arr) == 3) $video_duration = sprintf('%02d', $arr[0]).':'.sprintf('%02d', $arr[1]).':'.sprintf('%02d', $arr[2]);
}

// Query Pages
else {
	$query = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&videoEmbeddable=true&videoSyndicated=true&prettyPrint=false&key=$key";
	if ($channel) $query .= "&channelId=$channel";
	if ($category) $query .= "&videoCategoryId=$category";
	if ($order) $query .= "&order=$order";
	if ($duration) $query .= "&videoDuration=$duration";
	if ($hd) $query .= "&videoDefinition=high";
	if ($d3) $query .= "&videoDimension=3d";
	if ($live) $query .= "&eventType=live";
	if ($caption) $query .= "&videoCaption=closedCaption";
	if ($term) $query .= "&q=".urlencode($term);
	if ($random) {
		$tokens = array('CAEQAA', 'CAIQAA', 'CAMQAA', 'CAQQAA', 'CAUQAA', 'CAYQAA', 'CAcQAA', 'CAgQAA', 'CAkQAA', 'CAoQAA', 'CAsQAA', 'CAwQAA', 'CA0QAA', 'CA4QAA', 'CA8QAA', 'CBAQAA', 'CBEQAA', 'CBIQAA', 'CBMQAA', 'CBQQAA', 'CBUQAA', 'CBYQAA', 'CBcQAA', 'CBgQAA', 'CBkQAA', 'CBoQAA', 'CBsQAA', 'CBwQAA', 'CB0QAA', 'CB4QAA', 'CB8QAA', 'CCAQAA', 'CCEQAA', 'CCIQAA', 'CCMQAA', 'CCQQAA', 'CCUQAA', 'CCYQAA', 'CCcQAA', 'CCgQAA', 'CCkQAA', 'CCoQAA', 'CCsQAA', 'CCwQAA', 'CC0QAA', 'CC4QAA', 'CC8QAA', 'CDAQAA', 'CDEQAA', 'CDIQAA', 'CDMQAA', 'CDQQAA', 'CDUQAA', 'CDYQAA', 'CDcQAA');
		$query .= '&maxResults=1&fields=items(id(videoId))&pageToken='.$tokens[array_rand($tokens)];
		if (isset($_GET['related'])) $query .= '&relatedToVideoId='.$_GET['related'];
		$response = json_decode(@file_get_contents($query, false, stream_context_create($opts)), true);
		if (isset($response['items'][0])) header('Location: ?id='.$response['items'][0]['id']['videoId']);
		else header('Location: ?random');
		exit;
	}
	if ($page) $query .= "&pageToken=$page";
	$query .= "&maxResults=$perpage&fields=nextPageToken,prevPageToken,items(id(videoId),snippet(title,thumbnails(medium)".($channel ? ',channelTitle))' : '))');
	$response = json_decode(@file_get_contents($query, false, stream_context_create($opts)), true);
	$channel_title = ($channel and isset($response['items'][0])) ? $response['items'][0]['snippet']['channelTitle'] : '';
	$nextpage = isset($response['nextPageToken']) ? $response['nextPageToken'] : '';
	$prevpage = isset($response['prevPageToken']) ? $response['prevPageToken'] : '';
}

// Turn URLs into clickable links
function autolink($text) {
	$text = ' ' . $text;
	$text = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a href="$2" rel="nofollow" target="_blank">$2</a>', $text);
	$text = substr($text, 1);
	return nl2br($text);
}

?>
