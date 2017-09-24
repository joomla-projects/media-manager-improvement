Joomla = window.Joomla || {};

tinymce.PluginManager.add("Image", function(editor, url) {
	editor.addButton("Image", {
		text: 'Image',
		title: "Insert Media",
		icon: "image",
		onclick: function() {
			var options = Joomla.getOptions('xtd-image', {})
				editor.windowManager.open({
					title  : "Change or upload image",
					url    : options.tinyPath, // + editor.getContainer().id,
					width  : parent.document.body.getBoundingClientRect().width - 50,
					height : (window.innerHeight - 100),
					buttons: [{
						text   : "Insert",
						onclick: function (e) {
							console.log(e)
							// var url = editor.windowManager.getWindows()[0];
							// var findAncestorByClassName = function(el, className) {
							// 	while ((el = el.parentElement) && !el.classList.contains(className));
							// 	return el;
							// }
							//
							// var container = findAncestorByClassName(e.target, 'mce-reset');
							// console.log(container)
							// var iframe = container.querySelector('iframe');
							// console.log(iframe)
							// console.log(iframe.contentWindow)
							// console.log(iframe.contentWindow.Joomla.getImage())



Joomla.getImage(Joomla.selectedFile, editor)

							// if (Joomla.selectedFile.thumb && Joomla.selectedFile.url) {
								/**
								 * Attribute `data-mce-src` is representing the REAL url for the image
								 * Attribute `src` can be used for a thumbnail
								 */

								// editor.insertContent('<img src="' + url.getContentWindow().document.getElementById('f_url').value + '" alt="" />'), url.close()
							// }
							top.tinymce.activeEditor.windowManager.close();
						}
					}, {
						text   : "Close",
						onclick: "close"
					}]
				})
		}
	})
});