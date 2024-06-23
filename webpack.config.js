const path = require("path");
const webpackConfig = require("@nextcloud/webpack-vue-config");

const buildMode = process.env.NODE_ENV;
const isDev = buildMode === "development";
webpackConfig.devtool = isDev ? "cheap-source-map" : "source-map";

webpackConfig.stats = {
  colors: true,
  modules: false,
};

const appId = "dsonextcloud";
webpackConfig.entry = {
  main: {
    import: path.join(__dirname, "src", "mainScript.js"),
    filename: appId + "-mainScript.js",
  },
  taken: {
    import: path.join(__dirname, "src", "takenScript.js"),
    filename: appId + "-takenScript.js",
  },
  zaken: {
    import: path.join(__dirname, "src", "zakenScript.js"),
    filename: appId + "-zakenScript.js",
  },
	zaakTypen: {
		import: path.join(__dirname, "src", "zaakTypenScript.js"),
		filename: appId + "-zaakTypenScript.js",
	},
	klanten: {
		import: path.join(__dirname, "src", "klantenScript.js"),
		filename: appId + "-klantenScript.js",
	},
	contactMomenten: {
		import: path.join(__dirname, "src", "contactMomentenScript.js"),
		filename: appId + "-contactMomentenScript.js",
	},

  zakenDetail: {
    import: path.join(__dirname, "src", "zakenDetailScript.js"),
    filename: appId + "-zakenDetailScript.js",
  },
};

module.exports = webpackConfig;
