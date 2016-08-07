!function() {
  "use strict";

  document.onreadystatechange = function () {
    if (document.readyState == "interactive") {
      var image = document.getElementById('joomla-media-image-cropper');      // The image node
      window.imageUrl = image.getAttribute("src");                            // The image Url
      window.postUrl = image.getAttribute("data-url");                        // The upload Url
      window.submitInput = document.querySelectorAll('input[type="submit"]'); // The hidden submit input
      window.cropperBoxDim = {};

      // TODO get any data-* values and build the options
      // eg:   if (typeof image.getAttribute("data-some-attribute") != "undefined") {
      //           option1 = image.getAttribute("data-some-attribute");
      //       }

      /**
       * Initialiaze Cropper
       */
      var cropper = new Cropper(image, {
        // aspectRatio: 16 / 9,
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
      console.log(cropper);
      // The upload logic copy paste from tinymce jdrag and drop
      var UploadFile = function (fd) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", postUrl, true);

        xhr.onload = function () {
          var resp = JSON.parse(xhr.responseText);

          if (xhr.status == 200) {
            if (resp.status == '0') {
              submitInput[0].click();
              // console.log('Upload success');
            }

            if (resp.status == '1') {
              submitInput[0].click();
              // console.log('Upload success');
            }
          } else {
            // console.log('No Upload');
          }
        };

        xhr.onerror = function () {
          // console.log('Upload Error');
        };
        xhr.send(fd);
      };

      // Upload cropped image to server
      var doTheUpload = function () {
        console.log(cropper.getCroppedCanvas());
        cropper.getCroppedCanvas().toBlob(function (blob) {
          var imgFileName = imageUrl.split('/').pop();
          var fd = new FormData();

          fd.append('files', blob, imgFileName);

          UploadFile(fd);
        });
      };

      var registerClick = function (element) {
        element.addEventListener('click', function (event) {

          var action = event.currentTarget.getAttribute("data-method");
          var option = event.currentTarget.getAttribute("data-option");
          var option2 = event.currentTarget.getAttribute("data-second-option");

          switch (action) {
            case 'zoom':
              cropper.zoom(option);
              break;

            case 'rotate':
              cropper.rotate(option);
              break;

            case 'scaleX':
              cropper.scaleX(-cropper.getData().scaleX || -1);
              break;

            case 'scaleY':
              cropper.scaleY(-cropper.getData().scaleY || -1);
              break;

            case 'move':
              cropper.move(option, option2);
              break;

            default:
            case 'crop':
              console.log(cropper.getCroppedCanvas());
              //cropper.getCroppedCanvas();
              doTheUpload();
              break;
          }
        });
      };

      var buttons = document.querySelectorAll('.btn');

      for (var j = 0; j < buttons.length; j++) {
        registerClick(buttons[j]);
      }
    }
  }
}();