## Doing Javascript and SASS work in Joomla
### Day to Day to maintenance
There are 3 tasks that are commonly used by Joomla contributors:

1. Updating dependencies
2. Compiling SASS
3. Minifying Javascript

First things first you must install node onto your system (this will also install Node Package Manager (NPM)). If you
are running on windows this is a good tutorial on how to install node (which will automatically install NPM):
http://blog.teamtreehouse.com/install-node-js-npm-windows. If you are running on OSX we recommend installing Node with
brew.

Then navigate on command line to the Joomla install and run the following command

`npm install`

This will install all node dependencies onto your system. Then there are 3 easy commands

* To update dependencies to the version in grunt_settings.yaml run `npm run update-dependencies`
* To compile the SASS run `npm run compile-sass`
* To minify the javascript run `npm run compile-js`


### Maintainer's Area (TODO: This list of commands needs updating)
- Running `grunt` will automatically update all the assets. Make sure that you have updated the grunt-settings.yaml file in
order to update the libraries!!!

The full list of other Grunt Tasks available are:

- `grunt clean:assets`.................clears the media/vendor folder
- `grunt shell:update`.................will update all the npm packages to the version defined in /dev/assets/package.json
- `grunt curl:cmGet`...................fetches latest codemirror to assets/tmp folder
- `grunt unzip:cmUnzip`................extracts the downladed codemirror zip to assets/tmp/codemirror folder
- `grunt gitclone:cloneCombobox`.......fetches latest combobox to assets/tmp folder
- `grunt gitclone:cloneCropjs`.........fetches latest combobox to assets/tmp folder
- `grunt gitclone:cloneAutojs`.........fetches latest autocomplete to assets/tmp folder
- `grunt concat:someFiles`.............concatenates some codemirror files
- `grunt copy:fromSource`..............copy everything to media/vendor/*
- `grunt uglify:allJs`.................minifies various javascripts
- `grunt cssmin:allCss`................minifies various stylesheets
- `grunt text-scss`....................validate/lint the template SCSS
- `grunt compile`......................minifies all template JS, lints SCSS then compiles it

For running sass linting we require that ruby is installed on the system.

- Install Ruby:  https://rubyinstaller.org
- Run: `gem install scss_lint` to install the linter

Will update the following external sourced static assets that Joomla is using and is defined in /grunt_settings.yaml


The following are always fetched with curl (no module available)

- Jcrop
- Autocomplete
