<?php return array(
	'a11y.js' => array(
		'dependencies' => array(
			'wp-dom-ready',
			'wp-i18n'
		),
		'version' => '31c6cec5a4ff7aff483d'
	),
	'annotations.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-hooks',
			'wp-i18n',
			'wp-rich-text'
		),
		'version' => '348a030f1b5717cfaba4'
	),
	'api-fetch.js' => array(
		'dependencies' => array(
			'wp-i18n',
			'wp-private-apis',
			'wp-url'
		),
		'version' => '6cf932e67c46d9f2e92d'
	),
	'autop.js' => array(
		'dependencies' => array(
			
		),
		'version' => '79bd31ed7de4b0a8ebad'
	),
	'base-styles.js' => array(
		'dependencies' => array(
			
		),
		'version' => '67fd7250ac73fa2feba5'
	),
	'blob.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'c7582a735ddd2edc9731'
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
			'wp-url',
			'wp-warning'
		),
		'version' => 'e21e66333153b642b8d0'
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
		'version' => '33ef6bf47ba6425b6067'
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
			'wp-warning',
			'wp-wordcount'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/latex-to-mathml',
				'import' => 'dynamic'
			)
		),
		'version' => '52c8ff0df7e038f93116'
	),
	'block-serialization-default-parser.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'fc209395a022242d64aa'
	),
	'block-serialization-spec-parser.js' => array(
		'dependencies' => array(
			
		),
		'version' => '345804e2c9fb36e816cd'
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
		'version' => '55dc3c9eb46b7387460a'
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
			'wp-keycodes',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis'
		),
		'version' => 'bfadfc4e96de15d6f689'
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
		'version' => 'cec341439c1d110d6bcb'
	),
	'compose.js' => array(
		'dependencies' => array(
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
		'version' => '2c882cef80a075bd7c3d'
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
		'version' => '22fd327fce4d387921f2'
	),
	'core-data.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-deprecated',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices',
			'wp-primitives',
			'wp-private-apis',
			'wp-rich-text',
			'wp-style-engine',
			'wp-undo-manager',
			'wp-url',
			'wp-warning'
		),
		'version' => 'd2b82f6369d5503e99bb'
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
			'wp-warning',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '9c1c6c17e54fcd3dc546'
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
		'version' => '005f6e82abe15d14167c'
	),
	'data-controls.js' => array(
		'dependencies' => array(
			'wp-api-fetch',
			'wp-data',
			'wp-deprecated'
		),
		'version' => '7e8f932da184d5537725'
	),
	'date.js' => array(
		'dependencies' => array(
			'moment',
			'wp-deprecated'
		),
		'version' => 'af0f72250d99230696c5'
	),
	'deprecated.js' => array(
		'dependencies' => array(
			'wp-hooks'
		),
		'version' => 'fe587bac92b7d0ef760e'
	),
	'dom.js' => array(
		'dependencies' => array(
			'wp-deprecated'
		),
		'version' => '7d3dc66e67257499cfc2'
	),
	'dom-ready.js' => array(
		'dependencies' => array(
			
		),
		'version' => '3fe927cab37bf38d6a23'
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
			'wp-warning',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '245083963525a653bdd2'
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
			'wp-router',
			'wp-style-engine',
			'wp-theme',
			'wp-url',
			'wp-warning',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '7bb3220192e4066fbff3'
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
			'wp-warning',
			'wp-widgets'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => '40eaace1a70c69302c9c'
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
		'version' => '4edb636b73d7c9cbfbfb'
	),
	'element.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'wp-escape-html'
		),
		'version' => '5a4b1aa0e42aad8ef7db'
	),
	'escape-html.js' => array(
		'dependencies' => array(
			
		),
		'version' => '87ebe53e97bba59805a5'
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
			'wp-url',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/latex-to-mathml',
				'import' => 'dynamic'
			)
		),
		'version' => 'a42172f82fe130da1d3e'
	),
	'hooks.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f0f188028580e8dc1255'
	),
	'html-entities.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'a976ff3a0f00bc2999a3'
	),
	'i18n.js' => array(
		'dependencies' => array(
			'wp-hooks'
		),
		'version' => '1dfe7db3940c23ea9216'
	),
	'is-shallow-equal.js' => array(
		'dependencies' => array(
			
		),
		'version' => '7ad271045c1fe60f5496'
	),
	'keyboard-shortcuts.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-data',
			'wp-element',
			'wp-keycodes'
		),
		'version' => '2871b9b36c2a9aef74c4'
	),
	'keycodes.js' => array(
		'dependencies' => array(
			'wp-i18n'
		),
		'version' => '73137a5f59fa0d89f095'
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
		'version' => 'da035ad6b481354267f8'
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
			'wp-html-entities',
			'wp-i18n',
			'wp-keycodes',
			'wp-notices',
			'wp-preferences',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url',
			'wp-warning'
		),
		'version' => 'fea3f56efc43721e5f8e'
	),
	'notices.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-a11y',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-deprecated',
			'wp-element',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'version' => '5625f63d0c16e55cc8d1'
	),
	'nux.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-deprecated'
		),
		'version' => '1a78c05bba2c02820a7e'
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
			'wp-url',
			'wp-warning'
		),
		'version' => '6353149476754aed8148'
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
		'version' => 'd6302ace9ca6f37a9b8c'
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
		'version' => '00228fed7f2d274a78ab'
	),
	'preferences-persistence.js' => array(
		'dependencies' => array(
			'wp-api-fetch'
		),
		'version' => '63049460a74cb385aa0a'
	),
	'primitives.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element'
		),
		'version' => '44cc5a35c7b9fe07a838'
	),
	'priority-queue.js' => array(
		'dependencies' => array(
			
		),
		'version' => '6c0aa59b65d55dfd509b'
	),
	'private-apis.js' => array(
		'dependencies' => array(
			
		),
		'version' => '0286c29a68e664667316'
	),
	'react-i18n.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element',
			'wp-i18n'
		),
		'version' => 'ba2bd3d7a3817f0494af'
	),
	'redux-routine.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'acca2b4857d83ad1790e'
	),
	'reusable-blocks.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-deprecated'
		),
		'version' => 'fd62928dce12a860a327'
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
		'version' => '5510dd3aa4c6acb55215'
	),
	'router.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-element',
			'wp-private-apis',
			'wp-url'
		),
		'version' => 'dda75cd9ff9d7e0eb19f'
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
		'version' => '93e0b8da5b06ea5406ab'
	),
	'shortcode.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f6273476300cc5fad4cd'
	),
	'style-engine.js' => array(
		'dependencies' => array(
			
		),
		'version' => '6500509f32193fe14392'
	),
	'theme.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-compose',
			'wp-deprecated',
			'wp-element',
			'wp-private-apis'
		),
		'version' => '6751c71c2384cbf4a16c'
	),
	'token-list.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'e86ab419d8302d57822c'
	),
	'undo-manager.js' => array(
		'dependencies' => array(
			'wp-is-shallow-equal'
		),
		'version' => '4554fce6276d8910a4ae'
	),
	'upload-media.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-blob',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-private-apis'
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
		'version' => 'fb949e5ab5fec61c2f35'
	),
	'url.js' => array(
		'dependencies' => array(
			
		),
		'version' => '3c0d9743cc6cd9591281'
	),
	'viewport.js' => array(
		'dependencies' => array(
			'wp-compose',
			'wp-data',
			'wp-element'
		),
		'version' => '756cf72bd7ed0f096e87'
	),
	'warning.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'a0978839debc564a6608'
	),
	'widgets.js' => array(
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
			'wp-element',
			'wp-i18n',
			'wp-notices',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'version' => 'b6e3a408f3eb2a70ff74'
	),
	'wordcount.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f0b1f0e977b2ff6e0132'
	)
);