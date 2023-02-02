const $ = jQuery;

export default function() {

	let space = 'article-note';

	$('.' + space + '__title').on('click', function() {
		$(this).closest('.' + space).toggleClass(space + '__actived');
	});

}