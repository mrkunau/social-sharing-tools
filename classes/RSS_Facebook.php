<?php

namespace A3Rev\SocialShareTools;

class RSS_Facebook extends \A3Rev\SocialShareTools\RSS_OpenGraph {

	public $options = array();

	public function __construct( $options ) {

		$this->options = $options;

		if( ( defined( 'WPSEO_FILE' ) || class_exists('WPSEO_OpenGraph') ) && !is_admin() ){

			add_filter( 'wpseo_opengraph_type', array( $this, 'wpseo_space' ) , 99 );
			add_filter( 'wpseo_opengraph_title', array( $this, 'wpseo_space' ) , 99 );
			add_filter( 'wpseo_opengraph_desc', array( $this, 'wpseo_space' ) , 99 );
			add_filter( 'wpseo_opengraph_image', array( $this, 'wpseo_space' ) , 99 );
			add_action( 'wp_head', array( $this, 'sst_comment_start' ), 0 ); //0
			add_action( 'wp_head', array( $this, 'type' ), 0 ); //5
			add_action( 'wp_head', array( $this, 'og_title' ), 0 );//10
			add_action( 'wp_head', array( $this, 'description' ), 0 );//11
			add_action( 'wp_head', array( $this, 'image' ), 0 );//30
		}else{
			add_action( 'wp_head', array( $this, 'sst_comment_start' ), 1 ); //0
			add_action( 'wp_head', array( $this, 'locale' ), 1 );
			add_action( 'wp_head', array( $this, 'type' ), 1 ); //5
			add_action( 'wp_head', array( $this, 'og_title' ), 1 );//10
			add_action( 'wp_head', array( $this, 'description' ), 1 );//11
			add_action( 'wp_head', array( $this, 'url' ), 1 );//12
			add_action( 'wp_head', array( $this, 'site_name' ), 1 );//13
			add_action( 'wp_head', array( $this, 'website_facebook' ), 1 );//14
			if ( is_singular() && ! is_front_page() ) {
				add_action( 'wp_head', array( $this, 'article_author_facebook' ), 1 );//15
				add_action( 'wp_head', array( $this, 'tags' ), 1 );//16
				add_action( 'wp_head', array( $this, 'category' ), 1 );//17
				add_action( 'wp_head', array( $this, 'publish_date' ), 1 );//19
			}
			add_action( 'wp_head', array( $this, 'image' ), 1 );//30
		}

		add_action( 'wp_head', array( $this, 'publish_date_linkedin' ), 1 );//19

	}

	public function sst_comment_start() {
		echo '<!-- The og:meta tags below are created by a3rev Social Share Tools plugin version '.SOCIALSHARETOOLS_VERSION.' - https://a3rev.com/shop/social-share-tools/ -->';
	}

	public function wpseo_space( $str ){
		$str = '';
		return $str;
	}

	public function locale( $echo = true ) {
		/**
		 * Filter: 'rss_locale' - Allow changing the locale output
		 *
		 * @api string $unsigned Locale string
		 */
		$locale = apply_filters( 'rss_locale', get_locale() );

		// Catch some weird locales served out by WP that are not easily doubled up.
		$fix_locales = array(
			'ca' => 'ca_ES',
			'en' => 'en_US',
			'el' => 'el_GR',
			'et' => 'et_EE',
			'ja' => 'ja_JP',
			'sq' => 'sq_AL',
			'uk' => 'uk_UA',
			'vi' => 'vi_VN',
			'zh' => 'zh_CN',
		);

		if ( isset( $fix_locales[ $locale ] ) ) {
			$locale = $fix_locales[ $locale ];
		}

		// Convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does).
		if ( strlen( $locale ) == 2 ) {
			$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
		}

		// These are the locales FB supports.
		$fb_valid_fb_locales = array(
			'af_ZA', // Afrikaans.
			'ak_GH', // Akan.
			'am_ET', // Amharic.
			'ar_AR', // Arabic.
			'as_IN', // Assamese.
			'ay_BO', // Aymara.
			'az_AZ', // Azerbaijani.
			'be_BY', // Belarusian.
			'bg_BG', // Bulgarian.
			'bn_IN', // Bengali.
			'br_FR', // Breton.
			'bs_BA', // Bosnian.
			'ca_ES', // Catalan.
			'cb_IQ', // Sorani Kurdish.
			'ck_US', // Cherokee.
			'co_FR', // Corsican.
			'cs_CZ', // Czech.
			'cx_PH', // Cebuano.
			'cy_GB', // Welsh.
			'da_DK', // Danish.
			'de_DE', // German.
			'el_GR', // Greek.
			'en_GB', // English (UK).
			'en_IN', // English (India).
			'en_PI', // English (Pirate).
			'en_UD', // English (Upside Down).
			'en_US', // English (US).
			'eo_EO', // Esperanto.
			'es_CL', // Spanish (Chile).
			'es_CO', // Spanish (Colombia).
			'es_ES', // Spanish (Spain).
			'es_LA', // Spanish.
			'es_MX', // Spanish (Mexico).
			'es_VE', // Spanish (Venezuela).
			'et_EE', // Estonian.
			'eu_ES', // Basque.
			'fa_IR', // Persian.
			'fb_LT', // Leet Speak.
			'ff_NG', // Fulah.
			'fi_FI', // Finnish.
			'fo_FO', // Faroese.
			'fr_CA', // French (Canada).
			'fr_FR', // French (France).
			'fy_NL', // Frisian.
			'ga_IE', // Irish.
			'gl_ES', // Galician.
			'gn_PY', // Guarani.
			'gu_IN', // Gujarati.
			'gx_GR', // Classical Greek.
			'ha_NG', // Hausa.
			'he_IL', // Hebrew.
			'hi_IN', // Hindi.
			'hr_HR', // Croatian.
			'hu_HU', // Hungarian.
			'hy_AM', // Armenian.
			'id_ID', // Indonesian.
			'ig_NG', // Igbo.
			'is_IS', // Icelandic.
			'it_IT', // Italian.
			'ja_JP', // Japanese.
			'ja_KS', // Japanese (Kansai).
			'jv_ID', // Javanese.
			'ka_GE', // Georgian.
			'kk_KZ', // Kazakh.
			'km_KH', // Khmer.
			'kn_IN', // Kannada.
			'ko_KR', // Korean.
			'ku_TR', // Kurdish (Kurmanji).
			'ky_KG', // Kyrgyz.
			'la_VA', // Latin.
			'lg_UG', // Ganda.
			'li_NL', // Limburgish.
			'ln_CD', // Lingala.
			'lo_LA', // Lao.
			'lt_LT', // Lithuanian.
			'lv_LV', // Latvian.
			'mg_MG', // Malagasy.
			'mi_NZ', // Maori.
			'mk_MK', // Macedonian.
			'ml_IN', // Malayalam.
			'mn_MN', // Mongolian.
			'mr_IN', // Marathi.
			'ms_MY', // Malay.
			'mt_MT', // Maltese.
			'my_MM', // Burmese.
			'nb_NO', // Norwegian (bokmal).
			'nd_ZW', // Ndebele.
			'ne_NP', // Nepali.
			'nl_BE', // Dutch (Belgie).
			'nl_NL', // Dutch.
			'nn_NO', // Norwegian (nynorsk).
			'ny_MW', // Chewa.
			'or_IN', // Oriya.
			'pa_IN', // Punjabi.
			'pl_PL', // Polish.
			'ps_AF', // Pashto.
			'pt_BR', // Portuguese (Brazil).
			'pt_PT', // Portuguese (Portugal).
			'qu_PE', // Quechua.
			'rm_CH', // Romansh.
			'ro_RO', // Romanian.
			'ru_RU', // Russian.
			'rw_RW', // Kinyarwanda.
			'sa_IN', // Sanskrit.
			'sc_IT', // Sardinian.
			'se_NO', // Northern Sami.
			'si_LK', // Sinhala.
			'sk_SK', // Slovak.
			'sl_SI', // Slovenian.
			'sn_ZW', // Shona.
			'so_SO', // Somali.
			'sq_AL', // Albanian.
			'sr_RS', // Serbian.
			'sv_SE', // Swedish.
			'sw_KE', // Swahili.
			'sy_SY', // Syriac.
			'sz_PL', // Silesian.
			'ta_IN', // Tamil.
			'te_IN', // Telugu.
			'tg_TJ', // Tajik.
			'th_TH', // Thai.
			'tk_TM', // Turkmen.
			'tl_PH', // Filipino.
			'tl_ST', // Klingon.
			'tr_TR', // Turkish.
			'tt_RU', // Tatar.
			'tz_MA', // Tamazight.
			'uk_UA', // Ukrainian.
			'ur_PK', // Urdu.
			'uz_UZ', // Uzbek.
			'vi_VN', // Vietnamese.
			'wo_SN', // Wolof.
			'xh_ZA', // Xhosa.
			'yi_DE', // Yiddish.
			'yo_NG', // Yoruba.
			'zh_CN', // Simplified Chinese (China).
			'zh_HK', // Traditional Chinese (Hong Kong).
			'zh_TW', // Traditional Chinese (Taiwan).
			'zu_ZA', // Zulu.
			'zz_TR', // Zazaki.
		);

		// Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
		if ( ! in_array( $locale, $fb_valid_fb_locales ) ) {
			$locale = strtolower( substr( $locale, 0, 2 ) ) . '_' . strtoupper( substr( $locale, 0, 2 ) );
			if ( ! in_array( $locale, $fb_valid_fb_locales ) ) {
				$locale = 'en_US';
			}
		}

		if ( $echo !== false ) {
			$this->og_tag( 'og:locale', $locale );
		}

		return $locale;
	}

	public function type( $echo = true ) {

		$type = '';

		if ( is_front_page() || is_home() ) {
			$type = 'website';
		}
		elseif ( is_singular() ) {

			if ( $type === '' ) {
				$type = 'article';
			}
		}
		else {
			// We use "object" for archives etc. as article doesn't apply there.
			$type = 'object';
		}

		if ( $type === '' ) {
			$type = 'website';
		}

		/**
		 * Filter: 'rss_opengraph_type' - Allow changing the OpenGraph type of the page
		 *
		 * @api string $type The OpenGraph type string.
		 */
		$type = apply_filters( 'rss_opengraph_type', $type );

		if ( is_string( $type ) && $type !== '' ) {
			if ( $echo !== false ) {
				$this->og_tag( 'og:type', $type );
			}
			else {
				return $type;
			}
		}

		return '';
	}

	public function og_title( $echo = true ) {

		$title = $this->get_title( 'facebook' );

		/**
		 * Filter: 'rss_opengraph_title' - Allow changing the title specifically for OpenGraph
		 *
		 * @api string $unsigned The title string
		 */
		$title = trim( apply_filters( 'rss_opengraph_title', $title ) );

		if ( is_string( $title ) && $title !== '' ) {
			if ( $echo !== false ) {
				$this->og_tag( 'og:title', $title );

				return true;
			}
		}

		if ( $echo === false ) {
			return $title;
		}

		return false;
	}

	public function description( $echo = true ) {
		
		$ogdesc = $this->get_description( 'facebook' );

		/**
		 * Filter: 'rss_opengraph_desc' - Allow changing the OpenGraph description
		 *
		 * @api string $ogdesc The description string.
		 */
		$ogdesc = trim( apply_filters( 'rss_opengraph_desc', $ogdesc ) );

		if ( is_string( $ogdesc ) && $ogdesc !== '' ) {
			if ( $echo !== false ) {
				$this->og_tag( 'og:description', $ogdesc );
			}
		}

		return $ogdesc;
	}

	public function url() {
		$url = '';
		// Set decent canonicals for homepage, singulars and taxonomy pages.
		if ( is_singular() ) {
			$obj       = get_queried_object();
			$url = get_permalink( $obj->ID );
		}
		else {
			if ( is_search() ) {
				$search_query = get_search_query();
				// Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
				if ( ! empty( $search_query ) && ! preg_match( '|^page/\d+$|', $search_query ) ) {
					$url = get_search_link();
				}
			}
			elseif ( is_front_page() ) {
				$url = $this->home_url();
			}
			elseif ( $this->is_posts_page() ) {
				$posts_page_id = get_option( 'page_for_posts' );
				$url = get_permalink( $posts_page_id );
			}
			elseif ( is_tax() || is_tag() || is_category() ) {

				$term = get_queried_object();

				if ( ! empty( $term ) ) {

					$term_link          = get_term_link( $term, $term->taxonomy );

					if ( ! is_wp_error( $term_link ) ) {
						$url = $term_link;
					}
				}
			}
			elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}
				$url = get_post_type_archive_link( $post_type );
			}
			elseif ( is_author() ) {
				$url = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
			}
			elseif ( is_archive() ) {
				if ( is_date() ) {
					if ( is_day() ) {
						$url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
					}
					elseif ( is_month() ) {
						$url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
					}
					elseif ( is_year() ) {
						$url = get_year_link( get_query_var( 'year' ) );
					}
				}
			}
		}

		$url = apply_filters( 'rss_opengraph_url', $url );

		if ( is_string( $url ) && $url !== '' ) {
			$this->og_tag( 'og:url', esc_url( $url ) );

			return true;
		}

		return false;
	}

	public function site_name() {
		/**
		 * Filter: 'rss_opengraph_site_name' - Allow changing the OpenGraph site name
		 *
		 * @api string $unsigned Blog name string
		 */
		$name = apply_filters( 'rss_opengraph_site_name', get_bloginfo( 'name' ) );
		if ( is_string( $name ) && $name !== '' ) {
			$this->og_tag( 'og:site_name', $name );
		}
	}

	public function website_facebook() {

		if ( 'article' === $this->type( false ) && ! empty( $this->options['facebook_site'] ) ) {
			$this->og_tag( 'article:publisher', $this->options['facebook_site'] );

			return true;
		}

		return false;
	}

	public function article_author_facebook() {

		if ( ! is_singular() ) {
			return false;
		}

		/**
		 * Filter: 'rss_opengraph_author_facebook' - Allow developers to filter the Yoast SEO post authors facebook profile URL
		 *
		 * @api bool|string $unsigned The Facebook author URL, return false to disable
		 */
		$facebook = apply_filters( 'rss_opengraph_author_facebook', get_the_author_meta( 'display_name', $GLOBALS['post']->post_author ) );

		if ( $facebook && ( is_string( $facebook ) && $facebook !== '' ) ) {
			$this->og_tag( 'article:author', $facebook );

			return true;
		}

		return false;
	}

	public function tags() {
		if ( ! is_singular() ) {
			return false;
		}

		$tags = get_the_tags();
		if ( ! is_wp_error( $tags ) && ( is_array( $tags ) && $tags !== array() ) ) {

			foreach ( $tags as $tag ) {
				$this->og_tag( 'article:tag', $tag->name );
			}

			return true;
		}

		return false;
	}

	public function category() {

		if ( ! is_singular() ) {
			return false;
		}

		$terms = get_the_category();

		if ( ! is_wp_error( $terms ) && ( is_array( $terms ) && $terms !== array() ) ) {

			// We can only show one section here, so we take the first one.
			$this->og_tag( 'article:section', $terms[0]->name );

			return true;
		}

		return false;
	}

	public function publish_date() {

		if ( ! is_singular( 'post' ) ) {
			/**
			 * Filter: 'rss_opengraph_show_publish_date' - Allow showing publication date for other post types
			 *
			 * @api bool $unsigned Whether or not to show publish date
			 *
			 * @param string $post_type The current URL's post type.
			 */
			if ( false === apply_filters( 'rss_opengraph_show_publish_date', false, get_post_type() ) ) {
				return false;
			}
		}

		$pub = get_the_date( DATE_W3C );
		$this->og_tag( 'article:published_time', $pub );

		$mod = get_the_modified_date( DATE_W3C );
		if ( $mod != $pub ) {
			$this->og_tag( 'article:modified_time', $mod );
			$this->og_tag( 'og:updated_time', $mod );
		}

		return true;
	}

	public function publish_date_linkedin() {

		if ( ! is_singular( 'post' ) ) {
			/**
			 * Filter: 'rss_opengraph_show_publish_date' - Allow showing publication date for other post types
			 *
			 * @api bool $unsigned Whether or not to show publish date
			 *
			 * @param string $post_type The current URL's post type.
			 */
			if ( false === apply_filters( 'rss_opengraph_show_publish_date', true, get_post_type() ) ) {
				//return false;
			}
		}

		$pub = get_the_date( DATE_W3C );

		//$this->og_tag( 'og:publish_date', $pub );
		//$this->og_tag( 'og:published_time', $pub );
		$this->og_tag_datetime( 'og:publish_date', $pub );
		
		//$this->og_tag( 'article:published_time', $pub );

		$mod = get_the_modified_date( DATE_W3C );
		if ( $mod != $pub ) {
			//$this->og_tag( 'article:modified_time', $mod );
			//$this->og_tag( 'og:updated_time', $mod );
			//$this->og_tag( 'og:publish_date', $mod );
		}



		return true;
	}

	public function og_tag_datetime( $property, $content ) {
		$og_property = str_replace( ':', '_', $property );
		echo '<meta name="publish_date" property="', esc_attr( $property ), '" content="', esc_attr( $content ), '" />', "\n";
		return true;
	}

	public function image( $image = false ) {
		$opengraph_images = new \A3Rev\SocialShareTools\RSS_OpenGraph_Image( $this->options, 'facebook', $image );

		foreach ( $opengraph_images->get_images() as $img ) {
			$this->og_tag( 'og:image', esc_url( $img ) );

			if ( 0 === strpos( $img, 'https://' ) ) {
				$this->og_tag( 'og:image:secure_url', esc_url( $img ) );
			}
		}

		$dimensions = $opengraph_images->get_dimensions();

		if ( ! empty( $dimensions['width'] ) ) {
			$this->og_tag( 'og:image:width', absint( $dimensions['width'] ) );
		}

		if ( ! empty( $dimensions['height'] ) ) {
			$this->og_tag( 'og:image:height', absint( $dimensions['height'] ) );
		}
	}

	public function og_tag( $property, $content ) {
		$og_property = str_replace( ':', '_', $property );
		/**
		 * Filter: 'rss_og_' . $og_property - Allow developers to change the content of specific OG meta tags.
		 *
		 * @api string $content The content of the property
		 */
		$content = apply_filters( 'rss_og_' . $og_property, $content );
		if ( empty( $content ) ) {
			return false;
		}

		echo '<meta property="', esc_attr( $property ), '" content="', esc_attr( $content ), '" />', "\n";

		return true;
	}

}
?>
