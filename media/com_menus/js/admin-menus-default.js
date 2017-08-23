/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function(){

		if (Joomla.getOptions('menus-default')) {
			var items = Joomla.getOptions('menus-default').items;

			items.forEach(function(item) {
				console.log(item)
				window['jSelectPosition_' + item] = function (name) {
					document.getElementById(item).value = name;
					jQuery(".modal").modal("hide");
				}
			})
		}

		jQuery(".modal").on("hidden.bs.modal", function () {
			setTimeout(function(){
				window.parent.location.reload();
			},1000);
		});
	});
})();

(function (originalFn) {
    Joomla.submitform = function(task, form) {
        originalFn(task, form);
        if (task === "menu.exportXml") {
            document.adminForm.task.value = "";
        }
    };
})(Joomla.submitform);
