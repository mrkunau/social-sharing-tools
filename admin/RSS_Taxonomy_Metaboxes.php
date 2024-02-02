<?php

namespace A3Rev\SocialShareTools;

class RSS_Taxonomy_Metaboxes extends \A3Rev\SocialShareTools\RSS_Taxonomy_Meta{

	public function __construct() {
		add_action('init', array( $this, 'add_meta_boxes') );
	}

	function add_meta_boxes() {
	    if (!is_admin())
	        return;
	    $args       = array();
	    $taxonomies = get_taxonomies($args, 'names');
	    if ($taxonomies) {
	        foreach ($taxonomies as $taxonomy) {
	            $custom_taxonomy = $taxonomy;
	            //add_action($custom_taxonomy . '_add_form_fields', array( __CLASS__, 'add_meta_box_html'), 89, 2);
	            add_action($custom_taxonomy . '_edit_form', array( __CLASS__, 'edit_meta_box_html'), 89, 2);
	            add_action('edited_' . $custom_taxonomy, array( __CLASS__, 'save_term_metadata'), 11, 2);
	            add_action('create_' . $custom_taxonomy, array( __CLASS__, 'save_term_metadata'), 11, 2);
	            add_action('delete_' . $custom_taxonomy, array( __CLASS__, 'delete_term_metadata'), 11, 2);
	        }

	    }

	}

	public static function add_meta_box_html() {
		?>
		<div id="rss_meta" class="postbox metabox-holder" style="padding-top: 0">
			<h2 class="hndle ui-sortable-handle"><span><?php esc_attr_e( 'Social Share Tools', 'social-share-tools' );?></span></h2>
			<div class="inside"><?php self::add_meta_box(); ?></div>
		</div>
		<?php
	}

	public static function edit_meta_box_html( $term = '' ) {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'sstools-custom-fields', SOCIALSHARETOOLS_CSS_URL . '/custom-fields' . $suffix . '.css' );
		wp_enqueue_script( 'sstools-custom-fields', SOCIALSHARETOOLS_JS_URL . '/custom-fields' . $suffix . '.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-tabs' ), null, true );
		?>
		<div id="rss_meta" class="postbox metabox-holder" style="padding-top: 0">
			<h2 class="hndle ui-sortable-handle"><span><?php esc_attr_e( 'Social Share Tools', 'social-share-tools' );?></span></h2>
			<div class="inside"><?php self::add_meta_box( $term ); ?></div>
		</div>
		<?php
	}

	public static function add_meta_box( $term = '' ) {

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

		if( $term && $term->term_id > 0 ){

			$opengraph_title 		= get_term_meta( $term->term_id, '_rss_opengraph-title', true);
			$opengraph_description 	= get_term_meta( $term->term_id, '_rss_opengraph-description', true);
			
			$opengraph_image 		= get_term_meta( $term->term_id, '_rss_opengraph-image', true);
			$opengraph_image_attachment_id 		= get_term_meta( $term->term_id, '_rss_opengraph-image_attachment_id', true);

			$twitter_title 			= get_term_meta( $term->term_id, '_rss_twitter-title', true);
			$twitter_description 	= get_term_meta( $term->term_id, '_rss_twitter-description', true);
			
			$twitter_image 			= get_term_meta( $term->term_id, '_rss_twitter-image', true);
			$twitter_image_attachment_id 		= get_term_meta( $term->term_id, '_rss_twitter-image_attachment_id', true);
			
			$opengraph_image_default = '';
			$twitter_image_default = '';
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
		
	}

	public static function save_term_metadata( $term_id ) {

		if ( ! current_user_can( 'manage_options' ) )
        	return;

        do_action( 'rss_save_compare_data', $term_id );

		$default_meta_boxes = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::$default_meta_boxes;

		$meta_boxes = apply_filters( 'rss_save_term_metaboxes', array() );
		$meta_boxes = array_merge( $default_meta_boxes, $meta_boxes  );

		foreach ( $meta_boxes as $key => $meta_box ) {
			if ( isset( $_POST[$key] ) ) {
				self::set_value( $meta_box, wp_unslash($_POST[$key]), $term_id );
			}
		}

		do_action( 'rss_save_term_metadata' );

	}

	public static function delete_term_metadata($term_id){
		$default_meta_boxes = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::$default_meta_boxes;
		$meta_boxes = apply_filters( 'rss_delete_term_metaboxes', array() );
		$meta_boxes = array_merge( $default_meta_boxes, $meta_boxes  );
		foreach ( $meta_boxes as $key => $meta_box ) {
			self::delete( $meta_box, $term_id );
		}
	}
}
?>
