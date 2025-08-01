const mix = require('laravel-mix');
const glob = require('glob');
const path = require("path")

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 | Full API
 | mix.js(src, output);
 | mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
 | mix.preact(src, output); <-- Identical to mix.js(), but registers Preact compilation.
 | mix.coffee(src, output); <-- Identical to mix.js(), but registers CoffeeScript compilation.
 | mix.ts(src, output); <-- TypeScript support. Requires tsconfig.json to exist in the same folder as webpack.mix.js
 | mix.extract(vendorLibs);
 | mix.sass(src, output);
 | mix.less(src, output);
 | mix.stylus(src, output);
 | mix.postCss(src, output, [require('postcss-some-plugin')()]);
 | mix.browserSync('my-site.test');
 | mix.combine(files, destination);
 | mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
 | mix.copy(from, to);
 | mix.copyDirectory(fromDir, toDir);
 | mix.minify(file);
 | mix.sourceMaps(); // Enable sourcemaps
 | mix.version(); // Enable versioning.
 | mix.disableNotifications();
 | mix.setPublicPath('path/to/public');
 | mix.setResourceRoot('prefix/for/resource/locators');
 | mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
 | mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
 | mix.babelConfig({}); <-- Merge extra Babel configuration (plugins, etc.) with Mix's default.
 | mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
 | mix.dump(); <-- Dump the generated webpack config object to the console.
 | mix.extend(name, handler) <-- Extend Mix's API with your own components.
 | mix.options({
 |   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
 |   globalVueStyles: file, // Variables file to be imported in every component.
 |   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
 |   purifyCss: false, // Remove unused CSS selectors.
 |   terser: {}, // Terser-specific options. https://github.com/webpack-contrib/terser-webpack-plugin#options
 |   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
 | });
 */

mix.setPublicPath('public');

// 1. Version all compiled assets.
mix.version();

mix.options({
    terser: {
        extractComments: false,
    },
    postCss: [
        require('tailwindcss'),
        require('autoprefixer'),
    ],
});

mix.sass('lib/PortalBundle/Resources/assets/portal/src/_styles.scss',
    'build/portal/dist/styles.css').options({
    processCssUrls: false
});

mix.js(glob.sync('{lib/PortalBundle/Resources/assets/portal/src/scripts/*.js,lib/PortalBundle/Resources/assets/portal/src/components/**/js/index.js}'),
    'build/portal/dist/bundle.js')

mix.copyDirectory('lib/PortalBundle/Resources/assets/portal/media', 'public/build/portal/media');
mix.copyDirectory('lib/PortalBundle/Resources/assets/portal/lib', 'public/build/portal/lib');

mix.copyDirectory('lib/PortalBundle/Resources/assets/portal/media', 'public/build/stimulus/media');
mix.copyDirectory('lib/PortalBundle/Resources/assets/portal/lib', 'public/build/stimulus/lib');

mix.webpackConfig({
    resolve: {
        alias: {
            '@symfony/stimulus-bridge/controllers.json': path.resolve(
                __dirname,
                'lib/PortalBundle/Resources/assets/stimulus/controllers.json'
            ),
            extensions: ['.js', '.jsx', '.json'],
        }
    }
});

mix.js('lib/PortalBundle/Resources/assets/stimulus/app.js', 'public/build/stimulus')
    .version();