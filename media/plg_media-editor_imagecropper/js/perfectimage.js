jQuery(function ($) {
    'use strict';

    $('.perfect-image-select').on('click', function (e) {
        var id = $(this).closest('.perfect-image').attr('id'),
            image = $('#' + id + ' .perfect-image-image'),
            ratio = $('#' + id + ' .perfect-image-ratio').val(),
            ratioSplit = ratio.split('/');

        // Cropper
        image.cropper({aspectRatio: ratioSplit[0] / ratioSplit[1]});

        image.on({
            'crop.cropper': function (e) {
                var json = [
                    '{"x":' + e.x,
                    '"y":' + e.y,
                    '"height":' + e.height,
                    '"width":' + e.width,
                    '"rotate":' + e.rotate,
                    '"scaleX":' + e.scaleX,
                    '"scaleY":' + e.scaleY + '}'
                ].join();

                $('.perfect-image-data').val(json);
            }
        });
    });

    $('.perfect-image-upload').on('click', function (e) {
        var id = $(this).closest('.perfect-image').attr('id'),
            inputImage = $('#' + id + '_select'),
            image = $('#' + id + ' .perfect-image-image'),
            url = window.URL || window.webkitURL,
            bloburl;

        if (url) {
            inputImage.change(function () {
                var files = this.files;
                var file;

                if (!image.data('cropper')) {
                    return;
                }

                if (files && files.length) {
                    file = files[0];

                    if (/^image\/\w+$/.test(file.type)) {
                        bloburl = URL.createObjectURL(file);
                        image.one('built.cropper', function () {
                            URL.revokeObjectURL(bloburl);
                        }).cropper('reset').cropper('replace', bloburl);
                    } else {
                        window.alert('Please choose an image file.');
                    }
                }
            });
        } else {
            inputImage.prop('disabled', true).parent().addClass('disabled');
        }
    });

    // Methods
    $('.perfect-image-toolbar').on('click', '[data-method]', function (e) {
        e.preventDefault();

        var id = $(this).closest('.perfect-image').attr('id'),
            image = $('#' + id + ' .perfect-image-image'),
            data = $(this).data(),
            target,
            result;

        if ($(this).prop('disabled') || $(this).hasClass('disabled')) {
            return;
        }

        if (image.data('cropper') && data.method) {
            data = $.extend({}, data); // Clone a new one

            if (typeof data.target !== 'undefined') {
                target = $(data.target);

                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }

            result = image.cropper(data.method, data.option, data.secondOption);

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    $(this).data('option', -data.option);
                    break;
            }
        }
    });

    // On process cropped image
    $('.perfect-image-save').on('click', function (e) {
        e.preventDefault();

        var id = $(this).closest('.perfect-image').attr('id'),
            image = $('#' + id + '_select')[0].files[0],
            crop = $('#' + id + ' .perfect-image-data').val(),
            width = $('#' + id + ' .perfect-image-width').val(),
            ratio = $('#' + id + ' .perfect-image-ratio').val(),
            data = new FormData();

        // Add the form data
        data.append('option', 'com_ajax');
        data.append('group', 'content');
        data.append('plugin', 'perfectimage');
        data.append('format', 'json');
        data.append('image', image);
        data.append('crop', crop);
        data.append('width', width);
        data.append('ratio', ratio);

        // Try to upload and process the image
        $.ajax({
            url: '/administrator/index.php',
            type: 'POST',
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                if (response.message) {
                    console.log('Image upload failed: ' + response.message);
                    $('#' + id + ' .perfect-image-preview').html('<div class="alert alert-error">' + response.message + '</div>');
                } else {
                    console.log('Image uploaded: ' + response.data);

                    var image = response.data;
                    $('input#' + id + '').val(image).trigger('change');
                    $('#' + id + ' .perfect-image-preview').html('<img src="../' + image + '" />');
                    $('#' + id + ' .perfect-image-clear').removeClass('hidden');
                }
            },
            error: function (response) {
                console.log('Image upload failed: ' + response);
            }
        })
    });

    // Clear current image
    $('.perfect-image-clear').on('click', function (e) {
        e.preventDefault();

        var id = $(this).closest('.perfect-image').attr('id');
        $('input#' + id + '').val('');
        $('#' + id + ' .perfect-image-preview').html('');
        $('#' + id + ' .perfect-image-clear').addClass('hidden');
    });
});