!function() {
    document.onreadystatechange = function () {
        if (document.readyState == "interactive") {
            var image = document.getElementById('joomla-media-image-filters');      // The image node
            window.imageUrl = image.getAttribute("data-src");                       // The image Url
            window.postUrl = image.getAttribute("data-url");                        // The upload Url
            window.submitInput = document.querySelectorAll('input[type="submit"]'); // The hidden submit input
            window.SliderValues = {};

            // TODO get any data-* values and build the options
            // eg:   if (typeof image.getAttribute("data-some-attribute") != "undefined") {
            //           option1 = image.getAttribute("data-some-attribute");
            //       }
            // This way no inline script will be injected in the page

            /**
             * Initialiaze canvas manipulation (caman)
             */
            Caman("#filter-canvas", imageUrl, function () {
                this.render();
            });

            // The upload logic copy paste from tinymce jdrag and drop
            var UploadFile = function (fd) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", postUrl, true);

                xhr.onload = function () {
                    var resp = JSON.parse(xhr.responseText);

                    if (xhr.status == 200) {
                        if (resp.status == '0') {
console.log('Upload success');
                            submitInput[0].click();
                            //close the modal
                        }

                        if (resp.status == '1') {
console.log('Upload success');
                            submitInput[0].click();
                            //close the modal
                        }
                    } else {
console.log('No Upload');
                        //close the modal
                    }
                };

                xhr.onerror = function () {
console.log('Upload Error');
                    //close the modal
                };
                xhr.send(fd);
            };

            // Upload cropped image to server
            var doTheUpload = function () {
                var canvas = document.getElementById("filter-canvas"),
                    newImg = canvas.toDataURL("image/jpeg"),
                    blobBin = atob(newImg.split(',')[1]),
                    array = [];

                for (var i = 0; i < blobBin.length; i++) {
                    array.push(blobBin.charCodeAt(i));
                }

                var file = new Blob([new Uint8Array(array)], {type: 'image/png'}),
                    imgFileName = imageUrl.split('/').pop(),
                    fd = new FormData();
console.log(file);

                fd.append('files', file, imgFileName);
                UploadFile(fd);
            };

            var registerClick = function (element) {
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

                                // Reset the sliders
                                var sliders = document.querySelectorAll('input[type="range"]');
                                for (var k = 0; k < sliders.length; k++) {
                                    sliders[k].value = 0;
                                }
                            });
                            break;

                        case 'save':
                            doTheUpload();
                            break;
                    }
                });
            };


            var registerChange = function (element) {
                element.addEventListener('change', function (event) {
                    // Get all the current values from all the sliders
                    var parentDiv = event.currentTarget.parentNode.parentNode.parentNode.parentNode,
                        sliders = parentDiv.querySelectorAll('input[type="range"]');

                    for (var k = 0; k < sliders.length; k++) {
                        var tmpName = sliders[k].getAttribute("data-filter");
                        var parentTmpDiv = sliders[k].parentNode;
                        parentTmpDiv.querySelector('.range-value').innerHTML = sliders[k].value;
                        window.SliderValues[tmpName] = parseInt(sliders[k].value);
                    }
                    var action = event.currentTarget.getAttribute("data-filter");
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
                                this.reset();
                                (SliderValues.brightness != 0) ? this.brightness(SliderValues.brightness) : '';
                                (SliderValues.contrast != 0) ? this.contrast(SliderValues.contrast) : '';
                                (SliderValues.saturation != 0) ? this.saturation(SliderValues.saturation) : '';
                                (SliderValues.vibrance != 0) ? this.vibrance(SliderValues.vibrance) : '';
                                (SliderValues.exposure != 0) ? this.exposure(SliderValues.exposure) : '';
                                (SliderValues.hue =! 0) ? this.hue(SliderValues.hue) : '';
                                (SliderValues.sepia != 0) ? this.sepia(SliderValues.sepia) : '';
                                (SliderValues.gamma != 0) ? this.gamma(SliderValues.gamma) : '';
                                (SliderValues.noise != 0) ? this.noise(SliderValues.noise) : '';
                                (SliderValues.clip != 0) ? this.clip(SliderValues.clip) : '';
                                (SliderValues.sharpen != 0) ? this.sharpen(SliderValues.sharpen) : '';
                                (SliderValues.tiltShift != 0) ? this.tiltShift(SliderValues.tiltShift) : '';
                                this.render();
                            });
                            break;
                    }
                });
            };


            var inputs = document.querySelectorAll('input[type="range"]');
console.log(inputs);
            for (var i = 0; i < inputs.length; i++) {
                registerChange(inputs[i]);
            }

            var buttons = document.querySelectorAll('.btn');
console.log(buttons);
            for (var j = 0; j < buttons.length; j++) {
                registerClick(buttons[j]);
            }
        }
    }
}();
