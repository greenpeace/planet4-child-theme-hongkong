const $ = jQuery;

export default function() {

	let space = '.section-get-involved-cards';

	$('.buttons .link', space).on('click', function() {
		let $this = $(this);
		let target = $(this).data('group');
		if($this.hasClass('link--actived')) {
			return;
		}
		$('.link--actived', space).removeClass('link--actived');
		$('.grid__outer2--actived', space).removeClass('grid__outer2--actived');
		$this.addClass('link--actived');
		$('.grid__outer2[data-group="' + target + '"]', space).addClass('grid__outer2--actived');
	});

}