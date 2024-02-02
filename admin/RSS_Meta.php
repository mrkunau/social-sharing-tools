<?php

namespace A3Rev\SocialShareTools;

class RSS_Meta {

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

	public static function get_value( $key, $postid = 0 ) {
		global $post;

		$postid = absint( $postid );
		if ( $postid === 0 ) {
			if ( ( isset( $post ) && is_object( $post ) ) && ( isset( $post->post_status ) && $post->post_status !== 'auto-draft' ) ) {
				$postid = $post->ID;
			}
			else {
				return '';
			}
		}

		$custom = get_post_custom( $postid ); // Array of strings or empty array.

		if ( isset( $custom[ self::$meta_prefix . $key ][0] ) ) {
			$unserialized = maybe_unserialize( $custom[ self::$meta_prefix . $key ][0] );
			if ( $custom[ self::$meta_prefix . $key ][0] === $unserialized ) {
				return $custom[ self::$meta_prefix . $key ][0];
			}
		}

		return '';
	}

	public static function set_value( $key, $meta_value, $post_id ) {
		return update_post_meta( $post_id, self::$meta_prefix . $key, $meta_value );
	}

	public static function delete( $key, $post_id ) {
		return delete_post_meta( $post_id, self::$meta_prefix . $key );
	}
}

?>
