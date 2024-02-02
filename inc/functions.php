<?php

function social_sharing_tools_install(){
	update_option('social_share_tools_version', SOCIALSHARETOOLS_VERSION );
	update_option('social_share_tools_installed', true);
}
/**
 * Load Localisation files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales found in:
 * 		- WP_LANG_DIR/social-sharing-tools/social-sharing-tools-LOCALE.mo
 * 		- WP_LANG_DIR/plugins/social-sharing-tools-LOCALE.mo
 * 	 	- /wp-content/plugins/social-sharing-tools/languages/social-sharing-tools-LOCALE.mo (which if not found falls back to)
 */
function social_sharing_tools_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'social-sharing-tools' );

	load_textdomain( 'social-sharing-tools', WP_LANG_DIR . '/social-sharing-tools/social-sharing-tools-' . $locale . '.mo' );
	load_plugin_textdomain( 'social-sharing-tools', false, SOCIALSHARETOOLS_FOLDER . '/languages/' );
}

/**
 * Load languages file
 */
function social_share_tools_init() {
	if ( get_option('social_share_tools_installed') ) {
		delete_option('social_share_tools_installed');
	}

	social_sharing_tools_plugin_textdomain();
}

function sstools_get_placeholder_image( $file = 'placeholder.png' ) {
    
    if ( empty( $file ) ){
        return;
    }

    $file_url = '';

    // Look for file in stylesheet
    if ( file_exists(get_stylesheet_directory() . '/assets/images/' . $file ) ) {
        $file_url = get_stylesheet_directory_uri() . '/assets/images/' . $file;
        // Look for file in template
    } elseif ( file_exists(get_template_directory() . '/assets/images/' . $file ) ) {
        $file_url = get_template_directory_uri() . '/assets/images/' . $file;
    }

    if( '' == $file_url ){
    	$file_url = SOCIALSHARETOOLS_IMAGES_URL.'/'.$file;
    }

    if ( is_ssl() ){
        $file_url = str_replace( 'http://', 'https://', esc_url( $file_url ) );
    }

    //$file_url = str_replace( array( 'https:', 'http:' ), '', esc_url( $file_url ) );

    return apply_filters( 'sstools_get_placeholder_image', esc_url( $file_url ) );
}


