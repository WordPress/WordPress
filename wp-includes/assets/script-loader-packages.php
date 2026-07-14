<?php return array(
	'a11y.js' => array(
		'dependencies' => array(
			'wp-dom-ready',
			'wp-i18n'
		),
		'version' => '483af07a6016f640f456'
	),
	'annotations.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-hooks',
			'wp-i18n',
			'wp-rich-text'
		),
		'version' => 'd4fe1eeb787c2fd5ee89'
	),
	'api-fetch.js' => array(
		'dependencies' => array(
			'wp-i18n',
			'wp-private-apis',
			'wp-url'
		),
		'version' => 'b5b51750518787a93005'
	),
	'autop.js' => array(
		'dependencies' => array(
			
		),
		'version' => '9d0d0901b46f0a9027c9'
	),
	'base-styles.js' => array(
		'dependencies' => array(
			
		),
		'version' => '8ebe97b095beb7e9279b'
	),
	'blob.js' => array(
		'dependencies' => array(
			
		),
		'version' => '198af75fe06d924090d8'
	),
	'block-directory.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-editor',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices',
			'wp-plugins',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url'
		),
		'version' => '2be95e1c69e795bfc7c1'
	),
	'block-editor.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-blob',
			'wp-block-serialization-default-parser',
			'wp-blocks',
			'wp-commands',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-notices',
			'wp-preferences',
			'wp-primitives',
			'wp-priority-queue',
			'wp-private-apis',
			'wp-rich-text',
			'wp-style-engine',
			'wp-theme',
			'wp-token-list',
			'wp-upload-media',
			'wp-url',
			'wp-warning'
		),
		'version' => '71e7b84529c697129ad2'
	),
	'block-library.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-autop',
			'wp-blob',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-escape-html',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-notices',
			'wp-patterns',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-server-side-render',
			'wp-shortcode',
			'wp-style-engine',
			'wp-theme',
			'wp-upload-media',
			'wp-url',
			'wp-wordcount'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/latex-to-mathml',
				'import' => 'dynamic'
			)
		),
		'version' => '055223542466f9623198'
	),
	'block-serialization-default-parser.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'bff55bd3f1ce9df0c99c'
	),
	'block-serialization-spec-parser.js' => array(
		'dependencies' => array(
			
		),
		'version' => '9ebc5e95e1de1cabd1e6'
	),
	'blocks.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-autop',
			'wp-blob',
			'wp-block-serialization-default-parser',
			'wp-data',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-private-apis',
			'wp-rich-text',
			'wp-shortcode',
			'wp-warning'
		),
		'version' => 'dc4bdf700024000fd427'
	),
	'commands.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis'
		),
		'version' => '148d9b31ef4d2952561e'
	),
	'components.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-compose',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-escape-html',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-keycodes',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-theme',
			'wp-warning'
		),
		'version' => 'a5dafa2d4b6524d691ee'
	),
	'compose.js' => array(
		'dependencies' => array(
			'react',
			'react-jsx-runtime',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-is-shallow-equal',
			'wp-keycodes',
			'wp-priority-queue',
			'wp-private-apis',
			'wp-undo-manager'
		),
		'version' => '6176e314156a3d1f9501'
	),
	'core-commands.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-commands',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-router',
			'wp-url'
		),
		'version' => '8fc41d3503f7892d3ed8'
	),
	'core-data.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-blocks',
			'wp-compose',
			'wp-data',
			'wp-deprecated',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-private-apis',
			'wp-rich-text',
			'wp-sync',
			'wp-undo-manager',
			'wp-url',
			'wp-warning'
		),
		'version' => 'f8cc2c428aaee6d1cb92'
	),
	'customize-widgets.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-block-editor',
			'wp-block-library',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-media-utils',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-widgets'
		),
		'version' => 'f28ae391ffd39b8db426'
	),
	'data.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-deprecated',
			'wp-element',
			'wp-is-shallow-equal',
			'wp-priority-queue',
			'wp-private-apis',
			'wp-redux-routine'
		),
		'version' => 'c547bd40753de57cdc64'
	),
	'data-controls.js' => array(
		'dependencies' => array(
			'wp-api-fetch',
			'wp-data',
			'wp-deprecated'
		),
		'version' => '730061ade69d7f341014'
	),
	'date.js' => array(
		'dependencies' => array(
			'moment',
			'wp-deprecated'
		),
		'version' => '2faaf49020b2074de156'
	),
	'deprecated.js' => array(
		'dependencies' => array(
			'wp-hooks'
		),
		'version' => '990e85f234fee8f7d446'
	),
	'dom.js' => array(
		'dependencies' => array(
			'wp-deprecated'
		),
		'version' => '22d969bde5c7182cdd2f'
	),
	'dom-ready.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'a06281ae5cf5500e9317'
	),
	'edit-post.js' => array(
		'dependencies' => array(
			'media-models',
			'media-views',
			'postbox',
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-block-library',
			'wp-blocks',
			'wp-commands',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-deprecated',
			'wp-editor',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-notices',
			'wp-plugins',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-style-engine',
			'wp-theme',
			'wp-url',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '8473319428dab1c48631'
	),
	'edit-site.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-blob',
			'wp-block-editor',
			'wp-block-library',
			'wp-blocks',
			'wp-commands',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-dom-ready',
			'wp-editor',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-media-utils',
			'wp-notices',
			'wp-patterns',
			'wp-plugins',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-router',
			'wp-style-engine',
			'wp-theme',
			'wp-url',
			'wp-warning',
			'wp-widgets',
			'wp-wordcount'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '9c810e308a711a2bcdf2'
	),
	'edit-widgets.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-block-library',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-media-utils',
			'wp-notices',
			'wp-patterns',
			'wp-plugins',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url',
			'wp-viewport',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => 'ecad0946a7d57d563d4d'
	),
	'editor.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-blob',
			'wp-block-editor',
			'wp-block-serialization-default-parser',
			'wp-blocks',
			'wp-commands',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-media-utils',
			'wp-notices',
			'wp-patterns',
			'wp-plugins',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-server-side-render',
			'wp-style-engine',
			'wp-theme',
			'wp-upload-media',
			'wp-url',
			'wp-viewport',
			'wp-warning',
			'wp-wordcount'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => 'a2f8d72b2257e8f87177'
	),
	'element.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'wp-escape-html'
		),
		'version' => 'ce395381f7d64d2a6d71'
	),
	'escape-html.js' => array(
		'dependencies' => array(
			
		),
		'version' => '3f093e5cca67aa0f8b56'
	),
	'format-library.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-block-editor',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-theme',
			'wp-url'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/latex-to-mathml',
				'import' => 'dynamic'
			)
		),
		'version' => 'fc1a40ac6923d97797a4'
	),
	'hooks.js' => array(
		'dependencies' => array(
			
		),
		'version' => '7496969728ca0f95732d'
	),
	'html-entities.js' => array(
		'dependencies' => array(
			
		),
		'version' => '8c6fa5b869dfeadc4af2'
	),
	'i18n.js' => array(
		'dependencies' => array(
			'wp-hooks'
		),
		'version' => '125448662852c5e18937'
	),
	'is-shallow-equal.js' => array(
		'dependencies' => array(
			
		),
		'version' => '5d84b9f3cb50d2ce7d04'
	),
	'keyboard-shortcuts.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-data',
			'wp-element',
			'wp-keycodes'
		),
		'version' => '0dd268b2132a3f82b1d4'
	),
	'keycodes.js' => array(
		'dependencies' => array(
			'wp-i18n'
		),
		'version' => 'b156d58a707bff518176'
	),
	'list-reusable-blocks.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-blob',
			'wp-components',
			'wp-compose',
			'wp-element',
			'wp-i18n'
		),
		'version' => 'a44da9be02cdfef6e44d'
	),
	'media-utils.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-api-fetch',
			'wp-blob',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-element',
			'wp-i18n',
			'wp-keycodes',
			'wp-notices',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-theme',
			'wp-url',
			'wp-warning'
		),
		'version' => 'd8c18297580c50b16352'
	),
	'notices.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-components',
			'wp-data'
		),
		'version' => '505026883bbd05994872'
	),
	'nux.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-deprecated'
		),
		'version' => 'b0afe722eacfd6e9a364'
	),
	'patterns.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url'
		),
		'version' => '1d5dc833056614a65601'
	),
	'plugins.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-deprecated',
			'wp-element',
			'wp-hooks',
			'wp-is-shallow-equal',
			'wp-primitives'
		),
		'version' => '50bcc9bb42e4c0723a8c'
	),
	'preferences.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-a11y',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-deprecated',
			'wp-element',
			'wp-i18n',
			'wp-preferences-persistence',
			'wp-primitives',
			'wp-private-apis'
		),
		'version' => 'ba5e81b3db928d4649c6'
	),
	'preferences-persistence.js' => array(
		'dependencies' => array(
			'wp-api-fetch'
		),
		'version' => 'e8033be98338d1861bca'
	),
	'primitives.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element'
		),
		'version' => 'a5c905ec27bcd76ef287'
	),
	'priority-queue.js' => array(
		'dependencies' => array(
			
		),
		'version' => '1f0e89e247bc0bd3f9b9'
	),
	'private-apis.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'd253db066c622f144ae7'
	),
	'react-i18n.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element',
			'wp-i18n'
		),
		'version' => '9b74577dbd7e50f6b77b'
	),
	'redux-routine.js' => array(
		'dependencies' => array(
			
		),
		'version' => '64f9f5001aabc046c605'
	),
	'reusable-blocks.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-core-data',
			'wp-data',
			'wp-deprecated',
			'wp-element',
			'wp-i18n',
			'wp-notices',
			'wp-primitives',
			'wp-url'
		),
		'version' => '00a57a244d360831336a'
	),
	'rich-text.js' => array(
		'dependencies' => array(
			'wp-a11y',
			'wp-compose',
			'wp-data',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-escape-html',
			'wp-i18n',
			'wp-keycodes',
			'wp-private-apis'
		),
		'version' => '1a9947b38c18a34e1d2e'
	),
	'router.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-element',
			'wp-private-apis',
			'wp-url'
		),
		'version' => '0249e6724784b1c2583b'
	),
	'server-side-render.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-url'
		),
		'version' => '77621917ec58330ec283'
	),
	'shortcode.js' => array(
		'dependencies' => array(
			
		),
		'version' => '11742fe18cc215d3d5ab'
	),
	'style-engine.js' => array(
		'dependencies' => array(
			
		),
		'version' => '50b0461aa90d44c4123b'
	),
	'sync.js' => array(
		'dependencies' => array(
			'wp-api-fetch',
			'wp-hooks',
			'wp-private-apis'
		),
		'version' => '82121af3ec5dd7ba0296'
	),
	'theme.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-deprecated',
			'wp-element',
			'wp-private-apis'
		),
		'version' => 'f017490f1df372de8462'
	),
	'token-list.js' => array(
		'dependencies' => array(
			
		),
		'version' => '16f0aebdd39d87c2a84b'
	),
	'undo-manager.js' => array(
		'dependencies' => array(
			'wp-is-shallow-equal'
		),
		'version' => '27bb0ae036a2c9d4a1b5'
	),
	'upload-media.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-blob',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-private-apis',
			'wp-url'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/video-conversion/worker',
				'import' => 'dynamic'
			),
			array(
				'id' => '@wordpress/vips/worker',
				'import' => 'dynamic'
			)
		),
		'version' => '8f0937232c8009396479'
	),
	'url.js' => array(
		'dependencies' => array(
			
		),
		'version' => '9dd5f16a5ce37bf4ba2c'
	),
	'viewport.js' => array(
		'dependencies' => array(
			'wp-compose',
			'wp-data',
			'wp-element'
		),
		'version' => '83b39beb77dcc56c4d26'
	),
	'warning.js' => array(
		'dependencies' => array(
			
		),
		'version' => '36fdbdc984d93aee8a97'
	),
	'widgets.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-notices',
			'wp-primitives'
		),
		'version' => '2a2e101698084ec9e2c3'
	),
	'wordcount.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f53ba7c5b085d7a53357'
	)
);