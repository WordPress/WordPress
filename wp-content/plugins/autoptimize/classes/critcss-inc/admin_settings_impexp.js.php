<?php
/**
 * Javascript to import and export AO CCSS settings.
 */

?>
// Export and download settings
function exportSettings( idToEdit ) {
    console.log('Exporting...');
    var data = {
        'action': 'ao_ccss_export',
        'ao_ccss_export_nonce': '<?php echo wp_create_nonce( 'ao_ccss_export_nonce' ); ?>',
    };

    jQuery.post(ajaxurl, data, function(response) {
        response_array=JSON.parse(response);
        if (response_array['code'] == 200) {
            <?php
            if ( is_multisite() ) {
                $blog_id = '/' . get_current_blog_id() . '/';
            } else {
                $blog_id = '/';
            }
            ?>
            export_url = '<?php echo content_url(); ?>/uploads/ao_ccss' + '<?php echo $blog_id; ?>' + response_array['file'];
            msg = "Download export-file from: <a href=\"" + export_url + "\" target=\"_blank\">"+ export_url + "</a>";
        } else {
            msg = response_array['msg'];
        }
        jQuery("#importdialog").html(msg);
        jQuery("#importdialog").dialog({
            autoOpen: true,
            height: 210,
            width: 700,
            title: "<?php _e( 'Export settings result', 'autoptimize' ); ?>",
            modal: true,
            buttons: {
                OK: function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    });
}

// Upload and import settings
function upload(){
    var fd = new FormData();
    var file = jQuery(document).find('#settingsfile');
    var settings_file = file[0].files[0];
    fd.append('file', settings_file);
    fd.append('action', 'ao_ccss_import');
    fd.append('ao_ccss_import_nonce', '<?php echo wp_create_nonce( 'ao_ccss_import_nonce' ); ?>');

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function(response) {
            response_array=JSON.parse(response);
            if (response_array['code'] == 200) {
                window.location.reload();
            }
        }
    });
}
