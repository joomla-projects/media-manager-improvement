export default {
    methods: {
        /* Get the file extension */
        getFileExtension(fileName) {
            return fileName.split('.').pop();
        }
    }
};