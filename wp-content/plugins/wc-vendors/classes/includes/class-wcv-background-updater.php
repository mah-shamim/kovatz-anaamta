<?php
/**
 * WC Vendors Background Updater
 *
 * @version 2.0.6
 * @package WCVendors/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCVendors_Background_Updater Class.
 */
class WCVendors_Background_Updater extends WC_Background_Process {

    /**
     * Initiate new background process.
     */
    public function __construct() {

        // Uses unique prefix per blog so each blog has separate queue.
        $this->prefix = 'wp_' . get_current_blog_id();
        $this->action = 'wcvendors_updater';

        parent::__construct();
    }

    /**
     * Dispatch updater.
     *
     * Updater will still run via cron job if this fails for any reason.
     */
    public function dispatch() {

        $dispatched = parent::dispatch();
        $logger     = wc_get_logger();

        if ( is_wp_error( $dispatched ) ) {
            $logger->error(
                sprintf( 'Unable to dispatch WC Vendors updater: %s', $dispatched->get_error_message() ),
                array( 'source' => 'wcvendors_db_updates' )
            );
        }
    }

    /**
     * Handle cron health check
     *
     * Restart the background process if not already running
     * and data exists in the queue.
     */
    public function handle_cron_healthcheck() {

        if ( $this->is_process_running() ) {
            // Background process already running.
            return;
        }

        if ( $this->is_queue_empty() ) {
            // No data to process.
            $this->clear_scheduled_event();

            return;
        }

        $this->handle();
    }

    /**
     * Schedule fallback event.
     */
    protected function schedule_event() {

        if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
            wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
        }
    }

    /**
     * Is the updater running?
     *
     * @return boolean
     */
    public function is_updating() {

        return false === $this->is_queue_empty();
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param string $callback Update callback function.
     *
     * @return mixed
     */
    protected function task( $callback ) {

        wc_maybe_define_constant( 'WCV_UPDATING', true );

        $logger = wc_get_logger();

        include_once WCV_PLUGIN_DIR . 'classes/includes/wcv-update-functions.php';

        if ( is_callable( $callback ) ) {
            $logger->info( sprintf( 'Running %s callback', $callback ), array( 'source' => 'wcvendors_db_updates' ) );
            call_user_func( $callback );
            $logger->info( sprintf( 'Finished %s callback', $callback ), array( 'source' => 'wcvendors_db_updates' ) );
        } else {
            $logger->notice( sprintf( 'Could not find %s callback', $callback ), array( 'source' => 'wcvendors_db_updates' ) );
        }

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {

        $logger = wc_get_logger();
        $logger->info( 'Data update complete', array( 'source' => 'wcvendors_db_updates' ) );
        WCVendors_Install::update_db_version();
        parent::complete();
    }
}
