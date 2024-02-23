<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    /**
     * Class to define the WP-CLI command for synchronizing HPOS data for WC Vendors.
     *
     * @version 2.4.8.2
     * @since   2.4.8.2 - Added the CLI
     */
    class WCV_Order_Data_Synchronizer_CLI {
        /**
         * Maximum value for an unsigned BIGINT in MySQL.
         */
        private const MYSQL_MAX_UNSIGNED_BIGINT = 18446744073709551615;

        /**
         * Synchronizes HPOS data for WC Vendors.
         *
         * ## OPTIONS
         * [--batch-size=<batch-size>]
         * : The number of orders to process at a time. -1 is unlimited
         * default: -1
         *
         * [--force]
         * : Force the process to start from scratch.
         * default: false
         *
         *
         * ## EXAMPLES
         *
         *     wp wcv sync
         *     wp wcv sync --batch-size=2000
         *     wp wcv sync --force
         *     wp wcv sync --force --batch-size=2000
         *
         * @when after_wp_load
         *
         * @param array $args The arguments to be passed to the command.
         * @param array $assoc_args The named arguments to be passed to the command.
         */
        public function sync( $args, $assoc_args ) {
            $force      = WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );
            $batch_size = WP_CLI\Utils\get_flag_value( $assoc_args, 'batch-size', self::MYSQL_MAX_UNSIGNED_BIGINT );

            if ( $force ) {
                WP_CLI::log( 'Force option enabled. Starting the process from scratch.' );
                delete_option( 'wcv_hpos_data_sync_complete' );
                delete_option( 'wcv_tracking_details_migration_complete' );
                delete_option( 'wcv_vendor_id_migration_complete' );
                $this->dashes();
            }

            $batch_size = (int) $batch_size;

            $wcv_synchronizer = new WCV_Order_Data_Synchronizer( $batch_size );

            $pending_orders = $wcv_synchronizer->get_pending_orders();

            if ( count( $pending_orders ) < 1 ) {
                WP_CLI::log( 'WC Vendors HPOS data synchronized.' );
                $this->dashes();
            } else {
                WP_CLI::log( "Processing $batch_size orders in this batch" );
                $wcv_synchronizer->sync_orders();
                $this->dashes();
            }

            WP_CLI::log( 'Start synchronizing order customer IDs' );
            $wcv_synchronizer->maybe_verify_customer_ids();
            $this->dashes();

            WP_CLI::log( 'Start synchronizing order tracking details' );
            $wcv_synchronizer->migrate_tracking_details();
            $this->dashes();

            WP_CLI::success( 'Done processing this batch. Run the command again to process the next batch.' );
        }

        /**
		 * Print dashes.
		 */
		private function dashes() {
			WP_CLI::log( '-------------------------------------------------------------------------' );
		}
    }

    // 2. Register object as a function for the callable parameter.
    WP_CLI::add_command(
        'wcv',
        'WCV_Order_Data_Synchronizer_CLI',
        array(
			'before_invoke' => function () {
				if ( ! class_exists( 'WC_Vendors_Bootstrap' ) ) {
					WP_CLI::error( 'WC Vendors plugin not active. Please activate the plugin and try again.' );
				}
			},
		)
    );
}
