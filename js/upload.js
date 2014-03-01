/********** CAPTURE  **********/
var pic;
$(function() {

  $("input").change(function(ev) {

    var fd = new FormData(); // I wrote about it: https://hacks.mozilla.org/2011/01/how-to-develop-a-html5-image-uploader/
		fd.append("image", file); // Append the file
								fd.append("key", "6528448c258cff474ca9701c5bab6927"); // Get your own key http://api.imgur.com/
								var xhr = new XMLHttpRequest(); // Create the XHR (Cross-Domain XHR FTW!!!) Thank you sooooo much imgur.com
								xhr.open("POST", "http://api.imgur.com/2/upload.json"); // Boooom!
								xhr.onload = function() {
									pic=JSON.parse(xhr.responseText).upload.links.original;
									document.querySelector("#previewImage").src = pic; 
								}
			xhr.send(fd);
		});

  $(".latest")
  /*
    .click(function() {
      if ($("img",this).attr("src")) $(".menu", this).show();
    }, function() {
      $(".menu", this).hide();
    })
  */
  .click(function() {
      if ($("img",this).attr("src")) addPhoto($("img", this).data("name"));
   });
});

/********** ALBUM **********/

$(function() {

  $(".empty").click(function() {
    $(".photo").fly(null, function() { $(".photos").empty(); });
    for (var key in localStorage) delete localStorage[key];
  });
  $(".photo").live("mouseover", function(ev) {
      $(".menu", this).show();
  });
  $(".photo").live("mouseout", function(ev) {
      $(".menu", this).hide();
  });
  $(".open").live("click", function() {
    window.open($(this).closest(".photo").find("img").attr("src"), "_blank");
  });
  $(".delete").live("click", function() {
    var name = $(this).closest(".photo").data("name");
    $(this).closest(".photo").fly().remove();
    removePhoto(name);
  });

  var album = localStorage.album ? JSON.parse(localStorage.album) : [];
  $(".message").html(album.length);
  _(album).each(function(name) { renderPhoto(name, localStorage[name], true); });

});

function addPhoto() {
  var name = $(".latest img").data("name");
  if (localStorage[name]) return; // TODO error message
  var dataURI = $(".latest img").attr("src");
  var photo = renderPhoto(name, dataURI, false);
  $(".latest").fly($(".photo", photo), function() {
    $(photo).appear();
  });
  $(".latest img").fadeOut();
  $(".latest .menu").hide();

  $(".message").html("saving");
  var album = localStorage.album ? JSON.parse(localStorage.album) : [];
  localStorage[name] = dataURI;
  $(".message").html("adding photo");
  album.push(name);
  $(".message").html("storing photo");
  localStorage.album = JSON.stringify(album);
  $(".message").html("updated with "+album.length+" photos");
}

function removePhoto(name) {
  var album = JSON.parse(localStorage.album);
  var index = album.indexOf(name);
  album.remove(index);
  localStorage.album = JSON.stringify(album);
  delete localStorage[name];
  $(".photos img").eq(index).remove();
}

var photoTemplate;
function renderPhoto(name, dataURI, visible) {
  if (arguments.length<2) visible = true;
  if (!photoTemplate) photoTemplate = _($("#photoTemplate").html()).template();
  var $photoContainer = $("<span/>")
    .css("visibility", visible ? "visible" : "hidden")
    .html(photoTemplate({name: name, url: dataURI}))
    .appendTo(".photos");
  $photoContainer.find(".photo").data("name", name)
  return $photoContainer;
}

/********** GENERIC FUNCTIONALITY **********/

$.fn.replace= function() {
  var $el = $(this).fadeOut();
  $el.clone().insertBefore($el).fadeIn();
  $el.remove();
}

$.fn.fly = function(target, callback) {
  var callback = callback || function() {};
  var $target = $(target);
  target=$target.get(0);
  return $(this).each(function(i, el) {
    var $el = $(el);
    var $clone = $el
                 .clone()
                 .css({position: "absolute",
                       left: el.offsetLeft, top: el.offsetTop,
                       width: el.offsetWidth, height: el.offsetHeight,
                       padding: 0, margin: 0 })
                .appendTo("body");
    var targetStyle = target ?
      { width: target.offsetWidth, height: target.offsetHeight,
        top: target.offsetTop, left: target.offsetLeft } :
      { width: 0, height: 0, opacity: 0.4, top: 0, };

    var isLast = (i == $(this).length-1);
    $clone.animate(targetStyle, function() {
      $clone.remove();
      if (isLast) callback();
    });
  });
}

$.fn.disappear = function() { $(this).css("visibility", "hidden"); }
$.fn.appear = function() { $(this).css("visibility", "visible"); }

// http://ejohn.org/blog/javascript-array-remove/
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};