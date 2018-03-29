<?php

/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - render()
 * - enqueue()
 * - makeGoogleWebfontLink()
 * - makeGoogleWebfontString()
 * - output()
 * - getGoogleArray()
 * - getSubsets()
 * - getVariants()
 * Classes list:
 * - ReduxFramework_typography
 */

if ( ! class_exists( 'ReduxFramework_typography' ) ) {
    class ReduxFramework_typography {

        private $std_fonts = array(
            "Arial, Helvetica, sans-serif"                         => "Arial, Helvetica, sans-serif",
            "'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif",
            "'Bookman Old Style', serif"                           => "'Bookman Old Style', serif",
            "'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive",
            "Courier, monospace"                                   => "Courier, monospace",
            "Garamond, serif"                                      => "Garamond, serif",
            "Georgia, serif"                                       => "Georgia, serif",
            "Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif",
            "'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace",
            "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
            "'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif",
            "'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif",
            "'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
            "Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif",
            "'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif",
            "'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif",
            "Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif",
        );

        private $user_fonts = true;

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

            // Shim out old arg to new
            if ( isset( $this->field['all_styles'] ) && ! empty( $this->field['all_styles'] ) ) {
                $this->field['all-styles'] = $this->field['all_styles'];
                unset ( $this->field['all_styles'] );
            }

            // Set field array defaults.  No errors please
            $defaults    = array(
                'font-family'     => true,
                'font-size'       => true,
                'font-weight'     => true,
                'font-style'      => true,
                'font-backup'     => false,
                'subsets'         => true,
                'custom_fonts'    => true,
                'text-align'      => true,
                'text-transform'  => false,
                'font-variant'    => false,
                'text-decoration' => false,
                'color'           => true,
                'preview'         => true,
                'line-height'     => true,
                'multi' => array(
                    'subset' => false,
                    'weight' => false,
                ),
                'word-spacing'    => false,
                'letter-spacing'  => false,
                'google'          => true,
                'update_weekly'   => false,    // Enable to force updates of Google Fonts to be weekly
                'font_family_clear' => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );

            // Set value defaults.
            $defaults    = array(
                'font-family'     => '',
                'font-options'    => '',
                'font-backup'     => '',
                'text-align'      => '',
                'text-transform'  => '',
                'font-variant'    => '',
                'text-decoration' => '',
                'line-height'     => '',
                'word-spacing'    => '',
                'letter-spacing'  => '',
                'subsets'         => '',
                'google'          => false,
                'font-script'     => '',
                'font-weight'     => '',
                'font-style'      => '',
                'color'           => '',
                'font-size'       => '',
            );
            $this->value = wp_parse_args( $this->value, $defaults );

            // Get the google array
            $this->getGoogleArray();

            if ( empty( $this->field['fonts'] ) ) {
                $this->user_fonts     = false;
                $this->field['fonts'] = $this->std_fonts;
            }

            // Localize std fonts
            $this->localizeStdFonts();

        }

        function localize( $field, $value = "" ) {
            $params = array();

            if ( true == $this->user_fonts && ! empty( $this->field['fonts'] ) ) {
                $params['std_font'] = $this->field['fonts'];
            }

            return $params;
        }


        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {
            // Since fonts declared is CSS (@font-face) are not rendered in the preview,
            // they can be declared in a CSS file and passed here so they DO display in
            // font preview.  Do NOT pass style.css in your theme, as that will mess up
            // admin page styling.  It's recommended to pass a CSS file with ONLY font
            // declarations.
            // If field is set and not blank, then enqueue field
            if ( isset( $this->field['ext-font-css'] ) && $this->field['ext-font-css'] != '' ) {
                wp_register_style( 'redux-external-fonts', $this->field['ext-font-css'] );
                wp_enqueue_style( 'redux-external-fonts' );
            }

            if ( empty( $this->field['units'] ) && ! empty( $this->field['default']['units'] ) ) {
                $this->field['units'] = $this->field['default']['units'];
            }

            if ( empty( $this->field['units'] ) || ! in_array( $this->field['units'], array(
                    'px',
                    'em',
                    'rem',
                    '%'
                ) )
            ) {
                $this->field['units'] = 'px';
            }

            $unit = $this->field['units'];

            echo '<div id="' . $this->field['id'] . '" class="redux-typography-container" data-id="' . $this->field['id'] . '" data-units="' . $unit . '">';

            if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                $select2_params = json_encode( $this->field['select2'] );
                $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
            }

            /* Font Family */
            if ( $this->field['font-family'] === true ) {

                // font family clear
                echo '<input type="hidden" class="redux-font-clear" value="' . $this->field['font_family_clear'] . '">';

                //if (filter_var($this->value['google'], FILTER_VALIDATE_BOOLEAN)) {
                if ( filter_var( $this->value['google'], FILTER_VALIDATE_BOOLEAN ) ) {

                    // Divide and conquer
                    $fontFamily = explode( ', ', $this->value['font-family'], 2 );

                    // If array 0 is empty and array 1 is not
                    if ( empty( $fontFamily[0] ) && ! empty( $fontFamily[1] ) ) {

                        // Make array 0 = array 1
                        $fontFamily[0] = $fontFamily[1];

                        // Clear array 1
                        $fontFamily[1] = "";
                    }
                }

                // If no fontFamily array exists, create one and set array 0
                // with font value
                if ( ! isset( $fontFamily ) ) {
                    $fontFamily    = array();
                    $fontFamily[0] = $this->value['font-family'];
                    $fontFamily[1] = "";
                }

                // Is selected font a Google font
                $isGoogleFont = '0';
                if ( isset( $this->parent->fonts['google'][ $fontFamily[0] ] ) ) {
                    $isGoogleFont = '1';
                }

                // If not a Google font, show all font families
                if ( $isGoogleFont != '1' ) {
                    $fontFamily[0] = $this->value['font-family'];
                }

                $userFonts = '0';
                if ( true == $this->user_fonts ) {
                    $userFonts = '1';
                }

                echo '<input type="hidden" class="redux-typography-font-family ' . $this->field['class'] . '" data-user-fonts="' . $userFonts . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-family]' . '" value="' . $this->value['font-family'] . '" data-id="' . $this->field['id'] . '"  />';
                echo '<input type="hidden" class="redux-typography-font-options ' . $this->field['class'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-options]' . '" value="' . $this->value['font-options'] . '" data-id="' . $this->field['id'] . '"  />';

                echo '<input type="hidden" class="redux-typography-google-font" value="' . $isGoogleFont . '" id="' . $this->field['id'] . '-google-font">';

                echo '<div class="select_wrapper typography-family" style="width: 220px; margin-right: 5px;">';
                echo '<label>' . __( 'Font Family', 'redux-framework' ) . '</label>';
                $placeholder = $fontFamily[0] ? $fontFamily[0] : __( 'Font family', 'redux-framework' );

                echo '<div class=" redux-typography redux-typography-family select2-container ' . $this->field['class'] . '" id="' . $this->field['id'] . '-family" placeholder="' . $placeholder . '" data-id="' . $this->field['id'] . '" data-value="' . $fontFamily[0] . '">';

                echo '</div>';
                echo '</div>';

                $googleSet = false;
                if ( $this->field['google'] === true ) {

                    // Set a flag so we know to set a header style or not
                    echo '<input type="hidden" class="redux-typography-google ' . $this->field['class'] . '" id="' . $this->field['id'] . '-google" name="' . $this->field['name'] . $this->field['name_suffix'] . '[google]' . '" type="text" value="' . $this->field['google'] . '" data-id="' . $this->field['id'] . '" />';
                    $googleSet = true;
                }
            }

            /* Backup Font */
            if ( $this->field['font-family'] === true && $this->field['google'] === true ) {

                if ( false == $googleSet ) {
                    // Set a flag so we know to set a header style or not
                    echo '<input type="hidden" class="redux-typography-google ' . $this->field['class'] . '" id="' . $this->field['id'] . '-google" name="' . $this->field['name'] . $this->field['name_suffix'] . '[google]' . '" type="text" value="' . $this->field['google'] . '" data-id="' . $this->field['id'] . '"  />';
                }

                if ( $this->field['font-backup'] === true ) {
                    echo '<div class="select_wrapper typography-family-backup" style="width: 220px; margin-right: 5px;">';
                    echo '<label>' . __( 'Backup Font Family', 'redux-framework' ) . '</label>';
                    echo '<select data-placeholder="' . __( 'Backup Font Family', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-backup]' . '" class="redux-typography redux-typography-family-backup ' . $this->field['class'] . '" id="' . $this->field['id'] . '-family-backup" data-id="' . $this->field['id'] . '" data-value="' . $this->value['font-backup'] . '">';
                    echo '<option data-google="false" data-details="" value=""></option>';

                    foreach ( $this->field['fonts'] as $i => $family ) {
                        echo '<option data-google="true" value="' . $i . '"' . selected( $this->value['font-backup'], $i, false ) . '>' . $family . '</option>';
                    }

                    echo '</select></div>';
                }
            }

            /* Font Style/Weight */
            if ( $this->field['font-style'] === true || $this->field['font-weight'] === true ) {

                echo '<div class="select_wrapper typography-style" original-title="' . __( 'Font style', 'redux-framework' ) . '">';
                echo '<label>' . __( 'Font Weight &amp; Style', 'redux-framework' ) . '</label>';

                $style = $this->value['font-weight'] . $this->value['font-style'];

                echo '<input type="hidden" class="typography-font-weight" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-weight]' . '" value="' . $this->value['font-weight'] . '" data-id="' . $this->field['id'] . '"  /> ';
                echo '<input type="hidden" class="typography-font-style" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-style]' . '" value="' . $this->value['font-style'] . '" data-id="' . $this->field['id'] . '"  /> ';
                $multi = ( isset( $this->field['multi']['weight'] ) && $this->field['multi']['weight'] ) ? ' multiple="multiple"' : "";
                echo '<select' . $multi . ' data-placeholder="' . __( 'Style', 'redux-framework' ) . '" class="redux-typography redux-typography-style select ' . $this->field['class'] . '" original-title="' . __( 'Font style', 'redux-framework' ) . '" id="' . $this->field['id'] . '_style" data-id="' . $this->field['id'] . '" data-value="' . $style . '">';

                if ( empty( $this->value['subsets'] ) || empty( $this->value['font-weight'] ) ) {
                    echo '<option value=""></option>';
                }

                $nonGStyles = array(
                    '200' => 'Lighter',
                    '400' => 'Normal',
                    '700' => 'Bold',
                    '900' => 'Bolder'
                );

                if ( isset( $gfonts[ $this->value['font-family'] ] ) ) {
                    foreach ( $gfonts[ $this->value['font-family'] ]['variants'] as $v ) {
                        echo '<option value="' . $v['id'] . '" ' . selected( $this->value['subsets'], $v['id'], false ) . '>' . $v['name'] . '</option>';
                    }
                } else {
                    if ( ! isset( $this->value['font-weight'] ) && isset( $this->value['subsets'] ) ) {
                        $this->value['font-weight'] = $this->value['subsets'];
                    }

                    foreach ( $nonGStyles as $i => $style ) {
                        if ( ! isset( $this->value['font-weight'] ) ) {
                            $this->value['font-weight'] = false;
                        }

                        if ( ! isset( $this->value['subsets'] ) ) {
                            $this->value['subsets'] = false;
                        }

                        echo '<option value="' . $i . '" ' . selected( $this->value['font-weight'], $i, false ) . '>' . $style . '</option>';
                    }
                }

                echo '</select></div>';
            }

            /* Font Script */
            if ( $this->field['font-family'] == true && $this->field['subsets'] == true && $this->field['google'] == true ) {
                echo '<div class="select_wrapper typography-script tooltip" original-title="' . __( 'Font subsets', 'redux-framework' ) . '">';
                echo '<input type="hidden" class="typography-subsets" name="' . $this->field['name'] . $this->field['name_suffix'] . '[subsets]' . '" value="' . $this->value['subsets'] . '" data-id="' . $this->field['id'] . '"  /> ';
                echo '<label>' . __( 'Font Subsets', 'redux-framework' ) . '</label>';
                $multi = ( isset( $this->field['multi']['subset'] ) && $this->field['multi']['subset'] ) ? ' multiple="multiple"' : "";
                echo '<select'.$multi.' data-placeholder="' . __( 'Subsets', 'redux-framework' ) . '" class="redux-typography redux-typography-subsets ' . $this->field['class'] . '" original-title="' . __( 'Font script', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-subsets" data-value="' . $this->value['subsets'] . '" data-id="' . $this->field['id'] . '" >';

                if ( empty( $this->value['subsets'] ) ) {
                    echo '<option value=""></option>';
                }

                if ( isset( $gfonts[ $this->value['font-family'] ] ) ) {
                    foreach ( $gfonts[ $this->value['font-family'] ]['subsets'] as $v ) {
                        echo '<option value="' . $v['id'] . '" ' . selected( $this->value['subsets'], $v['id'], false ) . '>' . $v['name'] . '</option>';
                    }
                }

                echo '</select></div>';
            }

            /* Font Align */
            if ( $this->field['text-align'] === true ) {
                echo '<div class="select_wrapper typography-align tooltip" original-title="' . __( 'Text Align', 'redux-framework' ) . '">';
                echo '<label>' . __( 'Text Align', 'redux-framework' ) . '</label>';
                echo '<select data-placeholder="' . __( 'Text Align', 'redux-framework' ) . '" class="redux-typography redux-typography-align ' . $this->field['class'] . '" original-title="' . __( 'Text Align', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-align" name="' . $this->field['name'] . $this->field['name_suffix'] . '[text-align]' . '" data-value="' . $this->value['text-align'] . '" data-id="' . $this->field['id'] . '" >';
                echo '<option value=""></option>';

                $align = array(
                    'inherit',
                    'left',
                    'right',
                    'center',
                    'justify',
                    'initial'
                );

                foreach ( $align as $v ) {
                    echo '<option value="' . $v . '" ' . selected( $this->value['text-align'], $v, false ) . '>' . ucfirst( $v ) . '</option>';
                }

                echo '</select></div>';
            }

            /* Text Transform */
            if ( $this->field['text-transform'] === true ) {
                echo '<div class="select_wrapper typography-transform tooltip" original-title="' . __( 'Text Transform', 'redux-framework' ) . '">';
                echo '<label>' . __( 'Text Transform', 'redux-framework' ) . '</label>';
                echo '<select data-placeholder="' . __( 'Text Transform', 'redux-framework' ) . '" class="redux-typography redux-typography-transform ' . $this->field['class'] . '" original-title="' . __( 'Text Transform', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-transform" name="' . $this->field['name'] . $this->field['name_suffix'] . '[text-transform]' . '" data-value="' . $this->value['text-transform'] . '" data-id="' . $this->field['id'] . '" >';
                echo '<option value=""></option>';

                $values = array(
                    'none',
                    'capitalize',
                    'uppercase',
                    'lowercase',
                    'initial',
                    'inherit'
                );

                foreach ( $values as $v ) {
                    echo '<option value="' . $v . '" ' . selected( $this->value['text-transform'], $v, false ) . '>' . ucfirst( $v ) . '</option>';
                }

                echo '</select></div>';
            }

            /* Font Variant */
            if ( $this->field['font-variant'] === true ) {
                echo '<div class="select_wrapper typography-font-variant tooltip" original-title="' . __( 'Font Variant', 'redux-framework' ) . '">';
                echo '<label>' . __( 'Font Variant', 'redux-framework' ) . '</label>';
                echo '<select data-placeholder="' . __( 'Font Variant', 'redux-framework' ) . '" class="redux-typography redux-typography-font-variant ' . $this->field['class'] . '" original-title="' . __( 'Font Variant', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-font-variant" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-variant]' . '" data-value="' . $this->value['font-variant'] . '" data-id="' . $this->field['id'] . '" >';
                echo '<option value=""></option>';

                $values = array(
                    'inherit',
                    'normal',
                    'small-caps'
                );

                foreach ( $values as $v ) {
                    echo '<option value="' . $v . '" ' . selected( $this->value['font-variant'], $v, false ) . '>' . ucfirst( $v ) . '</option>';
                }

                echo '</select></div>';
            }

            /* Text Decoration */
            if ( $this->field['text-decoration'] === true ) {
                echo '<div class="select_wrapper typography-decoration tooltip" original-title="' . __( 'Text Decoration', 'redux-framework' ) . '">';
                echo '<label>' . __( 'Text Decoration', 'redux-framework' ) . '</label>';
                echo '<select data-placeholder="' . __( 'Text Decoration', 'redux-framework' ) . '" class="redux-typography redux-typography-decoration ' . $this->field['class'] . '" original-title="' . __( 'Text Decoration', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-decoration" name="' . $this->field['name'] . $this->field['name_suffix'] . '[text-decoration]' . '" data-value="' . $this->value['text-decoration'] . '" data-id="' . $this->field['id'] . '" >';
                echo '<option value=""></option>';

                $values = array(
                    'none',
                    'inherit',
                    'underline',
                    'overline',
                    'line-through',
                    'blink'
                );

                foreach ( $values as $v ) {
                    echo '<option value="' . $v . '" ' . selected( $this->value['text-decoration'], $v, false ) . '>' . ucfirst( $v ) . '</option>';
                }

                echo '</select></div>';
            }

            /* Font Size */
            if ( $this->field['font-size'] === true ) {
                echo '<div class="input_wrapper font-size redux-container-typography">';
                echo '<label>' . __( 'Font Size', 'redux-framework' ) . '</label>';
                echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-size mini typography-input ' . $this->field['class'] . '" title="' . __( 'Font Size', 'redux-framework' ) . '" placeholder="' . __( 'Size', 'redux-framework' ) . '" id="' . $this->field['id'] . '-size" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-size]' . '" value="' . str_replace( $unit, '', $this->value['font-size'] ) . '" data-value="' . str_replace( $unit, '', $this->value['font-size'] ) . '"><span class="add-on">' . $unit . '</span></div>';
                echo '<input type="hidden" class="typography-font-size" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font-size]' . '" value="' . $this->value['font-size'] . '" data-id="' . $this->field['id'] . '"  />';
                echo '</div>';
            }

            /* Line Height */
            if ( $this->field['line-height'] === true ) {
                echo '<div class="input_wrapper line-height redux-container-typography">';
                echo '<label>' . __( 'Line Height', 'redux-framework' ) . '</label>';
                echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-height mini typography-input ' . $this->field['class'] . '" title="' . __( 'Line Height', 'redux-framework' ) . '" placeholder="' . __( 'Height', 'redux-framework' ) . '" id="' . $this->field['id'] . '-height" value="' . str_replace( $unit, '', $this->value['line-height'] ) . '" data-value="' . str_replace( $unit, '', $this->value['line-height'] ) . '"><span class="add-on">' . $unit . '</span></div>';
                echo '<input type="hidden" class="typography-line-height" name="' . $this->field['name'] . $this->field['name_suffix'] . '[line-height]' . '" value="' . $this->value['line-height'] . '" data-id="' . $this->field['id'] . '"  />';
                echo '</div>';
            }

            /* Word Spacing */
            if ( $this->field['word-spacing'] === true ) {
                echo '<div class="input_wrapper word-spacing redux-container-typography">';
                echo '<label>' . __( 'Word Spacing', 'redux-framework' ) . '</label>';
                echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-word mini typography-input ' . $this->field['class'] . '" title="' . __( 'Word Spacing', 'redux-framework' ) . '" placeholder="' . __( 'Word Spacing', 'redux-framework' ) . '" id="' . $this->field['id'] . '-word" value="' . str_replace( $unit, '', $this->value['word-spacing'] ) . '" data-value="' . str_replace( $unit, '', $this->value['word-spacing'] ) . '"><span class="add-on">' . $unit . '</span></div>';
                echo '<input type="hidden" class="typography-word-spacing" name="' . $this->field['name'] . $this->field['name_suffix'] . '[word-spacing]' . '" value="' . $this->value['word-spacing'] . '" data-id="' . $this->field['id'] . '"  />';
                echo '</div>';
            }

            /* Letter Spacing */
            if ( $this->field['letter-spacing'] === true ) {
                echo '<div class="input_wrapper letter-spacing redux-container-typography">';
                echo '<label>' . __( 'Letter Spacing', 'redux-framework' ) . '</label>';
                echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-letter mini typography-input ' . $this->field['class'] . '" title="' . __( 'Letter Spacing', 'redux-framework' ) . '" placeholder="' . __( 'Letter Spacing', 'redux-framework' ) . '" id="' . $this->field['id'] . '-letter" value="' . str_replace( $unit, '', $this->value['letter-spacing'] ) . '" data-value="' . str_replace( $unit, '', $this->value['letter-spacing'] ) . '"><span class="add-on">' . $unit . '</span></div>';
                echo '<input type="hidden" class="typography-letter-spacing" name="' . $this->field['name'] . $this->field['name_suffix'] . '[letter-spacing]' . '" value="' . $this->value['letter-spacing'] . '" data-id="' . $this->field['id'] . '"  />';
                echo '</div>';
            }

            echo '<div class="clearfix"></div>';

            /* Font Color */
            if ( $this->field['color'] === true ) {
                $default = "";

                if ( empty( $this->field['default']['color'] ) && ! empty( $this->field['color'] ) ) {
                    $default = $this->value['color'];
                } else if ( ! empty( $this->field['default']['color'] ) ) {
                    $default = $this->field['default']['color'];
                }

                echo '<div class="picker-wrapper">';
                echo '<label>' . __( 'Font Color', 'redux-framework' ) . '</label>';
                echo '<div id="' . $this->field['id'] . '_color_picker" class="colorSelector typography-color"><div style="background-color: ' . $this->value['color'] . '"></div></div>';
                echo '<input data-default-color="' . $default . '" class="redux-color redux-typography-color ' . $this->field['class'] . '" original-title="' . __( 'Font color', 'redux-framework' ) . '" id="' . $this->field['id'] . '-color" name="' . $this->field['name'] . $this->field['name_suffix'] . '[color]' . '" type="text" value="' . $this->value['color'] . '" data-id="' . $this->field['id'] . '" />';
                echo '</div>';
            }

            echo '<div class="clearfix"></div>';

            /* Font Preview */
            if ( ! isset( $this->field['preview'] ) || $this->field['preview'] !== false ) {
                if ( isset( $this->field['preview']['text'] ) ) {
                    $g_text = $this->field['preview']['text'];
                } else {
                    $g_text = '1 2 3 4 5 6 7 8 9 0 A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z';
                }

                $style = '';
                if ( isset( $this->field['preview']['always_display'] ) ) {
                    if ( true === filter_var( $this->field['preview']['always_display'], FILTER_VALIDATE_BOOLEAN ) ) {
                        if ( $isGoogleFont == true ) {
                            $this->parent->typography_preview[ $fontFamily[0] ] = array(
                                'font-style' => array( $this->value['font-weight'] . $this->value['font-style'] ),
                                'subset'     => array( $this->value['subsets'] )
                            );

                            $protocol = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https:" : "http:";

                            wp_deregister_style( 'redux-typography-preview' );
                            wp_dequeue_style( 'redux-typography-preview' );

                            wp_register_style( 'redux-typography-preview', $protocol . $this->makeGoogleWebfontLink( $this->parent->typography_preview ), '', time() );
                            wp_enqueue_style( 'redux-typography-preview' );
                        }

                        $style = 'display: block; font-family: ' . $this->value['font-family'] . '; font-weight: ' . $this->value['font-weight'] . ';';
                    }
                }

                if ( isset( $this->field['preview']['font-size'] ) ) {
                    $style .= 'font-size: ' . $this->field['preview']['font-size'] . ';';
                    $inUse = '1';
                } else {
                    //$g_size = '';
                    $inUse = '0';
                }

                echo '<p data-preview-size="' . $inUse . '" class="clear ' . $this->field['id'] . '_previewer typography-preview" ' . 'style="' . $style . '">' . $g_text . '</p>';
                echo '</div>'; // end typography container
            }
        }  //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            if (!wp_style_is('select2-css')) {
                wp_enqueue_style( 'select2-css' );
            }

            if (!wp_style_is('wp-color-picker')) {
                wp_enqueue_style( 'wp-color-picker' );
            }

            if (!wp_script_is ( 'redux-field-typography-js' )) {
                wp_enqueue_script(
                    'redux-field-typography-js',
                    ReduxFramework::$_url . 'inc/fields/typography/field_typography' . Redux_Functions::isMin() . '.js',
                    array( 'jquery', 'wp-color-picker', 'select2-js', 'redux-js' ),
                    time(),
                    true
                );
            }
            
            wp_localize_script(
                'redux-field-typography-js',
                'redux_ajax_script',
                array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
            );

            if ($this->parent->args['dev_mode']) {
                if (!wp_style_is('redux-color-picker-css')) {
                    wp_enqueue_style( 'redux-color-picker-css' );
                }

                if (!wp_style_is('redux-field-typography-css')) {
                    wp_enqueue_style(
                        'redux-field-typography-css',
                        ReduxFramework::$_url . 'inc/fields/typography/field_typography.css',
                        array(),
                        time(),
                        'all'
                    );
                }
            }
        }  //function

        /**
         * makeGoogleWebfontLink Function.
         * Creates the google fonts link.
         *
         * @since ReduxFramework 3.0.0
         */
        function makeGoogleWebfontLink( $fonts ) {
            $link    = "";
            $subsets = array();

            foreach ( $fonts as $family => $font ) {
                if ( ! empty( $link ) ) {
                    $link .= "%7C"; // Append a new font to the string
                }
                $link .= $family;

                if ( ! empty( $font['font-style'] ) || ! empty( $font['all-styles'] ) ) {
                    $link .= ':';
                    if ( ! empty( $font['all-styles'] ) ) {
                        $link .= implode( ',', $font['all-styles'] );
                    } else if ( ! empty( $font['font-style'] ) ) {
                        $link .= implode( ',', $font['font-style'] );
                    }
                }

                if ( ! empty( $font['subset'] ) ) {
                    foreach ( $font['subset'] as $subset ) {
                        if ( ! in_array( $subset, $subsets ) ) {
                            array_push( $subsets, $subset );
                        }
                    }
                }
            }

            if ( ! empty( $subsets ) ) {
                $link .= "&amp;subset=" . implode( ',', $subsets );
            }


            return '//fonts.googleapis.com/css?family=' . str_replace( '|', '%7C', $link );
        }

        /**
         * makeGoogleWebfontString Function.
         * Creates the google fonts link.
         *
         * @since ReduxFramework 3.1.8
         */
        function makeGoogleWebfontString( $fonts ) {
            $link    = "";
            $subsets = array();

            foreach ( $fonts as $family => $font ) {
                if ( ! empty( $link ) ) {
                    $link .= "', '"; // Append a new font to the string
                }
                $link .= $family;

                if ( ! empty( $font['font-style'] ) || ! empty( $font['all-styles'] ) ) {
                    $link .= ':';
                    if ( ! empty( $font['all-styles'] ) ) {
                        $link .= implode( ',', $font['all-styles'] );
                    } else if ( ! empty( $font['font-style'] ) ) {
                        $link .= implode( ',', $font['font-style'] );
                    }
                }

                if ( ! empty( $font['subset'] ) ) {
                    foreach ( $font['subset'] as $subset ) {
                        if ( ! in_array( $subset, $subsets ) ) {
                            array_push( $subsets, $subset );
                        }
                    }
                }
            }

            if ( ! empty( $subsets ) ) {
                $link .= "&amp;subset=" . implode( ',', $subsets );
            }

            return "'" . $link . "'";
        }

        function output() {
            $font = $this->value;

            // Shim out old arg to new
            if ( isset( $this->field['all_styles'] ) && ! empty( $this->field['all_styles'] ) ) {
                $this->field['all-styles'] = $this->field['all_styles'];
                unset ( $this->field['all_styles'] );
            }

            // Check for font-backup.  If it's set, stick it on a variabhle for
            // later use.
            if ( ! empty( $font['font-family'] ) && ! empty( $font['font-backup'] ) ) {
                $font['font-family'] = str_replace( ', ' . $font['font-backup'], '', $font['font-family'] );
                $fontBackup          = ',' . $font['font-backup'];
            }

//                if (strpos($font['font-family'], ' ')) {
//                    $font['font-family'] = '"' . $font['font-family'] . '"';
//                }

            $style = '';

            $fontValueSet = false;

            if ( ! empty( $font ) ) {
                foreach ( $font as $key => $value ) {
                    if ( ! empty( $value ) && in_array( $key, array( 'font-family', 'font-weight' ) ) ) {
                        $fontValueSet = true;
                    }
                }
            }

            if ( ! empty( $font ) ) {
                foreach ( $font as $key => $value ) {
                    if ( $key == 'font-options' ) {
                        continue;
                    }
                    // Check for font-family key
                    if ( 'font-family' == $key ) {

                        // Enclose font family in quotes if spaces are in the
                        // name.  This is necessary because if there are numerics
                        // in the font name, they will not render properly.
                        // Google should know better.
                        if (strpos($value, ' ') && !strpos($value, ',')){
                            $value = '"' . $value . '"';
                        }

                        // Ensure fontBackup isn't empty (we already option
                        // checked this earlier.  No need to do it again.
                        if ( ! empty( $fontBackup ) ) {

                            // Apply the backup font to the font-family element
                            // via the saved variable.  We do this here so it
                            // doesn't get appended to the Google stuff below.
                            $value .= $fontBackup;
                        }
                    }

                    if ( empty( $value ) && in_array( $key, array(
                            'font-weight',
                            'font-style'
                        ) ) && $fontValueSet == true
                    ) {
                        $value = "normal";
                    }

                    if ($key == 'font-weight' && $this->field['font-weight'] == false) {
                        continue;
                    }

                    if ($key == 'font-style' && $this->field['font-style'] == false) {
                        continue;
                    }


                    if ( $key == "google" || $key == "subsets" || $key == "font-backup" || empty( $value ) ) {
                        continue;
                    }
                    $style .= $key . ':' . $value . ';';
                }
                if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                    $style .= 'opacity: 1;visibility: visible;-webkit-transition: opacity 0.24s ease-in-out;-moz-transition: opacity 0.24s ease-in-out;transition: opacity 0.24s ease-in-out;';
                }
            }

            if ( ! empty( $style ) ) {
                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $keys = implode( ",", $this->field['output'] );
                    $this->parent->outputCSS .= $keys . "{" . $style . '}';
                    if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                        $key_string    = "";
                        $key_string_ie = "";
                        foreach ( $this->field['output'] as $value ) {
                            $key_string .= ".wf-loading " . $value . ',';
                            $key_string_ie .= ".ie.wf-loading " . $value . ',';
                        }
                        $this->parent->outputCSS .= $key_string . "{opacity: 0;}";
                        $this->parent->outputCSS .= $key_string_ie . "{visibility: hidden;}";
                    }
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $keys = implode( ",", $this->field['compiler'] );
                    $this->parent->compilerCSS .= $keys . "{" . $style . '}';
                    if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                        $key_string    = "";
                        $key_string_ie = "";
                        foreach ( $this->field['compiler'] as $value ) {
                            $key_string .= ".wf-loading " . $value . ',';
                            $key_string_ie .= ".ie.wf-loading " . $value . ',';
                        }
                        $this->parent->compilerCSS .= $key_string . "{opacity: 0;}";
                        $this->parent->compilerCSS .= $key_string_ie . "{visibility: hidden;}";
                    }
                }
            }

            // Google only stuff!
            if ( ! empty( $font['font-family'] ) && ! empty( $this->field['google'] ) && filter_var( $this->field['google'], FILTER_VALIDATE_BOOLEAN ) ) {

                // Added standard font matching check to avoid output to Google fonts call - kp
                // If no custom font array was supplied, the load it with default
                // standard fonts.
                if ( empty( $this->field['fonts'] ) ) {
                    $this->field['fonts'] = $this->std_fonts;
                }

                // Ensure the fonts array is NOT empty
                if ( ! empty( $this->field['fonts'] ) ) {

                    //Make the font keys in the array lowercase, for case-insensitive matching
                    $lcFonts = array_change_key_case( $this->field['fonts'] );

                    // Rebuild font array with all keys stripped of spaces
                    $arr = array();
                    foreach ( $lcFonts as $key => $value ) {
                        $key         = str_replace( ', ', ',', $key );
                        $arr[ $key ] = $value;
                    }

                    $lcFonts = array_change_key_case( $this->field['custom_fonts'] );
                    foreach ( $lcFonts as $group => $fontArr ) {
                        foreach ( $fontArr as $key => $value ) {
                            $arr[ strtolower( $key ) ] = $key;
                        }
                    }

                    $lcFonts = $arr;

                    unset( $arr );

                    // lowercase chosen font for matching purposes
                    $lcFont = strtolower( $font['font-family'] );

                    // Remove spaces after commas in chosen font for mathcing purposes.
                    $lcFont = str_replace( ', ', ',', $lcFont );

                    // If the lower cased passed font-family is NOT found in the standard font array
                    // Then it's a Google font, so process it for output.
                    if ( ! array_key_exists( $lcFont, $lcFonts ) ) {
                        $family = $font['font-family'];

                        // Strip out spaces in font names and replace with with plus signs
                        // TODO?: This method doesn't respect spaces after commas, hence the reason
                        // for the std_font array keys having no spaces after commas.  This could be
                        // fixed with RegEx in the future.
                        $font['font-family'] = str_replace( ' ', '+', $font['font-family'] );

                        // Push data to parent typography variable.
                        if ( empty( $this->parent->typography[ $font['font-family'] ] ) ) {
                            $this->parent->typography[ $font['font-family'] ] = array();
                        }

                        if ( isset( $this->field['all-styles'] ) ) {
                            if ( ! isset( $font['font-options'] ) || empty( $font['font-options'] ) ) {
                                $this->getGoogleArray();

                                if ( isset( $this->parent->googleArray ) && ! empty( $this->parent->googleArray ) && isset( $this->parent->googleArray[ $family ] ) ) {
                                    $font['font-options'] = $this->parent->googleArray[ $family ];
                                }
                            } else {
                                $font['font-options'] = json_decode( $font['font-options'], true );
                            }
                            //print_r($font['font-options']);
                            //exit();
                        }

                        if ( isset( $font['font-options'] ) && ! empty( $font['font-options'] ) && isset( $this->field['all-styles'] ) && filter_var( $this->field['all-styles'], FILTER_VALIDATE_BOOLEAN ) ) {
                            if ( isset( $font['font-options'] ) && ! empty( $font['font-options']['variants'] ) ) {
                                if ( ! isset( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) || empty( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) ) {
                                    $this->parent->typography[ $font['font-family'] ]['all-styles'] = array();
                                    foreach ( $font['font-options']['variants'] as $variant ) {
                                        $this->parent->typography[ $font['font-family'] ]['all-styles'][] = $variant['id'];
                                    }
                                }
                            }
                        }

                        if ( ! empty( $font['font-weight'] ) ) {
                            if ( empty( $this->parent->typography[ $font['font-family'] ]['font-weight'] ) || ! in_array( $font['font-weight'], $this->parent->typography[ $font['font-family'] ]['font-weight'] ) ) {
                                $style = $font['font-weight'];
                            }

                            if ( ! empty( $font['font-style'] ) ) {
                                $style .= $font['font-style'];
                            }

                            if ( empty( $this->parent->typography[ $font['font-family'] ]['font-style'] ) || ! in_array( $style, $this->parent->typography[ $font['font-family'] ]['font-style'] ) ) {
                                $this->parent->typography[ $font['font-family'] ]['font-style'][] = $style;
                            }
                        }

                        if ( ! empty( $font['subsets'] ) ) {
                            if ( empty( $this->parent->typography[ $font['font-family'] ]['subset'] ) || ! in_array( $font['subsets'], $this->parent->typography[ $font['font-family'] ]['subset'] ) ) {
                                $this->parent->typography[ $font['font-family'] ]['subset'][] = $font['subsets'];
                            }
                        }
                    } // !array_key_exists
                } //!empty fonts array
            } // Typography not set
        }

        private function localizeStdFonts() {
            if ( false == $this->user_fonts ) {
                if ( isset( $this->parent->fonts['std'] ) && ! empty( $this->parent->fonts['std'] ) ) {
                    return;
                }

                $this->parent->font_groups['std'] = array(
                    'text'     => __( 'Standard Fonts', 'redux-framework' ),
                    'children' => array(),
                );

                foreach ( $this->field['fonts'] as $font => $extra ) {
                    $this->parent->font_groups['std']['children'][] = array(
                        'id'          => $font,
                        'text'        => $font,
                        'data-google' => 'false',
                    );
                }
            }

            if ( $this->field['custom_fonts'] !== false ) {
                $this->field['custom_fonts'] = apply_filters( "redux/{$this->parent->args['opt_name']}/field/typography/custom_fonts", array() );

                if ( ! empty( $this->field['custom_fonts'] ) ) {
                    foreach ( $this->field['custom_fonts'] as $group => $fonts ) {
                        $this->parent->font_groups['customfonts'] = array(
                            'text'     => $group,
                            'children' => array(),
                        );

                        foreach ( $fonts as $family => $v ) {
                            $this->parent->font_groups['customfonts']['children'][] = array(
                                'id'          => $family,
                                'text'        => $family,
                                'data-google' => 'false',
                            );
                        }
                    }
                }
            }
        }

        /**
         *   Construct the google array from the stored JSON/HTML

         */
        function getGoogleArray() {

            if ( ( isset( $this->parent->fonts['google'] ) && ! empty( $this->parent->fonts['google'] ) ) || isset( $this->parent->fonts['google'] ) && $this->parent->fonts['google'] == false ) {
                return;
            }

            $gFile = dirname( __FILE__ ) . '/googlefonts.php';

            // Weekly update
            if ( isset( $this->parent->args['google_update_weekly'] ) && $this->parent->args['google_update_weekly'] && ! empty( $this->parent->args['google_api_key'] ) ) {

                if ( file_exists( $gFile ) ) {
                    // Keep the fonts updated weekly
                    $weekback     = strtotime( date( 'jS F Y', time() + ( 60 * 60 * 24 * - 7 ) ) );
                    $last_updated = filemtime( $gFile );
                    if ( $last_updated < $weekback ) {
                        unlink( $gFile );
                    }
                }
            }

            if ( ! file_exists( $gFile ) ) {

                $result = @wp_remote_get( apply_filters( 'redux-google-fonts-api-url', 'https://www.googleapis.com/webfonts/v1/webfonts?key=' ) . $this->parent->args['google_api_key'], array( 'sslverify' => false ) );

                if ( ! is_wp_error( $result ) && $result['response']['code'] == 200 ) {
                    $result = json_decode( $result['body'] );
                    foreach ( $result->items as $font ) {
                        $this->parent->googleArray[ $font->family ] = array(
                            'variants' => $this->getVariants( $font->variants ),
                            'subsets'  => $this->getSubsets( $font->subsets )
                        );
                    }

                    if ( ! empty( $this->parent->googleArray ) ) {
                        $this->parent->filesystem->execute( 'put_contents', $gFile, array( 'content' => "<?php return json_decode( '" . json_encode( $this->parent->googleArray ) . "', true );" ) );
                    }
                }
            }

            if ( ! file_exists( $gFile ) ) {
                $this->parent->fonts['google'] = false;

                return;
            }

            if ( ! isset( $this->parent->fonts['google'] ) || empty( $this->parent->fonts['google'] ) ) {

                $fonts = include $gFile;

                if ( $fonts === true ) {
                    $this->parent->fonts['google'] = false;

                    return;
                }

                if ( isset( $fonts ) && ! empty( $fonts ) && is_array( $fonts ) && $fonts != false ) {
                    $this->parent->fonts['google'] = $fonts;
                    $this->parent->googleArray     = $fonts;

                    // optgroup
                    $this->parent->font_groups['google'] = array(
                        'text'     => __( 'Google Webfonts', 'redux-framework' ),
                        'children' => array(),
                    );

                    // options
                    foreach ( $this->parent->fonts['google'] as $font => $extra ) {
                        $this->parent->font_groups['google']['children'][] = array(
                            'id'          => $font,
                            'text'        => $font,
                            'data-google' => 'true'
                        );
                    }
                }
            }
        }

        /**
         * getSubsets Function.
         * Clean up the Google Webfonts subsets to be human readable
         *
         * @since ReduxFramework 0.2.0
         */
        private function getSubsets( $var ) {
            $result = array();

            foreach ( $var as $v ) {
                if ( strpos( $v, "-ext" ) ) {
                    $name = ucfirst( str_replace( "-ext", " Extended", $v ) );
                } else {
                    $name = ucfirst( $v );
                }

                array_push( $result, array(
                    'id'   => $v,
                    'name' => $name
                ) );
            }

            return array_filter( $result );
        }  //function

        /**
         * getVariants Function.
         * Clean up the Google Webfonts variants to be human readable
         *
         * @since ReduxFramework 0.2.0
         */
        private function getVariants( $var ) {
            $result = array();
            $italic = array();

            foreach ( $var as $v ) {
                $name = "";
                if ( $v[0] == 1 ) {
                    $name = 'Ultra-Light 100';
                } else if ( $v[0] == 2 ) {
                    $name = 'Light 200';
                } else if ( $v[0] == 3 ) {
                    $name = 'Book 300';
                } else if ( $v[0] == 4 || $v[0] == "r" || $v[0] == "i" ) {
                    $name = 'Normal 400';
                } else if ( $v[0] == 5 ) {
                    $name = 'Medium 500';
                } else if ( $v[0] == 6 ) {
                    $name = 'Semi-Bold 600';
                } else if ( $v[0] == 7 ) {
                    $name = 'Bold 700';
                } else if ( $v[0] == 8 ) {
                    $name = 'Extra-Bold 800';
                } else if ( $v[0] == 9 ) {
                    $name = 'Ultra-Bold 900';
                }

                if ( $v == "regular" ) {
                    $v = "400";
                }

                if ( strpos( $v, "italic" ) || $v == "italic" ) {
                    $name .= " Italic";
                    $name = trim( $name );
                    if ( $v == "italic" ) {
                        $v = "400italic";
                    }
                    $italic[] = array(
                        'id'   => $v,
                        'name' => $name
                    );
                } else {
                    $result[] = array(
                        'id'   => $v,
                        'name' => $name
                    );
                }
            }

            foreach ( $italic as $item ) {
                $result[] = $item;
            }

            return array_filter( $result );
        }   //function
    }       //class
}           //class exists
