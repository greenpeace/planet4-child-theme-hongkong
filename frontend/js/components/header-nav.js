const $ = jQuery;

export default function() {

	let $menu = $('.header .menu--primary');
	let $header = $('.header');
	let gt_lg = function() {
		return window.matchMedia('(min-width: 1280px)').matches;
	};

	$('.menu__item a[href="#"]', $menu).attr('href', 'javascript:void(0);');

	$('.has-children .menu__inner', $menu).each(function() {
		let label_translate = '';
		let $label_attr = $('[data-label-fake]', this);
		if($label_attr.length > 0) {
			label_translate = $label_attr.data('label-fake');
		}
		$('.menu__inner2', this)[0].innerHTML += $('#template-fake-sub-menu').text().replace('__PLACEHOLDER__', label_translate);
	});

	$('.menu__item--has-children', $menu).hover(function() {
		if(!gt_lg()) { return; }
		$(this).addClass('actived').siblings().removeClass('actived').find('.menu-item.actived').removeClass('actived');
		let $first_children = $('.menu-item-has-children:has(.menu-item):eq(0)', this);
		if($first_children.length > 0) {
			$first_children.trigger('gpea-fake-hover');
		}
	}, function() {
		if(!gt_lg()) { return; }
		$(this).removeClass('actived');
		$('.menu-item.actived', this).removeClass('actived');
	})
	$('.menu__item--has-children .header__link', $menu).on('click', function(e) {
		if(gt_lg()) { return; }
		e.preventDefault();
		if($(e.target).is('.menu-item-has-children:has(.menu-item), .menu-item-has-children:has(.menu-item) *')) { return; }
		let $target = $(this).closest('.menu__item');
		$target.toggleClass('actived').siblings().removeClass('actived').find('.menu-item.actived').removeClass('actived');
		if(!$target.hasClass('actived')) {
			$('.menu-item.actived', $target).removeClass('actived');
		}
	});

	$('.menu__item .menu-item:not(.menu-item--fake)', $menu).hover(function() {
		if(!gt_lg()) { return; }
		$(this).addClass('actived').siblings().removeClass('actived');
		if(!$(this).is('.menu-item-has-children:has(.menu-item)')) {
			$(this).closest('.menu__container').find('.sub-menu--fake').closest('.menu-item').removeClass('actived');
			return;
		}
		$(this).closest('.menu__container').find('.sub-menu--fake').html($('.sub-menu', this).html()).closest('.menu-item').addClass('actived');
	}, function() {
		return;
	}).on('gpea-fake-hover', function() {
		if(!$(this).is('.menu-item-has-children:has(.menu-item)')) {
			return;
		}
		$(this).addClass('actived').siblings().removeClass('actived');
		$(this).closest('.menu__container').find('.sub-menu--fake').html($('.sub-menu', this).html()).closest('.menu-item').addClass('actived');
	});
	$('.menu__item .menu-item-has-children:has(.menu-item) > a', $menu).on('click', function(e) {
		if(gt_lg()) { return; }
		e.preventDefault();
		let $target = $(this).closest('.menu-item');
		$target.toggleClass('actived').siblings().removeClass('actived');
	});

	$('.header__link--mobile-menu', $header).on('click', function() {
		if(gt_lg()) { return; }
		$('.header__column--menu', $header).addClass('header__column--actived');
		$('body').addClass('has-open-header-nav');
		if($('.menu__item--has-children.actived').length == 0) {
			$('.menu__item--has-children:first-child').addClass('actived');
		}
	});
	$('.menu__close-button', $header).on('click', function() {
		if(gt_lg()) { return; }
		$('.menu__search-button').removeClass('hidden');
		$('.menu--search').removeClass('actived');
		$('.header__column--menu', $header).removeClass('header__column--actived');
		$('body').removeClass('has-open-header-nav');
	});

	$('.menu__search-button').on('click', function() {
		$(this).addClass('hidden');
		$('.menu--search').addClass('actived');
	});

	function calcMenuItemWidth() {
		if(!gt_lg()) {
			$('.menu__container', $menu).attr('style', '');
			return;
		}
		$('.menu__search-button').removeClass('hidden');
		$('.menu--search').removeClass('actived');
		$('.header__column--menu', $header).removeClass('header__column--actived');
		$('body').removeClass('has-open-header-nav');
		$('.menu__container', $menu).each(function() {
			let $item = $(this).closest('.menu__item');
			$(this).attr('style', '').css({
				left: $item.offset().left - 16,
			});
		});
	}

	calcMenuItemWidth();

	$(window).resize(function() {
		calcMenuItemWidth();
	});

}