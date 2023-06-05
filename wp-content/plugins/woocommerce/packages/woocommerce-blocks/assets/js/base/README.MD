# Base Components/Context/Hooks

Base components are designed to be used on the frontend of a store. Due to this, we need to avoid using heavy WordPress externals as dependencies (wp-components, wp-block-editor, etc). To get around this, import from a local package instead.

e.g. Instead of importing from `@wordpress/components`, use:

```js
import { Component } from 'wordpress-components';
```

Check the built `*.assets.php` files to ensure extra dependencies aren't being added to the build.
