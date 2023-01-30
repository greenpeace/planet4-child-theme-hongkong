( function() {
	tinymce.PluginManager.add( 'article-notes', function( editor ) {
		editor.addButton( 'article-notes', {
			title: article_note_editor_locales.action_insert,
			cmd: 'article-notes',
			text: article_note_editor_locales.action_insert,
		});
		editor.addCommand('article-notes', function() {
			var selected_text = editor.selection.getContent( {
				'format': 'html'
			} );
			var open_tag = '[note title="';
			var close_tag = '"]' + article_note_editor_locales.default_content + '[/note]';
			var return_text = open_tag + ( selected_text.length > 0 ? selected_text : article_note_editor_locales.default_title) + close_tag;
			editor.execCommand( 'mceReplaceContent', false, return_text );
			return;
		} );
	} );
} )();