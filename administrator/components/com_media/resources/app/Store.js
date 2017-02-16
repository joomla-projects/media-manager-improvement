/**
 * Store
 */
export default class Store {

    /**
     * Store constructor
     * @param boolean isDebug
     */
    constructor(isDebug = false) {
        this._isDebug = isDebug;

        /* The com_media initial application state */
        this.state = {
            /* Whether or not the app is currently in loading state */
            isLoading: false,
            /* The currently selected directory */
            currentDir: '/',
            // The contents of the currently selected directory
            currentDirContents: [],
            /* Array of selected items */
            selectedItems: [],
        };
    }

    /**
     * Set the current directory
     * @param string dir
     */
    setCurrentDir(dir) {
        this._isDebug && console.log('STORE: setCurrentDir', dir);
        this.state.currentDir = dir;
    }

    /**
     * Set the contents of the currently selected directory
     * @param contents
     */
    setCurrentDirContents(contents) {
        this._isDebug && console.log('STORE: setCurrentDirContents', contents);
        this.state.currentDirContents = contents;
    }

    /**
     * Add a new item to the store
     * @param item
     */
    selectItem(item) {
        this._isDebug && console.log('STORE: selectItem', item);
        if (this.state.selectedItems.indexOf(item) !== -1) {
            this.state.selectedItems.push(item);
        }
    }

    /**
     * Remove an item from the store
     * @param item
     */
    unselectItem(item) {
        this._isDebug && console.log('STORE: unSelectItem', item);
        const index = this.state.selectedItems.indexOf(item);
        if (index !== -1) {
            this.state.selectedItems.splice(index, 1);
        }
    }
}
