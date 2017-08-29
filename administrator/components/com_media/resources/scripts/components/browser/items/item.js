import Directory from "./directory.vue";
import File from "./file.vue";
import Image from "./image.vue";
import Row from "./row.vue";
import * as types from "./../../../store/mutation-types";

export default {
    functional: true,
    props: ['item'],
    render: function (createElement, context) {

        const store = context.parent.$store;
        const item = context.props.item;

        /**
         * Return the correct item type component
         */
        function itemType() {
            if (store.state.listView == 'table') {
                return Row;
            }

            let imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Render directory items
            if (item.type === 'dir') return Directory;

            // Render image items
            if (item.extension && imageExtensions.indexOf(item.extension.toLowerCase()) !== -1) {
                return Image;
            }

            // Default to file type
            return File;
        }

        /**
         * Whether or not the item is currently selected
         * @returns {boolean}
         */
        function isSelected() {
            return store.state.selectedItems.some(selected => selected.path === item.path);
        }

        /**
         * Handle the click event
         * @param event
         */
        function handleClick(event) {
            const rootPath = Joomla.getOptions('com_media').fileBaseRelativeUrl;
            const cloudRootPath = Joomla.getOptions('com_media').fileBaseRelativeUrl; //@todo return the cloud root...
            const isCloud = false; //@todo return true if file is on the cloud
	        let path = false;

            if (item.type === 'file') {
                if (isCloud) {
                    path = cloudRootPath + item.path;
                } else {
	                path = rootPath + item.path;
                }
            }

	        const data = {
		        path: path,
		        thumb: false,
		        fileType: item.mime_type ? item.mime_type : false,
		        extension: item.extension ? item.extension : false,
	        };

	        const ev = new CustomEvent('onMediaFileSelected', {"bubbles":true, "cancelable":false, "detail": data});

            window.parent.document.dispatchEvent(ev);

            // Handle clicks when the item was not selected
            if (!isSelected()) {
                // Unselect all other selected items, if the shift key was not pressed during the click event
                if (!(event.shiftKey || event.keyCode === 13)) {
                    store.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
                }
                store.commit(types.SELECT_BROWSER_ITEM, item);
                return;
            }

            // If more than one item was selected and the user clicks again on the selected item,
            // he most probably wants to unselect all other items.
            if (store.state.selectedItems.length > 1) {
                store.commit(types.UNSELECT_ALL_BROWSER_ITEMS);
                store.commit(types.SELECT_BROWSER_ITEM, item);
            }
        }

        return createElement('div', {
                'class': {
                    'media-browser-item': true,
                    selected: isSelected(),
                },
                on: {
                    click: handleClick,
                }
            },
            [
                createElement(itemType(), {
                    props: context.props,
                })
            ]
        );
    }
}
