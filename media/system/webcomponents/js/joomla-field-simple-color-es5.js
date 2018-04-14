(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/**
 * Based on:
 * Very simple jQuery Color Picker
 * Copyright (C) 2012 Tanguy Krotoff
 * Licensed under the MIT license
 *
 * ADAPTED BY: Dimitris Grammatikogiannis
 *
 */
(function (customElements) {
	var KEYCODE = {
		TAB: 9,
		ESC: 27
	};

	var colorNames = {
		aliceblue: '#f0f8ff',
		antiquewhite: '#faebd7',
		aqua: '#00ffff',
		aquamarine: '#7fffd4',
		azure: '#f0ffff',
		beige: '#f5f5dc',
		bisque: '#ffe4c4',
		black: '#000000',
		blanchedalmond: '#ffebcd',
		blue: '#0000ff',
		blueviolet: '#8a2be2',
		brown: '#a52a2a',
		burlywood: '#deb887',
		cadetblue: '#5f9ea0',
		chartreuse: '#7fff00',
		chocolate: '#d2691e',
		coral: '#ff7f50',
		cornflowerblue: '#6495ed',
		cornsilk: '#fff8dc',
		crimson: '#dc143c',
		cyan: '#00ffff',
		darkblue: '#00008b',
		darkcyan: '#008b8b',
		darkgoldenrod: '#b8860b',
		darkgray: '#a9a9a9',
		darkgreen: '#006400',
		darkgrey: '#a9a9a9',
		darkkhaki: '#bdb76b',
		darkmagenta: '#8b008b',
		darkolivegreen: '#556b2f',
		darkorange: '#ff8c00',
		darkorchid: '#9932cc',
		darkred: '#8b0000',
		darksalmon: '#e9967a',
		darkseagreen: '#8fbc8f',
		darkslateblue: '#483d8b',
		darkslategray: '#2f4f4f',
		darkslategrey: '#2f4f4f',
		darkturquoise: '#00ced1',
		darkviolet: '#9400d3',
		deeppink: '#ff1493',
		deepskyblue: '#00bfff',
		dimgray: '#696969',
		dimgrey: '#696969',
		dodgerblue: '#1e90ff',
		firebrick: '#b22222',
		floralwhite: '#fffaf0',
		forestgreen: '#228b22',
		fuchsia: '#ff00ff',
		gainsboro: '#dcdcdc',
		ghostwhite: '#f8f8ff',
		gold: '#ffd700',
		goldenrod: '#daa520',
		gray: '#808080',
		green: '#008000',
		greenyellow: '#adff2f',
		grey: '#808080',
		honeydew: '#f0fff0',
		hotpink: '#ff69b4',
		indianred: '#cd5c5c',
		indigo: '#4b0082',
		ivory: '#fffff0',
		khaki: '#f0e68c',
		lavender: '#e6e6fa',
		lavenderblush: '#fff0f5',
		lawngreen: '#7cfc00',
		lemonchiffon: '#fffacd',
		lightblue: '#add8e6',
		lightcoral: '#f08080',
		lightcyan: '#e0ffff',
		lightgoldenrodyellow: '#fafad2',
		lightgray: '#d3d3d3',
		lightgreen: '#90ee90',
		lightgrey: '#d3d3d3',
		lightpink: '#ffb6c1',
		lightsalmon: '#ffa07a',
		lightseagreen: '#20b2aa',
		lightskyblue: '#87cefa',
		lightslategray: '#778899',
		lightslategrey: '#778899',
		lightsteelblue: '#b0c4de',
		lightyellow: '#ffffe0',
		lime: '#00ff00',
		limegreen: '#32cd32',
		linen: '#faf0e6',
		magenta: '#ff00ff',
		maroon: '#800000',
		mediumaquamarine: '#66cdaa',
		mediumblue: '#0000cd',
		mediumorchid: '#ba55d3',
		mediumpurple: '#9370db',
		mediumseagreen: '#3cb371',
		mediumslateblue: '#7b68ee',
		mediumspringgreen: '#00fa9a',
		mediumturquoise: '#48d1cc',
		mediumvioletred: '#c71585',
		midnightblue: '#191970',
		mintcream: '#f5fffa',
		mistyrose: '#ffe4e1',
		moccasin: '#ffe4b5',
		navajowhite: '#ffdead',
		navy: '#000080',
		oldlace: '#fdf5e6',
		olive: '#808000',
		olivedrab: '#6b8e23',
		orange: '#ffa500',
		orangered: '#ff4500',
		orchid: '#da70d6',
		palegoldenrod: '#eee8aa',
		palegreen: '#98fb98',
		paleturquoise: '#afeeee',
		palevioletred: '#db7093',
		papayawhip: '#ffefd5',
		peachpuff: '#ffdab9',
		peru: '#cd853f',
		pink: '#ffc0cb',
		plum: '#dda0dd',
		powderblue: '#b0e0e6',
		purple: '#800080',
		red: '#ff0000',
		rosybrown: '#bc8f8f',
		royalblue: '#4169e1',
		saddlebrown: '#8b4513',
		salmon: '#fa8072',
		sandybrown: '#f4a460',
		seagreen: '#2e8b57',
		seashell: '#fff5ee',
		sienna: '#a0522d',
		silver: '#c0c0c0',
		skyblue: '#87ceeb',
		slateblue: '#6a5acd',
		slategray: '#708090',
		slategrey: '#708090',
		snow: '#fffafa',
		springgreen: '#00ff7f',
		steelblue: '#4682b4',
		tan: '#d2b48c',
		teal: '#008080',
		thistle: '#d8bfd8',
		tomato: '#ff6347',
		turquoise: '#40e0d0',
		violet: '#ee82ee',
		wheat: '#f5deb3',
		white: '#ffffff',
		whitesmoke: '#f5f5f5',
		yellow: '#ffff00',
		yellowgreen: '#9acd32'
	};

	var JoomlaFieldSimpleColor = function (_HTMLElement) {
		_inherits(JoomlaFieldSimpleColor, _HTMLElement);

		function JoomlaFieldSimpleColor() {
			_classCallCheck(this, JoomlaFieldSimpleColor);

			// Define some variables
			var _this = _possibleConstructorReturn(this, (JoomlaFieldSimpleColor.__proto__ || Object.getPrototypeOf(JoomlaFieldSimpleColor)).call(this));

			_this.select = '';
			_this.options = [];
			_this.icon = '';
			_this.panel = '';
			_this.buttons = [];
			_this.focusableElements = null;
			_this.focusableSelectors = ['a[href]', 'area[href]', 'input:not([disabled])', 'select:not([disabled])', 'textarea:not([disabled])', 'button:not([disabled])', 'iframe', 'object', 'embed', '[contenteditable]', '[tabindex]:not([tabindex^="-"])'];
			return _this;
		}

		_createClass(JoomlaFieldSimpleColor, [{
			key: 'connectedCallback',
			value: function connectedCallback() {
				var _this2 = this;

				this.select = this.querySelector('select');

				if (!this.select) {
					throw new Error('Simple color field requires a select element');
				}

				this.options = [].slice.call(this.select.querySelectorAll('option'));

				this.select.classList.add('hidden');

				// Build the pop up
				this.options.forEach(function (option) {
					var color = option.value;
					var clss = 'swatch';

					if (color === 'none') {
						clss += ' nocolor';
						color = 'transparent';
					}
					if (option.selected) {
						clss += ' active';
					}

					var el = document.createElement('button');
					el.setAttribute('class', clss);
					el.style.backgroundColor = color;
					el.setAttribute('type', 'button');
					var a11yColor = color === 'transparent' ? _this2.textTransp : _this2.getColorName(color);
					el.innerHTML = '<span class="sr-only">' + a11yColor + '</span>';

					_this2.buttons.push(el);
				});

				// Add a close button
				var close = document.createElement('button');
				close.setAttribute('class', 'btn-close');
				close.setAttribute('type', 'button');
				close.innerHTML = this.textClose;

				this.buttons.push(close);

				var color = this.select.value;
				var clss = '';

				if (color === 'none') {
					clss += ' nocolor';
					color = 'transparent';
				}

				this.icon = document.createElement('button');

				if (clss) {
					this.icon.setAttribute('class', clss);
				}

				var uniqueId = 'simple-color-' + Math.random().toString(36).substr(2, 10);
				this.icon.setAttribute('type', 'button');
				this.icon.setAttribute('tabindex', '0');
				this.icon.style.backgroundColor = color;
				this.icon.innerHTML = '<span class="sr-only">' + this.textSelect + '</span>';
				this.icon.id = uniqueId;
				this.select.insertAdjacentElement('beforebegin', this.icon);
				this.icon.addEventListener('click', this.show.bind(this));

				this.panel = document.createElement('div');
				this.panel.classList.add('simplecolors-panel');
				this.panel.setAttribute('aria-labelledby', uniqueId);
				this.hide = this.hide.bind(this);
				this.colorSelect = this.colorSelect.bind(this);

				this.buttons.forEach(function (el) {
					if (el.classList.contains('btn-close')) {
						el.addEventListener('click', _this2.hide);
					} else {
						el.addEventListener('click', _this2.colorSelect);
					}

					_this2.panel.insertAdjacentElement('beforeend', el);
				});

				this.appendChild(this.panel);

				this.focusableElements = [].slice.call(this.panel.querySelectorAll(this.focusableSelectors.join()));

				this.keys = this.keys.bind(this);
				this.hide = this.hide.bind(this);
				this.mousedown = this.mousedown.bind(this);
			}
		}, {
			key: 'disconnectedCallback',
			value: function disconnectedCallback() {}
		}, {
			key: 'show',


			// Show the panel
			value: function show() {
				document.addEventListener('mousedown', this.hide);
				this.addEventListener('keydown', this.keys);
				this.panel.addEventListener('mousedown', this.mousedown);
				this.panel.setAttribute('data-open', '');

				var focused = this.panel.querySelector('button');

				if (focused) {
					focused.focus();
				}
			}

			// Hide panel

		}, {
			key: 'hide',
			value: function hide() {
				document.removeEventListener('mousedown', this.hide, false);
				this.removeEventListener('keydown', this.keys);

				if (this.panel.hasAttribute('data-open')) {
					this.panel.removeAttribute('data-open');
				}

				this.icon.focus();
			}
		}, {
			key: 'colorSelect',
			value: function colorSelect(e) {
				var color = '';
				var bgcolor = '';
				var clss = '';

				if (e.target.classList.contains('nocolor')) {
					color = 'none';
					bgcolor = 'transparent';
					clss = 'nocolor';
				} else {
					color = this.rgb2hex(e.target.style.backgroundColor);
					bgcolor = color;
				}

				// Reset the active class
				this.buttons.forEach(function (el) {
					if (el.classList.contains('active')) {
						el.classList.remove('active');
					}
				});

				// Add the active class to the selected button
				e.target.classList.add('active');

				this.icon.classList.remove('nocolor');
				this.icon.setAttribute('class', clss);
				this.icon.style.backgroundColor = bgcolor;

				// Hide the panel
				this.hide();

				// Change select value
				this.options.forEach(function (el) {
					if (el.selected) {
						el.removeAttribute('selected');
					}

					if (el.value === bgcolor) {
						el.setAttribute('selected', '');
					}
				});
			}
		}, {
			key: 'keys',
			value: function keys(e) {
				if (e.keyCode === KEYCODE.ESC) {
					this.hide();
				}

				if (e.keyCode === KEYCODE.TAB) {
					// Get the index of the current active element
					var focusedIndex = this.focusableElements.indexOf(document.activeElement);

					// If first element is focused and shiftkey is in use, focus last item within modal
					if (e.shiftKey && (focusedIndex === 0 || focusedIndex === -1)) {
						this.focusableElements[this.focusableElements.length - 1].focus();
						e.preventDefault();
					}
					// If last element is focused and shiftkey is not in use, focus first item within modal
					if (!e.shiftKey && focusedIndex === this.focusableElements.length - 1) {
						this.focusableElements[0].focus();
						e.preventDefault();
					}
				}
			}

			// Prevents the mousedown event from "eating" the click event.

		}, {
			key: 'mousedown',
			value: function mousedown(e) {
				e.stopPropagation();
				e.preventDefault();
			}
		}, {
			key: 'getColorName',
			value: function getColorName(value) {
				// Expand any short code
				var newValue = value;
				if (value.length === 4) {
					var tmpValue = value.split('');
					newValue = tmpValue[0] + tmpValue[1] + tmpValue[1] + tmpValue[2] + tmpValue[2] + tmpValue[3] + tmpValue[3];
				}

				for (var color in colorNames) {
					if (colorNames.hasOwnProperty(color) && newValue.toLowerCase() === colorNames[color]) {
						return color;
					}
				}

				return this.textColor + ' ' + value.replace('#', '').split('').join(', ');
			}

			/**
    * Converts a RGB color to its hexadecimal value.
    * See http://stackoverflow.com/questions/1740700/get-hex-value-rather-than-rgb-value-using-$
    */

		}, {
			key: 'rgb2hex',
			value: function rgb2hex(rgb) {
				var hex = function hex(x) {
					return ("0" + parseInt(x, 10).toString(16)).slice(-2);
				};
				var matches = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

				return '#' + hex(matches[1]) + hex(matches[2]) + hex(matches[3]);
			}
		}, {
			key: 'textSelect',
			get: function get() {
				return this.getAttribute('text-select');
			}
		}, {
			key: 'textColor',
			get: function get() {
				return this.getAttribute('text-color');
			}
		}, {
			key: 'textClose',
			get: function get() {
				return this.getAttribute('text-close');
			}
		}, {
			key: 'textTransp',
			get: function get() {
				return this.getAttribute('text-transparent');
			}
		}], [{
			key: 'observedAttributes',
			get: function get() {
				return ['text-select', 'text-color', 'text-close', 'text-transparent'];
			}
		}]);

		return JoomlaFieldSimpleColor;
	}(HTMLElement);

	customElements.define('joomla-field-simple-color', JoomlaFieldSimpleColor);
})(customElements);

},{}]},{},[1]);
