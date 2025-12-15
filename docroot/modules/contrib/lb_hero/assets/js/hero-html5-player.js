/**
 * HTML5 Media Video Player
 */
(function () {

  let audioControls = document.getElementById('hero_banner--audio-controls');
  let video = document.getElementById("html5_video");
  video.currentTime = 0;

  // Unmute the video.
  document.getElementById("hero_banner--btn-unmute").addEventListener('click', function () {
    video.muted = false;
    audioControls.classList.remove("is-muted");
  });

  // Mute the video.
  document.getElementById("hero_banner--btn-mute").addEventListener('click', function () {
    video.muted = true;
    audioControls.classList.add("is-muted");
  });

  // Play the video.
  document.getElementById("hero_banner--btn-play").addEventListener('click', function () {
    video.play();
    audioControls.classList.add("is-playing");
  });

  // Pause the video.
  document.getElementById("hero_banner--btn-pause").addEventListener('click', function () {
    video.pause();
    audioControls.classList.remove("is-playing");
  });

})();
