import { readFileSync } from 'node:fs';
import { copyFile, mkdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

export type SnipcartFixture = {
    entryEditRoute: string;
    orderRoute: string;
};

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-snipcart.php'), 'utf8');

export async function seedSnipcartFixture(context: ScreenshotSetupContext): Promise<SnipcartFixture> {
    await ensureSnipcartScreenshotModule(context.installDir);

    await writeFile(join(context.installDir, 'config/snipcart.php'), `<?php
return [
    'publicApiKey' => 'screenshot-public-key',
    'secretApiKey' => 'screenshot-secret-key',
    'defaultCurrency' => 'aud',
    'enabledCurrencies' => ['aud'],
    'cacheResponses' => false,
];
`);

    const output = await context.runCraftScript(seedScript, { label: 'seed-snipcart' });
    const fixture = JSON.parse(output.trim()) as SnipcartFixture;

    if (!fixture.entryEditRoute || !fixture.orderRoute) {
        throw new Error(`Invalid Snipcart fixture payload: ${output}`);
    }

    return fixture;
}

export async function ensureSnipcartScreenshotModule(installDir: string): Promise<void> {
    const moduleDir = join(installDir, 'modules/snipcartscreenshots');
    await mkdir(moduleDir, { recursive: true });

    for (const filename of ['Module.php', 'ScreenshotOrders.php', 'ScreenshotCustomers.php', 'ScreenshotData.php']) {
        await copyFile(join(supportDir, 'module', filename), join(moduleDir, filename));
    }

    const appPath = join(installDir, 'config/app.php');
    let contents = await readFile(appPath, 'utf8');

    if (contents.includes("'snipcartScreenshots'")) {
        return;
    }

    contents = contents.replace(
        /return\s*\[\s*'id'\s*=>\s*App::env\('CRAFT_APP_ID'\)\s*\?:\s*'CraftCMS',\s*\];/s,
        `require_once dirname(__DIR__) . '/modules/snipcartscreenshots/Module.php';
require_once dirname(__DIR__) . '/modules/snipcartscreenshots/ScreenshotOrders.php';
require_once dirname(__DIR__) . '/modules/snipcartscreenshots/ScreenshotCustomers.php';
require_once dirname(__DIR__) . '/modules/snipcartscreenshots/ScreenshotData.php';

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',
    'modules' => [
        'snipcartScreenshots' => \\modules\\snipcartscreenshots\\Module::class,
    ],
    'bootstrap' => ['snipcartScreenshots'],
];`,
    );

    if (!contents.includes("'snipcartScreenshots'")) {
        throw new Error('Failed to register the Snipcart screenshot module.');
    }

    await writeFile(appPath, contents);
}
