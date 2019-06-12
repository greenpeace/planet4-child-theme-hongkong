
$(document).ready(function(){

    // set cookies on topic click
    $('.topic-button').on('click', function(e){
        e.preventDefault();
        // var gpea_selected_topic_slug = $(this).data('slug');
        var gpea_selected_topic_id = $(this).data('id');
        var gpea_selected_topic_taxonomy = $(this).data('taxonomy');

        if ('category' == gpea_selected_topic_taxonomy) {
            var cookie_name = 'gpea_issues';
        } else if ('post_tag' == gpea_selected_topic_taxonomy) {
            var cookie_name = 'gpea_topics';
        } else return;

        var gpea_topics = Cookies.get(cookie_name) ? Cookies.getJSON(cookie_name) : new Array();
        if ( gpea_topics.includes(gpea_selected_topic_id) ) return;
        else gpea_topics.push(gpea_selected_topic_id);
        Cookies.set(cookie_name,gpea_topics);
    });

    // ajax function to serve the followed content
    // add condition, for example "if we are in home" ecc.
    $.ajax({
        url: p4_vars.ajaxurl,
        data: {
            'action':'topicsFollowing',
        },
        success:function(data) {
            // This outputs the result of the ajax request
            console.log(data);
        },
        error: function(errorThrown){
            //alert(errorThrown);
            console.log(errorThrown);
        }
    });

});