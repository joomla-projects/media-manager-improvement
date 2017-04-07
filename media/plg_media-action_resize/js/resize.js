Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};
Joomla.MediaManager.Edit = Joomla.MediaManager.Edit || {};
(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {
		var initResize = function(imageSrc) {
			// Clear previous cropper
			if (Joomla.cropper) Joomla.cropper = {};

			// Initiate the cropper
			Joomla.cropperResize = new Cropper(imageSrc, {
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
				minContainerWidth: imageSrc.offsetWidth,
				minContainerHeight: imageSrc.offsetHeight,
			});
		};

		// Update image
		var updateImage = function(data) {

			Joomla.MediaManager.Edit.current.content = Joomla.cropperResize.getCroppedCanvas(data).toDataURL("image/" + Joomla.MediaManager.Edit.original.extension).replace('data:image/' + Joomla.MediaManager.Edit.original.extension + ';base64,', '');
				// Joomla.cropperCrop.getCroppedCanvas().toDataURL("image/" + Joomla.MediaManager.Edit.original.extension, 1.0);
console.log(Joomla.MediaManager.Edit.current.content);
			// Notify the app that a change has been made
			window.dispatchEvent(new Event('mediaManager.history.point'));

			// Make sure that the plugin didn't remove the preview
			document.getElementById('image-preview').src = Joomla.MediaManager.Edit.current.content;
		};

		// Register the Events
		Joomla.MediaManager.Edit.resize = {
			onActivate: function(mediaData) {

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

				imageSrc = document.getElementById('media-manager-edit-container').querySelector('img');

				console.log(imageSrc)
				console.log(imageSrc.height)
				console.log(imageSrc.width)

				// Set the values for the range fields
				var resizeWidth = document.getElementById('jform_resize_w'),
				    resizeHeight = document.getElementById('jform_resize_h');


				resizeWidth.min = 0;
				resizeWidth.max = imageSrc.width;
				resizeWidth.value = imageSrc.width;

				resizeHeight.min = 0;
				resizeHeight.max = imageSrc.height;
				resizeHeight.value = imageSrc.height;

				// Initialize
				initResize(imageSrc);

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
			},
			onDeactivate: function() {
				if (!Joomla.cropperResize) {
					return;
				}
				// Destroy the instance
				Joomla.cropperResize.destroy();

				// Clear the DOM
				document.getElementById('media-manager-edit-container').innerHTML = '';
			}
		};
	});
})();
