module.exports = {
    locales: {
        '/': {
            lang: 'en-US',
            title: 'Snipcart Plugin Documentation',
        }
    },
    theme: 'craftdocs',
    themeConfig: {
        repo: 'workingconcept/snipcart-craft-plugin',
        docsBranch: 'master',
        docsDir: 'docs',
        sidebar: [
            // {
            //     title: 'Overview',
            //     children: [
            //         '/overview/snipcart'
            //     ]
            // },
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
            // {
            //     title: 'Templating',
            //     children: [
            //         '/templating/template-tags',
            //         '/templating/fields'
            //     ]
            // },
            {
                title: 'Examples',
                children: [
                    '/examples/config',
                    '/examples/webhooks'
                ]
            },
            {
                title: 'Testing',
                children: [
                    '/testing/overview',
                    '/testing/webhooks',
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
                    '/shipments/overview'
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
            }
            /*
            {
                title: 'Services',
                children: [
                    '/api/services/api',
                    '/api/services/carts',
                    '/api/services/customers',
                    '/api/services/discounts',
                    '/api/services/orders',
                    '/api/services/products',
                    '/api/services/shipments',
                    '/api/services/subscriptions',
                ]
            },
            {
                title: 'Models',
                children: [
                    '/api/models/settings',
                    '/api/models/abandonedcart',
                    '/api/models/address',
                    '/api/models/category',
                    '/api/models/customer',
                    '/api/models/customerstatistics',
                    '/api/models/customfield',
                    '/api/models/dimensions',
                    '/api/models/discount',
                    '/api/models/domain',
                    '/api/models/item',
                    '/api/models/notification',
                    '/api/models/order',
                    '/api/models/orderevent',
                    '/api/models/package',
                    '/api/models/paymentschedule',
                    '/api/models/plan',
                    '/api/models/product',
                    '/api/models/productvariant',
                    '/api/models/refund',
                    '/api/models/shippingevent',
                    '/api/models/shippingmethod',
                    '/api/models/subscription',
                    '/api/models/subscriptionevent',
                    '/api/models/tax',
                    '/api/models/taxesevent',
                    '/api/models/usersession',
                ]
            }
            */
        ],
        nav: [
            { text: 'Working Concept', link: 'https://workingconcept.com/' }
        ]
    }
}
