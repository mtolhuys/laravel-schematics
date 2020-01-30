const
    path = require('path'),
    webpack = require('webpack'),
    config = {
        entry: {
            'app': path.resolve('./resources/js/app.js'),
        },
        output: {
            filename: '../../public/vendor/schematics/[name].js',
            libraryTarget: 'umd',
            library: 'laravel-schematics',
            umdNamedDefine: true
        },
        module: {
            loaders: [{
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            }, {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    esModule: false,
                    loaders: {
                        scss: 'vue-style-loader!css-loader?{"url":false,"minimize":false,"sourceMap":false}!sass-loader?url=false',
                        sass: 'vue-style-loader!css-loader!sass-loader?indentedSyntax'
                    },
                }
            }]
        },
        resolve: {
            alias: {
                '@': path.resolve('resources/'),
                vue: 'vue/dist/vue.esm.js'
            },
            modules: [
                path.resolve('./'),
                path.resolve('./node_modules')
            ]
        },
        plugins: [],
    };


if (process.env.NODE_ENV === 'production') {
    config.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false,
            }
        })
    );
}

module.exports = config;

