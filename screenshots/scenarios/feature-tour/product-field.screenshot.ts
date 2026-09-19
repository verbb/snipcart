import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSnipcartFixture } from '../../support/fixtures';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'snipcart-feature-tour-product-field',
    output: 'feature-tour/snipcart-product-field.png',
    route: () => entryEditRoute,
    viewport: { width: 1180, height: 900, deviceScaleFactor: 2 },
    async setup(context) {
        entryEditRoute = (await seedSnipcartFixture(context)).entryEditRoute;
    },
    waitFor: [
        { type: 'selector', selector: '.snipcart-product-details', state: 'visible', timeout: 30000 },
        { type: 'text', text: 'Product details' },
    ],
    steps: [
        {
            type: 'evaluate',
            expression: `
                document.activeElement?.blur();
                window.scrollTo(0, 0);
                document.querySelectorAll('.field > .heading .action-btn').forEach((button) => {
                    button.style.visibility = 'hidden';
                });
            `,
        },
    ],
    target: {
        type: 'selector',
        selector: '.field:has(.snipcart-product-details)',
        padding: { top: 12, right: 0, bottom: 12, left: 0 },
    },
    caption: 'A Product Details field holding the SKU, price, inventory and fulfilment data Snipcart needs.',
    intent: 'Show editors managing a purchasable Craft entry through the current real field UI.',
});
