var player;
function onYouTubeIframeAPIReady() {
	player = new YT.Player('player', {
		host: 'https://www.youtube-nocookie.com',
		videoId: document.querySelector('.video').getAttribute('id'),
		playerVars: {
			'hl': 'en',
			'iv_load_policy': 3,
			'modestbranding': 1,
			'rel': 0
		},
		events: {
			'onReady': onPlayerReady,
			'onStateChange': onPlayerStateChange
		}
	});
}
$(function(){
	if (Cookies.get('autoplay')) $('.autoplay i').attr('class', 'fa fa-toggle-off');
	if (Cookies.get('continuous')) $('.continuous i').attr('class', 'fa fa-toggle-off');
	var id = $('.video').attr('id');
	function onPlayerReady() {
		var duration = player.getDuration(),
		hour = parseInt(duration / 3600),
		min = parseInt((duration / 60) % 60),
		sec = parseInt(duration - ((hour * 3600) + (min * 60)));
		$('.duration').text((hour ? hour + ':' : '') + (hour && min < 10 ? '0' + min : min) + ':' + (sec < 10 ? '0' + sec : sec));
		if (!Cookies.get('autoplay')) player.playVideo();
	}
	function onPlayerStateChange(event) {
		if (event.data == 0) {
			if (!Cookies.get('continuous')) $(location).attr('href', '?random&related=' + id);
		}
	}
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
		var text = $(this).parent().find('p');
		if ($(this).parent().parent().hasClass('tags')) text = $(this).parent().parent();
		var collapse = '<i class="fa fa-chevron-up"></i> ';
		if ($(this).parent().parent().hasClass('tags')) collapse += 'Less Tags';
		else collapse += 'Read Less';
		var expand = '<i class="fa fa-chevron-down"></i> ';
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
	$(window).on('scroll', function() {
		if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
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
});
