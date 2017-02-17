/**
 * Api class for communication with the server
 */
export default class Api {

    /**
     * Store constructor
     * @param string baseUrl
     */
    constructor(baseUrl) {
        this._baseUrl = baseUrl;
    }

    /**
     * Get the contents of a directory from the server
     * @param dir
     * @returns {Promise}
     */
    getContents(dir) {
        // Wrap the jquery call into a real promise
        return new Promise((resolve, reject) => {
            const url = this._baseUrl + '&task=api.files&path=' + dir;
            jQuery.getJSON(url)
                .success((json) => resolve(json))
                .fail((xhr, status, error) => {
                    reject(xhr)
                })
        }).catch(this._handleError);
    }

    /**
     * Handle errors
     * @param error
     * @private
     */
    _handleError(error) {
        alert(error.status + ' ' + error.statusText);
        switch (error.status) {
            case 404:
                break;
            case 401:
            case 403:
            case 500:
                window.location.href = '/administrator';
            default:
                window.location.href = '/administrator';
        }

        throw error;
    }
}
