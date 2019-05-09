<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title><?php
			if ($id) echo "$video_title &ndash; ";
			else {
				if ($channel) echo "$channel_title &ndash; ";
				if ($term) echo "$term &ndash; ";
			}
			echo $site_title;
		?></title>
		<meta name="description" content="<?php
			if ($id && $video_description) echo $video_description;
			else echo $site_description;
		?>">
		<meta property="og:title" content="<?php
			if ($id) echo $video_title;
			elseif ($channel) echo $channel_title;
			else echo $site_title;
		?>">
		<meta property="og:description" content="<?php
			if ($id && $video_description) echo $video_description;
			else echo $site_description;
		?>">
		<meta property="og:image" content="<?php
			if ($id) echo $video_image;
			else echo $site_image;
		?>">
		<meta property="og:url" content="<?= $url; ?>">
		<meta property="og:type" content="video">
		<meta name="twitter:card" content="summary">
		<meta name="twitter:site" content="@vidyosite">
		<meta name="twitter:creator" content="@mes">
		<link rel="shortcut icon" href="favicon.ico">
		<!--
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/memres/video/style.min.css">
		-->
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed:400,700|Cinzel:700&amp;subset=latin-ext"/>
	</head>
	<body>
		<div class="container">
			<div class="half-bottom"></div>
			<div class="sixteen columns">
				<div class="six columns logo alpha">
					<a href="./"><?= $site_title; ?></a>
				</div>
				<div class="ten columns menu omega half-bottom">
					<ul>
						<li><a href="?random<?php
							if ($page) echo "&amp;page=$page";
							if ($live) echo "&amp;live=1";
							if ($hd) echo "&amp;hd=1";
							if ($d3) echo "&amp;3d=1";
							if ($caption) echo "&amp;caption=1";
							if ($order) echo "&amp;order=$order";
							if ($duration) echo "&amp;duration=$duration";
							if ($term) echo "&amp;term=".urlencode($term);
							if ($id) echo "&amp;related=$id";
							if ($channel) echo "&amp;channel=$channel";
							if ($category) echo "&amp;category=$category";
						?>"><i class="fa fa-random"></i>Random</a></li>
						<li><a class="toggle-categories"><i class="fa fa-star-o"></i>Categories</a></li>
						<li><a class="toggle-search"><i class="fa fa-search-plus"></i>Search</a></li>
					</ul>
				</div>
			</div>
			<div class="sixteen columns search">
				<form action="./">
					<div class="four columns checkbox alpha">
						<label><input type="checkbox" name="live" value="1"<?php if ($live) echo ' checked="checked"'; ?>>Live</label>
					</div>
					<div class="four columns checkbox">
						<label><input type="checkbox" name="hd" value="1"<?php if ($hd) echo ' checked="checked"'; ?>>HD</label>
					</div>
					<div class="four columns checkbox">
						<label><input type="checkbox" name="3d" value="1"<?php if ($d3) echo ' checked="checked"'; ?>>3D</label>
					</div>
					<div class="four columns checkbox omega half-bottom">
						<label><input type="checkbox" name="caption" value="1"<?php if ($caption) echo ' checked="checked"'; ?>>Caption</label>
					</div>
					<div class="four columns select alpha">
						<select name="order">
							<optgroup label="Order by">
								<option value="">Relevance</option>
								<option value="rating"<?php if ($order == 'rating') echo ' selected="selected"'; ?>>Rating</option>
								<option value="viewcount"<?php if ($order == 'viewcount') echo ' selected="selected"'; ?>>View Count</option>
								<option value="date"<?php if ($order == 'date') echo ' selected="selected"'; ?>>Publish Date</option>
							</optgroup>
						</select>
					</div>
					<div class="four columns select half-bottom">
						<select name="duration">
							<optgroup label="Duration">
								<option value="">All</option>
								<option value="short"<?php if ($duration == 'short') echo ' selected="selected"'; ?>>Short (&lt;4&#8242;)</option>
								<option value="medium"<?php if ($duration == 'medium') echo ' selected="selected"'; ?>>Medium (4-20&#8242;)</option>
								<option value="long"<?php if ($duration == 'long') echo ' selected="selected"'; ?>>Long (&gt;20&#8242;)</option>
							</optgroup>
						</select>
					</div>
					<div class="eight columns omega add-bottom">
						<input type="search" name="term" placeholder="Search <?php if ($category) echo 'in Category'; elseif ($channel) echo 'in Channel'; else echo 'Term'; ?>" value="<?php if ($term) echo $term; ?>">
<?php if ($category) { ?>
						<input type="hidden" name="category" value="<?= $category; ?>">
<?php } if ($channel) { ?>
						<input type="hidden" name="channel" value="<?= $channel; ?>">
<?php } ?>
						<button type="submit"><i class="fa fa-search"></i></button>
					</div>
				</form>
			</div>
			<div class="sixteen columns categories">
				<ul class="add-bottom">
					<li><a <?php if ($category == 29) echo $active; ?>href="?category=29"><i class="fa fa-gift"></i> Activism</a></li>
					<li><a <?php if ($category == 15) echo $active; ?>href="?category=15"><i class="fa fa-paw"></i> Animals</a></li>
					<li><a <?php if ($category == 2) echo $active; ?>href="?category=2"><i class="fa fa-car"></i> Autos</a></li>
					<li><a <?php if ($category == 27) echo $active; ?>href="?category=27"><i class="fa fa-graduation-cap"></i> Education</a></li>
					<li><a <?php if ($category == 24) echo $active; ?>href="?category=24"><i class="fa fa-sign-language"></i> Entertainment</a></li>
					<li><a <?php if ($category == 20) echo $active; ?>href="?category=20"><i class="fa fa-gamepad"></i> Games</a></li>
					<li><a <?php if ($category == 26) echo $active; ?>href="?category=26"><i class="fa fa-wrench"></i> Howto</a></li>
					<li><a <?php if ($category == 10) echo $active; ?>href="?category=10"><i class="fa fa-music"></i> Music</a></li>
					<li><a <?php if ($category == 25) echo $active; ?>href="?category=25"><i class="fa fa-globe"></i> News</a></li>
					<li><a <?php if ($category == 17) echo $active; ?>href="?category=17"><i class="fa fa-futbol-o"></i> Sports</a></li>
					<li><a <?php if ($category == 28) echo $active; ?>href="?category=28"><i class="fa fa-rocket"></i> Science</a></li>
					<li><a <?php if ($category == 19) echo $active; ?>href="?category=19"><i class="fa fa-plane"></i> Travel</a></li>
				</ul>
			</div>
<?php if ($channel || $search) { ?>
			<div class="sixteen columns title half-bottom">
				<h4 class="ellipsis"><?php
					if ($channel) echo $channel_title;
					if ($search) {
						if ($channel) echo ' ';
						echo '<i class="fa fa-search-plus"></i> Search Results';
						if ($term) echo " for $term";
					}
				?></h4>
			</div>
<?php } if ($id) { ?>
			<div class="sixteen columns half-bottom video" id="<?= $id; ?>">
				<div id="player"></div>
			</div>
			<div class="sixteen columns add-bottom">
				<button class="autoplay"><i class="fa fa-toggle-on"></i> AutoPlay</button>
				<button class="continuous"><i class="fa fa-toggle-on"></i> Continuous Playing</button>
			</div>
			<div class="sixteen columns">
				<div class="row info">
					<div class="four columns alpha half-bottom">
						<p>Duration:</p>
						<h5><?= $video_duration; ?></h5>
					</div>
					<div class="four columns half-bottom">
						<p>Rating:</p>
						<h5 class="ellipsis"><i class="fa fa-thumbs-up"></i><?= number_format($video_likes); ?> <i class="fa fa-thumbs-down"></i><?= number_format($video_dislikes); ?></h5>
					</div>
					<div class="four columns">
						<p>View Count:</p>
						<h5 class="ellipsis"><?= number_format($video_views); ?></h5>
					</div>
					<div class="four columns omega">
						<p>Publish Date:</p>
						<h5><time datetime="<?= $video_datetime; ?>"><?= $video_date; ?></time></h5>
					</div>
				</div>
			</div>
			<div class="sixteen columns">
				<div class="row share">
					<div class="four columns alpha">
						<a width="586" height="316" href="https://twitter.com/intent/tweet?url=<?= urlencode($url); ?>&amp;text=<?= urlencode($video_title); ?>">
							<span class="fa-stack fa-lg">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
							</span>
							<span class="social">Twitter</span>
						</a>
					</div>
					<div class="four columns">
						<a width="574" height="514" href="https://www.reddit.com/submit?url=<?= urlencode($url); ?>">
							<span class="fa-stack fa-lg">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-reddit fa-stack-1x fa-inverse"></i>
							</span>
							<span class="social">Reddit</span>
						</a>
					</div>
					<div class="four columns">
						<a width="480" height="400" href="https://wa.me/?text=<?= urlencode($url); ?>">
							<span class="fa-stack fa-lg">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-whatsapp fa-stack-1x fa-inverse"></i>
							</span>
							<span class="social">Whatsapp</span>
						</a>
					</div>
					<div class="four columns omega">
						<a width="557" height="621" href="https://www.tumblr.com/widgets/share/tool?posttype=video&amp;content=http%3A%2F%2Fyoutu.be%2F<?= $id; ?>&amp;canonicalUrl=<?= urlencode($url); ?>">
							<span class="fa-stack fa-lg">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-tumblr fa-stack-1x fa-inverse"></i>
							</span>
							<span class="social">Tumblr</span>
						</a>
					</div>
				</div>
			</div>
			<div class="sixteen columns half-bottom">
				<h4 class="ellipsis"><?= $video_title; ?></h4>
				<h5 class="ellipsis">Published by <a href="?channel=<?= $video_channel_id ?>"><?= $video_channel ?></a></h5>
			</div>
<?php if ($video_description) { ?>
			<div class="sixteen columns">
				<div class="row description">
					<p><?= autolink($video_description); ?></p>
				</div>
			</div>
<?php } if ($video_tags) { ?>
			<div class="sixteen columns">
				<div class="row">
					<ul class="tags">
<?php foreach ($video_tags as $tag) { ?>
						<li class="tag"><a rel="tag" href="?term=<?= urlencode($tag); ?>"><?= $tag; ?></a></li>
<?php } ?>
					</ul>
				</div>
			</div>
<?php } ?>
			<div class="sixteen columns half-bottom scrolload">
				<h4><i class="fa fa-puzzle-piece"></i> Related Videos</h4>
			</div>
			<div class="related-videos"></div>
<?php } else {
	if (isset($response['items'][0])) {
		foreach ($response['items'] as $item) {
			$video_id = $item['id']['videoId'];
			$video_title = $item['snippet']['title'];
			$video_image = $item['snippet']['thumbnails']['medium']['url'];
?>
			<div class="four columns">
				<div class="row thumb">
					<a href="?id=<?= $video_id; ?>">
						<img class="scale" src="<?= $video_image; ?>" alt="<?= $video_title; ?>">
						<div class="caption">
							<p class="ellipsis"><?= $video_title; ?></p>
						</div>
					</a>
				</div>
			</div>
<?php } if ($nextpage || $prevpage) { ?>
			<div class="sixteen columns">
				<div class="row prevnext">
					<div class="eight columns alpha">
						<h5><?php
						if ($prevpage) {
							?><a href="?page=<?= $prevpage;
							if ($live) echo "&amp;live=1";
							if ($hd) echo "&amp;hd=1";
							if ($d3) echo "&amp;3d=1";
							if ($caption) echo "&amp;caption=1";
							if ($order) echo "&amp;order=$order";
							if ($duration) echo "&amp;duration=$duration";
							if ($term) echo "&amp;term=".urlencode($term);
							if ($channel) echo "&amp;channel=$channel";
							if ($category) echo "&amp;category=$category";
							?>"><i class="fa fa-arrow-circle-left"></i> Previous</a><?php
						} else echo "&nbsp;"; ?></h5>
					</div>
					<div class="eight columns omega right">
						<h5><?php
						if ($nextpage) {
							?><a href="?page=<?= $nextpage;
							if ($live) echo "&amp;live=1";
							if ($hd) echo "&amp;hd=1";
							if ($d3) echo "&amp;3d=1";
							if ($caption) echo "&amp;caption=1";
							if ($order) echo "&amp;order=$order";
							if ($duration) echo "&amp;duration=$duration";
							if ($term) echo "&amp;term=".urlencode($term);
							if ($channel) echo "&amp;channel=$channel";
							if ($category) echo "&amp;category=$category";
							?>">Next <i class="fa fa-arrow-circle-right"></i></a><?php
						} ?></h5>
					</div>
				</div>
			</div>
<?php } } else { ?>
			<div class="sixteen columns center add-bottom">
				<span class="fa-stack fa-lg"><i class="fa fa-times-circle fa-stack-2x"></i></span>
				<h4>No videos found<?php if ($channel) echo ' in this channel'; if ($search) echo ' matching with your query'; ?>.</h4>
			</div>
<?php } } ?>
			<div class="sixteen columns">
				<div class="row footer">
					<div class="one-third column alpha">
						<h6><a class="toggle-about">&copy; <?= date('Y ').$site_title; ?></a></h6>
					</div>
					<div class="one-third column top">
						<a><i class="fa fa-chevron-circle-up"></i></a>
					</div>
					<div class="one-third column omega right">
						<h6><a href="https://www.emresanli.com/" title="Device friendly design"><i class="fa fa-desktop"></i> <i class="fa fa-tv"></i> <i class="fa fa-tablet"></i> <i class="fa fa-mobile-phone"></i></a></h6>
					</div>
				</div>
			</div>
			<div class="sixteen columns">
				<div class="row about">
					<div class="eight columns alpha">
						<p><b><?= $site_title; ?></b> has been firstly created in 2006 in just a few hours as a course project. Initial version was a single page embedding live television channels. In time this thing has turned into an online theater by containing hundreds of movies and episodes of popular series. Via the latest version of the project which is completely renovated in 2017, billions of <b>YouTube</b> videos can be reached hassle free, even if it is blocked at your location.</p>
					</div>
					<div class="eight columns omega">
						<p>You can list videos with user friendly search options, and also under various categories pregenerated for easy access. Watch unlimited videos without ads having <b>AutoPlay</b> and <b>Continuous Playing</b> buttons enabled. Last but not least, there is an extra feature YouTube still does not have that you may jump to a <b>random</b> video by a single click whenever you feel lucky. If you want to stay in touch, you are welcome to follow <a href="https://twitter.com/vidyosite"><?= $site_title; ?> on <i class="fa fa-twitter-square"></i></a>.</p>
					</div>
				</div>
			</div>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.0/js.cookie.min.js"></script>
<?php if ($id) { ?>
		<script src="https://www.youtube.com/iframe_api" async></script>
		<!--
		<script src="https://cdn.jsdelivr.net/gh/memres/video/single.min.js"></script>
		-->
		<script>
			var player, YTdeferred = $.Deferred();
			window.onYouTubeIframeAPIReady = function() {
				YTdeferred.resolve(window.YT);
			};
			$(function(){
				if (Cookies.get('autoplay')) $('.autoplay').find('i').attr('class', 'fa fa-toggle-off');
				if (Cookies.get('continuous')) $('.continuous').find('i').attr('class', 'fa fa-toggle-off');
				var id = $('.video').attr('id');
				YTdeferred.done(function(YT) {
					player = new YT.Player('player', {
						host: 'https://www.youtube-nocookie.com',
						videoId: id,
						playerVars: {
							'hl': 'en',
							'iv_load_policy': 3,
							'modestbranding': 1,
							'rel': 0
						},
						events: {
							'onReady': function () {
								if (!Cookies.get('autoplay')) player.playVideo();
							},
							'onStateChange': function (event) {
								if (event.data == 0) {
									if (!Cookies.get('continuous')) $(location).attr('href', '?random&related=' + id);
								}
							}
						}
					});
				});
				$(document).on('click', '.autoplay', function() {
					if (Cookies.get('autoplay')) {
						Cookies.remove('autoplay');
						$(this).find('i').attr('class', 'fa fa-toggle-on');
					} else {
						Cookies.set('autoplay', '1', { expires: 30 });
						$(this).find('i').attr('class', 'fa fa-toggle-off');
					}
				});
				$(document).on('click', '.continuous', function() {
					if (Cookies.get('continuous')) {
						Cookies.remove('continuous');
						$(this).find('i').attr('class', 'fa fa-toggle-on');
					} else {
						Cookies.set('continuous', '1', { expires: 30 });
						$(this).find('i').attr('class', 'fa fa-toggle-off');
					}
				});
				$(document).on('click', '.share a', function(event) {
					event.preventDefault();
					var url = $(this).attr('href'),
					popwidth = $(this).attr('width'),
					popheight = $(this).attr('height'),
					popup = window.open (url, '_blank', 'width=' + popwidth + ', height=' + popheight);
					popup.moveTo((screen.width / 2) - (popwidth / 2), (screen.height / 2) - (popheight / 2)).focus();
				});
				var clamp = 74;
				if ($(window).width() > 767) clamp = 92;
				if ($('.tags').height() > clamp) {
					$('.tags').addClass('collapse').prepend('<li class="tag"><a class="expand"><i class="fa fa-chevron-down"></i> More Tags</a></li>');
				}
				var link = '<a class="expand"><i class="fa fa-chevron-down"></i> Read More</a>';
				if ($('.description p').height() > 130) {
					$('.description p').addClass('collapse').after(link);
				}
				$(document).on('click', '.expand', function() {
					text = $(this).parent().find('p');
					if ($(this).parent().parent().hasClass('tags')) text = $(this).parent().parent();
					collapse = '<i class="fa fa-chevron-up"></i> ';
					if ($(this).parent().parent().hasClass('tags')) collapse += 'Less Tags';
					else collapse += 'Read Less';
					expand = '<i class="fa fa-chevron-down"></i> ';
					if ($(this).parent().parent().hasClass('tags')) expand += 'More Tags';
					else expand += 'Read More';
					if (text.hasClass('collapse')) {
						text.removeClass('collapse');
						$(this).html(collapse);
					} else {
						text.addClass('collapse');
						$(this).html(expand);
					}
				});
				function elementScrolled(elem) {
					var docViewTop = $(window).scrollTop(),
					docViewBottom = docViewTop + $(window).height(),
					elemTop = $(elem).offset().top;
					return ((elemTop <= docViewBottom) && (elemTop >= docViewTop));
				}
				$(window).on('scroll', function() {
					if (elementScrolled('.scrolload')) {
						$(window).off('scroll');
						$.ajax({
							url: 'related.php',
							data: 'v=' + id,
							beforeSend: function() {
								$('.fa-puzzle-piece').addClass('fa-spin');
							},
							success: function(response) {
								$('.fa-puzzle-piece').removeClass('fa-spin');
								$('.related-videos').html(response);
							}
						})
					}
				});
				$(document).on('click', '.more-videos', function() {
					var diz = $(this), more = $(this).attr('value');
					$.ajax({
						url: 'related.php',
						data: more,
						beforeSend: function() {
							diz.css('pointer-events', 'none').find('i').addClass('fa-spin');
						},
						success: function(response) {
							diz.parent().remove();
							$('.related-videos').append(response);
						}
					});
				});
			});
		</script>
<?php } ?>
		<!--
		<script src="https://cdn.jsdelivr.net/gh/memres/video/main.min.js"></script>
		-->
		<script>
			$(function(){
				$(document).on('click', '.toggle-categories', function() {
					if ($(this).hasClass('active')) {
						$(this).removeClass('active');
						$('.categories').slideUp(200);
					}
					else {
						if ($('.toggle-search').hasClass('active')) $('.toggle-search').removeClass('active');
						$(this).addClass('active');
						$('.search').slideUp(200);
						$('.categories').slideDown(200);
					}
				});
				$(document).on('click', '.toggle-search', function() {
					if ($(this).hasClass('active')) {
						$(this).removeClass('active');
						$('.search').slideUp(200);
					}
					else {
						if ($('.toggle-categories').hasClass('active')) $('.toggle-categories').removeClass('active');
						$(this).addClass('active');
						$('.categories').slideUp(200);
						$('.search').slideDown(200);
					}
				});
				$(document).on('submit', '.search', function() {
					$(this).find('button').css('pointer-events', 'none').html('<i class="fa fa-spinner fa-spin"></i>');
				});
				$(document).on('click', '.toggle-about', function() {
					$('.about').slideToggle(200);
					$('html, body').animate({ scrollTop: $('.about').offset().top }, 200);
				});
				$(document).on('click', '.top a', function() {
					$('html, body').animate({ scrollTop: 0 }, 400);
				});
				if (/Mobi|Android/i.test(navigator.userAgent)) {
					$('.omega label').text('CC');
				}
			});
		</script>
		<!-- Analytics & Stats -- Remove or replace with your own -->
		<script>var sc_invisible = 1, sc_project = 5408945, sc_security = 'f75ba4c3';window.ga = window.ga || function(){(ga.q = ga.q || []).push(arguments)};ga.l = +new Date;ga('create', 'UA-28085788-1', 'auto');ga('send', 'pageview');</script>
		<script src="https://statcounter.com/counter/counter.js"></script>
		<script src="https://www.google-analytics.com/analytics.js" async></script>
	</body>
</html>
