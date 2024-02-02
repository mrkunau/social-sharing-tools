<?php

namespace A3Rev\SocialShareTools;

class Frontend extends \A3Rev\SocialShareTools\RSS_OpenGraph {

	public $options = array();
	
	public function __construct () {

		$default_options = array(
			'fb_admins' => array(),
			'opengraph' => 'yes',
		    'og_default_image' => '',
		    'og_frontpage_title' => '',
		    'og_frontpage_desc' => '',
		    'og_frontpage_image' => '',
		    'fbadminapp' => '',
		    'googleverify' => '',
		    'pinterestverify' => '',
		    'plus-publisher' => '',
		    'twitter' => 'yes',
		    'twitter_card_type' => 'summary_large_image',
		    'facebook_site' => '',
		    'pinterest_url' => '',
		    'google_plus_url' => '',
		);

		$social_share_tools_homepage 	= get_option( 'social_share_tools_homepage', array() );
		$social_share_tools_twitter 	= get_option( 'social_share_tools_twitter', array() );
		$social_share_tools_pinterest 	= get_option( 'social_share_tools_pinterest', array() );
		$social_share_tools_googleplus 	= get_option( 'social_share_tools_googleplus', array() );
		$rss_homepage 					= get_option( 'rss_homepage', array() );

		$this->options = array_merge( $this->options, $social_share_tools_homepage );
		$this->options = array_merge( $this->options, $social_share_tools_twitter );
		$this->options = array_merge( $this->options, $social_share_tools_pinterest );
		$this->options = array_merge( $this->options, $social_share_tools_googleplus );
		$this->options = array_merge( $this->options, $rss_homepage );
		$this->options = array_merge( $default_options, $this->options );

		if ( is_admin() ) {
			$this->admin_init();
		}else{
			add_action( 'plugins_loaded', array( $this, 'frontend_head_init' ), 16 );
		}

		add_filter( 'oembed_response_data', array( $this, 'set_oembed_data' ), 99, 4 );

	}

	public function set_oembed_data( $data, $post, $width, $height ) {

		$opengraph_title   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-title', $post->ID );
	    if ( ! empty( $opengraph_title ) ) {
	        $data['title'] = $opengraph_title;
	    }

	    $opengraph_description   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-description', $post->ID );
	    if ( ! empty( $opengraph_description ) ) {
	        $data['description'] = $opengraph_description;
	    }

	    $image_url = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-image', $post->ID );
	    
	    if ( ! empty( $image_url ) ) {
	        $data['thumbnail_url'] = $image_url;
	    }

	    $thumbnail_id = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-image_attachment_id', $post->ID );
	   
		if ( $thumbnail_id && (int)$thumbnail_id > 0 ) {
			list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $thumbnail_id, array( $width, 99999 ) );
			$data['thumbnail_url']                                      = $thumbnail_url;
			$data['thumbnail_width']                                    = $thumbnail_width;
			$data['thumbnail_height']                                   = $thumbnail_height;
		}

	    return $data;

	}


	public function frontend_head_init(){
		add_action( 'template_redirect', array( $this, 'frontend_init' ), 1000 );
	}

	public function admin_init(){
		add_action( 'social_share_tools-social_share_tools_homepage_settings_init', array( $this, 'clear_facebook_cache' ) );
		add_action( 'save_post', array( $this, 'clear_facebook_cache_while_save_post' ), 1, 2 );
		add_action( 'created_term', array( $this, 'clear_taxonomy_facebook_cache'), 99, 3 );
		add_action( "edited_term", array( $this, 'clear_taxonomy_facebook_cache'), 99, 3 );
	}

	public function frontend_init(){
		add_filter( 'language_attributes', array( $this, 'add_opengraph_namespace' ), 999 );

		if( is_front_page() ){
			add_action( 'wp_head', array( $this, 'webmaster_tools_authentication' ), 1 );//90
		}
		add_action( 'wp_head', array( $this, 'publisher' ), 1 );

		$rss_facebook = new \A3Rev\SocialShareTools\RSS_Facebook( $this->options );

		if ( $this->options['twitter'] === 'yes' ) {
			$rss_twitter = new \A3Rev\SocialShareTools\RSS_Twitter( $this->options );
		}

		
	}

	public function publisher() {

		if ( $this->options['plus-publisher'] !== '' ) {
			echo '<link rel="publisher" href="', esc_url( $this->options['plus-publisher'] ), '"/>', "\n";

			return true;
		}

		return false;
	}

	public function webmaster_tools_authentication() {

		// Google.
		if ( $this->options['googleverify'] !== '' ) {
			echo '<meta name="google-site-verification" content="' . esc_attr( $this->options['googleverify'] ) . "\" />\n";
		}

		// Pinterest.
		if ( $this->options['pinterestverify'] !== '' ) {
			echo '<meta name="p:domain_verify" content="' . esc_attr( $this->options['pinterestverify'] ) . "\" />\n";
		}
	}

	public function add_opengraph_namespace( $input ) {
		$namespaces = array(
			'og: http://ogp.me/ns#',
		);
		$namespaces       = apply_filters( 'rss_html_namespaces', $namespaces );
		$namespace_string = implode( ' ', array_unique( $namespaces ) );

		$check_namespaces = strpos( $namespace_string , $input );

		if( !$check_namespaces ) return $input;

		if ( strpos( $input, ' prefix=' ) !== false ) {
			$regex   = '`prefix=([\'"])(.+?)\1`';
			$replace = 'prefix="$2 ' . $namespace_string . '"';
			$input   = preg_replace( $regex, $replace, $input );
		}
		else {
			$input .= ' prefix="' . $namespace_string . '"';
		}
		return $input;
	}

	public function clear_facebook_cache_while_save_post( $post_id = '', $post = '' ) {
		if( !isset( $_REQUEST['sstools_clear_facebook_cache'] ) ) return;
		if ( empty( $post_id ) || empty( $post ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id  )) return;
		$furl = 'https://graph.facebook.com';

		$r_options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'body' 		=> array(
				'id' => get_permalink( $post_id ),
				'scrape' => true,
			)
		);
	    $raw_response = wp_remote_request($furl, $r_options);

	    if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
	       // $page_text = __('Error: ', 'social-sharing-tools').' '.$raw_response->get_error_message();
	    }else{
	        //$page_text = $raw_response['body'];
	    }

	}

	public function clear_facebook_cache() {
		if( !isset($_POST['rss_homepage'])) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		$furl = 'https://graph.facebook.com';
		$r_options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'body' 		=> array(
				'id' => home_url( '/' ),
				'scrape' => true,
			)
		);
	    $raw_response = wp_remote_request($furl, $r_options);

	    /*$r_options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'body' 		=> array(
				'id' => home_url(),
				'scrape' => true,
			)
		);
	    $raw_response = wp_remote_request($furl, $r_options);*/

	    if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
	       
	    }else{
	       
	    }
	}

	public function clear_taxonomy_facebook_cache( $term_id, $tt_id, $taxonomy ) {
		if( !isset( $_REQUEST['sstools_clear_facebook_cache'] ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		$furl = 'https://graph.facebook.com';
		$r_options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'body' 		=> array(
				'id' => get_term_link( (int)$term_id, $taxonomy ),
				'scrape' => true,
			)
		);
	    $raw_response = wp_remote_request($furl, $r_options);

	    if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
	       
	    }else{
	       
	    }
	}

}
?>