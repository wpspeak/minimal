var container = document.querySelector('.content');
var msnry;
// initialize Masonry after all images have loaded
imagesLoaded( container, function() {
  msnry = new Masonry( container, {
  	itemSelector: '.entry',
  	gutter: 40
  });
});