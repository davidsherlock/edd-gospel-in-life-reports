<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Registers the various report views
 *
 * @since       1.0.0
 * @param       array $views The existing report views
 * @return      array $views The views updated with commissions
 */
function edd_gospel_in_life_register_reports( $views ) {

	// Add "Earnings by Tag" report view
	$views['tags'] = __( 'Earnings by Tag', 'edd-gospel-in-life-reports' );

	// Add "Download Speaker" report view
	if ( taxonomy_exists( 'download_speaker' ) ) {
		$views['speakers'] = __( 'Earnings by Speaker', 'edd-gospel-in-life-reports' );
	}

	// Add "Download Location" report view
	if ( taxonomy_exists( 'download_location' ) ) {
		$views['locations'] = __( 'Earnings by Location', 'edd-gospel-in-life-reports' );
	}

	// Add "Download Format" report view
	if ( taxonomy_exists( 'download_format' ) ) {
		$views['formats'] = __( 'Earnings by Format', 'edd-gospel-in-life-reports' );
	}

	return $views;
}
add_filter( 'edd_report_views', 'edd_gospel_in_life_register_reports' );


/**
 * Renders the Reports Earnings By Tag Table & Graphs
 *
 * @since  		1.0.0
 * @return      void
 */
function edd_reports_tags() {
	if( ! current_user_can( 'view_shop_reports' ) ) {
		return;
	}

	include( dirname( __FILE__ ) . '/class-tags-reports-table.php' );
	?>
			<div class="inside">
				<?php

				$tags_table = new EDD_Tags_Reports_Table();
				$tags_table->prepare_items();
				$tags_table->display();
				?>

				<?php echo $tags_table->load_scripts(); ?>

				<div class="edd-mix-totals">
					<div class="edd-mix-chart">
						<strong><?php _e( 'Tag Sales Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $tags_table->output_sales_graph(); ?>
					</div>
					<div class="edd-mix-chart">
						<strong><?php _e( 'Tag Earnings Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $tags_table->output_earnings_graph(); ?>
					</div>
				</div>

				<?php do_action( 'edd_reports_graph_additional_stats' ); ?>

				<p class="edd-graph-notes">
					<span>
						<em><sup>&dagger;</sup> <?php _e( 'All Parent tags include sales and earnings stats from child tags.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
					<span>
						<em><?php _e( 'Stats include all sales and earnings for the lifetime of the store.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
				</p>

			</div>
	<?php
}
add_action( 'edd_reports_view_tags', 'edd_reports_tags' );


/**
 * Renders the Reports Earnings By Speaker Table & Graphs
 *
 * @since  		1.0.0
 * @return      void
 */
function edd_reports_speakers() {
	if( ! current_user_can( 'view_shop_reports' ) ) {
		return;
	}

	include( dirname( __FILE__ ) . '/class-speakers-reports-table.php' );
	?>
			<div class="inside">
				<?php

				$speakers_table = new EDD_Speakers_Reports_Table();
				$speakers_table->prepare_items();
				$speakers_table->display();
				?>

				<?php echo $speakers_table->load_scripts(); ?>

				<div class="edd-mix-totals">
					<div class="edd-mix-chart">
						<strong><?php _e( 'Speaker Sales Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $speakers_table->output_sales_graph(); ?>
					</div>
					<div class="edd-mix-chart">
						<strong><?php _e( 'Speaker Earnings Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $speakers_table->output_earnings_graph(); ?>
					</div>
				</div>

				<?php do_action( 'edd_reports_graph_additional_stats' ); ?>

				<p class="edd-graph-notes">
					<span>
						<em><sup>&dagger;</sup> <?php _e( 'All Parent Speakers include sales and earnings stats from child speakers.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
					<span>
						<em><?php _e( 'Stats include all sales and earnings for the lifetime of the store.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
				</p>

			</div>
	<?php
}
add_action( 'edd_reports_view_speakers', 'edd_reports_speakers' );


/**
 * Renders the Reports Earnings By Location Table & Graphs
 *
 * @since  		1.0.0
 * @return      void
 */
function edd_reports_locations() {
	if( ! current_user_can( 'view_shop_reports' ) ) {
		return;
	}

	include( dirname( __FILE__ ) . '/class-locations-reports-table.php' );
	?>
			<div class="inside">
				<?php

				$locations_table = new EDD_Locations_Reports_Table();
				$locations_table->prepare_items();
				$locations_table->display();
				?>

				<?php echo $locations_table->load_scripts(); ?>

				<div class="edd-mix-totals">
					<div class="edd-mix-chart">
						<strong><?php _e( 'Location Sales Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $locations_table->output_sales_graph(); ?>
					</div>
					<div class="edd-mix-chart">
						<strong><?php _e( 'Location Earnings Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $locations_table->output_earnings_graph(); ?>
					</div>
				</div>

				<?php do_action( 'edd_reports_graph_additional_stats' ); ?>

				<p class="edd-graph-notes">
					<span>
						<em><sup>&dagger;</sup> <?php _e( 'All Parent Locations include sales and earnings stats from child locations.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
					<span>
						<em><?php _e( 'Stats include all sales and earnings for the lifetime of the store.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
				</p>

			</div>
	<?php
}
add_action( 'edd_reports_view_locations', 'edd_reports_locations' );


/**
 * Renders the Reports Earnings By Format Table & Graphs
 *
 * @since  		1.0.0
 * @return      void
 */
function edd_reports_formats() {
	if( ! current_user_can( 'view_shop_reports' ) ) {
		return;
	}

	include( dirname( __FILE__ ) . '/class-formats-reports-table.php' );
	?>
			<div class="inside">
				<?php

				$formats_table = new EDD_Formats_Reports_Table();
				$formats_table->prepare_items();
				$formats_table->display();
				?>

				<?php echo $formats_table->load_scripts(); ?>

				<div class="edd-mix-totals">
					<div class="edd-mix-chart">
						<strong><?php _e( 'Format Sales Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $formats_table->output_sales_graph(); ?>
					</div>
					<div class="edd-mix-chart">
						<strong><?php _e( 'Format Earnings Mix: ', 'edd-gospel-in-life-reports' ); ?></strong>
						<?php $formats_table->output_earnings_graph(); ?>
					</div>
				</div>

				<?php do_action( 'edd_reports_graph_additional_stats' ); ?>

				<p class="edd-graph-notes">
					<span>
						<em><sup>&dagger;</sup> <?php _e( 'All Parent Formats include sales and earnings stats from child formats.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
					<span>
						<em><?php _e( 'Stats include all sales and earnings for the lifetime of the store.', 'edd-gospel-in-life-reports' ); ?></em>
					</span>
				</p>

			</div>
	<?php
}
add_action( 'edd_reports_view_formats', 'edd_reports_formats' );
