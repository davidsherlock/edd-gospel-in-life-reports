<?php
/**
 * Plugin Name:     Easy Digital Downloads - Gospel In Life Reports
 * Plugin URI:      https://sellcomet.com
 * Description:     Extends and improves existing Easy Digital Downloads reporting functionality.
 * Version:         1.0.0
 * Author:          Sell Comet
 * Author URI:      https://sellcomet.com
 * Text Domain:     edd-gospel-in-life-reports
 *
 * @package         EDD\Gospel_In_Life_Reports
 * @author          Sell Comet
 * @copyright       Copyright (c) Sell Comet
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'EDD_Gospel_In_Lift_Reports' ) ) {

    /**
     * Main EDD_Gospel_In_Lift_Reports class
     *
     * @since       1.0.0
     */
    class EDD_Gospel_In_Lift_Reports {

        /**
         * @var         EDD_Gospel_In_Lift_Reports $instance The one true EDD_Gospel_In_Lift_Reports
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Gospel_In_Lift_Reports
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Gospel_In_Lift_Reports();
                self::$instance->setup_constants();
                self::$instance->load_textdomain();
                self::$instance->includes();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_GOSPEL_IN_LIFE_REPORTS_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_GOSPEL_IN_LIFE_REPORTS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_GOSPEL_IN_LIFE_REPORTS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Includes
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            // Admin only requires
            if ( is_admin() ) {

                // Include admin settings
                require_once EDD_GOSPEL_IN_LIFE_REPORTS_DIR . 'includes/admin/reports.php';

            }

        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Sales Report CSV columns
            add_filter( 'edd_export_csv_cols_sales', array( $this, 'add_export_csv_cols_sales' ), 10, 1 );

            // Sales Report CSV row data
            add_filter( 'edd_export_get_data_sales', array( $this, 'add_export_csv_data_sales' ), 10, 1 );
        }


        /**
         * Set our "Sales Report" CSV column headers
         *
         * @access      public
         * @since       1.0.0
         * @param       array $cols the original column headers
         * @return      array $cols our
         */
        public function add_export_csv_cols_sales( $cols ) {

            // Unset all the original columns
            unset( $cols['ID'] );
            unset( $cols['user_id'] );
            unset( $cols['customer_id'] );
            unset( $cols['download'] );
            unset( $cols['amount'] );
            unset( $cols['payment_id'] );
            unset( $cols['date'] );

            // Set our new report column headings
            $cols['product']          = __( 'Product', 'edd-gospel-in-life-reports' );
            $cols['sku']              = __( 'SKU', 'edd-gospel-in-life-reports' );
            $cols['total_sales']      = __( 'Sales', 'edd-gospel-in-life-reports' );
            $cols['total_amount']     = __( 'Amount', 'edd-gospel-in-life-reports' ) . ' (' . html_entity_decode( edd_currency_filter( '' ) ) . ')';
            $cols['total_tax']        = __( 'Tax', 'edd-gospel-in-life-reports' ) . ' (' . html_entity_decode( edd_currency_filter( '' ) ) . ')';
            $cols['categories']       = __( 'Categories', 'edd-gospel-in-life-reports' );
            $cols['tags']             = __( 'Tags', 'edd-gospel-in-life-reports' );
            $cols['formats']          = __( 'Formats', 'edd-gospel-in-life-reports' );
            $cols['speakers']         = __( 'Speakers', 'edd-gospel-in-life-reports' );
            $cols['locations']        = __( 'Locations', 'edd-gospel-in-life-reports' );

            // Remove the Sku column if disabled
            if( ! edd_use_skus() ){
                unset( $cols['sku'] );
            }

            return $cols;
        }


        /**
         * The value for the "Fulfilled" column within the Payment History export
         *
         * @since 1.0.2
         *
         * @param array $data
         * @return array $data
         */
        public function add_export_csv_data_sales( $data ) {

            foreach( $data as $index => $log ) {

                $payment_id = get_post_meta( $log['ID'], '_edd_log_payment_id', true );
				$payment    = new EDD_Payment( $payment_id );

				if ( ! empty( $payment_id ) ) {
					$cart_items = $payment->cart_details;
					$amount     = 0;

					if ( is_array( $cart_items ) ) {
						foreach ( $cart_items as $item ) {
							if ( $item['name'] == $log['download'] ) {
								if ( isset( $item['item_number']['options']['price_id'] ) ) {
									$log_price_id = get_post_meta( $log['ID'], '_edd_log_price_id', true );

									if ( (int) $item['item_number']['options']['price_id'] !== (int) $log_price_id ) {
										continue;
									}

                                    // Set the base download name
                                    $download_name = $item['name'];

                                    // Get the price option name if variable prices are set
                                    if ( edd_has_variable_prices( $item['id'] ) && isset( $log_price_id ) ) {
                                        $download_name .= ' - ' . html_entity_decode( edd_get_price_option_name( $item['id'], $log_price_id, $payment_id ) );
                                    }

								}

                                // Get Download Tags
                                $tags = get_the_terms( $item['id'], 'download_tag' );
                                if( $tags ) {
                                    $tags = wp_list_pluck( $tags, 'name' );
                                    $tags = implode( ' | ', $tags );
                                }

                                // Get Download Categories
                                $cats = get_the_terms( $item['id'], 'download_category' );
                                if( $cats ) {
                                    $cats = wp_list_pluck( $cats, 'name' );
                                    $cats = implode( ' | ', $cats );
                                }

                                // Get Download Formats
                                if ( taxonomy_exists( 'download_format' ) ) {
                                    $formats = get_the_terms( $item['id'], 'download_format' );
                                    if ( $formats ) {
                                        $formats = wp_list_pluck( $formats, 'name' );
                                        $formats = implode( ' | ', $formats );
                                    }
                                }

                                // Get Download Locations
                                if ( taxonomy_exists( 'download_location' ) ) {
                                    $locations = get_the_terms( $item['id'], 'download_location' );
                                    if ( $locations ) {
                                        $locations = wp_list_pluck( $locations, 'name' );
                                        $locations = implode( ' | ', $locations );
                                    }
                                }

                                // Get Download Speakers
                                if ( taxonomy_exists( 'download_speaker' ) ) {
                                    $speakers = get_the_terms( $item['id'], 'download_speaker' );
                                    if ( $speakers ) {
                                        $speakers = wp_list_pluck( $speakers, 'name' );
                                        $speakers = implode( ' | ', $speakers );
                                    }
                                }

                                // Get Download Tax
                                $tax = isset( $item['tax'] ) ? $item['tax'] : 0;

                                // Get Download SKU
                                if ( edd_use_skus() ) {
                                    $sku = edd_get_download_sku( $item['id'] );

                                    if ( ! empty( $sku ) ) {
                                        $skus = $sku;
                                    }
                                }

								$amount = isset( $item['price'] ) ? $item['price'] : $item['item_price'];
								break;
							}
						}
					}
				}

                // Remove the data we don't need
                unset( $data[ $index ]['ID'] );
                unset( $data[ $index ]['user_id'] );
                unset( $data[ $index ]['customer_id'] );
                unset( $data[ $index ]['download'] );
                unset( $data[ $index ]['amount'] );
                unset( $data[ $index ]['payment_id'] );
                unset( $data[ $index ]['date'] );

                // Prepare our export data array
                $data[ $index ]['product']          = $download_name;
                $data[ $index ]['sku']              = isset( $skus ) ? $skus : '';
                $data[ $index ]['total_sales']      = 1;
				$data[ $index ]['total_amount']     = $amount;
                $data[ $index ]['total_tax']        = $tax;
                $data[ $index ]['categories']       = $cats;
                $data[ $index ]['tags']             = $tags;
                $data[ $index ]['formats']          = isset( $formats ) ? $formats : '';
                $data[ $index ]['speakers']         = isset( $speakers ) ? $speakers : '';
                $data[ $index ]['locations']        = isset( $locations ) ? $locations : '';

            }

            return $data;
        }



        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_BETTER_REPORTS_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_gospel_in_life_reports_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-gospel-in-life-reports' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-gospel-in-life-reports', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-gospel-in-life-reports/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-better-sales-reports/ folder
                load_textdomain( 'edd-gospel-in-life-reports', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-better-sales-reports/languages/ folder
                load_textdomain( 'edd-gospel-in-life-reports', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-gospel-in-life-reports', false, $lang_dir );
            }
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_Gospel_In_Lift_Reports
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Gospel_In_Lift_Reports The one true EDD_Gospel_In_Lift_Reports
 */
function EDD_Gospel_In_Lift_Reports_load() {
    if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if( ! class_exists( 'EDD_Extension_Activation' ) ) {
            require_once 'includes/classes/class-activation.php';
        }

        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return EDD_Gospel_In_Lift_Reports::instance();
    }
}
add_action( 'plugins_loaded', 'EDD_Gospel_In_Lift_Reports_load' );
