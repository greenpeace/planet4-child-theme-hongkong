!function(e){var n=!1;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var o=window.Cookies,t=window.Cookies=e();t.noConflict=function(){return window.Cookies=o,t}}}(function(){function g(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}return function e(l){function C(e,n,o){var t;if("undefined"!=typeof document){if(1<arguments.length){if("number"==typeof(o=g({path:"/"},C.defaults,o)).expires){var r=new Date;r.setMilliseconds(r.getMilliseconds()+864e5*o.expires),o.expires=r}o.expires=o.expires?o.expires.toUTCString():"";try{t=JSON.stringify(n),/^[\{\[]/.test(t)&&(n=t)}catch(e){}n=l.write?l.write(n,e):encodeURIComponent(String(n)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),e=(e=(e=encodeURIComponent(String(e))).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent)).replace(/[\(\)]/g,escape);var i="";for(var c in o)o[c]&&(i+="; "+c,!0!==o[c]&&(i+="="+o[c]));return document.cookie=e+"="+n+i}e||(t={});for(var a=document.cookie?document.cookie.split("; "):[],s=/(%[0-9A-Z]{2})+/g,f=0;f<a.length;f++){var p=a[f].split("="),d=p.slice(1).join("=");this.json||'"'!==d.charAt(0)||(d=d.slice(1,-1));try{var u=p[0].replace(s,decodeURIComponent);if(d=l.read?l.read(d,u):l(d,u)||d.replace(s,decodeURIComponent),this.json)try{d=JSON.parse(d)}catch(e){}if(e===u){t=d;break}e||(t[u]=d)}catch(e){}}return t}}return(C.set=C).get=function(e){return C.call(C,e)},C.getJSON=function(){return C.apply({json:!0},[].slice.call(arguments))},C.defaults={},C.remove=function(e,n){C(e,"",g(n,{expires:-1}))},C.withConverter=e,C}(function(){})});



$(document).ready(function(){

    // check project following and update cookies
    if ( $('body').hasClass('page-template-project') ) {
        var gpea_projects_followed = Cookies.get('gpea_projects') ? Cookies.getJSON('gpea_projects') : new Array();
        var current_project_id = $('.js-project-follow').data('project');
        // if already followging
        if ( gpea_projects_followed.includes(current_project_id) ) {
            $('.js-project-follow').hide();
            $('.js-project-unfollow').fadeIn();
        }
        
        $('.js-project-follow').on('click', function(e) {
            e.preventDefault();
            gpea_projects_followed.push(current_project_id);
            Cookies.set('gpea_projects',gpea_projects_followed);
            $('.js-project-follow').hide();
            $('.js-project-unfollow').fadeIn();
        });

        $('.js-project-unfollow').on('click', function(e) {
            e.preventDefault();
            var index = gpea_projects_followed.indexOf(current_project_id);
                if (index > -1) {
                    gpea_projects_followed.splice(index, 1);
            }
            Cookies.set('gpea_projects',gpea_projects_followed);
            $('.js-project-follow').fadeIn();
            $('.js-project-unfollow').hide();
        });

    }

    // set cookies on topic click
    $('#topic-submit-button').on('click', function(e){
        e.preventDefault();
        var cookie_name_issues = 'gpea_issues';        
        var gpea_cookie_issues = Cookies.get(cookie_name_issues) ? Cookies.getJSON(cookie_name_issues) : new Array();

        $('.topic-button[data-taxonomy="category"]').each(function(index) {
            var gpea_selected_topic_id = $(this).data('engaging');
            if ($(this).hasClass('active')) {
                // if ( gpea_cookie_issues.includes(gpea_selected_topic_id) ) return;
                // else gpea_cookie_issues.push(gpea_selected_topic_id);
                if ( ! gpea_cookie_issues.includes(gpea_selected_topic_id) ) gpea_cookie_issues.push(gpea_selected_topic_id);
                $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('Y');
            } else {
                var index = gpea_cookie_issues.indexOf(gpea_selected_topic_id);
                if (index > -1) {
                    gpea_cookie_issues.splice(index, 1);
                }
                $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('');
            }
        });
        Cookies.set(cookie_name_issues,gpea_cookie_issues);

        var cookie_name_topics = 'gpea_topics';
        var gpea_cookie_topics = Cookies.get(cookie_name_topics) ? Cookies.getJSON(cookie_name_topics) : new Array();

        $('.topic-button[data-taxonomy="post_tag"]').each(function(index) {
            var gpea_selected_topic_id = $(this).data('engaging');
            if ($(this).hasClass('active')) {                
                // if ( gpea_cookie_topics.includes(gpea_selected_topic_id) ) return;
                // else gpea_cookie_topics.push(gpea_selected_topic_id);
                if ( ! gpea_cookie_topics.includes(gpea_selected_topic_id) ) gpea_cookie_topics.push(gpea_selected_topic_id);
                $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('Y');
            } else {
                var index = gpea_cookie_topics.indexOf(gpea_selected_topic_id);
                if (index > -1) {
                    gpea_cookie_topics.splice(index, 1);
                }
                $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('');
            }
        });
        Cookies.set(cookie_name_topics,gpea_cookie_topics);
        
        $('.tag_cloud_form').fadeIn();
        $('.tag_cloud_form .signatures').addClass('is-open');

    });

    if ( $('.js-tag-cloud').length ) {
        var cookie_name_issues = 'gpea_issues';
        var gpea_cookie_issues = Cookies.get(cookie_name_issues) ? Cookies.getJSON(cookie_name_issues) : new Array();
        $.each( gpea_cookie_issues, function( key, value ) {
            $(`.topic-button[data-engaging='${value}']`).addClass('active');
            $(`.topic-button[data-engaging='${value}']`).show();
        });

        var cookie_name_topics = 'gpea_topics';
        var gpea_cookie_topics = Cookies.get(cookie_name_topics) ? Cookies.getJSON(cookie_name_topics) : new Array();
        $.each( gpea_cookie_topics, function( key, value ) {
            $(`.topic-button[data-engaging='${value}']`).addClass('active');
        });
          
    }

    // ajax function to serve the followed content
    // add condition, for example "if we are in home" ecc.
    if ( $('body').hasClass('js-home-follower') ) {
        $.ajax({
            url: p4_vars.ajaxurl,
            type : 'post',
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
        $.ajax({
            url: p4_vars.ajaxurl,
            type : 'post',
            data: {
                'action':'projectsFollowing',
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
    }

    // ajax function to serve the followed content
    // add condition, for example "if we are in home" ecc.
    if ( $('body').hasClass('js-latest-earth') ) {
        $.ajax({
            url: p4_vars.ajaxurl,
            type : 'post',
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
    }

    // ajax function to serve the followed content
    // add condition, for example "if we are in home" ecc.
    if ( $('body').hasClass('js-make-change') ) {
        $.ajax({
            url: p4_vars.ajaxurl,
            type : 'post',
            data: {
                'action':'topicsFollowing',
                'star':'petition'
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
        // $.ajax({
        //     url: p4_vars.ajaxurl,
        //     data: {
        //         'action':'tipsFollowing',
        //     },
        //     success:function(data) {
        //         // This outputs the result of the ajax request
        //         console.log(data);
        //     },
        //     error: function(errorThrown){
        //         //alert(errorThrown);
        //         console.log(errorThrown);
        //     }
        // });
    }

});