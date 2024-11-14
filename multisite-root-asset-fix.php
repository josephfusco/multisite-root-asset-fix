<?php
/**
 * Plugin Name:       Multisite Root Asset URL Fix
 * Plugin URI:        https://github.com/josephfusco/multisite-root-asset-fix
 * Description:       Ensures that all subsites in a multisite network with subdirectories load assets (CSS, JS, images) from the main site URL, fixing broken images and media in wp-admin and ACF.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Joseph Fusco
 * Author URI:        https://josephfus.co/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       multisite-root-asset-fix
 * Network:           true
 *
 * @package MultisiteRootAssetFix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class to handle URL fixes for WordPress multisite installations using subdirectories.
 *
 * @since 1.0.0
 */
class Multisite_Root_Asset_URL_Fix {

    /**
     * Constructor to add necessary hooks.
     */
    public function __construct() {
        // Only initialize if this is a multisite setup with subdirectories
        if ( $this->is_multisite_subdirectory() ) {
            // Filters for styles and scripts
            add_filter( 'style_loader_src', array( $this, 'fix_asset_url' ), 10, 2 );
            add_filter( 'script_loader_src', array( $this, 'fix_asset_url' ), 10, 2 );

            // Filters for media uploads and attachments
            add_filter( 'upload_dir', array( $this, 'fix_uploads_url' ) );
            add_filter( 'wp_get_attachment_url', array( $this, 'fix_attachment_url' ) );

            // General content URLs
            add_filter( 'content_url', array( $this, 'fix_general_url' ), 10, 2 );
            add_filter( 'includes_url', array( $this, 'fix_general_url' ), 10, 2 );

            // Additional URL filters for ACF and general URLs
            add_filter( 'acf/settings/url', array( $this, 'fix_acf_url' ) );
            add_filter( 'acf/format_value', array( $this, 'fix_acf_field_value' ), 10, 3 );
            add_filter( 'get_site_url', array( $this, 'fix_general_url' ), 10, 3 );
            add_filter( 'home_url', array( $this, 'fix_general_url' ), 10, 3 );
        }
    }

    /**
     * Checks if the current setup is a multisite with subdirectories.
     *
     * @since 1.0.0
     * @return bool True if multisite with subdirectories, false otherwise.
     */
    private function is_multisite_subdirectory() {
        return is_multisite() && defined( 'SUBDOMAIN_INSTALL' ) && ! SUBDOMAIN_INSTALL;
    }

    /**
     * Forces the asset URL (CSS/JS) to use the main site domain and path.
     *
     * @since 1.0.0
     * @param string $url    The URL to be filtered.
     * @param string $handle The asset handle (not used here but provided by WordPress).
     * @return string Modified URL using the root site's URL.
     */
    public function fix_asset_url( $url, $handle ) {
        return $this->replace_url_with_root( $url );
    }

    /**
     * Force the uploads directory URL to use the main site domain for media files.
     *
     * @since 1.0.0
     * @param array $uploads The array of upload directory data.
     * @return array Modified upload data with the root site's URL for uploads.
     */
    public function fix_uploads_url( $uploads ) {
        $uploads['baseurl'] = $this->replace_url_with_root( $uploads['baseurl'] );
        return $uploads;
    }

    /**
     * Force the attachment URL to use the main site domain for media files.
     *
     * @since 1.0.0
     * @param string $url The URL of the attachment.
     * @return string Modified URL using the root site's URL.
     */
    public function fix_attachment_url( $url ) {
        return $this->replace_url_with_root( $url );
    }

    /**
     * General URL fixer for content and includes URLs.
     *
     * @since 1.0.0
     * @param string $url The URL to be filtered.
     * @return string Modified URL using the root site's URL.
     */
    public function fix_general_url( $url ) {
        return $this->replace_url_with_root( $url );
    }

    /**
     * Fix ACF base URL setting to use the main site domain.
     *
     * @since 1.0.0
     * @param string $url The ACF URL setting to be modified.
     * @return string Modified ACF URL using the root site's URL.
     */
    public function fix_acf_url( $url ) {
        return $this->replace_url_with_root( $url );
    }

    /**
     * Fix ACF field values (images, links) to use the main site domain.
     *
     * @since 1.0.0
     * @param mixed  $value   The ACF field value.
     * @param int    $post_id The post ID.
     * @param array  $field   The ACF field array.
     * @return mixed Modified value with URLs replaced if necessary.
     */
    public function fix_acf_field_value( $value, $post_id, $field ) {
        if ( is_string( $value ) && strpos( $value, get_site_url() ) === 0 ) {
            $value = $this->replace_url_with_root( $value );
        }
        return $value;
    }

    /**
     * Replaces a given URL's base with the root site URL, if it starts with the current subsite URL.
     *
     * @since 1.0.0
     * @param string $url The URL to be modified.
     * @return string Modified URL if it contains the subsite path, or original if not.
     */
    private function replace_url_with_root( $url ) {
        $main_site_url = network_site_url();
        $current_site_url = get_site_url();

        // Replace the subsite URL part with the main site URL
        if ( strpos( $url, $current_site_url ) === 0 ) {
            $url = str_replace( $current_site_url, $main_site_url, $url );
        }

        return $url;
    }
}

// Initialize the plugin
new Multisite_Root_Asset_URL_Fix();
