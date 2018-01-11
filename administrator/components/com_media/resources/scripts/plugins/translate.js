/**
 * Translate plugin
 */

import './../../../node_modules/sprintf-js/src/sprintf';

let Translate = {};

Translate.translate = function (key) {
	// Translate from Joomla text
	return Joomla.JText._(key, key);
}

Translate.sprintf = function (key) {
	// Convert the arguments to array
	var args = Array.prototype.slice.call(arguments);

	// Remove the key
	args.shift();

	// Change the placeholders from the arguments
	return vsprintf(this.translate(key), args);
}

Translate.install = function (Vue, options) {
	Vue.mixin({
		methods: {
			translate: function (key) {
				 return Translate.translate(key);
			}
		}
	})
}

export default Translate;
