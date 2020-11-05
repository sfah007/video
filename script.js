var callback, YTdeferred = $.Deferred();
window.onYouTubeIframeAPIReady = function() {
	YTdeferred.resolve(window.YT);
};
$(document).ready(function() {
	var clear, player, home = $('h1 a').attr('href'), folder = location.pathname.split('/').slice(0, -1).join('/'), mobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
	$.each(['auto_start', 'infinite_playback', 'safe_search', 'save_history'], function(i, v) {
		if (Cookies.get(v)) $('[name="'+v+'"]').attr('checked', true);
	});
	$('[name="country"]').val(Cookies.get('country').toUpperCase());
	$('[name="language"]').val(Cookies.get('language'));
	injection();
	$('header li a:lt(3)').on('click', function() {
		if ($('header a').not(this).hasClass('on')) {
			$('header .on').removeClass('on');
			$('nav:visible').fadeOut();
		}
		$(this).toggleClass('on');
		$('.'+$(this).attr('id')).slideToggle();
	});
	$('form').on('submit', function(e) {
		e.preventDefault();
		navigator.vibrate(50);
		ajaxify(home+'?'+$(this).serialize().replace(/%20/g, '+'));
	});
	$('.settings :checkbox').on('change', function() {
		this.checked ? Cookies.set(this.name, 1, {expires: 365, path: folder}) : Cookies.remove(this.name, {path: folder});
	});
	$('.settings select').on('change', function() {
		Cookies.set(this.name, this.value, {expires: 365, path: folder});
		location.reload();
	});
	$('.settings u').on('click', function() {
		location.href = 'https://www.youtube.com/feed/history';
	});
	$('.fa-chevron-up').on('click', function() {
		$('html, body').animate({scrollTop: 0});
	});
	$('.fa-keyboard').on('click', function() {
		$('body').addClass('off').children('header, nav, main, footer').addClass('hide');
		$('kbd, kbd ul').show();
	});
	$('kbd').on('click', defrost);
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
				let suggestions = [];
				$.each(data[1], function(key, val) {
					suggestions.push({'value': val[0]});
				});
				suggestions.length = 10;
				response(suggestions);
			};
		},
		select: function(event, ui) {
			$(event.target).val(ui.item.value);
			$('form').submit();
		}
	});
	$(document).on('click', 'article b, h5 b', function(e) {
		let v = $(this).attr('id'), t = v+'ჲ፨ဇ'+($(e.target).closest('h5').length ? $('h4').text() : $(this).prev().children('span').text())+'ဇ፨ჲ';
		if ($('i', this).hasClass('fa-plus-circle')) {
			Cookies.set('playlist', v, {expires: 365, path: folder});
			if (!localStorage.getItem('playlist')) localStorage.setItem('playlist', '');
			localStorage.setItem('playlist', localStorage.getItem('playlist')+t);
		}
		else {
			localStorage.setItem('playlist', localStorage.getItem('playlist').replace(t, ''));
			if (localStorage.getItem('playlist') == '') Cookies.remove('playlist', {path: folder});
			if ($('.playlist').length) {
				let a = $(this).parent();
				a.fadeOut();
				setTimeout(function() {
					a.remove();
					if (!$('article').length) $('figure, ol, section').remove();
				}, 400);
				if (v == player.getVideoData().video_id && player.getPlayerState() > 0 && player.getPlayerState() < 4) forward();
			}
		}
		$('i', this).toggleClass('fa-plus-circle fa-check-circle');
	});
	$(document).on('click', '.playlist article a', function(e) {
		e.preventDefault();
		if ($(this).next().attr('id') == player.getVideoData().video_id) playpause();
		else player.loadVideoById($(this).next().attr('id'));
	});
	$(document).on('click', '.playlist aside a', function() {
		if ($(this).is('.prev')) {
			$('figure, ol, section').fadeOut();
			setTimeout(function() {
				$('figure, ol, section').remove();
			}, 400);
			Cookies.remove('playlist', {path: folder});
			localStorage.setItem('playlist', '');
		}
		else if ($(this).is('.next')) $(this).parent().html(clear);
		else {
			navigator.vibrate([50, 50, 50, 50, 50, 50, 100, 100, 200]);
			$(this).parent().html('<a class="prev"><i class="fas fa-check-circle"></i> '+$(this).attr('data-yes')+'</a><a class="next"><i class="fas fa-times-circle"></i> '+$(this).attr('data-no')+'</a>');
		}
	});
	$(document).on('click', 'ol b:not(.off)', function() {
		if (!$(this).hasClass('on')) {
			let n = $(this).attr('id'), t = $('.'+n);
			$('ol b').stop(true, true).removeClass('on');
			$(this).addClass('on');
			$('section').slideUp();
			if (t.is(':empty')) {
				t.show().html('<dl><i class="fas fa-sync-alt fa-spin fa-2x"></i></dl>');
				$.get(home+n+'.php?id='+t.attr('data-id'), function(e) {
					t.html(e);
					if (n == 'comments') $('.comments p').each(clamp);
					else if (Cookies.get('playlist')) $('article b').each(checkplus);
					if (!mobile) $('html, body').animate({scrollTop: t.offset().top - 50});
				});
			}
			else t.slideDown();
		}
	});
	$(document).on('click', '.more', function() {
		let a = $(this).parent();
		a.css('pointer-events', 'none').find('i').addClass('fa-spin');
		$.get(home+a.parent().attr('class')+'.php?id='+a.parent().attr('data-id')+'&token='+a.attr('id'), function(e) {
			a.after(e);
			if (a.parent().attr('class') == 'comments') a.nextAll().find('p').each(clamp);
			else if (Cookies.get('playlist')) a.nextAll().children('b').each(checkplus);
			if (!mobile) $('html, body').animate({scrollTop: a.offset().top - 60});
			a.remove();
		});
	});
	$(document).on('click', '.replies', function() {
		if (!$(this).parent().next().length) {
			let a = $(this);
			a.css('pointer-events', 'none').addClass('on').children('i').attr('class', 'fas fa-spinner fa-spin');
			$.get(home+'replies.php?id='+a.parent().parent().attr('id'), function(e) {
				a.css('pointer-events', 'auto').children('i').attr('class', 'fas fa-comment');
				a.parent().after('<dt>'+e+'</dt>');
				a.parent().next().find('p').each(clamp);
				if (!mobile) $('html, body').animate({scrollTop: a.offset().top - 10});
			});
		}
		else $(this).toggleClass('on').children('i').toggleClass('far fas').parent().parent().next().slideToggle();
	});
	$(document).on('click', 'dt b', function() {
		let a = $(this);
		a.css('pointer-events', 'none').children('i').addClass('fa-spin');
		$.get(home+'replies.php?id='+a.parent().parent().attr('id')+'&token='+a.attr('id'), function(e) {
			a.after(e);
			a.nextAll().find('p').each(clamp);
			if (!mobile) $('html, body').animate({scrollTop: a.offset().top - 70});
			a.remove();
		});
	});
	$(document).on('click', '.comments img', function() {
		let i = $(this);
		i.addClass('rotate');
		$('kbd img').attr('src', i.attr('src').replace('s40-c', 's0')).on('load', function() {
			i.removeClass('rotate');
			$('body').addClass('off').children('header, nav, main, footer').addClass('hide');
			$('kbd, kbd img').show();
		});
	});
	$(document).on('click', 'legend', function() {
		$(this).prev().toggleClass('clamp');
		$('span i', this).toggleClass('fa-chevron-circle-down fa-chevron-circle-up');
	});
	$(document).on('click', '.loop', loop);
	$(document).on('click', '.forward', forward);
	$(document).on('click', '.backward', backward);
	$(document).on('click', '.shuffle', shuffle);
	$(document).on('click', 'figcaption', playpause);
	$(document).on('click', 'time', function() {
		let e = $(this).attr('data-elapsed');
		$(this).text($(this).text() == e ? $(this).attr('datetime') : e);
	});
	$(document).on('click', 'a[href]:not([rel="external"])', function(e) {
		e.preventDefault();
		navigator.vibrate(50);
		ajaxify($(this).attr('href'));
	});
	$(window).on('popstate', function() {
		ajaxify(location.href, true);
	});
	$(window).on('beforeunload', durations);
	$(window).on('keydown', function(e) {
		if ($(e.target).is('INPUT')) return;
		if (player) {
			if (e.which == 32) return false;
			if (e.which == 37) player.seekTo(player.getCurrentTime() - 5);
			if (e.which == 39) player.seekTo(player.getCurrentTime() + 5);
			if (e.which == 109) player.setVolume(player.getVolume() - 5);
			if (e.which == 107) player.setVolume(player.getVolume() + 5);
		}
	});
	$(window).on('keyup', function(e) {
		if ($(e.target).is('INPUT')) return;
		if (e.which == 13) random();
		if (e.which == 27 && $('main').hasClass('hide')) defrost();
		if (e.which == 78 && $('[rel="next"]').length) ajaxify($('[rel="next"]').attr('href'));
		if (e.which == 80 && $('[rel="prev"]').length) ajaxify($('[rel="prev"]').attr('href'));
		if (player) {
			if (e.which == 32) playpause();
			if (e.which == 70) fullscreen();
			if (e.which == 77) player.isMuted() ? player.unMute() : player.mute();
			if ($('.playlist').length) {
				if (e.which == 76) loop();
				if (e.which == 83) shuffle();
				if (e.which == 78) forward();
				if (e.which == 80) backward();
			}
			if (e.which == 48 || e.which == 96) player.seekTo(0);
			if (e.which == 49 || e.which == 97) player.seekTo(.1 * player.getDuration());
			if (e.which == 50 || e.which == 98) player.seekTo(.2 * player.getDuration());
			if (e.which == 51 || e.which == 99) player.seekTo(.3 * player.getDuration());
			if (e.which == 52 || e.which == 100) player.seekTo(.4 * player.getDuration());
			if (e.which == 53 || e.which == 101) player.seekTo(.5 * player.getDuration());
			if (e.which == 54 || e.which == 102) player.seekTo(.6 * player.getDuration());
			if (e.which == 55 || e.which == 103) player.seekTo(.7 * player.getDuration());
			if (e.which == 56 || e.which == 104) player.seekTo(.8 * player.getDuration());
			if (e.which == 57 || e.which == 105) player.seekTo(.9 * player.getDuration());
			if (e.which == 110 || e.which == 223) player.seekTo(player.getDuration());
		}
	});
	function checkplus() {
		if (RegExp($(this).attr('id')).test(localStorage.getItem('playlist'))) $('i', this).removeClass('fa-plus-circle').addClass('fa-check-circle');
	}
	function clamp() {
		if ($(this).height() > 100) $(this).addClass('clamp').after('<legend><span><i class="fas fa-chevron-circle-down"></i></span></legend>');
	}
	function defrost() {
		$('kbd, kbd img, kbd ul').hide();
		$('body').removeClass('off').children('header, nav, main, footer').removeClass('hide');
	}
	function random() {
		ajaxify($('.fa-random').parent().attr('href'));
	}
	function playpause() {
		player.getPlayerState() == 1 ? player.pauseVideo() : player.playVideo();
	}
	function fullscreen() {
		if (document.fullscreenElement) document.exitFullscreen();
		else {
			document.querySelector('figure').requestFullscreen({navigationUI: 'hide'});
			if ($('main').hasClass('hide')) defrost();
		}
	}
	function forward() {
		let a = $('#'+player.getVideoData().video_id).parent().next().children('b').attr('id');
		if (a) player.loadVideoById(a);
		else {
			if ($('.loop').hasClass('on') && $('article').length) player.loadVideoById($('article').first().children('b').attr('id'));
			else if (Cookies.get('infinite_playback')) random();
			else iconize('play');
		}
	}
	function backward() {
		let a = $('#'+player.getVideoData().video_id).parent().prev().children('b');
		if (a.length) player.loadVideoById(a.attr('id'));
	}
	function loop() {
		if ($('.loop').hasClass('on')) {
			if ($('.fa-undo-alt').length) $('.loop').children('i').toggleClass('fa-undo-alt fa-history');
			else $('.loop').removeClass('on').children('i').toggleClass('fa-history fa-undo-alt');
		}
		else $('.loop').addClass('on');
	}
	function shuffle() {
		$('.shuffle').toggleClass('on');
		$('.shuffle').hasClass('on') ? Cookies.set('shuffle', 1, {expires: 365, path: folder}) : Cookies.remove('shuffle', {path: folder});
		listize();
	}
	function listize() {
		$('article').remove();
		let a = localStorage.getItem('playlist').replace(/ဇ፨ჲ$/, '').split('ဇ፨ჲ').reverse();
		if ($('.shuffle').hasClass('on')) {
			let m = a.length, t, i;
			while (m) {
				i = Math.floor(Math.random() * m--);
				t = a[m];
				a[m] = a[i];
				a[i] = t;
			}
		}
		a.map(function(e) {
			let v = e.split('ჲ፨ဇ'), b = v[0];
			$('.playlist').prepend('<article><a href="?v='+b+'" rel="external"><small><i class="fas fa-arrows-alt fa-2x"></i></small><span>'+v[1]+'</span><img src="https://i.ytimg.com/vi/'+b+'/mqdefault.jpg"/></a><b id="'+b+'"><i class="fas fa-check-circle fa-2x"></i></b></article>');
		});
		Cookies.set('random', $('article').eq(Math.floor(Math.random() * ($('article').length - 1))).children('b').attr('id'), {expires: 365, path: folder});
	}
	function iconize(i) {
		$('.fa-arrows-alt').removeClass('fa-play-circle fa-pause-circle');
		$('#'+player.getVideoData().video_id).prev().find('.fa-arrows-alt').removeClass('fa-'+(i == 'play' ? 'pause' : 'play')+'-circle').addClass('fa-'+i+'-circle');
	}
	function durations() {
		if ($('h5').length && $.inArray(player.getPlayerState(), [1, 2, 3])) localStorage.setItem($('h5 b').attr('id'), ~~(player.getCurrentTime()));
	}
	function ajaxify(u, s) {
		durations();
		$('main').addClass('on').load(u+' #content', function() {
			let p = $('#content').attr('data-page'), t = $('#content').attr('data-title');
			if (!s) {
				history.pushState({}, '', p);
				$('html, body').animate({scrollTop: 0});
			}
			document.title = t;
			injection();
			$('main, header .on, .menu .on').removeClass('on');
			$('nav:visible').slideUp();
			$('[type="hidden"]').remove();
			$('.menu a[href="./'+p.replace(/&.*|.*\//g, '')+'"]').addClass('on');
			if ($('.menu a').hasClass('on')) $('[for="q"]').before('<input type="hidden" name="c" value="'+p.replace(/&.*|.*c=/g, '')+'"/>');
			if (/\?u=/.test(p)) $('[for="q"]').before('<input type="hidden" name="u" value="'+p.replace(/&.*|.*u=/g, '')+'"/>');
			(adsbygoogle = window.adsbygoogle || []).push({});
			ga('send', 'pageview', {'page': p, 'title': t});
			$('.counter').attr('src', 'https://c.statcounter.com/t.php?sc_project=5408945&security=f75ba4c3&invisible=1&camefrom='+encodeURIComponent($('.counter').attr('alt'))+'&u='+encodeURIComponent(location.href)+'&t='+encodeURIComponent(t)+'&resolution='+screen.width+'&h='+screen.height);
		});
	}
	function injection() {
		if ($('h5').length || $('.playlist').length) {
			YTdeferred.done(function(YT) {
				player = new YT.Player('player', {
					host: 'https://www.youtube'+(Cookies.get('save_history') ? '.com' : '-nocookie.com'),
					playerVars: {
						'hl': Cookies.get('language'),
						'iv_load_policy': 3,
						'modestbranding': 1
					},
					events: {
						'onReady': function() {
							if ($('.playlist').length) listize();
							player.cueVideoById({videoId: ($('h5').length ? $('h5 b').attr('id') : $('article').first().children('b').attr('id')), startSeconds: ($('h5').length && localStorage.getItem(v) ? localStorage.getItem(v) : 0)});
							if (Cookies.get('auto_start')) player.playVideo();
						},
						'onStateChange': function(e) {
							if ($('.playlist').length) {
								if (e.data == 0) {
									if ($('.fa-history').length) player.playVideo();
									else forward();
								}
								if (e.data == 1) iconize('pause');
								if (e.data == 2 || e.data == 5) iconize('play');
							}
							else if (e.data == 0) {
								if (localStorage.getItem($('h5 b').attr('id'))) localStorage.removeItem($('h5 b').attr('id'));
								if (Cookies.get('infinite_playback')) random();
							}
						},
						'onError': function() {
							if ($('.playlist').length) {
								$('#'+player.getVideoData().video_id).parent().fadeOut();
								forward();
							}
							else $('figure').html('<video poster="https://i.ytimg.com/vi/'+$('h5 b').attr('id')+'/hqdefault.jpg" src="go.php?t=v&v='+$('h5 b').attr('id')+'" id="player" controls autoplay></video>');
						}
					}
				});
			});
		}
		if ($('h5').length) {
			Cookies.set('random', $('h5 b').attr('id'), {expires: 365, path: folder});
			if (Cookies.get('playlist')) $('h5 b').each(checkplus);
			if ($('.info p').length) $('.info p').each(clamp);
			if (mobile) $('figcaption').remove();
		}
		if ($('.playlist').length) {
			if ($('aside').length) clear = $('aside').html();
			if (Cookies.get('shuffle')) $('.shuffle').addClass('on');
			$('.playlist').sortable({
				handle: 'small',
				items: 'article',
				update: function() {
					let list = '';
					$('article').each(function(i, e) {
						list += $('b', e).attr('id')+'ჲ፨ဇ'+$('span', e).text()+'ဇ፨ჲ';
					});
					localStorage.setItem('playlist', list);
					if ($('.shuffle').hasClass('on')) {
						$('.shuffle').removeClass('on');
						Cookies.remove('shuffle', {path: folder});
					}
				}
			});
			$('.playlist').disableSelection();
		}
		if ($('article').length) {
			Cookies.set('random', $('article').eq(Math.floor(Math.random() * ($('article').length - 1))).children('b').attr('id'), {expires: 365, path: folder});
			if (Cookies.get('playlist')) $('article b').each(checkplus);
		}
	}
});
