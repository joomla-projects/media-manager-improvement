import Directory from "./directory.vue";
import File from "./file.vue";
import Image from "./image.vue";

export default {
    functional: true,
    props: ['item'],
    render: function (createElement, context) {

        // Return the correct item type
        function itemType() {
            let item = context.props.item;
            let imageExtensions = ['jpg', 'png'];

            // Render directory items
            if (item.type === 'dir') return Directory;

            // Render image items
            if (item.extension && item.extension.indexOf(imageExtensions)) {
                return Image;
            }

            // Default to file type
            return File;
        }

        return createElement('li', {
                'class': 'media-browser-item'
            }, [
                createElement(itemType(), {
                    props: context.props
                })
            ]
        );
    }
}
