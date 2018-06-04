/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

Joomla = window.Joomla || {};

(function (Joomla) {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    Joomla.twoFactorMethodChange = function () {
      var method = document.getElementById('jform_twofactor_method');
      if (method) {
        var selectedPane = 'com_users_twofactor_' + method.value;
        var twoFactorForms = [].slice.call(document.querySelectorAll('#com_users_twofactor_forms_container > div'));
        twoFactorForms.forEach(function (value) {
          var id = value.id;

          if (id !== selectedPane) {
            document.getElementById(id).style.display = 'none';
          } else {
            document.getElementById(id).style.display = 'block';
          }
        });
      }
    };
  });
})(Joomla);
