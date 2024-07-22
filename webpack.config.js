const path = require("path");
const webpackConfig = require("@nextcloud/webpack-vue-config");

const buildMode = process.env.NODE_ENV;
const isDev = buildMode === "development";
webpackConfig.devtool = isDev ? "cheap-source-map" : "source-map";

webpackConfig.stats = {
  colors: true,
  modules: false,
};

const appId = "opencatalogi";
webpackConfig.entry = {
  main: {
    import: path.join(__dirname, "src", "main.ts"),
    filename: appId + "-main.js",
  },
};

// Add the ts-loader for TypeScript files
webpackConfig.module.rules.push({
    test: /\.ts$/,
    loader: "ts-loader",
    exclude: /node_modules/,
    options: {
        appendTsSuffixTo: [/\.vue$/],
    },
});

// Resolve .ts and .tsx extensions
webpackConfig.resolve.extensions.push(".ts", ".tsx");

module.exports = webpackConfig;
