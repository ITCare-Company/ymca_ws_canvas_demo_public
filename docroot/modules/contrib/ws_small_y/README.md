## Introduction

The YMCA Website Services - Small Y module is a collection of modules to customize the appearance and functionality of the distribution for small Ys.

## Requirements

- [Y Layout Builder](https://github.com/YCloudYUSA/y_lb)
- [YMCA Website Services](https://github.com/YCloudYUSA/yusaopeny)

## Installation

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## Configuration

No configuration is necessary.

## Development

This project, `ws_small_y`, manages the styling and CSS compilation for the "Small Y" used on the YMCA Website Services platform. It uses several tools to streamline the CSS development process.

### Key Features

* **SASS Compilation:** Leverages node-sass to compile SASS files into CSS, supporting Bootstrap integration and custom importers.
* **Auto-prefixing:** Ensures cross-browser compatibility by automatically adding vendor prefixes using postcss and autoprefixer.
* **Submodule Support:** Enables styling of individual modules within the project through dedicated commands.
* **Development Workflow:** Provides scripts for CSS compilation, prefixing, watching for changes, and building the final CSS output.

### Getting Started

1. **Installation:**
   `npm install`
2. **Development:**
   * `npm run css:watch`: Watches for changes in SASS files and automatically recompiles the CSS.
   * `npm run watch`: Watches for changes in SASS and JavaScript files and triggers the build process.
3. **Production Build:**
   * `npm run build`: Compiles, prefixes, and generates the final CSS output.

### Scripts

* `css:compile`: Compiles SASS files from the ./assets/scss directory to CSS in the ./assets/css directory.
* `css:prefix`: Adds vendor prefixes to CSS files in the ./assets/css directory.
* `css:submodule --module=<submodule>`: Compiles a single module in the /modules directory.
* `css:submodules`: Compiles and prefixes CSS for submodules located in the ./modules/\*/assets/scss directory.
* `css:build`: Runs the css:compile and css:prefix scripts.
* `css:watch`: Watches for changes in SASS files and triggers the css:build script.
* `watch`: Watches for changes in SASS and JavaScript files and triggers the build script.
* `build`: Runs the css:build script.
* `npx npkill -D -y`: Removes all node_modules recursively.

### Adding a new submodule

The root `package.json` can compile all submodules for our convenience.

First, create the new directory structure:

- `small_y_{component}`
  - `assets`
    - `scss`
      - `small_y_{component}.scss`

Copy `package.json` from one of the other submodules.

Then, add the new submodule in the root `package.json`:

- Add a new module to the modules config array:
  ```json
  "config": {
    "modules": [
      "small_y_accordions",
      "small_y_cards",
      "new_modulename_folder"
