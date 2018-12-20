module.exports = {
    locales: {
        '/': {
            lang: 'en-US',
            title: 'Snipcart Plugin Documentation',
        }
    },
    themeConfig: {
        docsDir: 'docs',
        sidebar: [
            {
                title: 'Overview',
                children: [
                    '/overview/snipcart'
                ]
            },
            {
                title: 'Getting Started',
                children: [
                    '/setup/first-steps',
                    '/setup/front-end',
                    '/setup/products',
                ]
            },
            {
                title: 'Using Webhooks',
                children: [
                    '/webhooks/when',
                    '/webhooks/setup',
                    '/webhooks/events'
                ]
            },
            {
                title: 'Templating',
                children: [
                    '/'
                ]
            },
            {
                title: 'Examples',
                children: [
                    '/'
                ]
            },
            {
                title: 'Testing',
                children: [
                    '/testing/overview',
                    '/testing/going-live'
                ]
            },
            {
                title: 'Troubleshooting',
                children: [
                    '/troubleshooting/logging',
                    '/troubleshooting/common-problems',
                    '/troubleshooting/getting-help'
                ]
            },
            {
                title: 'Shipments',
                children: [
                    '/'
                ]
            },
            {
                title: 'Developer Reference',
                children: [
                    '/dev/overview',
                    '/dev/events',
                    '/dev/services',
                    '/dev/models',
                ]
            },
        ],
        nav: [
            { text: 'GitHub Repo', link: 'https://github.com/workingconcept/snipcart-craft-plugin' },
            { text: 'Working Concept', link: 'https://workingconcept.com/' }
        ]
    }
}
