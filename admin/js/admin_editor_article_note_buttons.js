( function() {
	tinymce.PluginManager.add( 'article-notes', function( editor, url ) {
        console.log( 1233, url );
		editor.addButton( 'article-notes', {
			title: '插入註解',
			cmd: 'article-notes',
			text: '插入註解',
		});
		editor.addCommand('article-notes', function() {
			var selected_text = editor.selection.getContent( {
				'format': 'html'
			} );
			var open_column = '[note title="';
			var close_column = '"]註解文字[/note]';
			var return_text = open_column + ( selected_text.length > 0 ? selected_text : '顯示文字') + close_column;
			editor.execCommand( 'mceReplaceContent', false, return_text );
			return;
		} );
	} );
} )();