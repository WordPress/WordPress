<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');
//View is managed by the Data!
return [
    'prefix' => 'ch_', //for keep in db only
    'templates' => [
        0 => [
            'title' => esc_html__('Template #1 (list, subcategories)', 'woocommerce-products-filter'),
            'use_subterms' => 1
        ],
        1 => [
            'title' => esc_html__('Template #2 (tile, no sub-categories)', 'woocommerce-products-filter'),
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
                    'width_auto' => [
                        ['value' => ['element' => 'switcher', 'value' => 0, 'conditions' => [
                                    'templates' => [1]
                                ]]], //cell
                        ['value' => esc_html__('Width auto', 'woocommerce-products-filter')] //cell
                    ],
                    'width' => [
                        ['value' => ['element' => 'ranger', 'value' => 21, 'min' => 10, 'max' => 500, 'conditions' => [
                                    'hide' => [
                                        'width_auto' => 1//if this selected element will be hidden
                                    ],
                                    'forced_change' => [
                                        'width_auto' => [
                                            'value' => 1,
                                            'set_to' => 'fit-content',
                                            'measure' => '',
                                            'exclude_in_template' => [0]//for php
                                        ]
                                    ]
                                ]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Min width', 'woocommerce-products-filter')] //cell
                    ],
                    'side_padding' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => 0, 'max' => 50, 'conditions' => [
                                    'hide' => [
                                        'width_auto' => 0
                                    ],
                                    'forced_change' => [
                                        'width_auto' => [
                                            'value' => 0,
                                            'set_to' => 0,
                                            'measure' => 'px'
                                        ]
                                    ],
                                    'templates' => [1]
                                ]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Side Padding', 'woocommerce-products-filter')] //cell
                    ],
                    'height' => [
                        ['value' => ['element' => 'ranger', 'value' => 21, 'min' => 10, 'max' => 500], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Height', 'woocommerce-products-filter')] //cell
                    ],
                    'font_size' => [
                        ['value' => ['element' => 'ranger', 'value' => 15, 'min' => 8, 'max' => 72], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text font size', 'woocommerce-products-filter')] //cell
                    ],
                    'font_family' => [
                        ['value' => ['element' => 'text', 'value' => 'inherit'], 'measure' => ''], //cell
                        ['value' => esc_html__('Text font family (theme must support)', 'woocommerce-products-filter'), 'help' => 'https://products-filter.com/extencion/smart-designer/#tips-about-customizations'], //cell
                    ],
                    'font_weight' => [
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
                    'line_height' => [
                        ['value' => ['element' => 'ranger', 'value' => 18, 'min' => 8, 'max' => 48, 'conditions' => [
                                    'templates' => [0]
                                ]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Text line height (px)', 'woocommerce-products-filter')], //cell
                    ],
                    'text_color' => [
                        ['value' => ['element' => 'color', 'value' => '#6d6d6d']], //cell
                        ['value' => esc_html__('Text color', 'woocommerce-products-filter')] //cell
                    ],
                    'text_top' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => -20, 'max' => 20, 'conditions' => [
                                    'templates' => [0]
                                ]], 'measure' => 'px'],
                        ['value' => esc_html__('Text top', 'woocommerce-products-filter')]
                    ],
                    'space' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 40], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Space width', 'woocommerce-products-filter')] //cell
                    ],
                    'space_color' => [
                        ['value' => ['element' => 'color', 'value' => '#ffffff']], //cell
                        ['value' => esc_html__('Space background color', 'woocommerce-products-filter')] //cell
                    ],
                    'image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Background image', 'woocommerce-products-filter')] //cell
                    ],
                    'color' => [
                        ['value' => ['element' => 'color', 'value' => '#ffffff']], //cell
                        ['value' => esc_html__('Background color', 'woocommerce-products-filter')] //cell
                    ],
                    'border_width' => [
                        ['value' => ['element' => 'ranger', 'value' => 1, 'min' => 0, 'max' => 20], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Border width', 'woocommerce-products-filter')] //cell
                    ],
                    'border_radius' => [
                        ['value' => ['element' => 'ranger', 'value' => 0, 'min' => 0, 'max' => 50], 'measure' => '%'], //cell
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
                    'childs_left_shift' => [
                        ['value' => ['element' => 'ranger', 'value' => 19, 'min' => 0, 'max' => 100, 'conditions' => [
                                    'templates' => [0]
                                ]], 'measure' => 'px'], //cell
                        ['value' => esc_html__('Child terms left shift', 'woocommerce-products-filter')] //cell
                    ],
                    'transition' => [
                        ['value' => ['element' => 'ranger', 'value' => 300, 'min' => 0, 'max' => 1000], 'measure' => 's'], //cell
                        ['value' => esc_html__('Transition (ms)', 'woocommerce-products-filter')] //cell
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
                    'selected_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Selected color', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Hover color', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Hover image', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_image' => [
                        ['value' => ['element' => 'image', 'value' => ''], 'before' => 'url(', 'after' => ')'], //cell
                        ['value' => esc_html__('Selected image', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_text_color' => [
                        ['value' => ['element' => 'color', 'value' => '#333333']], //cell
                        ['value' => esc_html__('Hover text color', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_text_color' => [
                        ['value' => ['element' => 'color', 'value' => '#000000']], //cell
                        ['value' => esc_html__('Selected text color', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_font_weight' => [
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
                        ['value' => 'Hover text font weight'], //cell
                    ],
                    'selected_font_weight' => [
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
                        ['value' => 'Selected text font weight'], //cell
                    ],
                    'hover_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Hover border color', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_border_color' => [
                        ['value' => ['element' => 'color', 'value' => '#79b8ff']], //cell
                        ['value' => esc_html__('Selected border color', 'woocommerce-products-filter')] //cell
                    ],
                    'hover_border_style' => [
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
                        ['value' => 'Hover border style'], //cell
                    ],
                    'selected_border_style' => [
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
                        ['value' => ['element' => 'ranger', 'value' => 100, 'min' => 50, 'max' => 200], 'measure' => ''], //cell
                        ['value' => esc_html__('Hover scale', 'woocommerce-products-filter')] //cell
                    ],
                    'selected_scale' => [
                        ['value' => ['element' => 'ranger', 'value' => 100, 'min' => 50, 'max' => 200], 'measure' => ''], //cell
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
                        ['value' => ['element' => 'ranger', 'value' => -2, 'min' => -100, 'max' => 100, 'conditions' => [
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

