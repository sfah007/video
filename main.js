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
