const $ = jQuery;
import "jquery-appear-original";

export default function () {
  let $counter = $(".counter");
  if (!$counter.length) return;
  animatecounters();
  //
  $counter.appear();
  $(document.body).on("appear", ".counter", function () {
    if (!$(this).hasClass("appear")) {
      animatecounters();
      $(this).addClass("appear");
    }
  });
  //
  function animatecounters() {
    function numberWithCommas(value) {
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    $(".counter:not(.appear)").each(function () {
      var $this = $(this),
        countTo = $this.attr("data-count");
      $({
        countNum: 0,
      }).animate(
        {
          countNum: countTo,
        },
        {
          duration: 2000,
          easing: "swing",
          step: function () {
            $this.text(numberWithCommas(Math.ceil(this.countNum)));
          },
          complete: function () {
            $this.text(numberWithCommas(countTo));
          },
        }
      );
    });
  }
}
