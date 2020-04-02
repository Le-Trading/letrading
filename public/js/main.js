$(document).ready(function() {

  //récupération lien actif navbar
  var url = window.location;
  $('ul.navbar-nav a').filter(function() {
    return this.href == url;
  }).addClass('active');

  //animation compteur homepage
  $('.count').each(function () {
    $(this).prop('Counter',0).animate({
      Counter: $(this).text()
    }, {
      duration: 3000,
      easing: 'swing',
      step: function (now) {
        $(this).text(Math.ceil(now));
      }
    });
  });

  //override play button video
  var compteur = 0;
  Array.prototype.forEach.call(document.getElementsByClassName('video-wrapper'), function(element) {
    var videoPlayButton, videoWrapper =
        element,
        video = document.getElementsByTagName('video')[compteur],
        videoMethods = {
          renderVideoPlayButton: function() {
            if (videoWrapper.contains(video)) {
              this.formatVideoPlayButton()
              video.classList.add('has-media-controls-hidden')
              videoPlayButton = document.getElementsByClassName('video-overlay-play-button')[compteur]
              videoPlayButton.addEventListener('click', this.hideVideoPlayButton)
            }
          },
          formatVideoPlayButton: function() {
            videoWrapper.insertAdjacentHTML('beforeend', '\
                <svg class="video-overlay-play-button" viewBox="0 0 200 200" alt="Play video">\\\n' +
                '                    <circle cx="100" cy="100" r="90" fill="none" stroke-width="15" stroke="#0984e3"/>\\\n' +
                '                    <polygon points="70, 55 70, 145 145, 100" stroke="#0984e3" stroke-width="10px" fill="transparent"/>\\\n' +
                '                </svg>\
                            ')
          },
          hideVideoPlayButton: function() {
            video.play()
            videoPlayButton.classList.add('is-hidden')
            video.classList.remove('has-media-controls-hidden')
            video.setAttribute('controls', 'controls')
          }
        }
    videoMethods.renderVideoPlayButton()
    compteur++
  });

  //apparition on scroll
  $(window).scroll(function(){
    var scrolledFromTop = $(window).scrollTop() + $(window).height();
    $(".appear").each(function(){
      var distanceFromTop = $(this).offset().top;
      if(scrolledFromTop >= distanceFromTop+100){
        var delaiAnim = $(this).data("delai");
        $(this).delay(delaiAnim).animate({
          top:0,
          opacity:1
        });
      }
    });
  });
});
