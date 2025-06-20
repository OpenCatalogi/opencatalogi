// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'Open Catalogi',
  tagline: 'Open source catalog management for government organizations',
  url: 'https://documentatie.opencatalogi.nl',
  baseUrl: '/',
  
  // GitHub pages deployment config
  organizationName: 'conductionnl', 
  projectName: 'opencatalogi',
  trailingSlash: false,

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  // Even if you don't use internalization, you can use this field to set useful
  // metadata like html lang
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  // Add markdown support for mermaid diagrams
  markdown: {
    mermaid: true,
  },

  // Add mermaid theme to the list of themes
  themes: ['@docusaurus/theme-mermaid'],
  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          sidebarPath: require.resolve('./sidebars.js'),
          editUrl:
            'https://github.com/conductionnl/opencatalogi/tree/main/website/',
        },
        blog: false,
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      }),
    ],
    [
      'redocusaurus',
      {
        // Plugin Options for loading OpenAPI files
        specs: [
          // Pass it a path to a local OpenAPI YAML file
          {
            // Redocusaurus will automatically bundle your spec into a single file during the build
            spec: 'static/oas/open-catalogi.yaml',
            route: '/api',
          },
        ],
        // Theme Options for modifying how redoc renders them
        theme: {
          // Change with your site colors
          primaryColor: '#1890ff',
        },
      },
    ]
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      // Add mermaid configuration to themeConfig
      mermaid: {
        theme: {light: 'neutral', dark: 'forest'},
      },
      navbar: {
        title: 'Open Catalogi',
        logo: {
          alt: 'Open Catalogi Logo',
          src: 'img/logo.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'tutorialSidebar',
            position: 'left',
            label: 'Documentation',
          },
          {
            href: '/api',
            label: 'API Documentation',
            position: 'right',
          },
          {
            href: 'https://github.com/conductionnl/opencatalogi',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Docs',
            items: [
              {
                label: 'Documentation',
                to: '/docs',
              },
            ],
          },
          {
            title: 'Community',
            items: [
              {
                label: 'GitHub',
                href: 'https://github.com/conductionnl/opencatalogi',
              },
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} Open Catalogi. Built with Docusaurus.`,
      },
      prism: {
        theme: require('prism-react-renderer/themes/github'),
        darkTheme: require('prism-react-renderer/themes/dracula'),
      },
    }),
};

module.exports = config;