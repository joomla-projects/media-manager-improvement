/**
 * BANNER
 */

Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};

(function() {
	"use strict";

	var options = Joomla.getOptions('com_media', {});

	if (!options) {
		return;
	}

	// Customize the buttons
	Joomla.submitform = function(task, form, validate) {
		var pathName = window.location.pathname.replace(/&view=file.*/g, ''),
			name = options.filePath.split('/').pop(),
			forUpload = {
				'name': name,
				'content': Joomla.MediaManager.Edit.current.content.replace(/data:image\/(png|jpeg);base64,/, '')
			},
			uploadPath = options.uploadPath,
			url = options.apiBaseUrl + '&task=api.files&path=' + uploadPath,
			type = 'application/json';

		forUpload[options.csrfToken] = "1";

		switch (task) {
			case 'apply':
				Joomla.UploadFile.exec(name, JSON.stringify(forUpload), uploadPath, url, type);
				break;
			case 'save':
				Joomla.UploadFile.exec(name, JSON.stringify(forUpload), uploadPath, url, type);
				window.location = pathName + '?option=com_media';
				break;
			case 'cancel':
				window.location = pathName + '?option=com_media&path=' + uploadPath;
				break;
		}
	};


	// The upload object
	Joomla.UploadFile = {};

	// Create the progress bar
	Joomla.UploadFile.createProgressBar = function() {
		setTimeout(function() {
//			document.querySelector('#jloader').outerHTML = "";
//			delete document.querySelector('#jloader');
//			document.querySelector('.media-browser').style.borderWidth = '1px';
//			document.querySelector('.media-browser').style.borderStyle = 'solid';
		}, 200);
	};

	// Update the progress bar
	Joomla.UploadFile.updateProgressBar = function(position) {
		setTimeout(function() {
//			document.querySelector('#jloader').outerHTML = "";
//			delete document.querySelector('#jloader');
//			document.querySelector('.media-browser').style.borderWidth = '1px';
//			document.querySelector('.media-browser').style.borderStyle = 'solid';
		}, 200);
	};

	// Remove the progress bar
	Joomla.UploadFile.removeProgressBar = function() {
		setTimeout(function() {
//			document.querySelector('#jloader').outerHTML = "";
//			delete document.querySelector('#jloader');
//			document.querySelector('.media-browser').style.borderWidth = '1px';
//			document.querySelector('.media-browser').style.borderStyle = 'solid';
		}, 200);
	};



	/**
	 *
	 * @param name       the name of the file
	 * @param data       the file data (base64 encoded)
	 * @param uploadPath the file
	 * @param url        the file
	 * @param type       the file
	 * @constructor
	 */
	Joomla.UploadFile.exec = function (name, data, uploadPath, url, type) {

		var xhr = new XMLHttpRequest();

		xhr.upload.onprogress = function(e) {
			Joomla.UploadFile.updateProgressBar((e.loaded / e.total) * 100);
		};

		xhr.onload = function() {
			try {
				var resp = JSON.parse(xhr.responseText);
			} catch (e) {
				var resp = null;
			}

			if (resp) {
				if (xhr.status == 200) {
					if (resp.success == true) {
						Joomla.UploadFile.removeProgressBar();
					}

					if (resp.status == '1') {
						Joomla.renderMessages({'success': [resp.message]}, 'true');
						Joomla.UploadFile.removeProgressBar();
					}
				}
			} else {
				Joomla.UploadFile.removeProgressBar();
			}
		};

		xhr.onerror = function() {
			Joomla.UploadFile.removeProgressBar();
		};

		xhr.open("PUT", url, true);
		xhr.setRequestHeader('Content-Type', type);
		Joomla.UploadFile.createProgressBar();
		xhr.send(data);
	};

	// Once the DOM is ready, initialize everything
	document.addEventListener('DOMContentLoaded', function() {
		// Initiate the plugins registry
		Joomla.MediaManager.Edit.original = {
			filename: options.filePath.split('/').pop(),
			extension: options.filePath.split('.').pop(),
			contents: options.contents
		};
		Joomla.MediaManager.Edit.history= {};
		Joomla.MediaManager.Edit.current= {};

		// This needs a good refactoring once we'll get the new UI/CE
		// Crap to satisfy jQuery's slowlyness!!!
		var func = function() {
			var links = [].slice.call(document.querySelectorAll('[data-toggle="tab"]'));

			for(var i = 0, l = links.length; i < l; i++){

				jQuery(links[i]).on('shown.bs.tab', function(event) {

					if (event.relatedTarget) {
						Joomla.MediaManager.Edit[event.relatedTarget.hash.replace('#attrib-', '').toLowerCase()].onDeactivate();
					}
					Joomla.MediaManager.Edit[event.target.hash.replace('#attrib-', '').toLowerCase()].onActivate(Joomla.MediaManager.Edit.original);
				});
			}

			Joomla.MediaManager.Edit[links[0].hash.replace('#attrib-', '').toLowerCase()].onActivate(Joomla.MediaManager.Edit.original);
		};
		setTimeout(func, 100); // jQuery...
	});
})();