!function() {
  // We are using jQuery only to add a click event to the buttons and the dom ready? We are doing it wrong!!
  jQuery(document).ready(function() {

    var image = document.getElementById('joomla-media-image-filters');
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
    Caman("#filter-canvas", imageUrl, function () {
      this.brightness(5);
      this.render();
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
      var canvas = document.getElementById("filter-canvas");
      var newImg = canvas.toDataURL("image/jpeg");

      var blobBin = atob(newImg.split(',')[1]);
      var array = [];
      for(var i = 0; i < blobBin.length; i++) {
        array.push(blobBin.charCodeAt(i));
      }
      var file = new Blob([new Uint8Array(array)], {type: 'image/png'});

      var imgFileName = imageUrl.split('/').pop();

        var fd = new FormData();
console.log(file);
        fd.append('files', file, imgFileName);

        UploadFile(fd);
      // });
    };

    var registerClick = function(element) {
      element.addEventListener('click', function (event) {
        var action = event.currentTarget.getAttribute("data-preset");

        switch (action) {
          case 'vintage':
          case 'lomo':
          case 'clarity':
          case 'sinCity':
          case 'sunrise':
          case 'crossProcess':
          case 'orangePeel':
          case 'love':
          case 'grungy':
          case 'jarques':
          case 'pinhole':
          case 'oldBoot':
          case 'glowingSun':
          case 'hazyDays':
          case 'herMajesty':
          case 'nostalgia':
          case 'hemingway':
          case 'concentrate':
            Caman("#filter-canvas", function () {
              this[action]().render();
            });
            break;

          default:
          case 'reset':
            Caman("#filter-canvas", function () {
              this.reset();
            });
            break;

          case 'save':
            doTheUpload();
            break;
        }
      });
    };


      var registerChange = function(element) {
        element.addEventListener('change', function (event) {
          var action = event.currentTarget.getAttribute("data-filter");
          var value  = +this.value;
              //+event.currentTarget.getAttribute("value");
          console.log(action);
          switch (action) {
            case 'brightness':
            case 'contrast':
            case 'saturation':
            case 'vibrance':
            case 'exposure':
            case 'hue':
            case 'sepia':
            case 'gamma':
            case 'noise':
            case 'clip':
            case 'sharpen':
            case 'tiltShift':
              Caman("#filter-canvas", function () {
                this[action](value).render();
              });
              break;
          }
        });
      };


      var inputs = document.querySelectorAll('input[type="range"]');
    console.log(inputs);
      for (var i = 0; i<inputs.length; i++) {
        registerChange(inputs[i]);
      }

      var buttons = document.querySelectorAll('.btn');
console.log(buttons);
      for (var j = 0; j<buttons.length; j++) {
        registerClick(buttons[j]);
      }
  });
}();