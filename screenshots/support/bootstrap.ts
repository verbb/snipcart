import { registerPluginBootstrap } from '@verbb/craft-screenshots/api';

import { ensureSnipcartScreenshotModule } from './fixtures';

export default registerPluginBootstrap({
    id: 'snipcart',
    async setup(context) {
        await context.runCraft(['migrate/up', '--plugin=snipcart'], { allowFailure: true });
        await ensureSnipcartScreenshotModule(context.installDir);
    },
});
