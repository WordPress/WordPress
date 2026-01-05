<?php return array(
  'a11y/index.js' => array(
    'dependencies' => array(
      
    ),
    'version' => '1c371cb517a97cdbcb9f'
  ),
  'abilities/index.js' => array(
    'dependencies' => array(
      'wp-data',
      'wp-i18n'
    ),
    'version' => 'bd07cd6be9d3678c2a45'
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
    'version' => '2f00eb94b5ef309f39eb'
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
    'version' => '2af01b43d30739c3fb8d'
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
    'version' => '7d4d261d10dca47ebecb'
  ),
  'block-library/form/view.js' => array(
    'dependencies' => array(
      
    ),
    'version' => '5542f8ad251fe43ef09e'
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
    'version' => '3aa348554e724dae4f1f'
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
    'version' => '7437ed5c45ee57daf02c'
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
    'version' => 'c08655def5ed243c4d65'
  ),
  'boot/index.js' => array(
    'dependencies' => array(
      'react-jsx-runtime',
      'wp-commands',
      'wp-components',
      'wp-compose',
      'wp-core-data',
      'wp-data',
      'wp-editor',
      'wp-element',
      'wp-html-entities',
      'wp-i18n',
      'wp-keyboard-shortcuts',
      'wp-keycodes',
      'wp-primitives',
      'wp-private-apis',
      'wp-theme',
      'wp-url'
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
    'version' => '5d918eb16ecdac63b2b1'
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
    'version' => '336043fa59033fb5e9b0'
  ),
  'edit-site-init/index.js' => array(
    'dependencies' => array(
      'react-jsx-runtime',
      'wp-data',
      'wp-element',
      'wp-primitives'
    ),
    'module_dependencies' => array(
      array(
        'id' => '@wordpress/boot',
        'import' => 'static'
      )
    ),
    'version' => '86ba14602c8af2333ca2'
  ),
  'interactivity/index.js' => array(
    'dependencies' => array(
      
    ),
    'version' => '771756b5dd00167d1664'
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
    'version' => 'bb48ce8e3364d5290463'
  ),
  'latex-to-mathml/index.js' => array(
    'dependencies' => array(
      
    ),
    'version' => 'e5fd3ae6d2c3b6e669da'
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
    'version' => 'b6f9e7d8891174056fa8'
  ),
  'route/index.js' => array(
    'dependencies' => array(
      'react',
      'react-dom',
      'react-jsx-runtime',
      'wp-private-apis'
    ),
    'version' => '333002943024efaa0bcc'
  ),
  'workflow/index.js' => array(
    'dependencies' => array(
      'react',
      'react-dom',
      'react-jsx-runtime',
      'wp-components',
      'wp-data',
      'wp-element',
      'wp-i18n',
      'wp-keyboard-shortcuts',
      'wp-primitives',
      'wp-private-apis'
    ),
    'module_dependencies' => array(
      array(
        'id' => '@wordpress/abilities',
        'import' => 'static'
      )
    ),
    'version' => '632383fbe66eca7e49c5'
  )
);