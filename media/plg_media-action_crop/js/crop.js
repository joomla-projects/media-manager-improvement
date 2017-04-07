Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};
Joomla.MediaManager.Edit = Joomla.MediaManager.Edit || {};

(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {

		var init = function(mediaData) {

			// Create the images for edit and preview
			var imageContainer = document.getElementById('media-manager-edit-container'),
				imageSrc = document.createElement('img'),
				imagePreview = document.createElement('img');

			imageSrc.src = 'data:image/' + mediaData.extension + ';base64,' + mediaData.contents;
			imagePreview.src = 'data:image/' + mediaData.extension + ';base64,' + mediaData.contents;
			imagePreview.id = 'image-preview';

			imageSrc.style.maxWidth = '100%';
			imageContainer.appendChild(imageSrc);
			imageContainer.appendChild(imagePreview);

			// Clear previous cropper
			if (Joomla.cropper) Joomla.cropper = {};

			// Initiate the cropper
			Joomla.cropperCrop = new Cropper(imageSrc, {
				// viewMode: 1,
				responsive:true,
				restore:true,
				autoCrop:true,
				movable: false,
				zoomable: false,
				rotatable: false,
				autoCropArea: 1,
				// scalable: false,
				minContainerWidth: imageSrc.offsetWidth,
				minContainerHeight: imageSrc.offsetHeight,
				crop: function(e) {
					document.getElementById('jform_crop_x').value = e.detail.x;
					document.getElementById('jform_crop_y').value = e.detail.y;
					document.getElementById('jform_crop_width').value = e.detail.width;
					document.getElementById('jform_crop_height').value = e.detail.height;

					// Update the store
					Joomla.MediaManager.Edit.current.content = Joomla.cropperCrop.getCroppedCanvas().toDataURL("image/" + Joomla.MediaManager.Edit.original.extension, 1.0);

					// Notify the app that a change has been made
					window.dispatchEvent(new Event('mediaManager.history.point'));

					// Make sure that the plugin didn't remove the preview
					document.getElementById('image-preview').src = Joomla.MediaManager.Edit.current.content;
				}
			});

			document.getElementById('jform_crop_x').value = 0;
			document.getElementById('jform_crop_y').value = 0;
			document.getElementById('jform_crop_width').value = imageSrc.offsetWidth;
			document.getElementById('jform_crop_height').value = imageSrc.offsetHeight;
		};

		// Register the Events
		Joomla.MediaManager.Edit.crop = {
			onActivate: function(mediaData) {
				// Initialize
				init(mediaData);
			},
			onDeactivate: function() {
				if (!Joomla.cropperCrop) {
					return;
				}
				// Destroy the instance
				Joomla.cropperCrop.destroy();

				// Clear the DOM
				document.getElementById('media-manager-edit-container').innerHTML = '';
			}
		};
	});
})();
