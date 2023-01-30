QTags.addButton( 'article_note', article_note_quicktag_locales.action_insert, function( e, c, editor, default_value ) {
	var start = editor.canvas.selectionStart;
	var end = editor.canvas.selectionEnd;
	var selected_text = editor.canvas.value.substring( start, end );
    var open_tag = '[note title="';
    var close_tag = '"]' + article_note_quicktag_locales.default_content + '[/note]';
    var return_text = open_tag + ( selected_text.length > 0 ? selected_text : article_note_quicktag_locales.default_title) + close_tag;
	QTags.insertContent( return_text );
} );