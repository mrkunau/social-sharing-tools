<?php

namespace A3Rev\SocialShareTools;

class RSS_Twitter extends \A3Rev\SocialShareTools\RSS_OpenGraph {

	/**
	 * @var array Images
	 */
	private $images = array();

	/**
	 * @var array $options Holds the options for the Twitter Card functionality
	 */
	public $options = array();

	/**
	 * Will hold the Twitter card type being created
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Class constructor
	 */
	public function __construct( $options ) {
		
		$this->options = $options;
		
		add_action( 'wp_head', array( $this, 'twitter' ), 1 );//40

		if( class_exists('WPSEO_Twitter') && !is_admin() ){
			add_filter( 'wpseo_output_twitter_card', array( $this, 'wpseo_output_twitter_card' ) , 99 );
			add_filter( 'wpseo_twitter_card_type', array( $this, 'wpseo_twitter_card_type' ) , 99 );
			add_filter( 'wpseo_twitter_description', array( $this, 'wpseo_space' ) , 99 );
			add_filter( 'wpseo_twitter_title', array( $this, 'wpseo_space' ) , 99 );
			add_filter( 'wpseo_twitter_image', array( $this, 'wpseo_space' ) , 99 );
		}

		add_action( 'wp_head', array( $this, 'sst_comment_end' ), 1 );
	}

	public function sst_comment_end() {
		echo '<!-- /og:meta Social Share Tools -->';
	}

	/**
	 * Outputs the Twitter Card code on singular pages.
	 */
	public function twitter() {

		/**
		 * Filter: 'rss_output_twitter_card' - Allow disabling of the Twitter card
		 *
		 * @api bool $enabled Enabled/disabled flag
		 */
		if ( false === apply_filters( 'rss_output_twitter_card', true ) ) {
			return;
		}

		//wp_reset_query();

		$this->type();
		$this->description();
		$this->title();
		$this->image();

		/**
		 * Action: 'rss_twitter' - Hook to add all Yoast SEO Twitter output to so they're close together.
		 */
		do_action( 'rss_twitter' );
	}

	public function wpseo_space( $str ){
		$str = '';
		return $str;
	}

	function wpseo_twitter_card_type( $type ){
		$type = $this->options['twitter_card_type'];
		return $type;
	}

	function wpseo_output_twitter_card( $card ){
		$card = false;
		return $card;
	}

	/**
	 * Display the Twitter card type.
	 *
	 * This defaults to summary but can be filtered using the <code>rss_twitter_card_type</code> filter.
	 *
	 * @link https://dev.twitter.com/docs/cards
	 */
	protected function type() {
		$this->determine_card_type();
		$this->sanitize_card_type();

		$this->output_metatag( 'card', $this->type );
	}

	/**
	 * Determines the twitter card type for the current page
	 */
	private function determine_card_type() {
		$this->type = $this->options['twitter_card_type'];

		// TODO this should be reworked to use summary_large_image for any fitting image R.
		if ( is_singular() && has_shortcode( $GLOBALS['post']->post_content, 'gallery' ) ) {

			$this->images = get_post_gallery_images();

			if ( count( $this->images ) > 0 ) {
				$this->type = 'summary_large_image';
			}
		}

		/**
		 * Filter: 'rss_twitter_card_type' - Allow changing the Twitter Card type as output in the Twitter card by Yoast SEO
		 *
		 * @api string $unsigned The type string
		 */
		$this->type = apply_filters( 'rss_twitter_card_type', $this->type );
	}

	/**
	 * Determines whether the card type is of a type currently allowed by Twitter
	 *
	 * @link https://dev.twitter.com/cards/types
	 */
	private function sanitize_card_type() {
		if ( ! in_array( $this->type, array(
			'summary',
			'summary_large_image',
			'app',
			'player',
		) )
		) {
			$this->type = 'summary';
		}
	}

	/**
	 * Output the metatag
	 *
	 * @param string $name    Tag name string.
	 * @param string $value   Tag value string.
	 * @param bool   $escaped Force escape flag.
	 */
	private function output_metatag( $name, $value, $escaped = false ) {

		// Escape the value if not escaped.
		if ( false === $escaped ) {
			$value = esc_attr( $value );
		}

		/**
		 * Filter: 'rss_twitter_metatag_key' - Make the Twitter metatag key filterable
		 *
		 * @api string $key The Twitter metatag key
		 */
		$metatag_key = apply_filters( 'rss_twitter_metatag_key', 'name' );

		// Output meta.
		echo '<meta ', esc_attr( $metatag_key ), '="twitter:', esc_attr( $name ), '" content="', esc_attr($value), '" />', "\n";
		if( 'image' == $name && !empty($value) ){
			//echo '<meta ', esc_attr( "property" ), '="og:', esc_attr( $name ), '" content="', esc_attr($value), '" />', "\n";
		}
	}

	/**
	 * Displays the title for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	protected function title() {

		$title = $this->get_title( 'twitter' );

		/**
		 * Filter: 'rss_opengraph_title' - Allow changing the title specifically for OpenGraph
		 *
		 * @api string $unsigned The title string
		 */
		$title = trim( apply_filters( 'rss_twitter_title', $title ) );

		if ( is_string( $title ) && $title !== '' ) {
	
			$this->output_metatag( 'title', $title );

			return true;
	
		}

	}

	/**
	 * Displays the description for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	protected function description() {

		$ogdesc = $this->get_description( 'twitter' );

		/**
		 * Filter: 'rss_opengraph_desc' - Allow changing the OpenGraph description
		 *
		 * @api string $ogdesc The description string.
		 */
		$ogdesc = trim( apply_filters( 'rss_twitter_description', $ogdesc ) );

		if ( is_string( $ogdesc ) && $ogdesc !== '' ) {
			
			$this->output_metatag( 'description', $ogdesc );
			
		}

		return $ogdesc;

	}

	/**
	 * Displays the image for Twitter
	 *
	 * Only used when OpenGraph is inactive or Summary Large Image card is chosen.
	 */
	protected function image( $image = false ) {

		$opengraph_images = new \A3Rev\SocialShareTools\RSS_OpenGraph_Image( $this->options, 'twitter', $image );

		foreach ( $opengraph_images->get_images() as $img ) {
			$this->image_output($img );
		}

		
	}

	/**
	 * Outputs a Twitter image tag for a given image
	 *
	 * @param string  $img The source URL to the image.
	 * @param boolean $tag Deprecated argument, previously used for gallery images.
	 *
	 * @return bool
	 */
	protected function image_output( $img, $tag = false ) {

		/**
		 * Filter: 'rss_twitter_image' - Allow changing the Twitter Card image
		 *
		 * @api string $img Image URL string
		 */
		$img = apply_filters( 'rss_twitter_image', $img );

		if ( $this->is_url_relative( $img ) === true && $img[0] === '/' ) {
			$parsed_url = wp_parse_url( home_url() );
			$img        = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $img;
		}

		$escaped_img = esc_url( $img );

		if ( is_string( $escaped_img ) && $escaped_img !== '' ) {
			$this->output_metatag( 'image', $escaped_img, true );
			return true;
		}

		return false;
	}
}
?>