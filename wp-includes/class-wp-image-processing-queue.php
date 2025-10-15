<?php
/**
 * Image processing queue.
 *
 * @package WordPress
 */

if ( ! class_exists( 'WP_Image_Processing_Queue' ) ) {
        /**
         * Handles the background processing of image related tasks.
         */
        class WP_Image_Processing_Queue {
                const OPTION_QUEUE   = 'wp_image_processing_queue';
                const OPTION_STATUS  = 'wp_image_processing_queue_status';
                const CRON_HOOK      = 'wp_image_processing_queue_run';
                const CACHE_GROUP    = 'image_processing_queue';
                const CACHE_LOCK_KEY = 'wp_image_processing_queue_lock';

                /**
                 * Holds the singleton instance.
                 *
                 * @var WP_Image_Processing_Queue|null
                 */
                protected static $instance = null;

                /**
                 * Returns the singleton instance.
                 *
                 * @return WP_Image_Processing_Queue
                 */
                public static function instance() {
                        if ( null === self::$instance ) {
                                self::$instance = new self();
                        }

                        return self::$instance;
                }

                /**
                 * Constructor.
                 */
                protected function __construct() {
                        if ( false === get_option( 'wp_image_processing_queue_batch_size', false ) ) {
                                add_option( 'wp_image_processing_queue_batch_size', 2, '', false );
                        }

                        if ( false === get_option( 'wp_image_processing_queue_concurrency', false ) ) {
                                add_option( 'wp_image_processing_queue_concurrency', 1, '', false );
                        }

                        add_action( self::CRON_HOOK, array( $this, 'run_worker' ) );
                }

                /**
                 * Enqueues a set of image sub-size generation tasks for an attachment.
                 *
                 * @param int    $attachment_id Attachment post ID.
                 * @param string $file          Absolute path to the original file.
                 * @param array  $new_sizes     Sizes to generate.
                 * @param array  $image_meta    Current image metadata.
                 * @return string Job identifier.
                 */
                public function enqueue_subsizes( $attachment_id, $file, $new_sizes, $image_meta ) {
                        $job_id = wp_generate_uuid4();

                        $sizes = array();
                        foreach ( $new_sizes as $name => $data ) {
                                $sizes[ $name ] = array(
                                        'data'   => $data,
                                        'status' => 'pending',
                                        'error'  => null,
                                );
                        }

                        $job = array(
                                'id'             => $job_id,
                                'attachment_id'  => $attachment_id,
                                'file'           => $file,
                                'sizes'          => $sizes,
                                'status'         => 'queued',
                                'created'        => time(),
                                'updated'        => time(),
                                'locked_at'      => 0,
                                'locked_by'      => '',
                                'rotated_source' => false,
                                'type'           => 'image_subsizes',
                        );

                        $this->update_queue( $job_id, $job );
                        $this->update_status_store(
                                $job_id,
                                array(
                                        'job_id'        => $job_id,
                                        'attachment_id' => $attachment_id,
                                        'status'        => 'queued',
                                        'sizes'         => $this->get_size_states( $job['sizes'] ),
                                        'created'       => $job['created'],
                                        'updated'       => $job['updated'],
                                )
                        );

                        /**
                         * Fires when a new image processing job is enqueued.
                         *
                         * @since 6.9.0
                         *
                         * @param string $job_id        Job identifier.
                         * @param array  $job           Raw job definition.
                         * @param array  $image_meta    Image metadata at the time of queueing.
                         */
                        do_action( 'wp_image_processing_job_enqueued', $job_id, $job, $image_meta );

                        $this->maybe_schedule_runner();

                        return $job_id;
                }

                /**
                 * Retrieves the status for a job.
                 *
                 * @param string $job_id Job identifier.
                 * @return array Status details.
                 */
                public function get_status( $job_id ) {
                        $queue = $this->get_queue();
                        if ( isset( $queue[ $job_id ] ) ) {
                                return $this->format_status( $queue[ $job_id ] );
                        }

                        $store = $this->get_status_store();
                        if ( isset( $store[ $job_id ] ) ) {
                                return $store[ $job_id ];
                        }

                        return array(
                                'job_id' => $job_id,
                                'status' => 'not_found',
                        );
                }

                /**
                 * Returns all queued jobs.
                 *
                 * @return array
                 */
                public function get_jobs() {
                        return $this->get_queue();
                }

                /**
                 * Returns stalled jobs.
                 *
                 * @return array
                 */
                public function get_stalled_jobs() {
                        $threshold = (int) apply_filters( 'wp_image_processing_queue_stalled_timeout', 15 * MINUTE_IN_SECONDS );
                        $now       = time();
                        $stalled   = array();

                        foreach ( $this->get_queue() as $job_id => $job ) {
                                if ( 'processing' !== $job['status'] ) {
                                        continue;
                                }

                                if ( ! empty( $job['updated'] ) && ( $now - (int) $job['updated'] ) > $threshold ) {
                                        $stalled[ $job_id ] = $job;
                                }
                        }

                        return $stalled;
                }

                /**
                 * Processes the queue via cron.
                 */
                public function run_worker() {
                        if ( ! $this->acquire_lock() ) {
                                $this->maybe_schedule_runner();
                                return;
                        }

                        $concurrency = $this->get_concurrency();
                        $jobs        = $this->claim_jobs( $concurrency );

                        if ( empty( $jobs ) ) {
                                $this->release_lock();
                                $this->cleanup_status_store();
                                return;
                        }

                        foreach ( $jobs as $job_id => $job ) {
                                $job = $this->process_job( $job_id, $job );
                                if ( $job ) {
                                        $this->update_queue( $job_id, $job );
                                }
                        }

                        $this->release_lock();

                        if ( ! empty( $this->get_queue() ) ) {
                                $this->maybe_schedule_runner();
                        }

                        $this->cleanup_status_store();
                }

                /**
                 * Updates the stored queue with a job entry.
                 *
                 * @param string $job_id Job identifier.
                 * @param array  $job    Job definition.
                 */
                protected function update_queue( $job_id, $job ) {
                        $queue             = $this->get_queue();
                        $queue[ $job_id ]  = $job;
                        $this->save_queue( $queue );
                }

                /**
                 * Removes a job from the queue.
                 *
                 * @param string $job_id Job identifier.
                 */
                protected function remove_from_queue( $job_id ) {
                        $queue = $this->get_queue();
                        if ( isset( $queue[ $job_id ] ) ) {
                                unset( $queue[ $job_id ] );
                                $this->save_queue( $queue );
                        }
                }

                /**
                 * Claims jobs for processing.
                 *
                 * @param int $max Number of jobs to claim.
                 * @return array
                 */
                protected function claim_jobs( $max ) {
                        $queue    = $this->get_queue();
                        $claimed  = array();
                        $worker   = wp_generate_uuid4();
                        $now      = time();
                        $counter  = 0;

                        foreach ( $queue as $job_id => $job ) {
                                if ( $counter >= $max ) {
                                        break;
                                }

                                if ( 'queued' !== $job['status'] && 'pending' !== $job['status'] && 'processing' !== $job['status'] ) {
                                        continue;
                                }

                                $queue[ $job_id ]['status']    = 'processing';
                                $queue[ $job_id ]['locked_at'] = $now;
                                $queue[ $job_id ]['locked_by'] = $worker;
                                $queue[ $job_id ]['updated']   = $now;

                                $claimed[ $job_id ] = $queue[ $job_id ];
                                ++$counter;
                        }

                        if ( $counter > 0 ) {
                                $this->save_queue( $queue );
                        }

                        return $claimed;
                }

                /**
                 * Processes a single job.
                 *
                 * @param string $job_id Job identifier.
                 * @param array  $job    Job definition.
                 * @return array|null Updated job definition, or null if completed.
                 */
                protected function process_job( $job_id, $job ) {
                        if ( empty( $job['sizes'] ) ) {
                                $job['status']  = 'completed';
                                $job['updated'] = time();
                                $this->finalise_job( $job_id, $job );
                                return null;
                        }

                        $attachment_id = $job['attachment_id'];
                        $file          = $job['file'];

                        if ( empty( $file ) ) {
                                $file = wp_get_original_image_path( $attachment_id );
                        }

                        if ( ! $file || ! file_exists( $file ) ) {
                                $job['status']  = 'failed';
                                $job['updated'] = time();
                                foreach ( $job['sizes'] as $name => $data ) {
                                        if ( 'done' !== $data['status'] ) {
                                                $job['sizes'][ $name ]['status'] = 'error';
                                                $job['sizes'][ $name ]['error']  = __( 'Source file missing.' );
                                        }
                                }

                                $this->finalise_job( $job_id, $job );
                                return null;
                        }

                        $image_meta = wp_get_attachment_metadata( $attachment_id );
                        if ( ! is_array( $image_meta ) ) {
                                $image_meta = array();
                        }

                        if ( ! isset( $image_meta['sizes'] ) || ! is_array( $image_meta['sizes'] ) ) {
                                $image_meta['sizes'] = array();
                        }

                        $batch_size = $this->get_batch_size();
                        $processed  = 0;

                        $editor = wp_get_image_editor( $file );

                        if ( is_wp_error( $editor ) ) {
                                $job['status']  = 'failed';
                                $job['updated'] = time();

                                foreach ( $job['sizes'] as $name => $data ) {
                                        if ( 'done' !== $data['status'] ) {
                                                $job['sizes'][ $name ]['status'] = 'error';
                                                $job['sizes'][ $name ]['error']  = $editor->get_error_message();
                                        }
                                }

                                $this->finalise_job( $job_id, $job, $image_meta );
                                return null;
                        }

                        if ( ! $job['rotated_source'] && ! empty( $image_meta['image_meta'] ) ) {
                                $rotated = $editor->maybe_exif_rotate();
                                if ( ! is_wp_error( $rotated ) ) {
                                        $job['rotated_source'] = true;
                                }
                        }

                        foreach ( $job['sizes'] as $size_name => $size_state ) {
                                if ( $processed >= $batch_size ) {
                                        break;
                                }

                                if ( 'done' === $size_state['status'] || 'error' === $size_state['status'] ) {
                                        continue;
                                }

                                $result = null;

                                if ( method_exists( $editor, 'make_subsize' ) ) {
                                        $result = $editor->make_subsize( $size_state['data'] );
                                } else {
                                        $result = $editor->multi_resize( array( $size_name => $size_state['data'] ) );
                                        if ( is_array( $result ) && isset( $result[ $size_name ] ) ) {
                                                $result = $result[ $size_name ];
                                        }
                                }

                                if ( is_wp_error( $result ) ) {
                                        $job['sizes'][ $size_name ]['status'] = 'error';
                                        $job['sizes'][ $size_name ]['error']  = $result->get_error_message();
                                        $this->update_processing_meta( $attachment_id, $image_meta, $job_id, $size_name, 'error', $result->get_error_message() );
                                } elseif ( is_array( $result ) ) {
                                        $image_meta['sizes'][ $size_name ]    = $result;
                                        $job['sizes'][ $size_name ]['status'] = 'done';
                                        $job['sizes'][ $size_name ]['error']  = null;
                                        $this->update_processing_meta( $attachment_id, $image_meta, $job_id, $size_name, 'done' );
                                }

                                $processed++;
                        }

                        $job['updated'] = time();

                        $remaining = $this->count_remaining_sizes( $job );
                        if ( 0 === $remaining ) {
                                $job['status'] = $this->determine_completion_status( $job );
                                $this->finalise_job( $job_id, $job, $image_meta );
                                return null;
                        }

                        $this->save_processing_meta( $attachment_id, $image_meta, $job_id, 'processing', $job );
                        $this->update_status_store(
                                $job_id,
                                array(
                                        'job_id'        => $job_id,
                                        'attachment_id' => $attachment_id,
                                        'status'        => 'processing',
                                        'sizes'         => $this->get_size_states( $job['sizes'] ),
                                        'created'       => $job['created'],
                                        'updated'       => $job['updated'],
                                )
                        );

                        return $job;
                }

                /**
                 * Determines the completion status for a job.
                 *
                 * @param array $job Job data.
                 * @return string
                 */
                protected function determine_completion_status( $job ) {
                        foreach ( $job['sizes'] as $size_state ) {
                                if ( 'error' === $size_state['status'] ) {
                                        return 'completed_with_errors';
                                }
                        }

                        return 'completed';
                }

                /**
                 * Counts the remaining sizes to process for a job.
                 *
                 * @param array $job Job definition.
                 * @return int
                 */
                protected function count_remaining_sizes( $job ) {
                        $remaining = 0;
                        foreach ( $job['sizes'] as $size_state ) {
                                if ( 'done' !== $size_state['status'] && 'error' !== $size_state['status'] ) {
                                        $remaining++;
                                }
                        }

                        return $remaining;
                }

                /**
                 * Saves processing meta for an attachment.
                 *
                 * @param int    $attachment_id Attachment ID.
                 * @param array  $image_meta    Current image meta.
                 * @param string $job_id        Job identifier.
                 * @param string $state         Processing state.
                 * @param array  $job           Job definition.
                 */
                protected function save_processing_meta( $attachment_id, $image_meta, $job_id, $state, $job ) {
                        $processing = $this->build_processing_meta( $image_meta, $job_id );
                        $processing['state']    = $state;
                        $processing['pending']  = $this->get_pending_sizes_from_job( $job );
                        $processing['completed'] = $this->get_completed_sizes_from_job( $job );
                        $processing['errors']   = $this->get_error_sizes_from_job( $job );
                        $processing['updated']  = time();

                        $image_meta['wp_image_processing'] = $processing;
                        wp_update_attachment_metadata( $attachment_id, $image_meta );
                }

                /**
                 * Updates processing metadata for a single size event.
                 *
                 * @param int    $attachment_id Attachment ID.
                 * @param array  $image_meta    Meta.
                 * @param string $job_id        Job identifier.
                 * @param string $size_name     Size processed.
                 * @param string $status        Status for size.
                 * @param string $error         Optional error.
                 */
                protected function update_processing_meta( $attachment_id, &$image_meta, $job_id, $size_name, $status, $error = '' ) {
                        $processing = $this->build_processing_meta( $image_meta, $job_id );
                        $processing['updated'] = time();

                        $processing['pending'] = array_values( array_diff( $processing['pending'], array( $size_name ) ) );

                        if ( 'done' === $status ) {
                                $processing['completed'] = array_values( array_unique( array_merge( $processing['completed'], array( $size_name ) ) ) );
                        } elseif ( 'error' === $status ) {
                                $processing['errors'][ $size_name ] = $error;
                        }

                        $image_meta['wp_image_processing'] = $processing;
                        wp_update_attachment_metadata( $attachment_id, $image_meta );
                }

                /**
                 * Finalises a job by removing it from the queue and recording status.
                 *
                 * @param string $job_id     Job identifier.
                 * @param array  $job        Job data.
                 * @param array  $image_meta Updated metadata.
                 */
                protected function finalise_job( $job_id, $job, $image_meta = array() ) {
                        $this->remove_from_queue( $job_id );

                        $status = $this->format_status( $job );
                        $status['finished'] = time();
                        $this->update_status_store( $job_id, $status );

                        if ( ! empty( $job['attachment_id'] ) ) {
                                if ( empty( $image_meta ) ) {
                                        $image_meta = wp_get_attachment_metadata( $job['attachment_id'] );
                                }

                                if ( is_array( $image_meta ) ) {
                                        $processing                 = $this->build_processing_meta( $image_meta, $job_id );
                                        $processing['state']        = $job['status'];
                                        $processing['pending']      = array();
                                        $processing['completed']    = $this->get_completed_sizes_from_job( $job );
                                        $processing['errors']       = $this->get_error_sizes_from_job( $job );
                                        $processing['finished']     = $status['finished'];
                                        $image_meta['wp_image_processing'] = $processing;
                                        wp_update_attachment_metadata( $job['attachment_id'], $image_meta );
                                }
                        }
                }

                /**
                 * Creates a normalised status array for a job.
                 *
                 * @param array $job Job data.
                 * @return array
                 */
                protected function format_status( $job ) {
                        return array(
                                'job_id'        => $job['id'],
                                'attachment_id' => $job['attachment_id'],
                                'status'        => $job['status'],
                                'sizes'         => $this->get_size_states( $job['sizes'] ),
                                'created'       => $job['created'],
                                'updated'       => $job['updated'],
                        );
                }

                /**
                 * Extracts state information for sizes.
                 *
                 * @param array $sizes Job sizes.
                 * @return array
                 */
                protected function get_size_states( $sizes ) {
                        $states = array();
                        foreach ( $sizes as $name => $data ) {
                                $states[ $name ] = array(
                                        'status' => isset( $data['status'] ) ? $data['status'] : 'pending',
                                        'error'  => isset( $data['error'] ) ? $data['error'] : null,
                                );
                        }

                        return $states;
                }

                /**
                 * Builds a processing meta structure.
                 *
                 * @param array  $image_meta Meta.
                 * @param string $job_id     Job identifier.
                 * @return array
                 */
                protected function build_processing_meta( $image_meta, $job_id ) {
                        $processing = array(
                                'job_id'    => $job_id,
                                'state'     => 'queued',
                                'pending'   => array(),
                                'completed' => array(),
                                'errors'    => array(),
                                'updated'   => time(),
                        );

                        if ( isset( $image_meta['wp_image_processing'] ) && is_array( $image_meta['wp_image_processing'] ) ) {
                                $processing = wp_parse_args( $image_meta['wp_image_processing'], $processing );
                        }

                        return $processing;
                }

                /**
                 * Gets the pending sizes from a job.
                 *
                 * @param array $job Job definition.
                 * @return array
                 */
                protected function get_pending_sizes_from_job( $job ) {
                        $pending = array();
                        foreach ( $job['sizes'] as $name => $size_state ) {
                                if ( 'done' !== $size_state['status'] && 'error' !== $size_state['status'] ) {
                                        $pending[] = $name;
                                }
                        }

                        return $pending;
                }

                /**
                 * Gets the completed sizes from a job.
                 *
                 * @param array $job Job definition.
                 * @return array
                 */
                protected function get_completed_sizes_from_job( $job ) {
                        $completed = array();
                        foreach ( $job['sizes'] as $name => $size_state ) {
                                if ( 'done' === $size_state['status'] ) {
                                        $completed[] = $name;
                                }
                        }

                        return $completed;
                }

                /**
                 * Gets the errored sizes from a job.
                 *
                 * @param array $job Job definition.
                 * @return array
                 */
                protected function get_error_sizes_from_job( $job ) {
                        $errors = array();
                        foreach ( $job['sizes'] as $name => $size_state ) {
                                if ( 'error' === $size_state['status'] && ! empty( $size_state['error'] ) ) {
                                        $errors[ $name ] = $size_state['error'];
                                }
                        }

                        return $errors;
                }

                /**
                 * Gets the configured batch size.
                 *
                 * @return int
                 */
                protected function get_batch_size() {
                        $option = (int) get_option( 'wp_image_processing_queue_batch_size', 2 );
                        $option = $option > 0 ? $option : 1;

                        return (int) apply_filters( 'wp_image_processing_queue_batch_size', $option );
                }

                /**
                 * Gets the configured concurrency.
                 *
                 * @return int
                 */
                protected function get_concurrency() {
                        $option = (int) get_option( 'wp_image_processing_queue_concurrency', 1 );
                        $option = $option > 0 ? $option : 1;

                        return (int) apply_filters( 'wp_image_processing_queue_concurrency', $option );
                }

                /**
                 * Schedules the queue runner if needed.
                 */
                protected function maybe_schedule_runner() {
                        if ( empty( wp_next_scheduled( self::CRON_HOOK ) ) ) {
                                wp_schedule_single_event( time(), self::CRON_HOOK );
                        }
                }

                /**
                 * Retrieves the queue from the options table.
                 *
                 * @return array
                 */
                protected function get_queue() {
                        $queue = get_option( self::OPTION_QUEUE, array() );
                        if ( ! is_array( $queue ) ) {
                                $queue = array();
                        }

                        return $queue;
                }

                /**
                 * Saves the queue.
                 *
                 * @param array $queue Queue data.
                 */
                protected function save_queue( $queue ) {
                        update_option( self::OPTION_QUEUE, $queue, false );
                }

                /**
                 * Updates the status cache entry for a job.
                 *
                 * @param string $job_id Job identifier.
                 * @param array  $status Status data.
                 */
                protected function update_status_store( $job_id, $status ) {
                        $store            = $this->get_status_store();
                        $store[ $job_id ] = $status;
                        update_option( self::OPTION_STATUS, $store, false );
                }

                /**
                 * Retrieves the status cache store.
                 *
                 * @return array
                 */
                protected function get_status_store() {
                        $store = get_option( self::OPTION_STATUS, array() );
                        if ( ! is_array( $store ) ) {
                                $store = array();
                        }

                        return $store;
                }

                /**
                 * Cleans up finished job status entries.
                 */
                protected function cleanup_status_store() {
                        $store     = $this->get_status_store();
                        $changed   = false;
                        $lifetime  = (int) apply_filters( 'wp_image_processing_queue_status_lifetime', DAY_IN_SECONDS );
                        $threshold = time() - $lifetime;

                        foreach ( $store as $job_id => $status ) {
                                if ( empty( $status['finished'] ) ) {
                                        continue;
                                }

                                if ( $status['finished'] < $threshold ) {
                                        unset( $store[ $job_id ] );
                                        $changed = true;
                                }
                        }

                        if ( $changed ) {
                                update_option( self::OPTION_STATUS, $store, false );
                        }
                }

                /**
                 * Attempts to acquire a lock for queue processing.
                 *
                 * @return bool
                 */
                protected function acquire_lock() {
                        $lock = wp_cache_add( self::CACHE_LOCK_KEY, 1, self::CACHE_GROUP, 30 );
                        if ( ! $lock ) {
                                return false;
                        }

                        return true;
                }

                /**
                 * Releases the processing lock.
                 */
                protected function release_lock() {
                        wp_cache_delete( self::CACHE_LOCK_KEY, self::CACHE_GROUP );
                }
        }
}

WP_Image_Processing_Queue::instance();
