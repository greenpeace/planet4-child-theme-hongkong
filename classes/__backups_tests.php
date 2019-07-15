// add_action( 'wp_footer', function () {
//     if ( ! current_user_can( 'manage_options' ) ) {
//         return;
// 	}
// 	wp_cache_add( 'sticaz', 'sti gran ca', 'test:sticaztest', 600 );
//     $GLOBALS['wp_object_cache']->stats();
// } );

/*

// to hyde blocks from admin

add_filter(
	'shortcode_ui_shortcode_args_shortcake_articles',
	function ( $args ) {
		$args['post_type'] = [ '' ];
		return $args;
	},
	1000,
	1
);

with filter:
shortcode_ui_shortcode_args_shortcake_{BLOCK_NAME}

You can even load the relative Block class file and do it like shortcode_ui_shortcode_args_shortcake_' . Articles_Controller::BLOCK_NAME to be sure that if we change block name it will still work



*/