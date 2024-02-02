<?php

namespace A3Rev\SocialShareTools;

class RSS_OpenGraph extends \A3Rev\SocialShareTools\RSS_OpenGraph_Image {

	public $options = array();

	public function get_url_single( $post_id, $type = 'facebook' ) {

		if( $post_id && $post_id > 0 ){
			return esc_url( get_permalink( $post_id ) );
		}

		return false;
	}

	public function get_title_single( $post_id, $type = 'facebook' ) {

		$title = '';

		if( $post_id && $post_id > 0 ){
			
			if( $type == 'facebook' ){
				$title   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-title', $post_id );
			}

			if( $type == 'twitter' ){
				$title   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-title', $post_id );
			}

			if( '' === $title ){
				$post    = get_post( $post_id );
				if ( isset( $post->post_title ) && ! empty( $post->post_title ) ) {
					$title   = $post->post_title;
				}
			}
		}

		return $title;
	}

	public function get_description_single( $post_id, $type = 'facebook' ) {
		
		$description = '';

		if( $post_id && $post_id > 0 ){
		
			if( $type == 'facebook' ){
				$description  = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-description', $post_id );
			}

			if( $type == 'twitter' ){
				$description  = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'twitter-description', $post_id );
			}

			if( '' === $description ){
				$post    = get_post( $post_id );
				if ( isset( $post->post_content ) && ! empty( $post->post_content ) ) {
					$description = $this->strip_shortcode( $post->post_content );
				}elseif ( isset( $post->post_excerpt ) && ! empty( $post->post_excerpt ) ) {
					$description = $this->strip_shortcode( $post->post_excerpt );
				}
			}
		}

		return $description;

	}

	public function get_image_single( $post_id, $type = 'facebook' ) {
		
		$image_url = '';

		if( $post_id && $post_id > 0 ){

			$post    = get_post( $post_id );

			return $this->get_singular_image_by_post( $post, $type );

		}

		return $image_url;
	}

	public function get_title( $type = 'facebook' ) {

		$title = '';

		if ( is_front_page() || is_home() ) {
			if( isset( $this->options['og_frontpage_title'] ) && $this->options['og_frontpage_title'] !== '' ){
				$title = ( isset( $this->options['og_frontpage_title'] ) && $this->options['og_frontpage_title'] !== '' ) ? $this->options['og_frontpage_title'] : wp_get_document_title();
				$title = esc_attr( stripslashes( $title ) );
				return $title;
			}
		}

		$is_posts_page = $this->is_posts_page();

		if ( is_singular() || $is_posts_page || is_single() ) {
			$post_id = ( $is_posts_page ) ? get_option( 'page_for_posts' ) : get_the_ID();
			
			if( $type == 'facebook' ){
				$title   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-title', $post_id );
			}

			if( $type == 'twitter' ){
				$title   = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'twitter-title', $post_id );
			}

			if( '' === $title ){
				$post    = get_post( $post_id );
				if ( isset( $post->post_title ) && ! empty( $post->post_title ) ) {
					$title   = $post->post_title;
				}
			}
			
		}elseif ( is_category() || is_tax() || is_tag() ) {
			if( $type == 'facebook' ){
				$title   = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'opengraph-title' );
			}

			if( $type == 'twitter' ){
				$title   = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'twitter-title' );
			}

			if ( $title === '' ) {
				$title = wp_title( '', false );
			}

		}elseif ( is_post_type_archive() || is_archive() ) {
			$title = wp_title( '', false );
		}
		else {
			$title = wp_title( '', false );
		}

		return $title;

	}

	public function get_description( $type = 'facebook' ) {

		$ogdesc = '';
		
		
		if ( is_front_page() || is_home() ) {
			if ( isset( $this->options['og_frontpage_desc'] ) && $this->options['og_frontpage_desc'] !== '' ) {
				$ogdesc   =  trim( $this->options['og_frontpage_desc'] );
				$ogdesc = esc_attr( stripslashes( $ogdesc ) );
				return $ogdesc;
			}
			else {
				$ogdesc = get_bloginfo( 'description' );
			}
		}

		$is_posts_page = $this->is_posts_page();
		
		if ( is_singular() || $is_posts_page || is_single() ) {
			$post_id = ( $is_posts_page ) ? get_option( 'page_for_posts' ) : get_the_ID();

			if( $type == 'facebook' ){
				$ogdesc  = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-description', $post_id );
			}

			if( $type == 'twitter' ){
				$ogdesc  = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'twitter-description', $post_id );
			}

			if( '' === $ogdesc ){
				$post    = get_post( $post_id );
				if ( isset( $post->post_content ) && ! empty( $post->post_content ) ) {
					$ogdesc = $this->strip_shortcode( $post->post_content );
				}elseif ( isset( $post->post_excerpt ) && ! empty( $post->post_excerpt ) ) {
					$ogdesc = $this->strip_shortcode( $post->post_excerpt );
				}
			}
		}
		elseif ( is_category() || is_tax() || is_tag() ) {

			if( $type == 'facebook' ){
				$ogdesc = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'opengraph-description' );
			}

			if( $type == 'twitter' ){
				$ogdesc = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'twitter-description' );
			}

			if( '' === $ogdesc ){
				$ogdesc = trim( strip_tags( term_description() ) );
			}
		}elseif ( is_post_type_archive() || is_archive() ) {
			
		}

		// Strip shortcodes if any.
		$ogdesc = strip_shortcodes( $ogdesc );

		return $ogdesc;

	}

	public function is_posts_page() {
		return ( is_home() && 'page' == get_option( 'show_on_front' ) );
	}

	public static function home_url( $path = '', $scheme = null ) {

		$home_url = home_url( $path, $scheme );

		if ( ! empty( $path ) ) {
			return $home_url;
		}

		$home_path = parse_url( $home_url, PHP_URL_PATH );

		if ( '/' === $home_path ) { // Home at site root, already slashed.
			return $home_url;
		}

		if ( is_null( $home_path ) ) { // Home at site root, always slash.
			return trailingslashit( $home_url );
		}

		if ( is_string( $home_path ) ) { // Home in subdirectory, slash if permalink structure has slash.
			return user_trailingslashit( $home_url );
		}

		return $home_url;
	}

	public static function strip_shortcode( $text ) {
		$text = stripslashes($text);
	    $text = strip_tags($text);
	    $text = htmlspecialchars($text);
	    // Clean double quotes
	    $text = str_replace('"', '', $text);
	    //$text = preg_replace('/(\n+)/', ' ', $text);
	    $text = preg_replace('/([\n \t\r]+)/', ' ', $text);
	    $text = preg_replace('/( +)/', ' ', $text);
	    $pattern = get_shortcode_regex();
	    $text = preg_replace('#' . $pattern . '#s', '', $text);
		return preg_replace( '`\[[^\]]+\]`s', '', strip_shortcodes( $text ) );
	}

	public static function is_url_relative( $url ) {
		return ( strpos( $url, 'http' ) !== 0 && strpos( $url, '//' ) !== 0 );
	}

}
?>
