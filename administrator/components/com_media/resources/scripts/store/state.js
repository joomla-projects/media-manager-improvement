// Get the disks from joomla option storage
const options = Joomla.getOptions('com_media', {});
if (options.providers === undefined || options.providers.length === 0) {
    throw new TypeError('Media providers are not defined.');
}

// Load disks from options
let loadedDisks = options.providers.map((disk) => {
    return {
        displayName : disk.displayName,
        drives : disk.adapterNames.map(
            (account, index) => {
                return {root : disk.name + '-' + index + ':/', displayName : account,}
            }
        ),
    }
});

if (loadedDisks[0].drives[0] === undefined || loadedDisks[0].drives.length === 0){
    throw new TypeError("No default media drive was found");
}

// The initial state
export default {
    // Will hold the activated filesystem disks
    disks: loadedDisks,
    // The loaded directories
    directories : loadedDisks.map((disk) => {
        return {path: disk.drives[0].root , name: disk.displayName, directories: [], files: [], directory: null}
    }),
    // The loaded files
    files: [],
    // The selected disk. Providers are ordered by plugin ordering, so we set the first provider
    // in the list as the default provider and load first drive on it as default
    selectedDirectory: options.currentPath || loadedDisks[0].drives[0].root,
    // The currently selected items
    selectedItems: [],
    // The state of create folder model
    showCreateFolderModal: false,
    // The state of the infobar
    showInfoBar: false,
}
