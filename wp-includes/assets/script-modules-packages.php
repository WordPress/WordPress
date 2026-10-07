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
		'version' => 'e5fcd7e304ead0fc9779'
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
		'version' => '25ee935fd6c67371d0f3'
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
		'version' => 'fde7e3309a48521aed56'
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
		'version' => 'f6c3c0c637fe0ed2dbd2'
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
		'version' => '9e49b9edcfc16dc5203c'
	),
	'interactivity/index.js' => array(
		'dependencies' => array(
			
		),
		'version' => 'b0929e0bd08daccaa778'
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
		'version' => '711aa380e66c57009b7a'
	),
	'latex-to-mathml/index.js' => array(
		'dependencies' => array(
			
		),
		'version' => '662cb0e7f68d2a21cd68'
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
		'version' => 'e50dce77f58e6e3ffa9b'
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
		'version' => '4d3627c45251ceb4f9a2'
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
		'version' => '7e33cd8c4128731126e8'
	),
	'widget-primitives/index.js' => array(
		'dependencies' => array(
			'react-jsx-runtime',
			'wp-element'
		),
		'version' => '3047de39283129372300'
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
		'version' => '02fa947b818b1e4c07fd'
	)
);