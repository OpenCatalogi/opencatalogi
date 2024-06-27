const path = require("path");
const webpackConfig = require("@nextcloud/webpack-vue-config");

const buildMode = process.env.NODE_ENV;
const isDev = buildMode === "development";
webpackConfig.devtool = isDev ? "cheap-source-map" : "source-map";

webpackConfig.stats = {
  colors: true,
  modules: false,
};

const appId = "opencatalog";
webpackConfig.entry = {
  main: {
    import: path.join(__dirname, "src", "main.js"),
    filename: appId + "-main.js",
  },
  metaData: {
    import: path.join(__dirname, "src", "metaDataScript.js"),
    filename: appId + "-metaDataScript.js",
  },
  catalogi: {
    import: path.join(__dirname, "src", "catalogiScript.js"),
    filename: appId + "-catalogiScript.js",
  },
  directory: {
    import: path.join(__dirname, "src", "directoryScript.js"),
    filename: appId + "-directoryScript.js",
  },
  publications: {
    import: path.join(__dirname, "src", "publicationsScript.js"),
    filename: appId + "-publicationsScript.js",
  },
  search: {
    import: path.join(__dirname, "src", "searchScript.js"),
    filename: appId + "-searchScript.js",
  },
};

module.exports = webpackConfig;
