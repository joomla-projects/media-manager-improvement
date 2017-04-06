(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {

		var init = function(imageElement) {
			// Clear previous cropper
			if (Joomla.cropper) Joomla.cropper = {};

			// Initiate the cropper
			Joomla.cropperCrop = new Cropper(imageElement, {
				// viewMode: 1,
				responsive:true,
				restore:true,
				autoCrop:true,
				movable: false,
				zoomable: false,
				rotatable: false,
				autoCropArea: 1,
				// scalable: false,
				minContainerWidth: imageElement.offsetWidth,
				minContainerHeight: imageElement.offsetHeight,
				crop: function(e) {
					document.getElementById('jform_crop_x').value = e.detail.x;
					document.getElementById('jform_crop_y').value = e.detail.y;
					document.getElementById('jform_crop_width').value = e.detail.width;
					document.getElementById('jform_crop_height').value = e.detail.height;

					var options = Joomla.getOptions('com_media', {});
					var imgFileName = options.filePath.split('/').pop();
					var format = options.filePath.split('.').pop();

					// jpg is really jpeg
					format = (format === 'jpg') ? 'jpeg' : format;

					// Make sure that the plugin didn't remove the preview
					if (document.getElementById('media-edit-file-new')) {
						document.getElementById('media-edit-file-new').src = Joomla.cropperCrop.getCroppedCanvas().toDataURL("image/" + format, 1.0);
					} else {
						var image = new Image();
						image.id = 'media-edit-file-new';
						image.src = Joomla.cropperCrop.getCroppedCanvas().toDataURL("image/" + format, 1.0);
						document.body.appendChild(image);
					}

					//Do the upload automatically????
					//Joomla.UploadFile.exec(imgFileName, Joomla.cropperCrop.getCroppedCanvas().toDataURL("image/" + format, 1.0));
				}
			});

			document.getElementById('jform_crop_x').value = 0;
			document.getElementById('jform_crop_y').value = 0;
			document.getElementById('jform_crop_width').value = imageElement.offsetWidth;
			document.getElementById('jform_crop_height').value = imageElement.offsetHeight;
		};

		EventBus.addEventListener('onActivate', function(e, context, imageElement){
			// Bail out early
			if (context.toLowerCase() != 'crop') {
				return;
			}

			// Initialize
			init(imageElement);
		});

		EventBus.addEventListener('onDeactivate', function(e, context, imageElement){

			if (context.toLowerCase() != 'crop' || !Joomla.cropperCrop) {
				return;
			}

			Joomla.cropperCrop.destroy();
		});
	});
})();
