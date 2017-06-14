// Get the disks from joomla option storage
const options = Joomla.getOptions('com_media', {});
if (options.providers === undefined || options.providers.length === 0) {
    throw new TypeError('Media providers are not defined.');
}


console.log(options);
// The initial state
export default {
    // The loaded directories
    directories: options.providers.map((disk) => {
        return {path: disk.name + ':/', name: disk.displayName, directories: [], files: [], directory: null}
    }),
    // The loaded files
    files: [],
    // The selected disk. Providers are ordered by plugin ordering, so we set the first provider
    // in the list as the default provider.
    selectedDirectory: options.providers[0].name + ':/',
    // The currently selected items
    selectedItems: [],
    // The currently selected items
    selectedItems: [],
}