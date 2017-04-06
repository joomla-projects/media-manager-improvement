(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {
		var initResize = function(imageElement) {
			// Clear previous cropper
			if (Joomla.cropper) Joomla.cropper = {};

			// Initiate the cropper
			Joomla.cropperResize = new Cropper(imageElement, {
				//viewMode: 1,
				restore: true,
				responsive:true,
				dragMode: false,
				autoCrop: false,
				autoCropArea: 1,
				guides: false,
				center: false,
				highlight: false,
				cropBoxMovable: false,
				scalable: false,
				zoomable:false,

				//cropBoxResizable: false,
				toggleDragModeOnDblclick: false,
				minContainerWidth: imageElement.offsetWidth,
				minContainerHeight: imageElement.offsetHeight,
			});
		};

		// Update image
		var updateImage = function(data) {
			var options = Joomla.getOptions('com_media', {});
			var format = options.filePath.split('/').pop();

			// jpg is really jpeg
			format = (format === 'jpg') ? 'jpeg' : format;

			// Make sure that the plugin didn't remove the preview
			if (document.getElementById('media-edit-file-new')) {
				document.getElementById('media-edit-file-new').src = Joomla.cropperResize.getCroppedCanvas(data).toDataURL("image/" + format);
			} else {
				var image = new Image();
				image.id = 'media-edit-file-new';
				image.src = Joomla.cropperResize.getCroppedCanvas(data).toDataURL("image/" + format, 1.0);
				document.body.appendChild(image);
			}
		};

		EventBus.addEventListener('onActivate', function(e, context, imageElement){
			// Bail out early
			if (context.toLowerCase() != 'resize') {
				return;
			}

			// Initialize
			initResize(imageElement);

			// Set the values for the range fields
			var resizeWidth = document.getElementById('jform_resize_w'),
				resizeHeight = document.getElementById('jform_resize_h');

			resizeWidth.min = 0;
			resizeWidth.max = imageElement.offsetWidth;
			resizeWidth.value = imageElement.offsetWidth;

			resizeHeight.min = 0;
			resizeHeight.max = imageElement.offsetHeight;
			resizeHeight.value = imageElement.offsetHeight;
			Joomla.cropperResize.aspectRatio = parseInt(resizeWidth.value) / parseInt(resizeHeight.value);

			resizeWidth.addEventListener('change', function(event) {
				var label = document.getElementById('jform_resize_w-lbl');
				var txt = label.innerText.replace(/:.*/, '');
				label.innerHTML = txt + ' : ' + event.target.value + ' px';

				Joomla.cropperResize.crop({ width: parseInt(document.getElementById('jform_resize_w').value), height: parseInt(document.getElementById('jform_resize_w').value)/ Joomla.cropperResize.aspectRatio });

				updateImage({ width: parseInt(document.getElementById('jform_resize_w').value), height: parseInt(document.getElementById('jform_resize_h').value)/ Joomla.cropperResize.aspectRatio })
			});

			resizeHeight.addEventListener('change', function(event) {
				var label = document.getElementById('jform_resize_h-lbl');
				var txt = label.innerText.replace(/:.*/, '');
				label.innerHTML = txt + ' : ' + event.target.value + ' px';

				Joomla.cropperResize.crop({ width: parseInt(document.getElementById('jform_resize_h').value) * Joomla.cropperResize.aspectRatio, height: parseInt(document.getElementById('jform_resize_h').value) });

				updateImage({ width: parseInt(document.getElementById('jform_resize_h').value) * Joomla.cropperResize.aspectRatio, height: parseInt(document.getElementById('jform_resize_h').value) })
			});

		});

		EventBus.addEventListener('onDeactivate', function(e, context, imageElement){
			if (context.toLowerCase() != 'resize' || !Joomla.cropperResize) {
				return;
			}

			Joomla.cropperResize.destroy();
		});
	});
})();
