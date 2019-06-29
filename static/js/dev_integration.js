$( document ).ready(
	function() {
		$( '#articles_list_load_more' ).on(
			'submit', function(ev) {
				ev.preventDefault();
				let query = $( ev.srcElement ).children().serializeArray().reduce(
					function(acc, el) {
						return Object.assign( acc, { [el.name]: el.value } );
					}, {}
				);
				let mi_id = $( '#main_issue_id_hidden' ).data();
				if (mi_id && mi_id.hasOwnProperty( 'mainIssueId' )) {
					query.miid = mi_id.mainIssueId;
				}
				$.ajax(
					{
						url: window.localizations.ajaxurl,
						type: 'POST',
						data: {
							action: 'gpea_blocks_articles_load_more',
							query: query,
						},
						dataType: 'html',
					}
				).done(
					function(response) {
						try {
							console.log( JSON.parse( response ).map( p => p.post_title ) );
						} catch (e) {
							console.log( response );
						}
					}
				).fail(
					function(jqXHR, textStatus, errorThrown) {
						console.log( errorThrown ); // eslint-disable-line no-console
					}
				);
				return false;
			}
		);
	}
);
