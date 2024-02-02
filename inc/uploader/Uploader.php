<?php
namespace A3Rev\SocialShareTools;

class Uploader {

	/**
	 * @var string
	 */
	private $uploader_url;

	/*-----------------------------------------------------------------------------------*/
	/* Admin Uploader Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		if ( is_admin() ) {

			// include scripts to Admin UI Interface
			add_action( 'social_sharing_tools_init_scripts', array( $this, 'uploader_script' ) );

			// include styles to Admin UI Interface
			add_action( 'social_sharing_tools_init_styles', array( $this, 'uploader_style' ) );

		}

	}

	/*-----------------------------------------------------------------------------------*/
	/* uploader_url */
	/*-----------------------------------------------------------------------------------*/
	public function uploader_url() {
		if ( $this->uploader_url ) {
			return $this->uploader_url;
		}
		return $this->uploader_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	public function uploader_script() {
		add_action( 'admin_enqueue_scripts', array( $this, 'uploader_js' ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Include Uploader Script */
	/*-----------------------------------------------------------------------------------*/
	public function uploader_js () {
		
		wp_enqueue_script( 'a3-uploader-script', $this->uploader_url() . '/uploader-script.js', array( 'jquery', 'thickbox' ), '1.0.0' );
		
		if ( function_exists( 'wp_enqueue_media' ) ) {
		    wp_enqueue_media();
		} else {
		    wp_enqueue_script('media-upload');
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Include Uploader Style */
	/*-----------------------------------------------------------------------------------*/
	public function uploader_style () {
		
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'a3-uploader-style', $this->uploader_url() . '/uploader.css', array(), '1.0.0' );
		
		if ( is_rtl() ) {
			wp_enqueue_style( 'a3-uploader-style-rtl', $this->uploader_url() . '/uploader.rtl.css', array(), '1.0.0' );
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Get Upload Input Field */
	/*-----------------------------------------------------------------------------------*/
	public function upload_input ( $name_attribute, $id_attribute = '', $value = '', $attachment_id = 0, $default_value = '', $field_name = '', $class = '', $css = '', $description = '', $strip_methods = true, $size = 'original' ) {
		$output = '';

		if ( trim( $value ) == '' ){
			$value = trim( $default_value );
		}

		if ( strstr( $name_attribute, ']' ) ) {
			
			$attachment_id_name_attribute = substr_replace( $name_attribute, '_attachment_id', -1, 0 );
			$attachment_size_name_attribute = substr_replace( $name_attribute, '_attachment_size', -1, 0 );

		} else {

			$attachment_id_name_attribute = $name_attribute.'_attachment_id';
			$attachment_size_name_attribute = $name_attribute.'_attachment_size';
		}

		if ( $strip_methods === false ) {

			$strip_methods = 0;

		} else {

			$strip_methods = 1;

		}

		$output .= '<input type="hidden" name="'.$attachment_id_name_attribute.'" id="'.$id_attribute.'_attachment_id" value="'.$attachment_id.'" class=" a3_upload_attachment_id" />';
		$output .= '<input type="hidden" name="'.$attachment_size_name_attribute.'" id="'.$id_attribute.'_attachment_size" value="'.$size.'" class=" a3_upload_attachment_size" />';
		$output .= '<input data-strip-methods="'.$strip_methods.'" type="text" name="'.$name_attribute.'" id="'.$id_attribute.'" value="'.esc_attr( $value ).'" class="'.$id_attribute. ' ' .$class.' a3_upload" style="'.$css.'" rel="'.$field_name.'" /> ';
		$output .= '<input id="upload_'.$id_attribute.'" class="a3rev-ui-upload-button a3_upload_button button" type="button" value="'.__( 'Upload Image', 'social-share-tools' ).'" /> '.$description;
		
		$output .= '<div style="clear:both;"></div><div class="a3_screenshot" id="'.$id_attribute.'_image" style="'.( ( $value == '' ) ? 'display:none;' : 'display:block;' ).'">';

		if ( $value != '' ) {
			$remove = '<a href="javascript:(void);" class="a3_uploader_remove a3-plugin-ui-delete-icon">&nbsp;</a>';

			$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );

			if ( $image ) {

				$output .= '<img class="a3_uploader_image" src="' . esc_url( $value ) . '" alt="" />'.$remove.'';

			} else {

				$parts = explode( "/", $value );

				for( $i = 0; $i < sizeof( $parts ); ++$i ) {
					$title = $parts[$i];
				}

				$output .= '';

				$title = __( 'View File', 'social-share-tools' );

				$output .= '<div class="a3_no_image"><span class="a3_file_link"><a href="'.esc_url( $value ).'" target="_blank" rel="noopener" rel="a3_external">'.$title.'</a></span>'.$remove.'</div>';

			}
		}

		$output .= '</div>';

		return $output;
	}
}
