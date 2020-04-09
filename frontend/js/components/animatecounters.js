export default function () {
  let $counter = $(".counter");
  if (!$counter.length) return;
  //
  function numberWithCommas(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
  $(".counter:not(.appear)").each(function () {
    var $this = $(this),
      countTo = $this.attr("data-count");
    console.log($this);
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
