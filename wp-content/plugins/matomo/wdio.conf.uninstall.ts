// wdio config for e2e tests that uninstall matomo

import { config as baseConfig } from './wdio.conf.js';

export const config = {
  ...baseConfig,
  maxInstances: 1,
  specs: [
    // in a separate folder so we can ensure it is run after the others
    './tests/e2e-uninstall/*.e2e.ts',
  ],
  exclude: [],
  onPrepare: null,
};
