<?php return array(
	'a11y/index.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f62f48906198ea8abc60'
	),
	'abilities/index.js' => array(
		'dependencies' => array(
			'wp-data',
			'wp-i18n'
		),
		'version' => '9448bb3053516a39086d'
	),
	'block-editor/utils/fit-text-frontend.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'f465b12ad4eba72e96bb'
	),
	'block-library/accordion/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => '6d4bb8df9c7539843ad3'
	),
	'block-library/cover/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'ef26735bd1dbc6c830a0'
	),
	'block-library/file/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'a9114a756e418400594c'
	),
	'block-library/image/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => '266101a88e5b01402a65'
	),
	'block-library/navigation/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => '5381c0a3642be76565c7'
	),
	'block-library/playlist/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'dcead465d87ec21eea99'
	),
	'block-library/query/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			),
			array(
				'id' => '@wordpress/interactivity-router',
				'import' => 'dynamic'
			)
		),
		'version' => '7a4ec5bfb61a7137cf4b'
	),
	'block-library/search/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => '38bd0e230eaffa354d2a'
	),
	'block-library/tabs/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => '0684c8bb3dc8582b92ea'
	),
	'block-library/video/view.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'a75554c34e6430cbfdd5'
	),
	'boot/index.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-dom',
			'wp-editor',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-notices',
			'wp-plugins',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			),
			array(
				'id' => '@wordpress/lazy-editor',
				'import' => 'dynamic'
			),
			array(
				'id' => '@wordpress/route',
				'import' => 'static'
			)
		),
		'version' => 'd8489a9113a214b5278c'
	),
	'connectors/index.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-private-apis'
		),
		'version' => '2023d06e79a4d7c81c30'
	),
	'core-abilities/index.js' => array(
		'dependencies' => array(
			'wp-api-fetch',
			'wp-url'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/abilities',
				'import' => 'static'
			)
		),
		'version' => '012760fd849397dd0031'
	),
	'core-actions/page.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-components',
			'wp-core-data',
			'wp-data',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices'
		),
		'version' => 'b7ea80c796cbace5c19c'
	),
	'core-actions/post_type_supports.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-api-fetch',
			'wp-blocks',
			'wp-components',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices',
			'wp-patterns',
			'wp-primitives',
			'wp-private-apis',
			'wp-url'
		),
		'version' => 'c68e5c9a605e907ca528'
	),
	'core-actions/wp_block.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-blob',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-patterns',
			'wp-primitives',
			'wp-private-apis'
		),
		'version' => 'fc39ba4261c0c13a18da'
	),
	'core-actions/wp_template_part.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-blocks',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-notices',
			'wp-patterns',
			'wp-private-apis'
		),
		'version' => 'c8126a6b5289ae455bf2'
	),
	'core-fields/attachment.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => '3a28135598a5699ab7f1'
	),
	'core-fields/page.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => 'cc78f2192db7a69e99c1'
	),
	'core-fields/post_type_supports.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-media-utils',
			'wp-notices',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-url',
			'wp-warning',
			'wp-wordcount'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => '4a571c13b124fb334523'
	),
	'core-fields/root_site.js' => array(
		'dependencies' => array(
			'wp-html-entities',
			'wp-media-utils',
			'wp-private-apis'
		),
		'version' => 'e94092be498b0e24d377'
	),
	'core-fields/wp_block.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-compose',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => '98921ca4890d4baead31'
	),
	'core-fields/wp_template.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => 'd94f646d08e9c4a6ba08'
	),
	'core-fields/wp_template_part.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			)
		),
		'version' => 'b528ca2741b1c38e32ff'
	),
	'dashboard-init/index.js' => array(
		'dependencies' => array(
			'react',
			'wp-core-data',
			'wp-data',
			'wp-dom',
			'wp-element',
			'wp-i18n'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/widget-primitives',
				'import' => 'static'
			)
		),
		'version' => 'e8caf739ce241e22ead9'
	),
	'edit-site-init/index.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-data',
			'wp-element',
			'wp-preferences',
			'wp-primitives'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/boot',
				'import' => 'static'
			)
		),
		'version' => 'cef91e6c10beba154e91'
	),
	'interactivity/index.js' => array(
		'dependencies' => array(
			
		),
		'version' => '6cd6345c25ad7329ab4f'
	),
	'interactivity-router/full-page.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/interactivity-router',
				'import' => 'dynamic'
			)
		),
		'version' => '5c07cd7a12ae073c5241'
	),
	'interactivity-router/index.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'dynamic'
			),
			array(
				'id' => '@wordpress/interactivity',
				'import' => 'static'
			)
		),
		'version' => 'e9218417e1212e9055c6'
	),
	'latex-to-mathml/index.js' => array(
		'dependencies' => array(
			
		),
		'version' => '4ec5f96f5cf884f76516'
	),
	'latex-to-mathml/loader.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/latex-to-mathml',
				'import' => 'dynamic'
			)
		),
		'version' => '4f37456af539bd3d2351'
	),
	'lazy-editor/index.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-core-data',
			'wp-data',
			'wp-editor',
			'wp-element',
			'wp-i18n',
			'wp-private-apis',
			'wp-style-engine'
		),
		'version' => 'b2631e2d72714c2d1dfd'
	),
	'route/index.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-private-apis'
		),
		'version' => 'ef851c0f4dda0d1dd359'
	),
	'video-conversion/loader.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/video-conversion/worker',
				'import' => 'dynamic'
			)
		),
		'version' => '81a2ac6d5619086278fd'
	),
	'video-conversion/worker.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'fa777d9ac60940d2e501'
	),
	'vips/loader.js' => array(
		'dependencies' => array(
			
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/vips/worker',
				'import' => 'dynamic'
			)
		),
		'version' => '07c9acb45d3e5d81829a'
	),
	'vips/worker.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'f731e8a2508597df70e0'
	),
	'widget-primitives/index.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element'
		),
		'version' => '355066e7b5d458a5968d'
	),
	'workflow/index.js' => array(
		'dependencies' => array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-keyboard-shortcuts',
			'wp-keycodes',
			'wp-primitives',
			'wp-private-apis',
			'wp-theme',
			'wp-warning'
		),
		'module_dependencies' => array(
			array(
				'id' => '@wordpress/a11y',
				'import' => 'static'
			),
			array(
				'id' => '@wordpress/abilities',
				'import' => 'static'
			),
			array(
				'id' => '@wordpress/core-abilities',
				'import' => 'dynamic'
			)
		),
		'version' => 'e351e6a92196af96b818'
	)
);