(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {
		var list = document.querySelectorAll('a[data-toggle="tab"]');

		list.forEach(function(item) {
			item.addEventListener('shown.bs.tab', function(event) {
				if (event.relatedTarget) {
					EventBus.dispatch('onDeactivate', this, event.relatedTarget.hash.replace('#attrib-', ''), document.getElementById('media-edit-file'));
				}
				EventBus.dispatch('onActivate', this, event.target.hash.replace('#attrib-', ''), document.getElementById('media-edit-file'));
			})
		})
	});
})();