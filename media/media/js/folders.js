/**
 * @copyright    Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JMediaManager behavior for media component
 *
 * @package        Joomla.Extensions
 * @subpackage  Media
 * @since        3.5
 */

var MediaManagerFoldersAjaxRequest = jQuery.ajax({});

function MediaManagerFoldersOnClick(elementName) {
    // @todo: Somebody stop me!
    $ = jQuery;
    $(elementName).click(function (e) {
        e.preventDefault();
        var relDir = $(elementName).data('href');

        // Show this folder as active
        $('a.ajaxInit').removeClass('active');
        $(elementName).addClass('active');

        MediaManagerFoldersAjaxRequest.abort();

        // Ajax call to reload the #filesview div with the files on that folder
        MediaManagerFoldersAjaxRequest = $.ajax({
            url: 'index.php?option=com_media',
            method: "GET",
            data: {folder: relDir, tmpl: 'component', format: 'raw', view: 'files'},
            dataType: "html"
        });

        MediaManagerFoldersAjaxRequest.done(function (msg) {
            $("#filesview").html(msg);

            // Push this information into the address bar
            window.history.pushState(null, null, "index.php?option=com_media&view=folders&folder=" + relDir);
        });

        // @todo: For some reason, this generates multiple AJAX calls per click? Check Error Console.
    });
}


jQuery(document).ready(function ($) {
    $('a.ajaxInit').each(function (index, value) {
        MediaManagerFoldersOnClick(this);
    });

    if (basepath === 'images') {
        // Ajax call to reload the #filesview div with the files on that folder
        var request = $.ajax({
            url: 'index.php?option=com_media&view=files&tmpl=component&format=raw',
            method: "GET",
            //data: { folder : "" },
            dataType: "html"
        });

        request.done(function (msg) {
            $("#filesview").html(msg);
        });

        request.fail(function (jqXHR, textStatus) {
            alert("Request failed: " + textStatus);
        });
    }
});