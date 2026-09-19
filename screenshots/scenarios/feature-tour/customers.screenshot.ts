import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSnipcartFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'snipcart-feature-tour-customers',
    output: 'feature-tour/snipcart-customers.png',
    route: '/admin/snipcart/customers',
    viewport: { width: 1440, height: 900, deviceScaleFactor: 2 },
    setup: seedSnipcartFixture,
    waitFor: [
        { type: 'selector', selector: '#customers', state: 'visible', timeout: 30000 },
        { type: 'text', text: 'Amelia Hart' },
        { type: 'text', text: 'Sofia Rossi' },
    ],
    steps: [{ type: 'evaluate', expression: 'document.activeElement?.blur(); window.scrollTo(0, 0);' }],
    target: {
        type: 'selector',
        selector: '#customers',
        padding: { top: 98, right: 20, bottom: 64, left: 20 },
    },
    caption: 'Customer accounts, order counts and lifetime spend available in Craft 5.',
    intent: 'Show the current customer index with realistic commerce activity and direct links to each customer.',
});
