<?php
/**
 * Add exit-survey logic to plugins-page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeExitSurvey
{
    function __construct() {
        global $pagenow;

        if ( 'plugins.php' != $pagenow ) {
            return;
        }

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_survey_scripts' ) );
        add_action( 'admin_footer', array( $this, 'render_survey_model' ) );
    }

    function enqueue_survey_scripts() {
        wp_enqueue_script( 'ao_exit_survey', plugins_url( '/static/exit-survey/exit-survey.js', __FILE__ ), array( 'jquery' ), AUTOPTIMIZE_PLUGIN_VERSION );
        wp_enqueue_style( 'ao_exit_survey', plugins_url( '/static/exit-survey/exit-survey.css', __FILE__ ), null, AUTOPTIMIZE_PLUGIN_VERSION );
    }

    function render_survey_model() {
        global $wp_version;

        $data = array(
            'home' => home_url(),
            'dest' => 'aHR0cHM6Ly9taXNjLm9wdGltaXppbmdtYXR0ZXJzLmNvbS9hb19leGl0X3N1cnZleS9pbmRleC5waHA=',
        );
        ?>

        <div class="ao-plugin-uninstall-feedback-popup ao-feedback" id="ao_uninstall_feedback_popup" data-modal="<?php echo base64_encode( json_encode( $data ) ); ?>">
            <div class="popup--header">
                <h5><?php _e( 'Sorry to see you go!', 'autoptimize' ); ?></h5>
            </div><!--/.popup--header-->
            <div class="popup--body">
                <p><strong><?php _e( 'We would appreciate if you let us know why you\'re deactivating Autoptimize!', 'autoptimize' ); ?></strong></p>
                <ul class="popup--form">
                    <li ao-option-id="5">
                        <input type="radio" name="ao-deactivate-option" id="ao_feedback5">
                        <label for="ao_feedback5">
                            <?php _e( 'I don\'t see a performance improvement.', 'autoptimize' ); ?>
                        </label>
                        <p class="last-attempt"><?php _e( 'As Autoptimize does not do page caching, you might have to install e.g. KeyCDN Cache Enabler or WP Super Cache as well. Feel free to create a topic on <a href="https://wordpress.org/support/plugin/autoptimize/#new-topic-0" target="_blank">the support forum here</a> to get pointers on how get the most out of Autoptimize!', 'autoptimize' ); ?></p>
                    </li>
                    <li ao-option-id="6">
                        <input type="radio" name="ao-deactivate-option" id="ao_feedback6">
                        <label for="ao_feedback6" data-reason="broke site">
                            <?php _e( 'It broke my site.', 'autoptimize' ); ?>
                        </label>
                        <p class="last-attempt"><?php _e( 'Ouch, sorry about that! But almost all problems can be fixed with the right configuration, have a look at <a href="https://blog.futtta.be/2022/05/05/what-to-do-when-autoptimize-breaks-your-site/" target="_blank">this short troubleshooting howto</a> or create a topic on <a href="https://wordpress.org/support/plugin/autoptimize/#new-topic-0" target="_blank">the support forum here</a>!', 'autoptimize' ); ?></p>
                    <li ao-option-id="4">
                        <input type="radio" name="ao-deactivate-option" id="ao_feedback4">
                        <label for="ao_feedback4" data-reason="found better">
                            <?php _e( 'I found a better solution.', 'autoptimize' ); ?>
                        </label>
                    <li ao-option-id="3">
                        <input type="radio" name="ao-deactivate-option" id="ao_feedback3">
                        <label for="ao_feedback3" data-reason="just temporarily">
                            <?php _e( 'I\'m just disabling temporarily.', 'autoptimize' ); ?>
                        </label>
                    <li ao-option-id="999">
                        <input type="radio" name="ao-deactivate-option" id="ao_feedback999">
                        <label for="ao_feedback999" data-reason="other">
                            <?php _e( 'Other (please specify below)', 'autoptimize' ); ?>
                        </label>
                        <textarea width="100%" rows="2" name="comments" placeholder="What can we do better?"></textarea></li>
                    <hr />
                    <li ao-option-id="998">
                        <label for="ao_feedback_email_toggle" data-reason="other detail">
                            <input type="checkbox" id="ao_feedback_email_toggle" name="ao_feedback_email_toggle" />
                            <?php _e( 'I would like be contacted about my experience with Autoptimize.', 'autoptimize' ); ?>
                        </label>
                        <input type="email" name="ao-deactivate-option" id="ao_feedback998" placeholder="mymail@domain.xyz" class="hidden">
                    </li>
                </ul>
            </div><!--/.popup--body-->
            <div class="popup--footer">
                <div class="actions">
                    <a href="#" class="info-disclosure-link"><?php _e( 'What info do we collect?', 'autoptimize' ); ?></a>
                    <div class="info-disclosure-content"><p><?php _e( 'Below is a detailed view of all data that Optimizing Matters will receive if you fill in this survey. Your email address is only shared if you explicitly fill it in, your IP addres is never sent.', 'autoptimize' ); ?></p>
                        <ul>
                            <li><strong><?php _e( 'Plugin version', 'autoptimize' ); ?> </strong> <code id="ao_plugin_version"> <?php echo AUTOPTIMIZE_PLUGIN_VERSION; ?> </code></li>
                            <li><strong><?php _e( 'WordPress version', 'autoptimize' ); ?> </strong> <code id="core_version"> <?php echo $wp_version; ?> </code></li>
                            <li><strong><?php _e( 'Current website:', 'autoptimize' ); ?></strong> <code> <?php echo trailingslashit( get_site_url() ); ?> </code></li>
                            <li><strong><?php _e( 'Uninstall reason', 'autoptimize' ); ?> </strong> <i> <?php _e( 'Selected reason from the above survey', 'autoptimize' ); ?> </i></li>
                        </ul>
                    </div>
                    <div class="buttons">
                        <input type="submit"
                               name="ao-deactivate-no"
                               id="ao-deactivate-no"
                               class="button"
                               value="Just Deactivate">
                        <input type="submit"
                               name="ao-deactivate-cancel"
                               id="ao-deactivate-cancel"
                               class="button"
                               value="Cancel">
                        <input type="submit"
                               name="ao-deactivate-yes"
                               id="ao-deactivate-yes"
                               class="button button-primary"
                               value="Submit &amp; Deactivate"
                               data-after-text="Submit &amp; Deactivate"
                               disabled="1"></div>

                </div><!--/.actions-->
            </div><!--/.popup--footer-->
        </div>
        <?php
    }
}
