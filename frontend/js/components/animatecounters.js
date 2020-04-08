export default function () {
  // Get Countries List from <script> data block.
  let $counter = $(".counter");
  if (!$counter.length) return;
  //
  function commaSeparateNumber(val) {
    while (/(\d+)(\d{3})/.test(val.toString())) {
      val = val.toString().replace(/(\d+)(\d{3})/, "$1" + "," + "$2");
    }
    return val;
  }
  $(".counter").each(function () {
    var $this = $(this),
      countTo = $this.attr("data-count");
    $({
      countNum: $this.text(),
    }).animate(
      {
        countNum: countTo,
      },
      {
        duration: 2000,
        easing: "swing",
        step: function () {
          $this.text(commaSeparateNumber(Math.floor(this.countNum)));
        },
        complete: function () {
          $this.text(commaSeparateNumber(this.countNum));
        },
      }
    );
  });
}
