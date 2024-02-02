<?php

namespace A3Rev\SocialShareTools;

class RSS_Taxonomy_Meta {

	public static $meta_prefix = '_rss_';

	public static $default_meta_boxes = array(
		'_rss_opengraph-title' 			=> 'opengraph-title',
		'_rss_opengraph-description' 	=> 'opengraph-description',
		'_rss_opengraph-image' 			=> 'opengraph-image',

		'_rss_opengraph-image_attachment_id' => 'opengraph-image_attachment_id',
		'_rss_opengraph-image_attachment_size' => 'opengraph-image_attachment_size',

		'_rss_twitter-title' 			=> 'twitter-title',
		'_rss_twitter-description' 		=> 'twitter-description',
		'_rss_twitter-image' 			=> 'twitter-image',

		'_rss_twitter-image_attachment_id' => 'twitter-image_attachment_id',
		'_rss_twitter-image_attachment_size' => 'twitter-image_attachment_size',
	);

	public static function get_term_meta( $term, $taxonomy, $meta = null ) {
		/* Figure out the term id */
		if ( is_int( $term ) ) {
			$term = get_term_by( 'id', $term, $taxonomy );
		}
		elseif ( is_string( $term ) ) {
			$term = get_term_by( 'slug', $term, $taxonomy );
		}

		if ( is_object( $term ) && isset( $term->term_id ) ) {
			$term_id = $term->term_id;
		}
		else {
			return false;
		}

		$tax_meta = get_term_meta( $term_id , self::$meta_prefix.$meta, true );

		return $tax_meta;

		return false;
	}

	/**
	 * Get the current queried object and return the meta value
	 *
	 * @param string $meta The meta field that is needed.
	 *
	 * @return bool|mixed
	 */
	public static function get_meta_without_term( $meta ) {
		$term = $GLOBALS['wp_query']->get_queried_object();
		return self::get_term_meta( $term, $term->taxonomy, $meta );
	}

	public static function set_value( $key, $meta_value, $term_id ) {
		return update_term_meta( $term_id, self::$meta_prefix . $key, $meta_value );
	}

	public static function delete( $key, $term_id ) {
		return delete_term_meta( $term_id, self::$meta_prefix . $key );
	}
}
?>
