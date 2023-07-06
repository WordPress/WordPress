<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');
//View is managed by the Data!
return [
    'prefix' => 'clr_', //for keep in db only
    'templates' => [
        0 => [
            'title' => esc_html__('Template #1 (as checkbox)', 'woocommerce-products-filter'),
            'use_subterms' => 0
        ],
        1 => [
            'title' => esc_html__('Template #2 (as radio)', 'woocommerce-products-filter'),
            'use_subterms' => 0
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
                    'width' => [
                        ['value' => ['element' => 'ranger', 'value' => 60, 'min' => 10, 'max' => 500, 'conditions' => []], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Min width', 'woocommerce-products-filter')] //cell
                    ],
                    'height' => [
                        ['value' => ['element' => 'ranger', 'value' => 60, 'min' => 10, 'max' => 500], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Height', 'woocommerce-products-filter')] //cell
                    ],
                    'color' => [
                        ['value' => ['element' => 'color', 'value' => '#000000']], //cell
                        ['value' => esc_html__('Default color', 'woocommerce-products-filter')] //cell
                    ],
                    'border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 20], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Border width', 'woocommerce-products-filter')] //cell
                    ],
                    'border_radius' => [
                        ['value' => ['element' => 'ranger', 'value' => 50, 'min' => 0, 'max' => 50], 'measure' => '%'], //cell
                        ['value' => esc_html__('Border radius', 'woocommerce-products-filter')] //cell
                    ],
                    'border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Border color', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'] //cell
                    ],
                    'border_style' => [
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
                        ['value' => 'Border style'], //cell
                    ],
                    'margin_right' => [
                        ['value' => ['element' => 'ranger', 'value' => 9, 'min' => 0, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Margin right', 'woocommerce-products-filter')] //cell
                    ],
                    'margin_bottom' => [
                        ['value' => ['element' => 'ranger', 'value' => 11, 'min' => 0, 'max' => 100], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Margin bottom', 'woocommerce-products-filter')] //cell
                    ],
                    'transition' => [
                        ['value' => ['element' => 'ranger', 'value' => 300, 'min' => 0, 'max' => 1000], 'measure' => 's'], //cell
                        ['value' => esc_html__('Transition (ms)', 'woocommerce-products-filter')] //cell
                    ],
                    'show_tooltip' => [
                        ['value' => ['element' => 'switcher', 'value' => 'none', 'yes' => 'block', 'no' => 'none']], //cell
                        ['value' => esc_html__('Show tooltip on hover', 'woocommerce-products-filter')] //cell
                    ],
                    'show_tooltip_count' => [
                        ['value' => ['element' => 'switcher', 'value' => 'block-inline', 'yes' => 'block-inline', 'no' => 'none', 'conditions' => [
                                    'hide' => [
                                        'show_tooltip' => 'none'//if this selected element will be hidden
                                    ],
                                    'forced_change' => []
                                ]]], //cell
                        ['value' => esc_html__('Show count inside tooltip', 'woocommerce-products-filter')] //cell
                    ],
                ]
            ]
        ],
        [
            'title' => esc_html__('Selected/Hovered Terms', 'woocommerce-products-filter'),
            'table' => [
                'header' => [
                    [
                        'value' => esc_html__('Options', 'woocommerce-products-filter'),
                        'width' => '50%',
                        'action' => 'save_custom_element_option'
                    ],
                    [
                        'value' => esc_html__('Description', 'woocommerce-products-filter'),
                        'width' => '50%'
                    ],
                ],
                'rows' => [
                    'hover_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Hover border color', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Selected border color', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'dashed', 'options' => [
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
                        ['value' => 'Hover border style'], //cell
                    ],
                    'selected_border_style' => [
                        ['value' => ['element' => 'select', 'value' => 'dashed', 'options' => [
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
                        ['value' => 'Selected border style'], //cell
                    ],
                    'hover_border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 20], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Hover border width', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 20], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Selected border width', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_scale' => [
                        ['value' => ['element' => 'ranger', 'value' => 110, 'min' => 50, 'max' => 200], 'measure' => ''], //cell
                        ['value' => esc_html__('Hover scale', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_scale' => [
                        ['value' => ['element' => 'ranger', 'value' => 110, 'min' => 50, 'max' => 200], 'measure' => ''], //cell
                        ['value' => esc_html__('Selected scale', 'woocommerce-products-filter')] //cell
                    ],
                ]
            ]
        ],
        [
            'title' => esc_html__('Counter', 'woocommerce-products-filter'),
            'table' => [
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
                        ['value' => ['element' => 'ranger', 'value' => 4, 'min' => -100, 'max' => 100, 'conditions' => [
                                    'hide' => [
                                        'counter_show' => 'none'//if this selected element will be hidden
                                    ]]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Top', 'woocommerce-products-filter')] //cell
                    ],
                    'counter_right' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => -100, 'max' => 100, 'conditions' => [
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

