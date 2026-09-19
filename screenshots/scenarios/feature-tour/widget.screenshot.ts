import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSnipcartFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'snipcart-feature-tour-widget',
    output: 'feature-tour/snipcart-widget.png',
    route: '/admin/dashboard',
    viewport: { width: 1280, height: 900, deviceScaleFactor: 2 },
    setup: seedSnipcartFixture,
    waitFor: [
        { type: 'selector', selector: '.orders-chart svg', state: 'visible', timeout: 30000 },
        { type: 'text', text: 'Snipcart Weekly Sales' },
    ],
    steps: [
        { type: 'wait', waitFor: { type: 'timeout', ms: 500 } },
        {
            type: 'evaluate',
            expression: `
                document.activeElement?.blur();
                window.scrollTo(0, 0);
                const chart = document.querySelector('.orders-chart svg');
                if (chart) {
                    chart.style.overflow = 'visible';
                }

                document.documentElement.style.background = '#fff';
                document.body.style.background = '#fff';
                document.querySelectorAll('#main, #content-container, #content, #dashboard-grid').forEach((element) => {
                    element.style.background = '#fff';
                });

                const widgetItem = document.querySelector('.orders-chart')?.closest('.item');
                document.querySelectorAll('.widget:not(:has(.orders-chart))').forEach((widget) => {
                    widget.style.visibility = 'hidden';
                });
                widgetItem?.parentElement?.querySelectorAll(':scope > .item').forEach((item) => {
                    if (item !== widgetItem) {
                        item.style.visibility = 'hidden';
                    }
                });
            `,
        },
    ],
    target: {
        type: 'selector',
        selector: '.widget:has(.orders-chart)',
        padding: 0,
    },
    caption: 'A live Snipcart sales chart on the Craft dashboard.',
    intent: 'Show that store performance can stay visible alongside the editor’s other Craft dashboard widgets.',
});
