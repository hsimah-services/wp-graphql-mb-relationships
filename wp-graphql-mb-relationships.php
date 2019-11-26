<?php
/**
 * Plugin Name: WP GraphQL MB Relationships
 * Plugin URI: https://github.com/hsimah/wp-graphql-mb-relationships
 * Description: WP GraphQL provider for MB Relationships
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.0.1
 * Text Domain: wpgraphql-mb-relationships
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_MB_Relationships
 * @author   hsimah
 * @version  0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPGraphQL_MB_Relationships' ) ) {

  add_action( 'admin_init', 'show_admin_notice' );

	if ( class_exists( 'MBR_Loader' ) && class_exists( 'WPGraphQL' ) )
		require_once __DIR__ . '/class-mb-relationships.php';

}


/**
 * Show admin notice to admins if this plugin is active but either MB Relationships and/or WPGraphQL
 * are not active
 *
 * @return bool
 */
function show_admin_notice() {

    $wp_graphql_required_min_version = '0.3.2';

	if ( ! class_exists( 'MBR_Loader' ) || ! class_exists( 'WPGraphQL' ) || ( defined( 'WPGRAPHQL_VERSION' ) && version_compare( WPGRAPHQL_VERSION, $wp_graphql_required_min_version, 'lt' ) ) ) {

		/**
		 * For users with lower capabilities, don't show the notice
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		add_action(
			'admin_notices',
			function() use ( $wp_graphql_required_min_version ) {
				?>
			<div class="error notice">
				<p>
					<?php _e( sprintf('Both WPGraphQL (v%s+) and MB Relationships (v3.3.9) must be active for "wp-graphql-mb-relationships" to work', $wp_graphql_required_min_version ), 'wpgraphql-mb-relationships' ); ?>
				</p>
			</div>
				<?php
			}
		);

		return false;
	}
}
