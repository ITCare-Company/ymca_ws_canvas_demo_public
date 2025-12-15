/**
 * Vimeo Player
 */
(function ($, Drupal) {

  var iframe = $('#hero_banner-video--vimeo-player')[0];
  var player = new Vimeo.Player(iframe);
  var audioControls = document.getElementById('hero_banner--audio-controls');

  // Pause
  $('#hero_banner--btn-pause').bind('click', function() {
    player.pause().catch(function(error) {
      console.error('error playing the video:', error.name);
    });
    audioControls.classList.remove("is-playing");
  });

  // Play
  $('#hero_banner--btn-play').bind('click', function() {
    player.play().catch(function(error) {
      console.error('error playing the video:', error.name);
    });
    audioControls.classList.add("is-playing");
  });

  // Unmute
  $('#hero_banner--btn-unmute').bind('click', function() {
    player.setVolume(1);
    audioControls.classList.remove("is-muted");
  });

  // Mute
  $('#hero_banner--btn-mute').bind('click', function() {
    player.setVolume(0);
    audioControls.classList.add("is-muted");
  });

})(jQuery, Drupal);
