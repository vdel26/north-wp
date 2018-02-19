jQuery(document).ready(function($){
	/*
	 * HOME
	 */
	/*var homeLoading = $('.home-loader');
	if(homeLoading.length > 0){
		if(typeof(Storage) !== "undefined") {
			if(!sessionStorage.northloaded) {
				setTimeout(function() {
					homeLoading.remove();
				}, 4000);
				sessionStorage.northloaded = 1;
			} else {
				homeLoading.remove();
			}

		} else {
			setTimeout(function() {
				homeLoading.remove();
			}, 4000);
		}
	}*/


	/*
	 * LOGO ANIMATION
	 */
	var logo = $('.semplice-navbar .logo, .back-button .ce-button');
    $(window).scroll(function(){
		if($(document).scrollTop() > 100){
			logo.addClass('go-up');
		} else {
			logo.removeClass('go-up');
		}
	})


	/*
	 * CUSTOM MENU CONTENT
	 */
	var container = $('<div class="random-menu-content"><div class="container"></div>').appendTo('#overlay-menu');
	var holder = $('<div id="random-content-holder"></div>').appendTo(container.children());
	var randomLinks;

	// rest api
	$.ajax( {
		url: 'http://thenorthstudio.com/wp-json/wp/v2/random-link',
		dataType: "json",
		success: function ( data ) {
			randomLinks = data;
		},
		cache: false
    } );

	$('.hamburger').click(function(){
		if(randomLinks && $(this).attr('data-status') != 'open'){
			holder.html(randomLinks[Math.floor(Math.random()*randomLinks.length)].content.rendered);
		}
	})


	/*
	 * FORMS
	 */
	if( $('.material-design').length > 0 ){
		$('.material-design .form-control').each(function(index,elem){
			var elem = $(elem);
			var label = elem.attr('placeholder');

			if( label ){
				elem.after('<label class="material-label">'+ label +'</label>');
				elem.removeAttr('placeholder');

				if(elem.val() != ''){
					elem.addClass('has-value');
				}
			}
		}).blur(function(){
			if( $(this).val() != '' ){
				$(this).addClass('has-value');
			}
		})
	}

	if( $('.logos-carousel').length > 0 ){
		var logosCarousels = $('.logos-carousel .content-wrapper');
		$(logosCarousels).on('beforeChange', function(event, slick, currentSlide, nextSlide){
			$(slick.$slides[currentSlide]).addClass('up');
		}).on('afterChange', function(event, slick, currentSlide){
			setTimeout(function() {
				$(slick.$slides[currentSlide-1]).removeClass('up');
			}, 2000);
		}).slick({
			//vertical: true,
			autoplay: true,
			autoplaySpeed:2000,
			arrows: false,
			draggable: false,
			swipe: false,
			touchMove: false,
			pauseOnHover:false,
			easing:'swing',
			speed: 800,
			fade: true
		}).slick("slickPause");

		var initialDelay = [0, 300, 120];

		logosCarousels.each(function(i, elem){
			setTimeout(function() {
				$(elem).slick("slickPlay");
			},initialDelay[i]);
		})
	}


	// portfolio items reveal
	// scroll animations
	/*var srw_reveal = {
		viewFactor : 0.4,
		duration   : 800,
		delay: 75,
		easing: 'ease-out',
		scale: 1,
		distance: '2rem',
		opacity:0
	}
	window.srw = new ScrollReveal(srw_reveal);
	srw.reveal('.masonry-item');*/


	var leftArrow = '<button type="button" class="slick-prev icom-arrow-left"></button>',
		rightArrow= '<button type="button" class="slick-next icom-arrow-right"></button>';
	if($('.north-slider').length > 0){
		$('.north-slider .content-wrapper').slick({
			autoplay: true,
			autoplaySpeed:6000,
			speed: 1200,
			infinite: true,
			centerMode: true,
			prevArrow: leftArrow,
			nextArrow: rightArrow,
			centerPadding: '10%',
			pauseOnHover: false,
			responsive: [
				{
					breakpoint: 599,
      				settings: "unslick"
				}
			]
		});
	}
	if($('.tcn-slider').length > 0){
		$('.tcn-slider .content-wrapper').slick({
			autoplay: true,
			autoplaySpeed:4000,
			speed: 1000,
			infinite: true,
			centerMode: true,
			swipeToSlide: true,
			prevArrow: leftArrow,
			nextArrow: rightArrow,
			pauseOnHover: false,
			slidesToShow: 4,
			slidesToScroll: 4,
			centerPadding: '10%',
			responsive: [
				{
					breakpoint: 1599,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				},{
					breakpoint: 1199,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2,
						centerPadding: '12%',
					}
				},{
					breakpoint: 699,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						centerPadding: '16%',
					}
				}
			]
		});
	}
})