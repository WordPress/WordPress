<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-stat">
    <div class="woof-tabs woof-tabs-style-line">

        <nav>
            <ul>
                <li>
                    <a href="#woof-step-filter-1">
                        <span class="icon-cog-outline"></span>
                        <span><?php esc_html_e("Step by step filter", 'woocommerce-products-filter') ?></span>
                    </a>
                </li>

            </ul>
        </nav>

        <div class="content-wrap">
            <section id="woof-step-filter-1">
                <div class="woof-control-section">
                    <div class="woof-control-container">
                        <p class="description">

                        </p>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h4 class="woof_fix5"><?php esc_html_e('Statistical parameters:', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">
                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('Select taxonomy, taxonomies combinations OR leave this field empty to see general data for all the most requested taxonomies', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
            </section>

        </div>

    </div>
</section>


