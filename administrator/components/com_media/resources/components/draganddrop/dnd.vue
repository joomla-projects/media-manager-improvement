<template>

</template>
<script>


	export default {
		name: 'media-dnd',
		props: ['content']
	};

	var openFile = function(f) {
		var reader = new FileReader();
		reader.onloadend = function(e) {
		UploadFile(f.name, reader.result);
		};
		reader.readAsDataURL(f);
	};

	// Reset the drop area border
	document.addEventListener('dragleave', function(e) {
		e.stopPropagation();
		e.preventDefault();
		document.querySelector('.media-browser').style.borderWidth='1px';
		document.querySelector('.media-browser').style.borderStyle='solid';

		return false;
	});

	// The upload logic
	window.UploadFile = function (name, f) {
		var forUpload = {
			'name': name,
			'content': f.replace(/data:+.+base64,/, '')
		};
// @TODO get these from the store
		var uploadPath = '';
		//var url = 'index.php?option=com_media&path=/' + uploadPath + '/' + name;


			var xhr = new XMLHttpRequest();

			xhr.upload.onprogress = function(e) {
				var percentComplete = (e.loaded / e.total) * 100;
				document.getElementById('progress-bar-com-media-tmp').style.width = percentComplete + '%';
			};

			var removeProgessBar = function(){
				setTimeout(function(){
					document.querySelector('#jloader').outerHTML = "";
					delete document.querySelector('#jloader');
					document.querySelector('.media-browser').style.borderWidth = '1px';
					document.querySelector('.media-browser').style.borderStyle = 'solid';
				}, 200);
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
							removeProgessBar();
						}

						if (resp.status == '1') {
							Joomla.renderMessages({'success': [resp.message]}, 'true');
							removeProgessBar();
						}
					}
				} else {
					removeProgessBar();
				}
			};

			xhr.onerror = function() {
				removeProgessBar();
			};

			xhr.open("POST", '/administrator/index.php?option=com_media&task=api.files&format=json&path=' + uploadPath + '/' + name, true);
			xhr.setRequestHeader('Content-Type', 'application/json');
			xhr.send(JSON.stringify(forUpload));

	}

	// Listers for drag and drop
	// Fix for Chrome
	document.addEventListener('dragenter', function(e) {
		e.stopPropagation();
		return false;
	});


	// Notify user when file is over the drop area
	document.addEventListener('dragover', function(e) {
		e.preventDefault();
		document.querySelector('.media-browser').style.borderStyle = 'dashed';
		document.querySelector('.media-browser').style.borderWidth = '5px';

		return false;
	});

	// Logic for the dropped file
	document.addEventListener('drop', function(e) {
		e.preventDefault();

		// We override only for files
		if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
			for (var i = 0, f; f = e.dataTransfer.files[i]; i++) {

				// Only images allowed
				if (f.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)) {

					// Display a progress bar
					document.querySelector('.media-browser').insertAdjacentHTML('afterbegin',
						'<div id=\"jloader\">' +
						'   <div class=\"progress progress-success progress-striped active\" style=\"width:100%;height:30px;\">' +
						'       <div id=\"progress-bar-com-media-tmp\" class=\"bar\" style=\"width: 0%\"></div>' +
						'   </div>' +
						'</div>');
					document.querySelector('.media-browser').style.borderWidth = '1px';
					document.querySelector('.media-browser').style.borderStyle = 'solid';
				}
				e.preventDefault();
				openFile(f);
			}
		}
		document.querySelector('.media-browser').style.borderWidth = '1px';
		document.querySelector('.media-browser').style.borderStyle = 'solid';
	});
</script>