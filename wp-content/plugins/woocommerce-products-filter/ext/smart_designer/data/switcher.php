<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');
//View is managed by the Data!
return [
    'prefix' => 'sw_', //for keep in db only
    'templates' => [
        0 => [
            'title' => esc_html__('Template #1', 'woocommerce-products-filter'),
            'use_subterms' => 1
        ]
    ],
    'template' => 0,
    'demo_taxonomies' => [
        0 => esc_html__('Example terms by real taxonomy', 'woocommerce-products-filter')
    ],
    'selected_demo_taxonomy' => 0, //demo data for visor
    'sections' => [
        [
            'title' => esc_html__('Terms', 'woocommerce-products-filter'),
            'table' => [
                'class' => 'woof-sd-table-terms',
                'header' => [
                    [
                        'value' => esc_html__('Options', 'woocommerce-products-filter'),
                        'width' => '50%',
                        'action' => 'save_custom_element_option'
                    ],
                    [
                        'value' => esc_html__('Description', 'woocommerce-products-filter'),
                        'width' => '50%',
                    ],
                ],
                'rows' => [
                    'vertex_enabled_bg_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Vertex enabled background color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'vertex_enabled_bg_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Vertex enabled background image', 'woocommerce-products-filter')] //cell
                    ],
                    'vertex_enabled_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Vertex enabled border color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'vertex_enabled_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'solid', 'options' => [
                                    'dotted' => 'dotted',
                                    'dashed' => 'dashed',
                                    'solid' => 'solid',
                                    'double' => 'double',
                                    'groove' => 'groove',
                                    'ridge' => 'ridge',
                                    'inset' => 'inset',
                                    'outset' => 'outset',
                                    'none' => 'none',
                                    'hidden' => 'hidden'
                                ]]],
                        ['value' => 'Vertex enabled border style'], //cell
                    ],
                    'vertex_disabled_bg_color' => [
                        ['value' => ['element' => 'color', 'value' => '#ffffff']], //cell
                        ['value' => esc_html__('Vertex disabled background color', 'woocommerce-products-filter')], //cell
                    ],
                    'vertex_disabled_bg_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Vertex disabled background image', 'woocommerce-products-filter')] //cell
                    ],
                    'vertex_disabled_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#ffffff']], //cell
                        ['value' => esc_html__('Vertex disabled border color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'vertex_disabled_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'solid', 'options' => [
                                    'dotted' => 'dotted',
                                    'dashed' => 'dashed',
                                    'solid' => 'solid',
                                    'double' => 'double',
                                    'groove' => 'groove',
                                    'ridge' => 'ridge',
                                    'inset' => 'inset',
                                    'outset' => 'outset',
                                    'none' => 'none',
                                    'hidden' => 'hidden'
                                ]]],
                        ['value' => 'Vertex disabled border style'], //cell
                    ],
                    'vertex_border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 10], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Vertex border width', 'woocommerce-products-filter')], //cell
                    ],
                    'vertex_size' => [
                        ['value' => ['element' => 'ranger', 'value' => 20, 'min' => 15, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Vertex size (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'vertex_top' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => -50, 'max' => 50], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Vertex top (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'vertex_border_radius' => [
                        ['value' => ['element' => 'ranger', 'value' => 50, 'min' => 0, 'max' => 50], 'measure' => '%'], //cell
                        ['value' => esc_html__('Vertex border radius (%)', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_enabled_bg_color' => [
                        ['value' => ['element' => 'color', 'value' => '#c8e1ff']], //cell
                        ['value' => esc_html__('Substrate enabled background color', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_enabled_bg_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Substrate enabled background image', 'woocommerce-products-filter')] //cell
                    ],
                    'substrate_enabled_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#c8e1ff']], //cell
                        ['value' => esc_html__('Substrate enabled border color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'substrate_enabled_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'solid', 'options' => [
                                    'dotted' => 'dotted',
                                    'dashed' => 'dashed',
                                    'solid' => 'solid',
                                    'double' => 'double',
                                    'groove' => 'groove',
                                    'ridge' => 'ridge',
                                    'inset' => 'inset',
                                    'outset' => 'outset',
                                    'none' => 'none',
                                    'hidden' => 'hidden'
                                ]]],
                        ['value' => 'Substrate enabled border style'], //cell
                    ],
                    'substrate_disabled_bg_color' => [
                        ['value' => ['element' => 'color', 'value' => '#9a9999']], //cell
                        ['value' => esc_html__('Substrate disabled background color', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_disabled_bg_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Substrate disabled background image', 'woocommerce-products-filter')] //cell
                    ],
                    'substrate_disabled_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#9a9999']], //cell
                        ['value' => esc_html__('Substrate disabled border color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'substrate_disabled_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'solid', 'options' => [
                                    'dotted' => 'dotted',
                                    'dashed' => 'dashed',
                                    'solid' => 'solid',
                                    'double' => 'double',
                                    'groove' => 'groove',
                                    'ridge' => 'ridge',
                                    'inset' => 'inset',
                                    'outset' => 'outset',
                                    'none' => 'none',
                                    'hidden' => 'hidden'
                                ]]],
                        ['value' => 'Substrate disabled border style'], //cell
                    ],
                    'substrate_border_radius' => [
                        ['value' => ['element' => 'ranger', 'value' => 8, 'min' => 0, 'max' => 50], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Substrate border radius (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 10], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Substrate border width', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 34, 'min' => 30, 'max' => 200], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Substrate width (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'substrate_height' => [
                        ['value' => ['element' => 'ranger', 'value' => 14, 'min' => 0, 'max' => 80], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Substrate height (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'label_font_color' => [
                        ['value' => ['element' => 'color', 'value' => '#6d6d6d']], //cell
                        ['value' => esc_html__('Text color', 'woocommerce-products-filter')], //cell
                    ],
                    'label_font_size' => [
                        ['value' => ['element' => 'ranger', 'value' => 16, 'min' => 8, 'max' => 48], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text font size (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'label_line_height' => [
                        ['value' => ['element' => 'ranger', 'value' => 23, 'min' => 8, 'max' => 48], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text line height (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'label_font_family' => [
                        ['value' => ['element' => 'text', 'value' => 'inherit'], 'measure' => ''], //cell
                        ['value' => esc_html__('Text font family (theme must support)', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'label_font_weight' => [
                        ['value' => ['element' => 'select', 'value' => 400, 'options' => [
                                    '100' => '100',
                                    '200' => '200',
                                    '300' => '300',
                                    '400' => '400',
                                    '500' => '500',
                                    '600' => '600',
                                    '700' => '700',
                                    '800' => '800'
                                ], 'conditions' => []]],
                        ['value' => 'Text font weight'], //cell
                    ],
                    'label_left' => [
                        ['value' => ['element' => 'ranger', 'value' => 15, 'min' => 0, 'max' => 60], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text left (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'label_top' => [
                        ['value' => ['element' => 'ranger', 'value' => -16, 'min' => -100, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text top (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'margin_bottom' => [
                        ['value' => ['element' => 'ranger', 'value' => 5, 'min' => 0, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Margin bottom', 'woocommerce-products-filter')] //cell
                    ],
                    'childs_left_shift' => [
                        ['value' => ['element' => 'ranger', 'value' => 15, 'min' => 0, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Child terms left shift', 'woocommerce-products-filter')] //cell
                    ],
                ]
            ]
        ],
        [
            'title' => esc_html__('Counter', 'woocommerce-products-filter'),
            'table' => [
                'class' => 'woof-sd-table-counter',
                'header' => [
                    [
                        'value' => esc_html__('Options', 'woocommerce-products-filter'),
                        'width' => '50%',
                        'action' => 'save_custom_element_option'
                    ],
                    [
                        'value' => esc_html__('Description', 'woocommerce-products-filter'),
                        'width' => '50%',
                    ]
                ],
                'rows' => [
                    'counter_show' => [
                        ['value' => ['element' => 'switcher', 'value' => 'inline-flex', 'yes' => 'inline-flex', 'no' => 'none']], //cell
                        ['value' => esc_html__('Show counter', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 14, 'min' => 10, 'max' => 72, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'],
                        ['value' => esc_html__('Min width', 'woocommerce-products-filter')]
                    ],
                    'counter_height' => [
                        ['value' => ['element' => 'ranger', 'value' => 14, 'min' => 10, 'max' => 72, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'],
                        ['value' => esc_html__('Min height', 'woocommerce-products-filter')]
                    ],
                    'counter_top' => [
                        ['value' => ['element' => 'ranger', 'value' => -2, 'min' => -100, 'max' => 100, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Top', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_right' => [
                        ['value' => ['element' => 'ranger', 'value' => -3, 'min' => -100, 'max' => 100, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'],
                        ['value' => esc_html__('Right', 'woocommerce-products-filter')]
                    ],
                    'counter_font_size' => [
                        ['value' => ['element' => 'ranger', 'value' => 9, 'min' => 8, 'max' => 48, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text font size', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_color' => [
                        ['value' => ['element' => 'color', 'value' => '#477bff', 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]]], //cell
                        ['value' => esc_html__('Text color', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_font_family' => [
                        ['value' => ['element' => 'text', 'value' => 'consolas', 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => ''], //cell
                        ['value' => esc_html__('Text font family (theme must support)', 'woocommerce-products-filter')], //cell
                    ],
                    'counter_font_weight' => [
                        ['value' => ['element' => 'select', 'value' => 500, 'options' => [
                                    '100' => '100',
                                    '200' => '200',
                                    '300' => '300',
                                    '400' => '400',
                                    '500' => '500',
                                    '600' => '600',
                                    '700' => '700',
                                    '800' => '800'
                                ], 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]]],
                        ['value' => 'Text font weight'], //cell
                    ],                    
                    'counter_side_padding' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => 0, 'max' => 48, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Side padding', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_bg_color' => [
                        ['value' => ['element' => 'color', 'value' => '#ffffff', 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]]], //cell
                        ['value' => esc_html__('Background color', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_bg_image' => [
                        ['value' => ['element' => 'image', 'value' => '', 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Background image', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 10, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Border width', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_border_radius' => [
                        ['value' => ['element' => 'ranger', 'value' => 50, 'min' => 0, 'max' => 50, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => '%'], //cell
                        ['value' => esc_html__('Border radius', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#477bff', 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]]], //cell
                        ['value' => esc_html__('Border color', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'solid', 'options' => [
                                    'dotted' => 'dotted',
                                    'dashed' => 'dashed',
                                    'solid' => 'solid',
                                    'double' => 'double',
                                    'groove' => 'groove',
                                    'ridge' => 'ridge',
                                    'inset' => 'inset',
                                    'outset' => 'outset',
                                    'none' => 'none',
                                    'hidden' => 'hidden'
                                ], 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]]],
                        ['value' => 'Border style'], //cell
                    ],
                ]
            ]
        ],
    ]
];

