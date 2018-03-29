<?php

    if ( ! class_exists( 'Redux_Customizer_Control_rAds' ) && ! class_exists( 'Redux_Customizer_section_rAds' ) ) {
        /**
         * Customizer section representing widget area (sidebar).
         *
         * @package    WordPress
         * @subpackage Customize
         * @since      4.1.0
         * @see        WP_Customize_Section
         */
        class Redux_Customizer_section_rAds extends WP_Customize_Section {

            /**
             * Type of this section.
             *
             * @since  4.1.0
             * @access public
             * @var string
             */
            public $type = 'redux-rAds';

            protected function render() {
                ?>
                    <li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="accordion-section rAdsContainer"></li>
                <?php
            }
        }

        class Redux_Customizer_Control_rAds extends WP_Customize_Control {
            public function render() {
            }

            public function label() {
            }

            public function description() {
            }

            public function title() {
            }
        }
    }