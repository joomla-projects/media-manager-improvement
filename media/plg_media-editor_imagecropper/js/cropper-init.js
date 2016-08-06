!function() {
  // We are using jQuery only to add a click event to the buttons and the dom ready? We are doing it wrong!!
  jQuery(document).ready(function() {

    var image = document.getElementById('joomla-media-image-cropper');
    window.imageUrl = image.getAttribute("src");
    window.postUrl  = image.getAttribute("data-url");
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
    // The upload logic copy paste from tinymce jdrag and drop
    var UploadFile = function(fd) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", postUrl, true);

      // No progress bar here
      // xhr.upload.onprogress = function(e) {
      //   var percentComplete = (e.loaded / e.total) * 100;
      //   jQuery('.bar').width(percentComplete + '%');
      // };

      // removeProgessBar = function(){
      //   setTimeout(function(){
      //     jQuery('#jloader').remove();
      //     editor.contentAreaContainer.style.borderWidth = '';
      //   }, 200);
      // };

      xhr.onload = function() {
        var resp = JSON.parse(xhr.responseText);

        if (xhr.status == 200) {
          if (resp.status == '0') {
            // removeProgessBar();

            console.log('Upload success');
            //close the modal
          }

          if (resp.status == '1') {
            // removeProgessBar();
            console.log('Upload success');
            //close the modal


            // Create the image tag
            // var newNode = tinyMCE.activeEditor.getDoc().createElement ('img');
            // newNode.src= setCustomDir + resp.location;
            // tinyMCE.activeEditor.execCommand('mceInsertContent', false, newNode.outerHTML);
          }
        } else {
          console.log('No Upload');
          //close the modal
        }
      };

      xhr.onerror = function() {
        console.log('Upload Error');
        //close the modal
      };
      xhr.send(fd);
    }

    // Upload cropped image to server
    var doTheUpload = function() {
      cropper.getCroppedCanvas().toBlob(function (blob) {
        var imgFileName = imageUrl.split('/').pop();
        var fd = new FormData();

        fd.append('files', blob, imgFileName);

        UploadFile(fd);
      });
    }

    window.cropperBoxDim = {};

    // We are using jQuery only to add a click event to the buttons and the dom ready? We are doing it wrong!!
    jQuery('.btn-toolbar button').click(function (event) {
      console.log(event.currentTarget);

      var action  = event.currentTarget.getAttribute("data-method");
      var option  = event.currentTarget.getAttribute("data-option");
      var option2  = event.currentTarget.getAttribute("data-second-option");

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
          cropper.getCroppedCanvas();
          doTheUpload();
          break;
      }
    });
  });
}();