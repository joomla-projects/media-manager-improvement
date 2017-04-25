/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.Atum
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0
 */

(function() {
	"use strict";

	document.addEventListener('DOMContentLoaded', function() {
		var wrapper = document.getElementById('wrapper');

		/** http://stackoverflow.com/questions/18663941/finding-closest-element-without-jquery */
		function closest(el, selector) {
			var parent;

			// traverse parents
			while (el) {
				parent = el.parentElement;
				if (parent && parent['matches'](selector)) {
					return parent;
				}
				el = parent;
			}

			return null;
		}

		/**
		 * Bootstrap tooltips
		 */
		jQuery('*[rel=tooltip]').tooltip({
			html: true
		});

		// Fix toolbar and footer width for edit views
		if (document.getElementById('wrapper').classList.contains('wrapper0')) {
			document.querySelector('.subhead').style.left = 0;
			document.getElementById('status').style.marginLeft = 0;
		}
		if (document.getElementById('sidebar-wrapper') && !document.getElementById('sidebar-wrapper').getAttribute('data-hidden')) {
			/** Sidebar */
			var sidebar       = document.getElementById('sidebar-wrapper'),
			    menu          = sidebar.querySelector('#menu'),
			    logo          = document.getElementById('main-brand'),
			    logoSm        = document.getElementById('main-brand-sm'),
			    menuToggle    = document.getElementById('header').querySelector('.menu-toggle'),
			    wrapperClosed = document.querySelector('#wrapper.closed'),
			    // Apply 2nd level collapse
			    first         = menu.querySelectorAll('.collapse-level-1');

			for (var i = 0; i < first.length; i++) {
				var second = first[i].querySelectorAll('.collapse-level-1');
				for (var j = 0; j < second.length; j++) {
					if (second[j]) {
						second[j].classList.remove('collapse-level-1');
						second[j].classList.add('collapse-level-2');
					}
				}
			}

			var menuClose = function() {
				sidebar.querySelector('.collapse').classList.remove('in');
				sidebar.querySelector('.collapse-arrow').classList.add('collapsed');
			};

			// Toggle menu
			menuToggle.addEventListener('click', function(e) {
				wrapper.classList.toggle("closed");
			});

			/**
			 * Sidebar 
			 */
			jQuery('.main-nav li.parent > a').on('click', function() {
				var $self  = jQuery(this),
				    parent = $self.parent('li');

				$self.removeAttr('href');

				if (parent.hasClass('open')) {
					parent.removeClass('open');
					parent.find('li').removeClass('open');
					parent.find('ul').stop(true, false).slideUp();
				}
				else {
					var siblings = parent.siblings('li');
					parent.addClass('open');
					parent.children('ul').stop(true, false).slideDown();
					siblings.children('ul').stop(true, false).slideUp();
					siblings.removeClass('open');
					siblings.find('li').removeClass('open');
					siblings.find('ul').stop(true, false).slideUp();
				}
			});

			// Add class to sidebar container when hovered 
			jQuery('#sidebar-wrapper').hover(function(){     
		        jQuery('#wrapper').removeClass('closed');    
		    },     
		    function(){    
		        jQuery('#wrapper').addClass('closed');     
		    });

			/** Accessibility */
			var allLiEl = sidebar.querySelectorAll('ul[role="menubar"] li');
			for (var i = 0; i < allLiEl.length; i++) {
				// We care for enter and space
				allLiEl[i].addEventListener('keyup', function(e) { if (e.keyCode == 32 || e.keyCode == 13 ) e.target.querySelector('a').click(); });
			}

			// Set the height of the menu to prevent overlapping
			var setMenuHeight = function() {
				var height = document.getElementById('header').offsetHeight + document.getElementById('main-brand').offsetHeight;
				document.getElementById('menu').height = window.height - height ;
			};

			setMenuHeight();

			// Remove 'closed' class on resize
			window.addEventListener('resize', function() {
				setMenuHeight();
			});

			/** Set active class */
			var allLinks = wrapper.querySelectorAll("a.no-dropdown, a.collapse-arrow");
			var currentUrl = window.location.href.toLowerCase();

			for (var i = 0; i < allLinks.length; i++) {
				if (currentUrl === allLinks[i].href) {
					allLinks[i].classList.add('active');
					if (!allLinks[i].parentNode.classList.contains('parent')) {
						var parentLink = closest(allLinks[i], '.panel-collapse');
						/** Auto Expand First Level */
						if (parentLink){
							parentLink.parentNode.querySelector('a.collapse-arrow').classList.add('active');
							if (!wrapper.classList.contains('closed')) {
									parentLink.classList.add('in');
							}
						}
						/** Auto Expand Second Level */
						if (allLinks[i].parentNode.parentNode.parentNode.classList.contains('parent')) {
							var parentLink2 = closest(parentLink, '.panel-collapse');
							if (parentLink2){
								parentLink2.parentNode.parentNode.parentNode.querySelector('a.collapse-arrow').classList.add('active');
								if (!wrapper.classList.contains('closed')) {
									parentLink2.classList.add('in');
								}
							}
						}
					}
				}
			}

			if (typeof(Storage) !== 'undefined') {
				if (localStorage.getItem('adminMenuState') == "true") {
					menuClose();
				}
			}

		} else {
			if (document.getElementById('sidebar-wrapper')) {
				document.getElementById('sidebar-wrapper').style.display = 'none';
				document.getElementById('sidebar-wrapper').style.width = '0';
			}

			if (document.getElementsByClassName('wrapper').length)
				document.getElementsByClassName('wrapper')[0].style.paddingLeft = '0';
		}



		/**
		 * Turn radios into btn-group
		 */
		var container = document.querySelectorAll('.btn-group');
		for (var i = 0; i < container.length; i++) {
			var labels = container[i].querySelectorAll('label');
			for (var j = 0; j < labels.length; j++) {
				labels[j].classList.add('btn');
				if ((j % 2) == 1) {
					labels[j].classList.add('btn-outline-danger');
				} else {
					labels[j].classList.add('btn-outline-success');

				}
			}
		}

		var btnNotActive = document.querySelector('.btn-group label:not(.active)');
		if (btnNotActive) {
			btnNotActive.addEventListener('click', function(event) {
				var label = event.target,
					input = document.getElementById(label.getAttribute('for'));

				if (input.getAttribute('checked') !== 'checked') {
					var label = closest(label, '.btn-group').querySelector('label');
					label.classList.remove('active');
					label.classList.remove('btn-success');
					label.classList.remove('btn-danger');
					label.classList.remove('btn-primary');

					if (closest(label, '.btn-group').classList.contains('btn-group-reversed')) {
						if (!label.classList.contains('btn')) label.classList.add('btn');
						if (input.value == '') {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-primary');
						} else if (input.value == 0) {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-success');
						} else {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-danger');
						}
					} else {
						if (input.value == '') {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-primary');
						} else if (input.value == 0) {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-danger');
						} else {
							label.classList.add('active');
							label.classList.add('btn');
							label.classList.add('btn-outline-success');
						}
					}
					input.setAttribute('checked', true);
					//input.dispatchEvent('change');
				}
			});
		}

		var btsGrouped = document.querySelectorAll('.btn-group input[checked=checked]');
		for (var i = 0, l = btsGrouped.length; l>i; i++) {
			var self   = btsGrouped[i],
			    attrId = self.id;
			if (self.parentNode.parentNode.classList.contains('btn-group-reversed')) {
				var label = document.querySelector('label[for=' + attrId + ']');
				if (self.value == '') {
					label.classList.add('active');
					label.classList.add('btn');
					label.classList.add('btn-outline-primary');
				} else if (self.value == 0) {
					label.classList.add('active');
					label.classList.add('btn');
					label.classList.add('btn-outline-success');
				} else {
					label.classList.add('active');
					label.classList.add('btn');
					label.classList.add('btn-outline-danger');
				}
			} else {
				var label = document.querySelector('label[for=' + attrId + ']');
				if (self.value == '') {
					label.classList.add('active');
					label.classList.add('btn-outline-primary');
				} else if (self.value == 0) {
					label.classList.add('active');
					label.classList.add('btn');
					label.classList.add('btn-outline-danger');
				} else {
					label.classList.add('active');
					label.classList.add('btn');
					label.classList.add('btn-outline-success');
				}
			}
		}

		/**
		 * Sticky Toolbar
		 */
		var navTop;
		var isFixed = false;

		processScrollInit();
		processScroll();

		document.addEventListener('resize', processScrollInit, false);
		document.addEventListener('scroll', processScroll);

		function processScrollInit() {
			var subhead = document.getElementById('subhead');

			if (subhead) {
				navTop = document.querySelector('.subhead').offsetHeight;

				if (document.getElementById('sidebar-wrapper') && document.getElementById('sidebar-wrapper').style.display === 'none') {
					subhead.style.left = 0;
				}

				// Only apply the scrollspy when the toolbar is not collapsed
				if (document.body.clientWidth > 480) {
					document.querySelector('.subhead-collapse').style.height = document.querySelector('.subhead').style.height;
					subhead.style.width = 'auto';
				}
			}
		}

		function processScroll() {
			var subhead = document.getElementById('subhead');

			if (subhead) {
				var scrollTop = (window.pageYOffset || subhead.scrollTop)  - (subhead.clientTop || 40);

				if (scrollTop >= navTop && !isFixed) {
					isFixed = true;
					subhead.classList.add('subhead-fixed');

					if (document.getElementById('sidebar-wrapper') && document.getElementById('sidebar-wrapper').style.display === 'none') {
						subhead.style.left = 0;
					}
				} else if (scrollTop <= navTop && isFixed) {
					isFixed = false;
					subhead.classList.remove('subhead-fixed');
				}
			}
		}
	});
})();
