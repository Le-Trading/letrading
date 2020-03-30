$(document).ready(function() {

  //récupération lien actif navbar
  var url = window.location;
  $('ul.navbar-nav a').filter(function() {
    return this.href == url;
  }).addClass('active');

});
