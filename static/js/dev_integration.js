$(document).ready(function() {
  // ajax function to serve the followed content
  // add condition, for example "if we are in home" ecc.
  if ($('body').hasClass('js-home-follower')) {
    $.ajax({
      url: p4_vars.ajaxurl,
      type: 'post',
      data: {
        action: 'topicsFollowing',
      },
      success: function(data) {
        // This outputs the result of the ajax request
        console.log(data);
      },
      error: function(errorThrown) {
        //alert(errorThrown);
        console.log(errorThrown);
      },
    });
    $.ajax({
      url: p4_vars.ajaxurl,
      type: 'post',
      data: {
        action: 'projectsFollowing',
      },
      success: function(data) {
        // This outputs the result of the ajax request
        console.log(data);
      },
      error: function(errorThrown) {
        //alert(errorThrown);
        console.log(errorThrown);
      },
    });
  }

  // ajax function to serve the followed content
  // add condition, for example "if we are in home" ecc.
  if ($('body').hasClass('js-latest-earth')) {
    $.ajax({
      url: p4_vars.ajaxurl,
      type: 'post',
      data: {
        action: 'topicsFollowing',
      },
      success: function(data) {
        // This outputs the result of the ajax request
        console.log(data);
      },
      error: function(errorThrown) {
        //alert(errorThrown);
        console.log(errorThrown);
      },
    });
  }

  // ajax function to serve the followed content
  // add condition, for example "if we are in home" ecc.
  if ($('body').hasClass('js-make-change')) {
    $.ajax({
      url: p4_vars.ajaxurl,
      type: 'post',
      data: {
        action: 'topicsFollowing',
        star: 'petition',
      },
      success: function(data) {
        // This outputs the result of the ajax request
        console.log(data);
      },
      error: function(errorThrown) {
        //alert(errorThrown);
        console.log(errorThrown);
      },
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
