// Get the disks from joomla option storage
const options = Joomla.getOptions('com_media', {});
if (options.providers === undefined || options.providers.length === 0) {
    throw new TypeError('Media providers are not defined.');
}

// The initial state
export default {
    // Will hold the activated filesystem disks
    disks: options.providers.map((provider) => {
        let result = {};
        result.displayName = provider.displayName;
        result.adapters = [];

        for(let i = 0; i < provider.adapterNames.length; i++)
        {
            let adapter = {
                root : provider.name + '-' + i + ':/',
                displayName : provider.adapterNames[i],
            };

            result.adapters.push(adapter);
        }

        return result;
    }),
    // The loaded directories
    directories: options.providers.map((disk) => {
        return {path: disk.name + '-0:/', name: disk.displayName, directories: [], files: [], directory: null}
    }),
    // The loaded files
    files: [],
    // The selected disk. Providers are ordered by plugin ordering, so we set the first provider
    // in the list as the default provider.
    selectedDirectory: options.providers[0].name + '-0:/',
    // The currently selected items
    selectedItems: [],
    // The state of create folder model
    showCreateFolderModal: false
}
