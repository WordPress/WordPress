<?php
/**
 * JS code to manage queue.
 */

?>
// Hide object text box
var queueOriginEl = document.getElementById('ao-ccss-queue' );
if (queueOriginEl) {
    queueOriginEl.style.display = 'none';

    // Get queue object and call table renderer
    jQuery(document).ready(function() {
        // Instance and parse queue object
        var aoCssQueueRaw = document.getElementById('ao-ccss-queue').value;
        var aoCssQueue    = aoCssQueueRaw.indexOf('{"') === 0 ?
                                JSON.parse(aoCssQueueRaw) :
                                "";
        var aoCssQueueLog = aoCssQueue === "" ?
                                "empty" :
                                aoCssQueue;
        <?php
        if ( $ao_ccss_debug ) {
            echo "console.log( 'Queue Object:', aoCssQueueLog );\n";
        }
        ?>

        // hook up "remove all jobs" button to the JS action.
        jQuery("#removeAllJobs").click(function(){removeAllJobs();});

        // Render queue table
        drawQueueTable(aoCssQueue);

        // Make queue table sortable if there are any elements
        var queueBodyEl = jQuery('#queue > tr').length;
        if (queueBodyEl > 0) {
            jQuery('#queue-tbl').tablesorter({
                sortList: [[0,0]],
                headers: {6: {sorter: false}}
            });
        }
        
        // unhide queuerunner button conditionally (we don't want people running the queue continuously) and attach event to it.
        if (queueBodyEl > 4 || ( queueBodyEl > 0 && jQuery('#rules > tr').length < 1 ) ) {
            jQuery('#queuerunner-container').show();
            jQuery("#queuerunner").click(function(){queuerunner();});
        }
    });
}

// Render the queue in a table
function drawQueueTable(queue) {
    jQuery('#queue').empty();
    rowNumber=0;
    jQuery.each(queue, function(path, keys) {
        // Prepare commom job values
        ljid      = keys.ljid;
        targetArr = keys.rtarget.split('|' );
        target    = targetArr[1];
        type      = keys.ptype;
        ctime     = EpochToDate(keys.jctime);
        rbtn      = false;
        dbtn      = false;
        hbtn      = false;

        // don't list jobs that don't have a type, they are irrelevant and this also avoids "type.replace is not a function".
        if ( type == false ) {
            return;
        }

        // Prepare job statuses
        if (keys.jqstat === 'NEW') {
            // Status: NEW (N, sort order 1)
            status      = '<span class="hidden">1</span>N';
            statusClass = 'new';
            title       = '<?php _e( 'New', 'autoptimize' ); ?> (' + ljid + ')';
            buttons     = '<?php _e( 'None', 'autoptimize' ); ?>';
        } else if (keys.jqstat === 'JOB_QUEUED' || keys.jqstat === 'JOB_ONGOING') {
            // Status: PENDING (P, sort order 2)
            status      = '<span class="hidden">2</span>P';
            statusClass = 'pending';
            title       = '<?php _e( 'PENDING', 'autoptimize' ); ?> (' + ljid + ')';
            buttons     = '<?php _e( 'None', 'autoptimize' ); ?>';
        } else if (keys.jqstat === 'JOB_DONE' && keys.jrstat === 'GOOD' && (keys.jvstat === 'WARN' || keys.jvstat === 'BAD')) {
            // Status: REVIEW (R, sort order 5)
            status      = '<span class="hidden">5</span>R';
            statusClass = 'review';
            title       = "<?php _e( 'REVIEW', 'autoptimize' ); ?> (" + ljid + ")\n<?php _e( 'Info from criticalcss.com:', 'autoptimize' ); ?>\n<?php _e( '- Job ID: ', 'autoptimize' ); ?>" + keys.jid + "\n<?php _e( '- Status: ', 'autoptimize' ); ?>" + keys.jqstat + "\n<?php _e( '- Result: ', 'autoptimize' ); ?>" + keys.jrstat + "\n<?php _e( '- Validation: ', 'autoptimize' ); ?>" + keys.jvstat;
            buttons     = '<span class="button-secondary" id="' + ljid + '_remove" title="<?php _e( 'Delete Job', 'autoptimize' ); ?>"><span class="dashicons dashicons-trash"></span></span>';
            dbtn        = true;
        } else if (keys.jqstat === 'JOB_DONE') {
            // Status: DONE (D, sort order 6)
            status      = '<span class="hidden">6</span>D';
            statusClass = 'done';
            title       = '<?php _e( 'DONE', 'autoptimize' ); ?> (' + ljid + ')';
            buttons     = '<span class="button-secondary" id="' + ljid + '_remove" title="<?php _e( 'Delete Job', 'autoptimize' ); ?>"><span class="dashicons dashicons-trash"></span></span>';
            dbtn        = true;
        } else if (keys.jqstat === 'JOB_FAILED' || keys.jqstat === 'STATUS_JOB_BAD' || keys.jqstat === 'INVALID_JWT_TOKEN' || keys.jqstat === 'NO_CSS' || keys.jqstat === 'NO_RESPONSE') {
        // Status: ERROR (E, sort order 4)
            status      = '<span class="hidden">4</span>E';
            statusClass = 'error';
            title       = "<?php _e( 'ERROR', 'autoptimize' ); ?> (" + ljid + ")\n<?php _e( 'Info from criticalcss.com:', 'autoptimize' ); ?>\n<?php _e( '- Job ID: ', 'autoptimize' ); ?>" + keys.jid + "\n<?php _e( '- Status: ', 'autoptimize' ); ?>" + keys.jqstat + "\n<?php _e( '- Result: ', 'autoptimize' ); ?>" + keys.jrstat + "\n<?php _e( '- Validation: ', 'autoptimize' ); ?>" + keys.jvstat;
            buttons     = '<span class="button-secondary" id="' + ljid + '_retry" title="<?php _e( 'Retry Job', 'autoptimize' ); ?>"><span class="dashicons dashicons-update"></span></span><span class="button-secondary to-right" id="' + ljid + '_remove" title="<?php _e( 'Delete Job', 'autoptimize' ); ?>"><span class="dashicons dashicons-trash"></span></span><span class="button-secondary to-right" id="' + ljid + '_help" title="<?php _e( 'Get Help', 'autoptimize' ); ?>"><span class="dashicons dashicons-sos"></span></span>';
            rbtn        = true;
            dbtn        = true;
            hbtn        = true;
        } else {
            // Status: UNKNOWN (U, sort order 5)
            status      = '<span class="hidden">5</span>U';
            statusClass = 'unknown';
            title       = "<?php _e( 'UNKNOWN', 'autoptimize' ); ?> (" + ljid + ")\n<?php _e( 'Info from criticalcss.com:', 'autoptimize' ); ?>\n<?php _e( '- Job ID: ', 'autoptimize' ); ?>" + keys.jid + "\n<?php _e( '- Status: ', 'autoptimize' ); ?>" + keys.jqstat + "\n<?php _e( '- Result: ', 'autoptimize' ); ?>" + keys.jrstat + "\n<?php _e( '- Validation: ', 'autoptimize' ); ?>" + keys.jvstat;
            buttons     = '<span class="button-secondary" id="' + ljid + '_remove" title="<?php _e( 'Delete Job', 'autoptimize' ); ?>"><span class="dashicons dashicons-trash"></span></span><span class="button-secondary to-right" id="' + ljid + '_help" title="<?php _e( 'Get Help', 'autoptimize' ); ?>"><span class="dashicons dashicons-sos"></span></span>';
            dbtn        = true;
            hbtn        = true;
        }

        // Prepare job finish time
        if (keys.jftime === null) {
            ftime = '<?php _e( 'N/A', 'autoptimize' ); ?>';
        } else {
            ftime = EpochToDate(keys.jftime);
        }

        // Append job entry
        jQuery("#queue").append("<tr id='" + ljid + "' class='job " + statusClass + "'><td class='status'><span class='badge " + statusClass + "' title='<?php _e( 'Job status is ', 'autoptimize' ); ?>" + title + "'>" + status + "</span></td><td>" + target.replace(/(woo_|template_|custom_post_|edd_|bp_|bbp_)/,'') + "</td><td>" + path + "</td><td>" + type.replace(/(woo_|template_|custom_post_|edd_|bp_|bbp_)/,'') + "</td><td>" + ctime + "</td><td>" + ftime + "</td><td class='btn'>" + buttons + "</td></tr>");

        // Attach button actions
        if (rbtn) {
            jQuery('#' + ljid + '_retry').click(function(){retryJob(queue, this.id, path);});
        }
        if (dbtn) {
            jQuery('#' + ljid + '_remove').click(function(){delJob(queue, this.id, path);});
        }
        if (hbtn) {
            jQuery('#' + ljid + '_help').click(function(){jid=this.id.split('_' );window.open('https://criticalcss.com/faq?aoid=' + jid[0], '_blank' );});
        }
    });
}

// Delete a job from the queue
function delJob(queue, jid, jpath) {
    jid = jid.split('_' );
    jQuery('#queue-confirm-rm').dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "<?php _e( 'Delete', 'autoptimize' ); ?>": function() {
                delete queue[jpath];
                updateQueue(queue);
                jQuery(this).dialog('close' );
            },
            "<?php _e( 'Cancel', 'autoptimize' ); ?>": function() {
                jQuery(this).dialog('close' );
            }
        }
    });
}

function removeAllJobs() {
    jQuery( "#queue-confirm-rm-all" ).dialog({
        resizable: false,
        height:235,
        modal: true,
        buttons: {
            "<?php _e( 'Delete all jobs?', 'autoptimize' ); ?>": function() {
                queue=[];
                updateQueue(queue);
                jQuery( this ).dialog( "close" );
            },
            "<?php _e( 'Cancel', 'autoptimize' ); ?>": function() {
                jQuery( this ).dialog( "close" );
            }
        }
    });
}

// Retry jobs with error
function retryJob(queue, jid, jpath) {
    jid = jid.split('_' );
    jQuery('#queue-confirm-retry').dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "<?php _e( 'Retry', 'autoptimize' ); ?>": function() {
                <?php
                if ( $ao_ccss_debug ) {
                    echo "console.log( 'SHOULD retry job:', jid[0], jpath );\n";
                }
                ?>
                queue[jpath].jid = null;
                queue[jpath].jqstat = 'NEW';
                queue[jpath].jrstat = null;
                queue[jpath].jvstat = null;
                queue[jpath].jctime = (new Date).getTime() / 1000;
                queue[jpath].jftime = null;
                updateQueue(queue);
                jQuery(this).dialog('close' );
            },
            "<?php _e( 'Cancel', 'autoptimize' ); ?>": function() {
                jQuery(this).dialog('close' );
            }
        }
    });
}

// Refresh queue
function updateQueue(queue) {
    document.getElementById('ao-ccss-queue').value=JSON.stringify(queue);
    drawQueueTable(queue);
    jQuery('#unSavedWarning').show();
    document.getElementById('ao_title_and_button').scrollIntoView();  
    <?php
    if ( $ao_ccss_debug ) {
        echo "console.log('Updated Queue Object:', queue);\n";
    }
    ?>
}

// Run the queue manually (in case of cron issues/ impatient users).
function queuerunner() {
    var data = {
        'action': 'ao_ccss_queuerunner',
        'ao_ccss_queuerunner_nonce': '<?php echo wp_create_nonce( 'ao_ccss_queuerunner_nonce' ); ?>',
    };

    jQuery.post(ajaxurl, data, function(response) {
        response_array=JSON.parse(response);
        if (response_array['code'] == 200) {
            displayNotice( '<?php _e( 'Queue processed, reloading page.', 'autoptimize' ); ?>', 'success' )
            setTimeout(window.location.reload.bind(window.location), 1.5*1000);
        } else if ( response_array['code'] == 302 ) {
            displayNotice( '<?php _e( 'The queue is locked, retry in a couple of minutes. If this problem persists and the queue is not moving at all remove the <code>wp-content/uploads/ao_ccss/queue.lock</code> file.', 'autoptimize' ); ?>', 'warning' )
        } else {
            displayNotice( '<?php _e( 'Could not process queue.', 'autoptimize' ); ?>', 'error' )
        }
    });
}

// Convert epoch to date for job times
function EpochToDate(epoch) {
    if (epoch < 10000000000)
        epoch *= 1000;
    var epoch = epoch + (new Date().getTimezoneOffset() * -1); //for timeZone
    var sdate = new Date(epoch);
    var ldate = sdate.toLocaleString();
    return ldate;
}
