var callback, folder = window.location.pathname.split('/').slice(0, -1).join('/'), mobile = false, player, timeout, YTdeferred = $.Deferred();
window.onYouTubeIframeAPIReady = function() {
	YTdeferred.resolve(window.YT);
};
$(function() {
	$('[name="country"]').val(Cookies.get('country'));
	$('[name="language"]').val(Cookies.get('language'));
	if (Cookies.get('playlist')) {
		if ($('h4').length) $('h4').each(checkplus);
		if ($('ins').length) $('ins').each(checkplus);
	}
	if ($('h4').length) Cookies.set('random', $('h4').attr('id'), {expires: 365, path: folder});
	if ($('ins').length) Cookies.set('random', $('ins:eq(' + ~~(Math.random() * ($('ins').length - 1)) + ')').attr('id'), {expires: 365, path: folder});
	if ($('.info p').length) $('.info p').each(clamp);
	if ($('.shuffle').length && Cookies.get('shuffle')) $('.shuffle').addClass('on');
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		mobile = true;
		var script = document.createElement('script');
		script.onload = function() {
			var shakeEvent = new Shake({threshold: 30});
			shakeEvent.start();
			window.addEventListener('shake', random, false);
		}
		script.src = 'https://cdn.jsdelivr.net/npm/shake.js@1.2.2/shake.min.js';
		document.head.appendChild(script);
	}
	$('footer i:eq(0)').attr('src', 'https://lh3.googleusercontent.com/' + (mobile ? '-vZXIiRQJlBE/XSdhtGSzx7I/AAAAAAAACQ0/U6zS9omB0CMIYnGLTIZrFRfin6a193tWQCDMYAw' : '-1k3S10FowvA/XSdb1I9egYI/AAAAAAAACQY/3A0DNHNZwI0p8mDigBWtt15pVILInssEQCDMYAw') + '/s40-c/');
	$(window).on('beforeunload', function() {
		$('header h1 i').addClass('fa-spin');
		setTimeout(function() {$('header h1 i').removeClass('fa-spin')}, 10000);
	});
	$('form').on('submit', function() {
		$('button i', this).addClass('fa-spinner fa-spin');
	});
	$('header b').on('click', function() {
		if ($('header b').not(this).hasClass('on')) {
			$('header b.on').removeClass('on');
			$('nav').fadeOut();
		}
		$(this).toggleClass('on');
		$('.' + $(this).attr('id')).slideToggle();
	});
	$('.settings :checkbox').on('change', function() {
		var name = $(this).attr('name');
		this.checked ? Cookies.set(name, 1, {expires: 365, path: folder}) : Cookies.remove(name, {path: folder});
	});
	$('.settings select').on('change', function() {
		Cookies.set($(this).attr('name'), this.value, {expires: 365, path: folder});
		window.location.reload();
	});
	$('.settings u').on('click', function() {
		window.location.href = 'https://www.youtube.com/feed/history';
	});
	$('footer i:eq(1)').on('click', function() {
		$('html, body').animate({scrollTop: 0});
	});
	$('#q').autocomplete({
		source: function(request, response) {
			$.getJSON('https://www.google.com/complete/search?callback=?', {
				'client': 'youtube',
				'ds': 'yt',
				'hl': Cookies.get('language'),
				'jsonp': 'callback',
				'q': request.term
			});
			callback = function(data) {
				var suggestions = [];
				$.each(data[1], function(key, val) {
					suggestions.push({'value': val[0]});
				});
				suggestions.length = 8;
				response(suggestions);
			};
		}
	});
	$('.playlist').sortable({
		handle: 'small',
		items: 'article',
		update: function() {
			var list = '';
			$('article').each(function(i, e) {
				list += $('ins', e).attr('id') + 'ჲ፨ဇ' + $('span', e).text() + 'ဇ፨ჲ';
			});
			localStorage.setItem('playlist', list);
			if ($('.shuffle').hasClass('on')) {
				$('.shuffle').removeClass('on');
				Cookies.remove('shuffle', {path: folder});
			}
		}
	});
	$('.playlist').disableSelection();
	YTdeferred.done(function(YT) {
		player = new YT.Player('player', {
			host: 'https://www.youtube' + (Cookies.get('save_history') ? '.com' : '-nocookie.com'),
			playerVars: {
				'fs': 0,
				'hl': Cookies.get('language'),
				'iv_load_policy': 3,
				'modestbranding': 1
			},
			events: {
				'onReady': function() {
					if ($('.playlist').length) listize();
					var first = $('h4').length ? $('h4').attr('id') : $('ins:eq(0)').attr('id');
					if (Cookies.get('save_history')) {
						player.loadVideoById(first);
						if (!Cookies.get('auto_start')) player.pauseVideo();
					}
					else {
						player.cueVideoById(first);
						if (Cookies.get('auto_start')) player.playVideo();
					}
				},
				'onStateChange': function(event) {
					if ($('.playlist').length) {
						if (event.data == 0) {
							if ($('.fa-history').length) player.playVideo();
							else prevnext();
						}
						if (event.data == 1) iconize('pause');
						if (event.data == 2 || event.data == 5) iconize('play');
					}
					else if (event.data == 0 && Cookies.get('infinite_playback')) random();
				},
				'onError': function() {
					if ($('.playlist').length) {
						var broken = $('#' + player.getVideoData()['video_id']).parent().parent();
						prevnext();
						broken.remove();
					}
					else $('figcaption').remove();
				}
			}
		});
	});
	$('figcaption').on('mousemove', function() {
		window.clearTimeout(timeout);
		$('i', this).show();
		$(this).css('cursor', 'auto');
		timeout = window.setTimeout(function() {
			$('figcaption').css('cursor', 'none');
			$('figcaption i').fadeOut();
		}, 2000);
	});
	$('figcaption').on('click', function(event) {
		if ($(event.target).is('i')) return;
		playpause();
	});
	function playpause() {
		player.getPlayerState() == 1 ? player.pauseVideo() : player.playVideo();
	}
	$('.playlist aside a').on('click', function() {
		$('figure, ol, section').fadeOut();
		setTimeout(function() {$('main').empty()}, 600);
		Cookies.remove('playlist', {path: folder});
		localStorage.setItem('playlist', '');
	});
	$(document).on('click', '.playlist img', function() {
		if ($(this).prev().attr('id') == player.getVideoData()['video_id']) playpause();
		else player.loadVideoById($(this).prev().attr('id'));
	});
	$(document).on('click', '.playlist span', function() {
		window.location.href = '?v=' + $(this).next().attr('id');
	});
	$(document).on('click', 'h4, ins', function(event) {
		event.preventDefault();
		var id = $(this).attr('id'), title = $(this).parent('.info').length ? $(this).text() : $(this).prev().text(), li = id + 'ჲ፨ဇ' + title + 'ဇ፨ჲ';
		if ($('i', this).hasClass('fa-plus-circle')) {
			Cookies.set('playlist', id, {expires: 365, path: folder});
			if (!localStorage.getItem('playlist')) localStorage.setItem('playlist', '');
			localStorage.setItem('playlist', localStorage.getItem('playlist') + li);
		}
		else {
			localStorage.setItem('playlist', localStorage.getItem('playlist').replace(li, ''));
			if (localStorage.getItem('playlist') == '') Cookies.remove('playlist', {path: folder});
			if ($('.playlist').length) {
				var item = $(this).parent().parent();
				item.fadeOut();
				setTimeout(function() {item.remove()}, 600);
				if (id == player.getVideoData()['video_id']) prevnext();
			}
		}
		$('i', this).toggleClass('fa-plus-circle fa-check-circle');
	});
	function listize() {
		var arr = localStorage.getItem('playlist').replace(/ဇ፨ჲ$/, '').split('ဇ፨ჲ');
		if ($('.shuffle').hasClass('on')) {
			for (let i = arr.length - 1; i > 0; i--) {
				let j = Math.floor(Math.random() * (i + 1));
				[arr[i], arr[j]] = [arr[j], arr[i]];
			}
		}
		$('article').remove();
		$.each(arr.reverse(), function(i, val) {
			var str = val.split('ჲ፨ဇ');
			$('.playlist').prepend('<article><b><small><i class="fas fa-arrows-alt fa-2x"></i></small><span>' + str[1] + '</span><ins id="' + str[0] + '"><i class="fas fa-check-circle fa-2x"></i></ins><img src="https://i.ytimg.com/vi/' + str[0] + '/mqdefault.jpg"/></b></article>');
		});
		$('#' + player.getVideoData()['video_id']).parent().find('.fa-arrows-alt').addClass('fa-' + (player.getPlayerState() != 1 ? 'play' : 'pause') + '-circle');
		Cookies.set('random', $('ins:eq(' + ~~(Math.random() * ($('ins').length - 1)) + ')').attr('id'), {expires: 365, path: folder});
	}
	function iconize(pp) {
		$('.fa-arrows-alt').removeClass('fa-play-circle fa-pause-circle');
		$('#' + player.getVideoData()['video_id']).parent().find('.fa-arrows-alt').removeClass('fa-' + (pp == 'play' ? 'pause' : 'play') + '-circle').addClass('fa-' + pp + '-circle');
	}
	$('.forward').on('click', prevnext);
	$('.backward').on('click', function() {
		prevnext(false);
	});
	function prevnext(bool = true) {
		var ins = $('#' + player.getVideoData()['video_id']).parent().parent();
		if (bool) ins = ins.next().find('ins');
		else ins = ins.prev().find('ins');
		if (ins.length) player.loadVideoById(ins.attr('id'));
		else if (bool) {
			if ($('.loop').hasClass('on')) player.loadVideoById($('ins:eq(0)').attr('id'));
			else if (Cookies.get('infinite_playback')) random();
			else iconize('play');
		}
	}
	$('.loop').on('click', looper);
	function looper() {
		if ($('.loop').hasClass('on')) {
			if ($('.fa-undo-alt').length) $('.loop').find('i').toggleClass('fa-undo-alt fa-history');
			else $('.loop').removeClass('on').find('i').toggleClass('fa-history fa-undo-alt');
		}
		else $('.loop').addClass('on');
	}
	$('.shuffle').on('click', shuffle);
	function shuffle() {
		$('.shuffle').toggleClass('on');
		$('.shuffle').hasClass('on') ? Cookies.set('shuffle', 1, {expires: 365, path: folder}) : Cookies.remove('shuffle', {path: folder});
		listize();
	}
	$(window).on('keydown', function(event) {
		if ($(event.target).is('INPUT')) return;
		if ($('h4').length || $('.playlist').length) {
			if (event.which == 32) return false;
			if (event.which == 37) player.seekTo(player.getCurrentTime() - 5);
			if (event.which == 39) player.seekTo(player.getCurrentTime() + 5);
			if (event.which == 40 && event.ctrlKey) player.setVolume(player.getVolume() - 5);
			if (event.which == 38 && event.ctrlKey) player.setVolume(player.getVolume() + 5);
		}
	});
	$(window).on('keyup', function(event) {
		if ($(event.target).is('INPUT')) return;
		if (event.which == 13) random();
		if (event.which == 27 && $('body').hasClass('freeze')) defrost();
		if (event.which == 78 && $('aside a[rel="next"]').length) window.location.href = $('aside a[rel="next"]').attr('href');
		if ($('h4').length || $('.playlist').length) {
			if (event.which == 32) playpause();
			if (event.which == 70 && !$('body').hasClass('freeze')) $('figure').toggleFullScreen();
			if (event.which == 77) player.isMuted() ? player.unMute() : player.mute();
			if ($('.playlist').length) {
				if (event.which == 78) prevnext();
				if (event.which == 80) prevnext(false);
				if (event.which == 76) looper();
				if (event.which == 83) shuffle();
			}
		}
	});
	$('.fa-expand').on('click', function() {
		$('figure').toggleFullScreen();
	});
	$(document).on('fullscreenchange', function() {
		$('.fa-expand').toggleClass('off');
	});
	$(document).on('click', 'time', function() {
		var elapsed = $(this).attr('data-time');
		$(this).text($(this).text() == elapsed ? $(this).attr('datetime') : elapsed);
	});
	$(document).on('click', '.comments img, footer i:eq(0)', function() {
		var img = $(this);
		img.addClass('rotate');
		$('.modal').attr('src', img.attr('src').replace('s40-c', 's0')).on('load', function() {
			$(this).show();
			img.removeClass('rotate');
			$('body').addClass('freeze');
		});
	});
	$('.modal').on('click', defrost);
	function defrost() {
		$('.modal').hide();
		$('body').removeClass('freeze');
	}
	$('.download a').on('click', function() {
		$(this).addClass('on');
		var button = $(this);
		setTimeout(function() {button.removeClass('on')}, 3000);
		window.location.href = 'https://invidio.us/latest_version?download_widget=' + encodeURI('{"id":"' + $('h4').attr('id') + '","title":"' + $('h4').text() + '.' + $(this).attr('type') + '","itag":"' + $(this).attr('download') + '"}');
	});
	$(document).on('click', 'legend', function() {
		$(this).prev().toggleClass('clamp');
		$('span i', this).toggleClass('fa-chevron-circle-down fa-chevron-circle-up');
	});
	$('ol b:not(.off)').on('click', function() {
		if (!$(this).hasClass('on')) {
			var name = $(this).attr('id'), tab = $('.' + name);
			$('ol .on').removeClass('on');
			$(this).addClass('on');
			$('section').slideUp();
			if (tab.is(':empty')) {
				$.ajax({
					url: name + '.php',
					data: 'id=' + tab.attr('data-id'),
					beforeSend: function() {
						$('ol').css('pointer-events', 'none');
						tab.show().html('<div class="loading"><i class="fas fa-sync-alt fa-spin fa-2x"></i></div>');
					},
					success: function(response) {
						$('ol').css('pointer-events', 'auto');
						tab.html(response);
						if (name == 'comments') $('.comments p').each(clamp);
						else if (Cookies.get('playlist')) $('ins').each(checkplus);
						if (!mobile) $('html, body').animate({scrollTop: tab.offset().top - 50});
					}
				});
			}
			else tab.slideDown();
		}
	});
	$(document).on('click', 'aside b', function() {
		var button = $(this).parent();
		$.ajax({
			url: button.parent().attr('class') + '.php',
			data: 'id=' + button.parent().attr('data-id') + '&token=' + button.attr('id'),
			beforeSend: function() {
				button.css('pointer-events', 'none').find('i').addClass('fa-spin');
			},
			success: function(response) {
				button.after(response);
				if (button.parent().attr('class') == 'comments') button.nextAll().find('p').each(clamp);
				else if (Cookies.get('playlist')) button.nextAll().find('ins').each(checkplus);
				if (!mobile) $('html, body').animate({scrollTop: button.offset().top - 60});
				button.remove();
			}
		});
	});
	$(document).on('click', 'blockquote b', function() {
		if (!$(this).parent().next().length) {
			var button = $(this);
			$.ajax({
				url: 'replies.php',
				data: 'id=' + button.parent().parent().attr('id'),
				// $('.comments').attr('data-id') + '&token=' + button.attr('id')
				beforeSend: function() {
					button.css('pointer-events', 'none').addClass('on').find('i').removeClass('far fa-comment').addClass('fas fa-spinner fa-spin');
				},
				success: function(response) {
					button.css('pointer-events', 'auto').find('i').removeClass('fa-spinner fa-spin').addClass('fa-comment');
					button.parent().after('<dt>' + response + '</dt>');
					button.parent().next().find('p').each(clamp);
					if (!mobile) $('html, body').animate({scrollTop: button.offset().top - 10});
				}
			});
		}
		else $(this).toggleClass('on').find('i').toggleClass('far fas').parent().parent().next().slideToggle();
	});
	$(document).on('click', 'dt b', function() {
		var button = $(this);
		$.ajax({
			url: 'replies.php',
			data: 'id=' + button.parent().parent().attr('id') + '&token=' + button.attr('id'),
			// $('.comments').attr('data-id')
			beforeSend: function() {
				button.css('pointer-events', 'none').find('i').addClass('fa-spin');
			},
			success: function(response) {
				button.after(response);
				button.nextAll().find('p').each(clamp);
				if (!mobile) $('html, body').animate({scrollTop: button.offset().top - 70});
				button.remove();
			}
		});
	});
	function checkplus() {
		if (localStorage.getItem('playlist').includes($(this).attr('id'))) $('i', this).removeClass('fa-plus-circle').addClass('fa-check-circle');
	}
	function clamp() {
		if ($(this).height() > 100) $(this).addClass('clamp').after('<legend><span><i class="fas fa-chevron-circle-down"></i></span></legend>');
	}
	function random() {
		window.location.href = '?r';
	}
});
