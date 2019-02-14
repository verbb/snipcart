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
        docsBranch: 'docs',
        docsDir: 'docs',
        sidebar: [
            {
                title: 'Getting Started',
                collapsable: false,
                children: [
                    '/setup/first-steps',
                    '/setup/products',
                    '/setup/frontend',
                    '/setup/notifications',
                    '/setup/order-fields',
                ]
            },
            {
                title: 'Using Webhooks',
                collapsable: false,
                children: [
                    '/webhooks/when',
                    '/webhooks/setup',
                    '/webhooks/events'
                ]
            },
            // {
            //     title: 'Templating',
            //     collapsable: false,
            //     children: [
            //         '/templating/template-tags',
            //         '/templating/fields'
            //     ]
            // },
            {
                title: 'Examples',
                collapsable: false,
                children: [
                    '/examples/config',
                    '/examples/webhooks'
                ]
            },
            {
                title: 'Troubleshooting',
                collapsable: false,
                children: [
                    '/troubleshooting/logging',
                    '/troubleshooting/testing-webhooks',
                    //'/troubleshooting/common-problems',
                    '/troubleshooting/getting-help'
                ]
            },
            {
                title: 'Shipments',
                collapsable: false,
                children: [
                    '/shipments/overview'
                ]
            },
            {
                title: 'Template Reference',
                collapsable: false,
                children: [
                    '/templating/template-tags',
                    '/templating/fields',
                ]
            },
            {
                title: 'Developer Reference',
                collapsable: false,
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
            { text: 'Working Concept', link: 'https://workingconcept.com/plugins/snipcart' }
        ]
    }
}
