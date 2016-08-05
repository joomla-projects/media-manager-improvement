!function() {
  // We are using jQuery only to add a click event to the buttons and the dom ready? We are doing it wrong!!
  jQuery(document).ready(function() {

    var image = document.getElementById('joomla-media-image-cropper');

    // TODO get any data-* values and build the options
    // eg:   if (typeof image.getAttribute("data-some-attribute") != "undefined") {
    //           option1 = image.getAttribute("data-some-attribute");
    //       }
    // This way no inline script will be injected in the page
    /**
     * Initialiaze Cropper
     */
    var cropper = new Cropper(image, {
      aspectRatio: 16 / 9,
      crop       : function (e) {

        var json = [{
          "x"     : e.detail.x,
          "y"     : e.detail.y,
          "height": e.detail.height,
          "width" : e.detail.width,
          "rotate": e.detail.rotate,
          "scaleX": e.detail.scaleX,
          "scaleY": e.detail.scaleY
        }];

        document.getElementById('imagecropper-jsondata').value = json;
      }
    });
    // We are using jQuery only to add a click event to the buttons and the dom ready? We are doing it wrong!!
    jQuery('.btn-toolbar button').click(function () {
      var action = jQuery(this).data('method');

      switch (action) {
        case 'zoom-in':
          cropper.zoom(0.1);
          break;

        case 'zoom-out':
          cropper.zoom(-0.1);
          break;

        case 'rotate':
          cropper.rotate(jQuery(this).data('option'));
          break;

        case 'scaleX':
          cropper.scaleX(-cropper.getData().scaleX || -1);
          break;

        case 'scaleY':
          cropper.scaleY(-cropper.getData().scaleY || -1);
          break;

          // No default
      }
    });
  });
}();