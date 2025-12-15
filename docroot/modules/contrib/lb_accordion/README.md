# Accordion for Layout Builder

This component displays expandable pairs of question/answer or header/section fields using the [YMCA Layout Builder](https://github.com/YCloudYUSA/y_lb) package.

*   Read our [instructions for getting started](https://github.com/YCloudYUSA/yusaopeny#installation).
*   [Search our documentation](https://ds-docs.y.org/docs/) for assistance.
*   [Review our Community Resources](https://ds-docs.y.org/community/) for more information.

## Requirements

This project is meant to be used with the [YMCA's Website Service distribution](https://www.drupal.org/project/openy).

## Development

The HTML and styles in this component, when combined with the base styles in [YMCA Layout Builder](https://github.com/YCloudYUSA/y_lb), should create a mostly atomic component, independent of, but overridable by, a containing Drupal theme.

### Toolchain

- [NVM](https://github.com/nvm-sh/nvm?tab=readme-ov-file#nvmrc) for maintaining a consistent Node version.
- [Bootstrap 5](https://getbootstrap.com/docs/5.3/getting-started/introduction/) for base styles
- [Webpack](https://webpack.js.org/) for compiling and bundling CSS/JS
- [PurgeCSS](https://purgecss.com/) for removing unused CSS.

### Building

1. Run `nvm install` to use the proper node version, specified by `.nvmrc`.
2. Run `npm i` to install dependencies.
3. `npm run build` to build assets.
4. `npm run watch` to watch and rebuild assets.

### Committing

- When including CSS or JS from Bootstrap, follow their [SASS Importing](https://getbootstrap.com/docs/5.3/customize/sass/#importing), [Lean SASS imports](https://getbootstrap.com/docs/5.3/customize/optimize/#lean-sass-imports) and [Lean JS](https://getbootstrap.com/docs/5.3/customize/optimize/#lean-javascript) guidelines.
- Any changes in SCSS/JS should be accompanied by:
  - the compiled build files,
  - a bump in the corresponding library string in `.libraries.yml`.
- PurgeCSS reviews your twig files and removes any CSS selectors that seem not to be used in the component. See [Safelisting](https://purgecss.com/safelisting.html) if you need ensure styles are not removed.
