/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};

(function() {
	"use strict";

	// Get the options from Joomla.optionStorage
	var options = Joomla.getOptions('com_media', {});

	if (!options) {
		// @TODO Throw an alert
		return;
	}

	// Initiate the registry
	Joomla.MediaManager.Edit.original = {
		filename: options.uploadPath.split('/').pop(),
		extension: options.uploadPath.split('.').pop(),
		contents: 'data:image/' + options.uploadPath.split('.').pop() + ';base64,' +  options.contents
	};
	Joomla.MediaManager.Edit.history= {};
	Joomla.MediaManager.Edit.current= {};

	// Reset the image to the initial state
	Joomla.MediaManager.Edit.Reset = function(current) {
		if (!current || (current && current !== true)) {
			Joomla.MediaManager.Edit.current.contents = Joomla.MediaManager.Edit.original.contents;
		}

		// Clear the DOM
		document.getElementById('media-manager-edit-container').innerHTML = '';

		// Reactivate the current plugin
		var links = [].slice.call(document.querySelectorAll('[data-toggle="tab"]'));

		for (var i = 0, l = links.length; i < l; i++){
			if (!links[i].classList.contains('active')) {
				continue;
			}

			Joomla.MediaManager.Edit[links[i].hash.replace('#attrib-', '').toLowerCase()].Deactivate();

			var data = Joomla.MediaManager.Edit.current;
			if (!current || (current && current !== true)) {
				data = Joomla.MediaManager.Edit.original;
			}

			activate(links[i].hash.replace('#attrib-', ''), data);
			break;
		}
	};

	// Create history entry
	window.addEventListener('mediaManager.history.point', function() {
		if (Joomla.MediaManager.Edit.original !== Joomla.MediaManager.Edit.current.contents) {
			var key = Object.keys(Joomla.MediaManager.Edit.history).length;
			if (Joomla.MediaManager.Edit.history[key] && Joomla.MediaManager.Edit.history[key - 1] && Joomla.MediaManager.Edit.history[key] === Joomla.MediaManager.Edit.history[key - 1]) {
				return;
			}
			Joomla.MediaManager.Edit.history[key + 1] = Joomla.MediaManager.Edit.current.contents;
		}
	});

	// @TODO History
	Joomla.MediaManager.Edit.Undo = function() {};
	// @TODO History
	Joomla.MediaManager.Edit.Redo = function() {};

	// Create the progress bar
	Joomla.MediaManager.Edit.createProgressBar = function() {};

		const alert = document.createElement('joomla-alert');
		options.type
		alert.setAttribute('type', 'success');
		alert.setAttribute('dismiss', true);
		alert.setAttribute('auto-dismiss', true);
		alert.innerHTML = '<progress id="mediaProgressBar" value="0" max="100"'
        		+ 'style="font-size: 20px;vertical-align: middle;"> </progress>'
        		+ '<span id="mediaProgressText" style="font-size: 20px;vertical-align: middle;">0 %</span>';
		const messageContainer = document.getElementById('system-message');
        	messageContainer.appendChild(alert);
	};
 
 	// Update the progress bar
	Joomla.MediaManager.Edit.updateProgressBar = function(e) {
		let value = 0;
		e.lengthComputable ? value = Math.round((e.loaded / e.total) * 100) : 0;
		const mediaProgressBar = document.getElementById('mediaProgressBar');
		const mediaProgressText = document.getElementById('mediaProgressText');
		mediaProgressBar.setAttribute('value', value);
		mediaProgressText.innerHTML = value + ' %';
	};

	/**
	 * Start the upload with a promise
	 */
	Joomla.MediaManager.Edit.Promise = function() {

		let format = Joomla.MediaManager.Edit.original.extension === 'jpg' ? 'jpeg' : Joomla.MediaManager.Edit.original.extension,
		pathName = window.location.pathname.replace(/&view=file.*/g, ''),
		name = options.uploadPath.split('/').pop(),
		forUpload = {
			'name': name,
			'content': Joomla.MediaManager.Edit.current.contents.replace('data:image/' + format + ';base64,', '')
		},
		uploadPath = options.uploadPath,
		url = options.apiBaseUrl + '&task=api.files&path=' + uploadPath;

		// @ToDo Replace with Joomla token in header if Joomla 4.0 supports it.
		forUpload[options.csrfToken] = "1";

		let fileDirectory = uploadPath.split('/');
		fileDirectory.pop();
		fileDirectory = fileDirectory.join('/');

		// If we are in root add a backslash
		if (fileDirectory.endsWith(':')) {
			fileDirectory = fileDirectory + '/';
		}

		// Wrap the ajax call into a real promise
        	return new Promise((resolve, reject) => {
	        	Joomla.UploadFile.exec({
        			name: name,
        			data: JSON.stringify(forUpload),
        			url: url,
        			headers: {'Content-Type': 'application/json'},
        			method: "PUT",
        			onBefore: Joomla.MediaManager.Edit.createProgressBar,
        			onSuccess: (response) => {
        				resolve(response)
                		},
        			onError: (xhr) => {
        				reject(xhr);
        			},
      		  		onUploadProgress: Joomla.MediaManager.Edit.updateProgressBar,
        		});
        	});
	}

	// Customize the buttons
	Joomla.submitbutton = function(task) {

		switch (task) {
			case 'apply':
				Joomla.MediaManager.Edit.Promise()
					.then(function(responseText, xhr){
						try {
							var resp = JSON.parse(responseText);
						} catch (e) {
							var resp = null;
						}

						if (resp && resp.success == 1) {
							// Success message?
							//const options = {'type': 'success'};
							//_notify('COM_MEDIA_UPDLOAD_SUCCESS', options);

							Joomla.MediaManager.Edit.Reset(true);
						} else {
							window.log ? console.log("error", Joomla.JText._('COM_MEDIA_SERVER_ERROR', 'COM_MEDIA_SERVER_ERROR')): null;

							const options = {'type': 'danger'};
		        				_notify('COM_MEDIA_SERVER_ERROR', options);

							Joomla.MediaManager.Edit.Reset(false);
						}
					})
					.catch(error => {
						window.log ? console.log("error", Joomla.JText._('COM_MEDIA_ERROR', 'COM_MEDIA_ERROR')): 'Error Ajax';

						const options = {'type': 'danger','dismiss': 'false','autoDismiss': 'false'};
						_notify('COM_MEDIA_ERROR', options);

						Joomla.MediaManager.Edit.Reset(false);
					})
				break;
			case 'save':
			    Joomla.MediaManager.Edit.Promise()
				.then(function(responseText, xhr){
					try {
						var resp = JSON.parse(responseText);
					} catch (e) {
						var resp = null;
					}

					if (resp && resp.success == 1) {
						// Success message?
						//const options = {'type': 'success'};
	        				//_notify('COM_MEDIA_UPDLOAD_SUCCESS', options);

	        				window.location = pathName + '?option=com_media&path=' + fileDirectory;
					} else {
						window.log ? console.log("error", Joomla.JText._('COM_MEDIA_SERVER_ERROR', 'COM_MEDIA_SERVER_ERROR')): null;

						const options = {'type': 'danger','dismiss': 'false','autoDismiss': 'false'};
						_notify('COM_MEDIA_SERVER_ERROR', options);

						Joomla.MediaManager.Edit.Reset(false);
					}
				})
				.catch(error => {
					window.log ? console.log("error", Joomla.JText._('COM_MEDIA_ERROR', 'COM_MEDIA_ERROR')): 'Error Ajax';

					const options = {'type': 'danger','dismiss': 'false','autoDismiss': 'false'};
        				_notify('COM_MEDIA_ERROR', options);

					Joomla.MediaManager.Edit.Reset(false);
				})
				break;
			case 'cancel':
				window.location = pathName + '?option=com_media&path=' + fileDirectory;
				break;
			case 'reset':
				Joomla.MediaManager.Edit.Reset('initial');
				break;
			case 'undo':
				// @TODO magic goes here
				break;
			case 'redo':
				// @TODO other magic goes here
				break;
		}
	};

	/**
	 * @TODO Extend Joomla.request and drop this code!!!!
	 */
	// The upload object
	Joomla.UploadFile = {};

	/**
	 * @TODO Extend Joomla.request and drop this code!!!!
	 */
	Joomla.UploadFile.exec = function (name, data, uploadPath, url, type) {

		// Prepare the options
		options = Joomla.extend({
		url:    '',
		method: 'GET',
		data:    null,
		perform: true
		}, options);

		// Set up XMLHttpRequest instance
		try{
			const xhr = new XMLHttpRequest();

			xhr.onload = function() {
				if (options.onSuccess) {
					options.onSuccess.call(this, xhr.responseText, xhr);
				}
			};

			xhr.onerror = function() {
				if (options.onError) {
					options.onError.call(this, xhr);
				}
			};

			// Events missing in core.js
			// Progress us used here
			if(options.onUploadProgress){
				xhr.upload.addEventListener("progress", options.onUploadProgress);
			}
			if(options.onUploadComplete){
				xhr.upload.addEventListener("load", options.onUploadComplete);
			}
			if(options.onUploadError){
				xhr.upload.addEventListener("error", options.onUploadError);
			}
			if(options.onUploadAbort){
				xhr.upload.addEventListener("abort", options.onUploadAbort);
			}

			// Mising in core.js
			xhr.onprogress = function() {
				if (options.onProgress) {
					options.onProgress(event);
				}
			};

			xhr.open(options.method, options.url, true);

			// Set the headers
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.setRequestHeader('X-Ajax-Engine', 'Joomla!');

			// This does not work in 4.0
			if (options.method !== 'GET') {
				var token = Joomla.getOptions('csrf.token', '');

				if (token) {
					xhr.setRequestHeader('X-CSRF-Token', token);
				}

				if (!options.headers || !options.headers['Content-Type']) {
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				}
			}

			// Custom headers
			if (options.headers){
				for (var p in options.headers) {
					if (options.headers.hasOwnProperty(p)) {
						xhr.setRequestHeader(p, options.headers[p]);
					}
				}
			}

			// Do request
			if (options.perform) {
				if (options.onBefore && options.onBefore.call(this, xhr) === false) {
					// Request interrupted
					return xhr;
				}
	
				xhr.send(options.data);
			}

		} catch (error) {
			window.console ? console.log(error) : null;
			return false;
		}
	};

	// Once the DOM is ready, initialize everything
	document.addEventListener('DOMContentLoaded', function() {
		// @TODO This needs a good refactoring once we'll get the new UI/C-E
		// Crap to satisfy jQuery's slowlyness!!!
		var func = function() {
			var links = [].slice.call(document.querySelectorAll('[data-toggle="tab"]'));

			if (!links.length) {
				return;
			}

			// Couple the tabs with the plugin objects
			for (var i = 0, l = links.length; i < l; i++){
				jQuery(links[i]).on('shown.bs.tab', function(event) {
					if (event.relatedTarget) {
						Joomla.MediaManager.Edit[event.relatedTarget.hash.replace('#attrib-', '').toLowerCase()].Deactivate();

						// Clear the DOM
						document.getElementById('media-manager-edit-container').innerHTML = '';
					}

					var contents;
					var data = Joomla.MediaManager.Edit.current;;
					if (!contents in Joomla.MediaManager.Edit.current) {
						data = Joomla.MediaManager.Edit.original;
					}

					activate(event.target.hash.replace('#attrib-', ''), data);
				});
			}

			activate(links[0].hash.replace('#attrib-', ''), Joomla.MediaManager.Edit.original);
		};

		setTimeout(func, 100); // jQuery...
	});

	var activate = function(name, data) {
		// Amend the layout
		var tabContent = document.getElementById('myTabContent'),
		    pluginControls = document.getElementById('attrib-' + name);

		tabContent.classList.add('row', 'ml-0', 'mr-0', 'p-0');
		pluginControls.classList.add('col-md-3', 'p-4');

		// Create the images for edit and preview
		var baseContainer = document.getElementById('media-manager-edit-container'),
		    editContainer = document.createElement('div'),
		    previewContainer = document.createElement('div'),
		    imageSrc = document.createElement('img'),
		    imagePreview = document.createElement('img');

		baseContainer.innerHTML = '';

		imageSrc.src = data.contents;
		imageSrc.id = 'image-source';
		imageSrc.style.maxWidth = '100%';
		imagePreview.src = data.contents;
		imagePreview.id = 'image-preview';
		imagePreview.style.maxWidth = '100%';
		editContainer.style.display = 'none';

		editContainer.appendChild(imageSrc);
		baseContainer.appendChild(editContainer);

		previewContainer.appendChild(imagePreview);
		baseContainer.appendChild(previewContainer);

		// Activate the first plugin
		Joomla.MediaManager.Edit[name.toLowerCase()].Activate(data);
	};

	// @ToDo Replace this code with calls to a future Joomla core code. 
	/** 
	* Send a notification as a modal message.
	*/
	function _notify(message, options) { 
		const alert = document.createElement('joomla-alert');
		alert.setAttribute('type', options.type || 'info');	
		alert.setAttribute('dismiss', options.dismiss || true);
		alert.setAttribute('auto-dismiss', options.autoDismiss || true);
		alert.innerHTML = Joomla.JText._(message, message) || '';

		const messageContainer = document.getElementById('system-message');
		messageContainer.appendChild(alert);
    }

})();
