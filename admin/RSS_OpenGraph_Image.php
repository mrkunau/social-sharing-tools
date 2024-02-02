<?php

namespace A3Rev\SocialShareTools;

class RSS_OpenGraph_Image {

	/**
	 * @var array $options Holds options passed to the constructor
	 */
	private $options;

	/**
	 * @var array $images Holds the images that have been put out as OG image.
	 */
	private $images = array();

	/**
	 * @TODO This needs to be refactored since we only hold one set of dimensions for multiple images. R.
	 * @var array $dimensions Holds image dimensions, if determined.
	 */
	protected $dimensions = array();

	/**
	 * Constructor
	 *
	 * @param array          $options Options set.
	 * @param string|boolean $image   Optional image URL.
	 */
	public function __construct( $options, $type = 'facebook', $image = false ) {
		$this->options = $options;

		// If an image was not supplied or could not be added.
		if ( empty( $image ) || ! $this->add_image( $image ) ) {
			$this->set_images( $type );
		}
	}

	/**
	 * Return the images array
	 *
	 * @return array
	 */
	public function get_images() {
		return $this->images;
	}

	/**
	 * Return the dimensions array.
	 *
	 * @return array
	 */
	public function get_dimensions() {
		return $this->dimensions;
	}

	/**
	 * Display an OpenGraph image tag
	 *
	 * @param string $img - Source URL to the image.
	 *
	 * @return bool
	 */
	public function add_image( $img ) {

		$original = trim( $img );

		// Filter: 'opengraph_image' - Allow changing the OpenGraph image.
		$img = trim( apply_filters( 'opengraph_image', $img ) );

		if ( $original !== $img ) {
			$this->dimensions = array();
		}

		if ( empty( $img ) ) {
			return false;
		}

		if ( $this->is_url_relative( $img ) === true ) {
			$img = $this->get_relative_path( $img );
		}

		if ( in_array( $img, $this->images ) ) {
			return false;
		}
		array_push( $this->images, $img );

		return true;
	}

	public static function is_url_relative( $url ) {
		return ( strpos( $url, 'http' ) !== 0 && strpos( $url, '//' ) !== 0 );
	}

	/**
	 * Check if page is front page or singular and call the corresponding functions. If not, call get_default_image.
	 */
	private function set_images( $type ) {

		/**
		 * Filter: add_opengraph_images - Allow developers to add images to the OpenGraph tags
		 *
		 * @api RSS_OpenGraph_Image The current object.
		 */
		do_action( 'add_opengraph_images', $this );

		if ( is_front_page() || is_home() ) {
			if( isset( $this->options['og_frontpage_image'] ) && $this->options['og_frontpage_image'] !== '' ){
				$this->get_front_page_image();
				return;
			}elseif ( is_home() ) { // Posts page, which won't be caught by is_singular() below.
				$this->get_posts_page_image( $type );
			}
		}

		if ( is_singular() ) {
			$this->get_singular_image( $type );
		}

		if ( is_category() || is_tax() || is_tag() ) {
			$this->get_opengraph_image_taxonomy( $type );
		}

		//$this->get_default_image();
	}

	/**
	 * If the frontpage image exists, call add_image
	 */
	private function get_front_page_image() {
		if ( $this->options['og_frontpage_image'] !== '' ) {

			$size = 'original';

			if( isset($this->options['og_frontpage_image_attachment_size']) && $this->options['og_frontpage_image_attachment_size'] != '' ){
				$size = $this->options['og_frontpage_image_attachment_size'];
			}

			if( isset($this->options['og_frontpage_image_attachment_id']) && $this->options['og_frontpage_image_attachment_id'] > 0 ){
				$thumb = wp_get_attachment_image_src( $this->options['og_frontpage_image_attachment_id'], apply_filters( 'opengraph_image_size', $size ) );
				if ( $this->check_featured_image_size( $thumb ) ) {
					$this->dimensions['width']  = $thumb[1];
					$this->dimensions['height'] = $thumb[2];
				}
			}
			$this->add_image( esc_url( $this->options['og_frontpage_image'] ) );
		}
	}

	/**
	 * Get the images of the posts page.
	 */
	private function get_posts_page_image( $type ) {

		$post_id = get_option( 'page_for_posts' );

		if ( $this->get_opengraph_image_post( $post_id, $type ) ) {
			return;
		}

		if ( $this->get_featured_image( $post_id ) ) {
			return;
		}
	}

	/**
	 * Get the images of the singular post.
	 */
	private function get_singular_image( $type ) {
		global $post;

		if ( $this->get_opengraph_image_post( $post->ID, $type ) ) {
			return;
		}

		if ( $this->get_attachment_page_image( $post->ID ) ) {
			return;
		}

		if ( $this->get_featured_image( $post->ID ) ) {
			return;
		}

		$this->get_content_images( $post );
	}

	/**
	 * Get default image and call add_image
	 */
	private function get_default_image() {
		if ( count( $this->images ) === 0 && isset( $this->options['og_default_image'] ) && $this->options['og_default_image'] !== '' ) {
			$this->add_image( esc_url( $this->options['og_default_image'] ) );
		}
		if( count( $this->images ) === 0 ){
			$img = function_exists('sstools_get_placeholder_image') ? sstools_get_placeholder_image() : '';
        	$this->add_image( $img );
        }
	}

	/**
	 * If opengraph-image is set, call add_image and return true.
	 *
	 * @param int $post_id Optional post ID to use.
	 *
	 * @return bool
	 */
	private function get_opengraph_image_post( $post_id, $type, $return = false ) {

		$post_id = isset($post_id) ? $post_id : 0;
		
		$ogimg = '';
		
		if( $type == 'facebook' ){
			$ogimg = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'opengraph-image', $post_id );
		}

		if( $type == 'twitter' ){
			$ogimg = \A3Rev\SocialShareTools\RSS_Meta::get_value( 'twitter-image', $post_id );
		}

		if ( $ogimg !== '' ) {

			if( $return ) return esc_url( $ogimg );

			$this->add_image( esc_url( $ogimg ) );

			return true;
		}

		return false;
	}

	/**
	 * Check if taxonomy has an image and add this image
	 */
	private function get_opengraph_image_taxonomy( $type ) {

		if( $type == 'facebook' ){
			if ( ( $ogimg = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'opengraph-image' ) ) !== '' ) {
				$this->add_image( esc_url( $ogimg ) );
			}
		}

		if( $type == 'twitter' ){
			if ( ( $ogimg = \A3Rev\SocialShareTools\RSS_Taxonomy_Meta::get_meta_without_term( 'twitter-image' ) ) !== '' ) {
				$this->add_image( esc_url( $ogimg ) );
			}
		}

	}

	/**
	 * If there is a featured image, check image size. If image size is correct, call add_image and return true
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return bool
	 */
	private function get_featured_image( $post_id, $return = false ) {

		if ( has_post_thumbnail( $post_id ) ) {
			/**
			 * Filter: 'opengraph_image_size' - Allow changing the image size used for OpenGraph sharing
			 *
			 * @api string $unsigned Size string
			 */
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), apply_filters( 'opengraph_image_size', 'original' ) );

			if ( $this->check_featured_image_size( $thumb ) ) {
				if( $return ){
					return esc_url( $thumb[0] );
				}
				$this->dimensions['width']  = $thumb[1];
				$this->dimensions['height'] = $thumb[2];
				return $this->add_image( esc_url( $thumb[0] ) );
			}
		}

		return false;
	}

	/**
	 * If this is an attachment page, call add_image with the attachment and return true
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return bool
	 */
	private function get_attachment_page_image( $post_id, $return = false ) {
		if ( get_post_type( $post_id ) === 'attachment' ) {
			$mime_type = get_post_mime_type( $post_id );
			switch ( $mime_type ) {
				case 'image/jpeg':
				case 'image/png':
				case 'image/gif':
					if( $return ){
						return wp_get_attachment_url( $post_id );
					}else{
						return $this->add_image( wp_get_attachment_url( $post_id ) );
					}
			}
		}

		return false;
	}

	/**
	 * Filter: 'pre_analysis_post_content' - Allow filtering the content before analysis
	 *
	 * @api string $post_content The Post content string
	 *
	 * @param object $post - The post object.
	 */
	private function get_content_images( $post, $return = false ) {
		$content = apply_filters( 'pre_analysis_post_content', $post->post_content, $post );

		if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`src=(["\'])(.*?)\1`', $img, $match ) ) {
					if( $return ) return esc_url( $match[2] );
					$this->add_image( esc_url( $match[2] ) );
				}
			}
		}

		if ( preg_match_all( '|<iframe [^>]*(src="[^"]+")[^>]*|', $content, $matches ) ) {
	 		foreach ( $matches[1] as $youtube_url ) {
	 			if ( preg_match( '`src=(["\'])(.*?)\1`', $youtube_url, $match ) ) {
					$img = esc_url( $this->_get_video_image( esc_url(  $match[2]  ) ) );
					if( $return ) return esc_url( $img );
	 				$this->add_image( esc_url( $img ) );
				}
		    }
	    }

		if ( preg_match_all( '@(https?://)?(?:www\.)?(youtu(?:\.be/([-\w]+)|be\.com/watch\?v=([-\w]+)))\S*@im', $content, $matches ) ) {
			foreach ( $matches[0] as $youtube_url ) {
				$pattern = $this->_parse_yturl_pattern();
	            $output  = preg_match_all( $pattern, $youtube_url, $matchesImg );
	            if ( !empty( $matchesImg[1][0] ) ) {
	                $img = "http://img.youtube.com/vi/" . urlencode($matchesImg[1][0]) . "/0.jpg";
	                if( $return ) return esc_url( $img );
	                $this->add_image( esc_url( $img ) );
	            }
			}
		}

	}

	public function _get_video_image( $embed ) {
	    $video_thumb = '';

	    // YouTube - get the video code if this is an embed code (old embed)
	    preg_match( '/youtube\.com\/v\/([\w\-]+)/', $embed, $match );

	    // YouTube - if old embed returned an empty ID, try capuring the ID from the new frame embed
	    if ( !isset( $match[1] ) )
	        preg_match('/youtube\.com\/embed\/([\w\-]+)/', $embed, $match );

	    // YouTube - if it is not an embed code, get the video code from the youtube URL
	    if ( !isset( $match[1] ) )
	        preg_match( '/v\=(.+)&/', $embed, $match );

	    // YouTube - get the corresponding thumbnail images
	    if ( isset( $match[1] ) )
	        $video_thumb = "http://img.youtube.com/vi/" . urlencode( $match[1] ) . "/0.jpg";

	    // return whichever thumbnail image you would like to retrieve
	    return $video_thumb;
	}

	public function _parse_yturl_pattern() {
	    $pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
	    $pattern .= '(?:www\.)?';         #  Optional www subdomain.
	    $pattern .= '(?:';                #  Group host alternatives:
	    $pattern .=   'youtu\.be/';       #    Either youtu.be,
	    $pattern .=   '|youtube\.com';    #    or youtube.com
	    $pattern .=   '(?:';              #    Group path alternatives:
	    $pattern .=     '/embed/';        #      Either /embed/,
	    $pattern .=     '|/v/';           #      or /v/,
	    $pattern .=     '|/watch\?v=';    #      or /watch?v=,    
	    $pattern .=     '|/watch\?.+&v='; #      or /watch?other_param&v=
	    $pattern .=   ')';                #    End path alternatives.
	    $pattern .= ')';                  #  End host alternatives.
	    $pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
	    $pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.
	    return $pattern;
	}

	public function get_singular_image_by_post( $post, $type ) {

		if ( $this->get_opengraph_image_post( $post->ID, $type, true ) ) {
			return $this->get_opengraph_image_post( $post->ID, $type, true );
		}

		if ( $this->get_attachment_page_image( $post->ID, true ) ) {
			return $this->get_attachment_page_image( $post->ID, true );
		}

		if ( $this->get_featured_image( $post->ID, true ) ) {
			return $this->get_featured_image( $post->ID, true );
		}

		if ( $this->get_content_images( $post, true ) ) {
			return $this->get_content_images( $post, true );
		}

		return function_exists('sstools_get_placeholder_image') ? sstools_get_placeholder_image() : '';
	}

	public function get_singular_default_image_by_post( $post, $type ) {

		if ( $this->get_attachment_page_image( $post->ID, true ) ) {
			return $this->get_attachment_page_image( $post->ID, true );
		}

		if ( $this->get_featured_image( $post->ID, true ) ) {
			//return $this->get_featured_image( $post->ID, true );
		}

		if ( $this->get_content_images( $post, true ) ) {
			return $this->get_content_images( $post, true );
		}

		return '';
	}

	/**
	 * Check size of featured image. If image is too small, return false, else return true
	 *
	 * @param array $img_data wp_get_attachment_image_src: url, width, height, icon.
	 *
	 * @return bool
	 */
	private function check_featured_image_size( $img_data ) {

		if ( ! is_array( $img_data ) ) {
			return false;
		}

		// Get the width and height of the image.
		if ( $img_data[1] < 200 || $img_data[2] < 200 ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the relative path of the image
	 *
	 * @param array $img Image data array.
	 *
	 * @return bool|string
	 */
	private function get_relative_path( $img ) {
		if ( $img[0] != '/' ) {
			return false;
		}

		// If it's a relative URL, it's relative to the domain, not necessarily to the WordPress install, we
		// want to preserve domain name and URL scheme (http / https) though.
		$parsed_url = wp_parse_url( home_url() );
		$img        = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $img;

		return $img;
	}
}
?>