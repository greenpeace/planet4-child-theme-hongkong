const $ = jQuery;

export default function() {

	let menu = '.header .menu--primary';
	let gt_lg = window.matchMedia('(min-width: 1280px)');

	$('.menu__item', menu).hover(function() {
		if(!gt_lg) { return; }
		$(this).removeClass('actived');
	}, function() {
		if(!gt_lg) { return; }
		$(this).removeClass('actived');
	}).on('click', function() {
		if(gt_lg) { return; }
		$(this).tohggleClass('actived');
	});

	$('.buttons .link', space).on('click', function() {
		let $this = $(this);
		let target = $(this).data('group');
		if($this.hasClass('link--actived')) {
			return;
		}
		$('.link--actived', space).removeClass('link--actived');
		$('.grid--actived', space).removeClass('grid--actived');
		$this.addClass('link--actived');
		$('.grid[data-group="' + target + '"]', space).addClass('grid--actived');
	});

}