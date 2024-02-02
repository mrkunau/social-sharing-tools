<?php

namespace A3Rev\SocialShareTools;

class RSS_Social_Preview {

	private function get_website() {
		// We only want the host part of the URL.
		$website = parse_url( home_url(), PHP_URL_HOST );
		$website = trim( $website, '/' );
		$website = strtolower( $website );

		return $website;
	}

	public function social_preview( 
		$opengraph_title 		= '',
		$opengraph_description 	= '',
		$opengraph_image 		= '',
		$opengraph_image_attachment_id = '',
		$opengraph_image_default = '',
		$twitter_title 			= '',
		$twitter_description 	= '',
		$twitter_image 			= '',
		$twitter_image_attachment_id = '',
		$twitter_image_default = ''
	) {

		global $post, $social_share_tools_uploader, $social_share_tools_icons;
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$social_share_tools_uploader->uploader_style();
		$social_share_tools_uploader->uploader_js();
		wp_enqueue_style( 'sstools-custom-fields', SOCIALSHARETOOLS_CSS_URL . '/custom-fields' . $suffix . '.css' );
		wp_enqueue_script( 'sstools-custom-fields', SOCIALSHARETOOLS_JS_URL . '/custom-fields' . $suffix . '.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-tabs' ), null, true );
		wp_enqueue_style( 'social-preview', SOCIALSHARETOOLS_CSS_URL . '/social-preview' . $suffix . '.css' );
		wp_enqueue_script( 'rss-social-preview', SOCIALSHARETOOLS_JS_URL . '/social-preview' . $suffix . '.js', array( 'jquery' ), null, true );
		
		$params = array(
			'upload_button_text'                => esc_attr__( 'Upload Image', 'social-share-tools' ),
			'upload_button_other_text'          => esc_attr__( 'Add image', 'social-share-tools' ),
			'fb_default_img'                	=> $opengraph_image_default,
			'tw_default_img'          			=> $twitter_image_default,
			'fb_default_desc'                	=> esc_attr__( 'Modify your Facebook description by editing it right here.', 'social-share-tools' ),
			'tw_default_desc'          			=> esc_attr__( 'Modify your Twitter description by editing it right here.', 'social-share-tools' ),
			'fb_placeholder_msg'          		=> esc_attr__( 'Preview image.', 'social-share-tools' ),
			'fb_placeholder_error'          	=> esc_attr__( 'The image you selected is too small for Facebook.', 'social-share-tools' ),
		);

		wp_localize_script( 'rss-social-preview', '_rss_social_preview', $params );
		
		$_rss_feature_image_full_url = '';

		if ( $post && $post->ID > 0 ) {

			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
				$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), apply_filters( 'rss_social_image_size', 'full' ) );

				if ( $featured_img ) {
					$_rss_feature_image_full_url = $featured_img[0];
				}
			}
		}

		$debugUrl = '';

		if( isset( $_REQUEST['tag_ID'] ) ){
			$debugUrl = get_term_link( (int) $_REQUEST['tag_ID'] );
		}elseif( $post && $post->ID > 0 ){
			$debugUrl = get_permalink( $post->ID );
		}

		$fbDebugUrl = 'https://developers.facebook.com/tools/debug/';
		$liDebugUrl = 'https://www.linkedin.com/post-inspector/inspect/';

		if( !empty($debugUrl ) ){
			$fbDebugUrl = 'https://developers.facebook.com/tools/debug/?q='.urlencode($debugUrl);
			$liDebugUrl = 'https://www.linkedin.com/post-inspector/inspect/'.urlencode($debugUrl);
		}

		?>
		<style>#rss_meta .inside{opacity: 0;}</style>
		<div style="clear: both;"></div>
		<div class="sstools-metabox-tabs">

			<input name="_rss_feature_image_full_url" type="hidden" value="<?php echo esc_url($_rss_feature_image_full_url);?>" />
			
			<ul class="tabber hide-if-no-js">
				<li class="wf-tab-facebook"><a href="#wf-tab-facebook" title="<?php esc_attr_e( 'Facebook', 'social-share-tools' );?>"><i class="facebook-square" aria-hidden="true"><?php echo $social_share_tools_icons['facebook-square'];?></i></a></li>
				<li class="wf-tab-linkedin"><a href="#wf-tab-linkedin" title="<?php esc_attr_e( 'Linkedin', 'social-share-tools' );?>"><i class="linkedin" aria-hidden="true"><?php echo $social_share_tools_icons['linkedin'];?></i></a></li>
				<li class="wf-tab-discord"><a href="#wf-tab-discord" title="<?php esc_attr_e( 'Discord', 'social-share-tools' );?>"><i class="discord" aria-hidden="true"><?php echo $social_share_tools_icons['discord'];?></i></a></li>
				<li class="wf-tab-twitter"><a href="#wf-tab-twitter" title="<?php esc_attr_e( 'Twitter', 'social-share-tools' );?>"><i class="twitter-square"><?php echo $social_share_tools_icons['twitter-square'];?></i></a></li>

			</ul>
			
			<div id="wf-tab-facebook" class="wf-tab-content">

				<div style="clear: both;"></div>
				<div id="fbPreview">
					<div class="editable-preview">
						<h3 class="editor-heading "><?php esc_attr_e( 'Facebook Share Preview', 'social-share-tools' );?></h3>
						<section class="editable-preview-inner">
							<div class="social-preview-inner editable-preview-largex">
								<div class="editor-container editable-preview-image">
									<div class="social-image-placeholder">
			
									</div>
								</div>
								<div class="editable-preview-text-keeper">
									<div class="editable-preview-website">
										<div class="editable-preview-value editable-preview-value-website">
											<?php echo esc_html($this->get_website());?>
											<span class="editable-preview-value editable-preview-value-author"></span>
										</div>
									</div>
									<div class="editable-preview-title">
										<div class="editable-preview-value editable-preview-value-title"><?php esc_attr_e( 'Facebook Title', 'social-share-tools' );?></div>
									</div>
									<div class="editable-preview-description">
										<div class="editable-preview-value editable-preview-value-description"><?php esc_attr_e( 'Facebook description', 'social-share-tools' );?></div>
									</div>
								</div>
							</div>
							
							
						</section>

						<!-- <h3 class="editor-heading"><?php esc_attr_e( 'Facebook editor', 'social-share-tools' );?></h3> -->
						
						<div class="editable-preview-inner editable-preview-form">

							<span><?php esc_attr_e( 'Use the settings below to create the perfect Social share Image, Title and Description that is displayed when this post/page link is shared on Facebook, LinkedIn, Discord and more.', 'social-share-tools' );?></span>

							<label for="rss_opengraph-button"><?php esc_attr_e( 'Free Share Image Creation Tool', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_opengraph-button-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
							<p class="rss_metabox_description rss-help-panel" id="rss_opengraph-button-desc"><?php esc_attr_e( 'Create Image button will open Pablo a free online social media image creation tool. Upload and edit your own image or choose from over 600k royalty free images. Once created download your image to your computer and then use the Upload Image button to add it.', 'social-share-tools' ); ?></p>
							<a target="_blank" rel="noopener" href="https://pablo.buffer.com/" class="_rss_opengraph-button button"><?php esc_attr_e( 'Create Image', 'social-share-tools' );?></a>

							<label for="rss_opengraph-image"><?php esc_attr_e( 'Custom Share Image', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_opengraph-image-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
							<p class="rss_metabox_description rss-help-panel" id="rss_opengraph-image-desc"><?php esc_attr_e( 'Upload your share image here, 1024 x 512 pixels size is recommended. If it is smaller, it will show as a small thumbnail on the left with title and description to the right or if it is larger, it will be cropped.', 'social-share-tools' ); ?></p>
							<div style="clear: both;"></div>
							<div style="position: relative;">
								<?php
								$rss_opengraph_remove  = '<a class="_rss_opengraph-remove-image button button-primary hidden">'.esc_attr__( 'Remove Image', 'social-share-tools' ).'</a>';
								echo $social_share_tools_uploader->upload_input( '_rss_opengraph-image', '_rss_opengraph-image', $opengraph_image, $opengraph_image_attachment_id, '', esc_attr__( 'Facebook Image', 'social-share-tools' ), '', '' , $rss_opengraph_remove, false );
								?>
								<span style="margin-top: 5px;display:block"><?php esc_attr_e( 'Recommended size 1024 x 512 pixels.', 'social-share-tools' );?></span>
							</div>
							<div style="clear: both;"></div>
							<div class="rss_opengraph-title-wrap">
								<label for="rss_opengraph-title"><?php esc_attr_e( 'Custom Share Title', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_opengraph-title-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
								<p class="rss_metabox_description rss-help-panel" id="rss_opengraph-title-desc"><?php esc_attr_e( 'Leave this empty and the post title will be used.', 'social-share-tools' ); ?></p>
								<input aria-describedby="rss_opengraph-title-desc" class="large-text" id="rss_opengraph-title" name="_rss_opengraph-title" type="text" value="<?php echo $opengraph_title;?>" />
							</div>
							<div class="rss_opengraph-description-wrap">
								<label for="rss_opengraph-description"><?php esc_attr_e( 'Custom Share Description', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_opengraph-description-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
								<p class="rss_metabox_description rss-help-panel" id="rss_opengraph-description-desc"><?php esc_attr_e( 'Leave this empty and the post extract will be used as the description.', 'social-share-tools' ); ?></p>
								<textarea aria-describedby="rss_opengraph-description-desc" class="large-text" id="rss_opengraph-description" name="_rss_opengraph-description" rows="3"><?php echo $opengraph_description;?></textarea>
							</div>
							<div style="clear: both;"></div>
							<div style="display:flex;gap: 10px;justify-content: space-between;width: 100%;margin-bottom: 10px;">
							<a style="margin-top: 10px;" class="button button-primary" target="_blank" href="<?php echo $fbDebugUrl; ?>"><?php esc_attr_e( 'Facebook Debugger', 'social-share-tools' ); ?></a>
							</div>
							<span><?php esc_attr_e( 'Use the debugger tools to set or change the Share Image and or details.', 'social-share-tools' ); ?></span>
							<div style="clear: both;"></div>
						</div>

					</div>
				</div>
				<div style="clear: both;"></div>
				<div style="margin-top:20px;clear: both;display: none;">
					<input type="checkbox" name="sstools_clear_facebook_cache" id="sstools_clear_facebook_cache" value="1" /> <span><?php esc_attr_e( 'Clear this post facebook cache.', 'social-share-tools' ); ?></span>
				</div>
			</div>


			<div id="wf-tab-linkedin" class="wf-tab-content">

				<div style="clear: both;"></div>
				<div id="liPreview">
					<div class="editable-preview">
						<h3 class="editor-heading "><?php esc_attr_e( 'Linkedin Share Preview', 'social-share-tools' );?></h3>
						<section class="editable-preview-inner">
							<div class="social-preview-inner editable-preview-largex">
								<div class="editor-container editable-preview-image">
									<div class="social-image-placeholder">
			
									</div>
								</div>
								<div class="editable-preview-text-keeper">
									<div class="editable-preview-title">
										<div class="editable-preview-value editable-preview-value-title"><?php esc_attr_e( 'Linkedin Title', 'social-share-tools' );?></div>
									</div>
									<div class="editable-preview-website">
										<div class="editable-preview-value editable-preview-value-website">
											<?php echo esc_html($this->get_website());?>
											<span class="editable-preview-value editable-preview-value-author"></span>
										</div>
									</div>
								</div>
							</div>
							
							
						</section>

						
						<div class="editable-preview-inner editable-preview-form">
							<div>
							<label><?php esc_attr_e( 'Things to Know!', 'social-share-tools' ); ?></label>
							<span><?php esc_attr_e( 'This tab shows the LinkedIn share preview and is not used for editing. LinkedIn post/page share uses Image and Title but not description. These are set from the "Social Share Display Settings" on the Facebook Preview tab.', 'social-share-tools' ); ?></span>
							</div>
							<div style="clear: both;"></div>
							<div style="display:flex;gap: 10px;justify-content: space-between;width: 100%;margin-bottom: 10px;">
							<a style="margin-top: 10px;" class="button button-primary" target="_blank" href="<?php echo $liDebugUrl; ?>"><?php esc_attr_e( 'LinkedIn Debugger', 'social-share-tools' ); ?></a>
							</div>
							<span><?php esc_attr_e( 'Use the debugger tools to set or break the LinkedIn Post Share Cache.', 'social-share-tools' ); ?></span>
							<div style="clear: both;"></div>
						</div>

					</div>
				</div>
				<div style="clear: both;"></div>
				<div style="margin-top:20px;clear: both;display: none;">
					<input type="checkbox" name="sstools_clear_linkedin_cache" id="sstools_clear_linkedin_cache" value="1" /> <span><?php esc_attr_e( 'Clear this post linkedin cache.', 'social-share-tools' ); ?></span>
				</div>
			</div>


			<div id="wf-tab-discord" class="wf-tab-content">

				<div style="clear: both;"></div>
				<div id="dcPreview">
					<div class="editable-preview">
						<h3 class="editor-heading "><?php esc_attr_e( 'Discord Share Preview', 'social-share-tools' );?></h3>
						<section class="editable-preview-inner">
							<div class="social-preview-inner editable-preview-largex">
								<div class="editable-preview-text-keeper">
									<div class="editable-preview-website">
										<div class="editable-preview-value editable-preview-value-website">
											<?php echo esc_html($this->get_website());?>
											<span class="editable-preview-value editable-preview-value-author"></span>
										</div>
									</div>
									<div class="editable-preview-title">
										<div class="editable-preview-value editable-preview-value-title"><?php esc_attr_e( 'Discord Title', 'social-share-tools' );?></div>
									</div>
									<div class="editable-preview-description">
										<div class="editable-preview-value editable-preview-value-description"><?php esc_attr_e( 'Discord description', 'social-share-tools' );?></div>
									</div>
								</div>
								<div class="editor-container editable-preview-image">
									<div class="social-image-placeholder">
			
									</div>
								</div>
							</div>
							
							
						</section>

						
						<div class="editable-preview-inner editable-preview-form">
							<div>
							<label><?php esc_attr_e( 'Things to Know!', 'social-share-tools' ); ?></label>
							<span><?php esc_attr_e( 'This tab shows the Discord share preview and is not used for editing. The Discord Share Image, Title and Description are set from the "Social Share Display Settings" on the Facebook Preview tab.', 'social-share-tools' ); ?></span>
							</div>
							<div style="clear: both;"></div>
						</div>

					</div>
				</div>
				<div style="clear: both;"></div>
				<div style="margin-top:20px;clear: both;display: none;">
					<input type="checkbox" name="sstools_clear_discord_cache" id="sstools_clear_discord_cache" value="1" /> <span><?php esc_attr_e( 'Clear this post discord cache.', 'social-share-tools' ); ?></span>
				</div>
			</div>


			<div id="wf-tab-twitter" class="wf-tab-content">

				<div style="clear: both;"></div>
				<div id="twPreview">
					<div class="editable-preview">
						<h3 class="editor-heading "><?php esc_attr_e( 'X (formerly Twitter) Share Preview', 'social-share-tools' );?></h3>
						<section class="editable-preview-inner">
							<div class="social-preview-inner editable-preview-large">
								<div class="editor-container editable-preview-image">
									<div class="social-image-placeholder">
									
									</div>
								</div>
								<div class="editable-preview-text-keeper">
									<div class="editable-preview-title">
										<div class="editable-preview-value editable-preview-value-title"><?php esc_attr_e( 'Twitter Title', 'social-share-tools' );?></div>
									</div>
									<div class="editable-preview-description">
										<div class="editable-preview-value editable-preview-value-description"><?php esc_attr_e( 'Twitter description', 'social-share-tools' );?></div>
									</div>
									<div class="editable-preview-website">
										<div class="editable-preview-value editable-preview-value-website">
											<?php echo esc_html($this->get_website());?>
											<span class="editable-preview-value editable-preview-value-author"></span>
										</div>
									</div>
								</div>
							</div>
							
							
						</section>

						


						<!-- <h3 class="editor-heading"><?php esc_attr_e( 'Twitter editor', 'social-share-tools' );?></h3> -->

						<div class="editable-preview-inner editable-preview-form">

							<div>
							<label><?php esc_attr_e( 'Things to Know!', 'social-share-tools' ); ?></label>
							<span><?php esc_attr_e( 'Here you can customize the appearance of your post specifically for X (formerly Twitter). If you leave these settings untouched, the "Social Share Display Settings" on the Facebook tab will applied for sharing on X. Sharing on X shows the share image with the Title in the bottom left corner.', 'social-share-tools' );?></span>
							</div>

							<label for="rss_twitter-button"><?php esc_attr_e( 'X Free Image Creator', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_twitter-button-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
							<p class="rss_metabox_description rss-help-panel" id="rss_twitter-button-desc"><?php esc_attr_e( 'Create Image button will open Pablo a free online social media image creation tool. Upload and edit your own image or choose from over 600k royalty free images. Once created download your image to your computer and then use the Upload Image button to add it.', 'social-share-tools' ); ?></p>
							<a target="_blank" rel="noopener" href="https://pablo.buffer.com/" class="_rss_twitter-button button"><?php esc_attr_e( 'Create Image', 'social-share-tools' );?></a>
							
							<label for="rss_twitter-image"><?php esc_attr_e( 'X Share Image', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_twitter-image-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
							<p class="rss_metabox_description rss-help-panel" id="rss_twitter-image-desc"><?php esc_attr_e( 'Recommended image size for X post / page share image is 1024 by 512 pixels. Upload your image here.', 'social-share-tools' ); ?></p>
							<div style="clear: both;"></div>
							<div style="position: relative;">
								<?php
								$rss_twitter_remove  = '<a class="_rss_twitter-remove-image button button-primary hidden">'.esc_attr__( 'Remove Image', 'social-share-tools' ).'</a>';
								echo $social_share_tools_uploader->upload_input( '_rss_twitter-image', '_rss_twitter-image', $twitter_image, $twitter_image_attachment_id, '', esc_attr__( 'Twitter Image', 'social-share-tools' ), '', '' , $rss_twitter_remove, false );
								?>
								<span style="margin-top: 5px;display:block"><?php esc_attr_e( 'Image size must be 1024 x 512 pixels.', 'social-share-tools' );?></span>
							</div>
							<div style="clear: both;"></div>
							<div class="rss_opengraph-title-wrap">
								<label for="rss_twitter-title"><?php esc_attr_e( 'X Share Title', 'social-share-tools' ); ?></label>
								<input aria-describedby="rss_twitter-title-desc" class="large-text" id="rss_twitter-title" name="_rss_twitter-title" type="text" value="<?php echo $twitter_title;?>" />
							</div>
								<div class="rss_opengraph-description-wrap">
								<label for="rss_twitter-description"><?php esc_attr_e( 'X Share Description', 'social-share-tools' ); ?> <i class="question icon-tip" data-desc="rss_twitter-description-desc"><?php echo $social_share_tools_icons['question'];?></i></label>
								<p class="rss_metabox_description rss-help-panel" id="rss_twitter-description-desc"><?php esc_attr_e( 'Leave this empty and every share to Twitter will use the post / page extract, or write a custom share description here. See the custom description in the Twitter preview card as you write it.', 'social-share-tools' ); ?></p>
								<textarea aria-describedby="rss_twitter-description-desc" class="large-text" id="rss_twitter-description" name="_rss_twitter-description" rows="3"><?php echo $twitter_description;?></textarea>
					     	</div>
							<div style="clear: both;"></div>
							<a style="margin-top: 10px;display: none;" class="button button-primary" target="_blank" href="https://cards-dev.twitter.com/validator"><?php esc_attr_e( 'Clear Cache', 'social-share-tools' ); ?></a>
							<div style="clear: both;"></div>
						</div>

					</div>
				</div>
				
				<div style="clear: both;"></div>
			</div>
			
		</div>
		<div style="clear: both;"></div>
		<?php
    
	}
}
?>