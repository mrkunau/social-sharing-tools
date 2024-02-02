<?php

namespace A3Rev\SocialShareTools;

class RSS_Metaboxes extends \A3Rev\SocialShareTools\RSS_Meta {

	public function __construct() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'wp_insert_post', array( __CLASS__, 'save_postdata' ) );
		add_action( 'edit_attachment', array( __CLASS__, 'save_postdata' ) );
		add_action( 'add_attachment', array( __CLASS__, 'save_postdata' ) );
	}

	public static function add_meta_box() {
		$post_types = get_post_types( array( 'public' => true ) );

		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $post_type ) {

				if( 'attachment' === $post_type )
					continue;

				$product_title = __('Social Share Tools', 'social-share-tools' );
				
				/*
				add_meta_box( 'rss_meta', $product_title, array(
					$this,
					'meta_box',
				), $post_type, 'normal', 'high' );
				*/

				$settings = array(
					'id'               => 'rss_meta',
					'title'            => $product_title,
					'callback'         => array(
						__CLASS__,
						'meta_box',
					),
					'page'             => $post_type,
                    'context'          => 'advanced',
					'priority'         => 'high',
					'callback_args'    => array(
                        '__block_editor_compatible_meta_box'    => true,
                        '__back_compat_meta_box'                => false,
                    )
				);

				add_meta_box( 
                    $settings['id'], 
                    $settings['title'], 
                    $settings['callback'], 
                    $settings['page'], 
                    $settings['context'], 
                    $settings['priority'], 
                    $settings['callback_args']
                );
			
			}
		}

	}

	private function get_website() {
		// We only want the host part of the URL.
		$website = parse_url( home_url(), PHP_URL_HOST );
		$website = trim( $website, '/' );
		$website = strtolower( $website );

		return $website;
	}

	public static function meta_box() {
		global $post;

		$opengraph_title 		= '';
		$opengraph_description 	= '';
		$opengraph_image 		= '';
		$opengraph_image_attachment_id = '';
		$opengraph_image_default = '';

		$twitter_title 			= '';
		$twitter_description 	= '';
		$twitter_image 			= '';
		$twitter_image_attachment_id = '';
		$twitter_image_default = '';

		if( $post && $post->ID > 0 ){

			$opengraph_title 		= get_post_meta( $post->ID, '_rss_opengraph-title', true);
			$opengraph_description 	= get_post_meta( $post->ID, '_rss_opengraph-description', true);
			
			$opengraph_image 		= get_post_meta( $post->ID, '_rss_opengraph-image', true);
			$opengraph_image_attachment_id 		= get_post_meta( $post->ID, '_rss_opengraph-image_attachment_id', true);

			$twitter_title 			= get_post_meta( $post->ID, '_rss_twitter-title', true);
			$twitter_description 	= get_post_meta( $post->ID, '_rss_twitter-description', true);
			
			$twitter_image 			= get_post_meta( $post->ID, '_rss_twitter-image', true);
			$twitter_image_attachment_id 		= get_post_meta( $post->ID, '_rss_twitter-image_attachment_id', true);

			$rss_opengraph_image = new \A3Rev\SocialShareTools\RSS_OpenGraph_Image( array(), 'facebook', false );
			$opengraph_image_default = $rss_opengraph_image->get_singular_default_image_by_post( $post, 'facebook' );

			$rss_opengraph_image = new \A3Rev\SocialShareTools\RSS_OpenGraph_Image( array(), 'twitter', false );
			$twitter_image_default = $rss_opengraph_image->get_singular_default_image_by_post( $post, 'twitter' );
		}

		

		$rss_social_preview = new \A3Rev\SocialShareTools\RSS_Social_Preview();

		$rss_social_preview->social_preview(
			$opengraph_title,
			$opengraph_description,
			$opengraph_image,
			$opengraph_image_attachment_id,
			$opengraph_image_default,
			$twitter_title, $twitter_description,
			$twitter_image,
			$twitter_image_attachment_id,
			$twitter_image_default
		);

		?>
		
		<?php
    
	}

	public static function save_postdata( $post_id = '' ) {

	    global $post;

	    if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
	        if ( ! current_user_can( 'edit_page', $post_id ) ) {
	            return $post_id;
	        }
	    } else {
	        if ( ! current_user_can( 'edit_post', $post_id ) ) {
	            return $post_id;
	        }
	    }

	    // Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return false;
		}

		if ( $post_id === null ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			$post_id = wp_is_post_revision( $post_id );
		}

		/**
		 * Determine we're not accidentally updating a different post.
		 * We can't use filter_input here as the ID isn't available at this point, other than in the $_POST data.
		 */
		// @codingStandardsIgnoreStart
		if ( ! isset( $_POST['ID'] ) || $post_id !== (int) $_POST['ID'] ) {
			return false;
		}
		// @codingStandardsIgnoreEnd

		clean_post_cache( $post_id );
		$post = get_post( $post_id );

		if ( ! is_object( $post ) ) {
			// Non-existent post.
			return false;
		}

		do_action( 'rss_save_compare_data', $post );

		$default_meta_boxes = \A3Rev\SocialShareTools\RSS_Meta::$default_meta_boxes;

		$meta_boxes = apply_filters( 'rss_save_metaboxes', array() );
		$meta_boxes = array_merge( $default_meta_boxes, $meta_boxes  );

		foreach ( $meta_boxes as $key => $meta_box ) {
			if ( isset( $_POST[$key] ) ) {
				self::set_value( $meta_box, $_POST[$key], $post_id );
			}
		}

		do_action( 'rss_saved_postdata' );

	}
}
?>
