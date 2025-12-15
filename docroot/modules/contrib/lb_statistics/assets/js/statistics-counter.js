/**
 * Stats counter.
 */
(function ($, Drupal) {

  // Sets height of the div based on its width.
  function setStatItemHeight() {
    $('.statistic-item .outer-circle, .statistic-item .inner').each(function (){
      $(this).css({height: $(this).outerWidth()});
    });
  }

  // Animate the statistic up with commas.
  function animateNumber() {
    $(".js-count").each(function () {
      var $this = $(this),
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
    $(".js-count").each(function () {
      $(this).text('0');
    });
  }

  var controller = new ScrollMagic.Controller();

  // When scrolling in & out of view.
  new ScrollMagic.Scene({
    triggerElement: ".lb-stats-block"
  })
  .on("enter", function () {
    $(".lb-stats-block").addClass("lb-stats-block-animated");
    animateNumber();
  })
  .on("leave", function () {
    $(".lb-stats-block").removeClass("lb-stats-block-animated");
    resetNumber();
  })
  .addTo(controller);

  $(window).resize(function (){
    setStatItemHeight();
  }).resize();

})(jQuery, Drupal);
