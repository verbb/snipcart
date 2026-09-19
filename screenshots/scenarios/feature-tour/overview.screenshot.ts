import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSnipcartFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'snipcart-feature-tour-overview',
    output: 'feature-tour/snipcart-overview.png',
    route: '/admin/snipcart',
    viewport: { width: 1440, height: 1200, deviceScaleFactor: 2 },
    setup: seedSnipcartFixture,
    waitFor: [
        { type: 'selector', selector: '#overview-chart svg', state: 'visible', timeout: 30000 },
        { type: 'text', text: 'INV-1048' },
        { type: 'text', text: 'Amelia Hart' },
    ],
    steps: [
        { type: 'wait', waitFor: { type: 'timeout', ms: 500 } },
        {
            type: 'evaluate',
            expression: `
                document.activeElement?.blur();
                window.scrollTo(0, 0);
            `,
        },
    ],
    target: {
        type: 'selector',
        selector: '#content',
        padding: { top: 0, right: 0, bottom: 0, left: 0 },
    },
    caption: 'Snipcart store performance, recent orders and top customers inside the Craft 5 control panel.',
    intent: 'Show the current operational overview with genuine plugin charts, statistics and linked commerce records.',
});
