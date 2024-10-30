<?php
/**
 * Inslight Analytics WordPress Plugin
 *
 * @link              https://inslight.de
 * @since             1.0.0
 * @package           Inslight_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       Inslight Analytics
 * Plugin URI:        https://de.wordpress.org/plugins/inslight-analytics/
 * Description:       Einfache Einbindung des Inslight Analytics Codes in die WordPress Seite
 * Version:           1.0.0
 * Author:            Inslight Analytics App
 * Author URI:        https://inslight.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       inslight-analytics
 */

const INSLIGHT_ADMIN_TRACKING_OPTION = 'inslight_track_admin';

/**
 * @since 1.0.0
 */
function inslight_get_admin_tracking() {
    return get_option( INSLIGHT_ADMIN_TRACKING_OPTION, '');
}

/**
* @since 1.0.0
*/
function inslight_print_js_snippet() {
   $exclude_admin = inslight_get_admin_tracking();

   if( empty( $exclude_admin ) && current_user_can('manage_options') ) {
       return;
   }

    ?>
   <!-- inslight Analytics App - Einfache Web Analyse, 100% DSGVO Konform -->
    <script data-host="https://inslight.de" data-dnt="false" src="https://inslight.de/js/inslight.js" id="jFo84uKaFd" async defer></script>
   <!-- / inslight Analytics App -->
   <?php
}

/**
* @since 1.0.0
*/
function inslight_register_settings() {
   $inslight_logo_html = sprintf( '<a href="https://inslight.de/" style="margin-left: 6px;"><img src="%s" width=20 height=20 style="vertical-align: bottom;"></a>', plugins_url( 'inslight.png', __FILE__ ) );

   // register page + section
   add_options_page( 'Inslight Analytics', 'Inslight Analytics', 'manage_options', 'inslight-analytics', 'inslight_print_settings_page' );
   add_settings_section(  'default', "Inslight Analytics {$inslight_logo_html}", '__return_true', 'inslight-analytics' );

   // register options
   register_setting( 'inslight', INSLIGHT_ADMIN_TRACKING_OPTION, array( 'type' => 'string') );

   // register settings fields
   add_settings_field( 'inslight_general_info', __('Infos', 'inslight-analytics'), 'inslight_print_admin_general_setting_field', 'inslight-analytics', 'default');
   add_settings_field( INSLIGHT_ADMIN_TRACKING_OPTION, __('Administratoren tracken', 'inslight-analytics'), 'inslight_print_admin_tracking_setting_field', 'inslight-analytics', 'default');

   add_filter( 'plugin_action_links_inslight-analytics/inslight-analytics.php', 'inslight_setting_links' );

}

/**
* @since 1.0.0
*/
function inslight_print_settings_page() {
   echo '<div class="wrap">';
   echo sprintf( '<form method="POST" action="%s">', esc_attr( admin_url( 'options.php' ) ) );
   settings_fields( 'inslight' );
   do_settings_sections( 'inslight-analytics' );
   submit_button();
   echo '</form>';
   echo '</div>';
}

/**
 * @since 1.0.0
 */
function inslight_print_admin_general_setting_field( $args = array() ) {
    echo '<p>' . __( 'Du kannst Inslight Analytics nur mit einem <a href="https://inslight.de">Inslight Konto</a> benutzen. Wenn du noch kein Konto hast, kannst du dich <a href="https://inslight.de/register">hier</a> kostenlos anmelden. <br> Solltest du Hilfe bei der Einrichtung oder der Benutzung brauchen, schau in unseren <a href="https://inslight.de/docs" target="_blank">Docs</a> vorbei oder schreib und eine Email an <a href="mailto:hallo@inslight.de">hallo@inslight.de</a>', 'inslight-analytics' ) . '</p>';
}

/**
 * @since 1.0.0
 */
function inslight_print_admin_tracking_setting_field( $args = array() ) {
    $value = get_option( INSLIGHT_ADMIN_TRACKING_OPTION );
    echo sprintf( '<input type="checkbox" name="%s" id="%s" value="1" %s />', INSLIGHT_ADMIN_TRACKING_OPTION, INSLIGHT_ADMIN_TRACKING_OPTION, checked( 1, $value, false ) );
    echo '<p class="description">' . __( 'Sollen Administratoren getrackt werden?', 'inslight-analytics' ) . '</p>';
}

/**
 * @since 1.0.0
 */
function inslight_setting_links( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'inslight-analytics',
		get_admin_url() . 'options-general.php'
	) );
	$settings_link = "<a href='$url'>" . __( 'Einstellungen' ) . '</a>';
	array_push(
		$links,
		$settings_link
	);
	return $links;
}

/**
 * @since 1.0.0
 */
function inslight_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'options-general.php?page=inslight-analytics' ) ) );
    }
}

add_action( 'activated_plugin', 'inslight_activation_redirect' );

add_action( 'wp_footer', 'inslight_print_js_snippet', 50 );

if( is_admin() && ! wp_doing_ajax() ) {
   add_action( 'admin_menu', 'inslight_register_settings' );
}
