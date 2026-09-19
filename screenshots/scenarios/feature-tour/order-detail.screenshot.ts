import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSnipcartFixture } from '../../support/fixtures';

let orderRoute = '/admin/snipcart/order/order-melbourne-1048';

export default defineScreenshotScenario({
    id: 'snipcart-feature-tour-order-detail',
    output: 'feature-tour/snipcart-order-detail.png',
    route: () => orderRoute,
    viewport: { width: 1440, height: 980, deviceScaleFactor: 2 },
    async setup(context) {
        orderRoute = (await seedSnipcartFixture(context)).orderRoute;
    },
    waitFor: [
        { type: 'text', text: 'Order INV-1048' },
        { type: 'text', text: 'Harbour linen throw' },
        { type: 'text', text: 'Amelia Hart' },
    ],
    steps: [{ type: 'evaluate', expression: 'document.activeElement?.blur(); window.scrollTo(0, 0);' }],
    target: {
        type: 'selector',
        selector: '#content',
        padding: { top: 0, right: 0, bottom: 0, left: 0 },
    },
    caption: 'A complete Snipcart order with customer, payment, shipping and line-item details in Craft 5.',
    intent: 'Show the current order workflow using the real plugin detail view rather than the legacy Craft 2 layout.',
});
