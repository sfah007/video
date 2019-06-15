var player, YTdeferred = $.Deferred();
window.onYouTubeIframeAPIReady = function() {
	YTdeferred.resolve(window.YT);
};
$(function() {
	if (Cookies.get('autoplay')) $('.autoplay i').attr('class', 'fa fa-toggle-off');
	if (Cookies.get('continuous')) $('.continuous i').attr('class', 'fa fa-toggle-off');
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
					var hour = parseInt(player.getDuration() / 3600),
					min = parseInt((player.getDuration() / 60) % 60),
					sec = parseInt(player.getDuration() - ((hour * 3600) + (min * 60))),
					duration = (hour ? hour + ':' : '') + (hour && min < 10 ? '0' + min : min) + ':' + (sec < 10 ? '0' + sec : sec);
					$('.duration').text(duration == '0:00' ? 'live' : duration);
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
	if ($('.tags').height() > ($(window).width() > 767 ? 92 : 74)) {
		$('.tags').addClass('collapse').prepend('<li class="expand"><i class="fa fa-chevron-down"></i> More Tags</li>');
	}
	if ($('.description p').height() > 130) {
		$('.description p').addClass('collapse').after('<a class="expand"><i class="fa fa-chevron-down"></i> Read More</a> ');
	}
	$(document).on('click', '.expand', function() {
		var text = $(this).html(),
		collapse = '<i class="fa fa-chevron-up"></i> ' + ($(this).parent().hasClass('tags') ? 'Less Tags' : 'Read Less'),
		expand = '<i class="fa fa-chevron-down"></i> ' + ($(this).parent().hasClass('tags') ? 'More Tags' : 'Read More');
		$(this).html(text == expand ? collapse : expand);
		($(this).parent().hasClass('tags') ? $('.tags') : $('.description p')).toggleClass('collapse');
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
