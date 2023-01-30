QTags.addButton( 'article_note', '插入註解', function( e, c, ed, default_value ) {
	var start = ed.canvas.selectionStart;
	var end = ed.canvas.selectionEnd;
	var selected_text = ed.canvas.value.substring( start, end );
    var return_text = '[note title="' + ( selected_text.length > 0 ? selected_text : '顯示文字') + '"]註解文字[/note]';
	QTags.insertContent( return_text );
} );