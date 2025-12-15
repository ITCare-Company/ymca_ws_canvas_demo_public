/**
 * Stats counter.
 */
(function ($, Drupal) {

  let $countElements = $('.js-count');
  // Animate the statistic up with commas.
  function animateNumber() {
    $countElements.each(function () {
      let $this = $(this),
        countTo = $this.attr("data-count"),
        countDuration = $this.attr("data-duration");
      $({countNum: $this.text()}).animate(
        {
          countNum: countTo
        },
        {
          duration: countDuration ? parseInt(countDuration) : 3000,
          easing: "linear",
          step: function () {
            // Count up with commas
            $this.text(Math.floor(this.countNum).toLocaleString("en"));
          },
          complete: function () {
            // Add comma after done counting
            $this.text(this.countNum.toLocaleString("en"));
          }
        }
      );
    });
  }

  // Set the counter back to 0.
  function resetNumber() {
    $countElements.each(function () {
      $(this).text('0');
    });
  }

  let controller = new ScrollMagic.Controller();
  let $lbStatsBlock = $(".lb-stats-block");
  // When scrolling in & out of view.
  new ScrollMagic.Scene({
    triggerElement: ".lb-stats-block"
  })
  .on("enter", function () {
    $lbStatsBlock.addClass("lb-stats-block-animated");
    animateNumber();
  })
  .on("leave", function () {
    $lbStatsBlock.removeClass("lb-stats-block-animated");
    resetNumber();
  })
  .addTo(controller);

})(jQuery, Drupal);
