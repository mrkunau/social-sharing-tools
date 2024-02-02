(function($) {

	var rssSocialPreview = {

		fb_img_small_width 		: 200,
	    fb_img_small_height 	: 200,

	    fb_img_medium_width 	: 158,
	    fb_img_medium_height 	: 470,

	    fb_img_large_width 		: 600,
	    fb_img_large_height 	: 315,

	    tw_img_small_width 		: 120,
	    tw_img_small_height 	: 506,

	    tw_img_large_width 		: 280,
	    tw_img_large_height 	: 150,

		init: function() {

			$(document).on( 'click', '._rss_opengraph-remove-image', function( e ) {
		    	e.preventDefault();
				$('#_rss_opengraph-image_image .a3_uploader_remove').trigger('click');
			});

			$(document).on( 'click', '._rss_twitter-remove-image', function( e ) {
				e.preventDefault();
				$('#_rss_twitter-image_image .a3_uploader_remove').trigger('click');
			});

		    $(document).on( 'click', '.icon-tip', function( e ) {
				e.preventDefault();
				var toggle_id = $(this).data('desc');
				$('#'+toggle_id).toggle();
			});

			$(window).on( 'load', function() {
			
				rssSocialPreview.rssImageContainer($('input[name="_rss_opengraph-image"]').val(), 'fb' );
				rssSocialPreview.rssImageContainer($('input[name="_rss_twitter-image"]').val(), 'tw');
			
				rssSocialPreview.rssTitleContainer($('input[name="_rss_opengraph-title"]').val(), 'fb' );
				rssSocialPreview.rssTitleContainer($('input[name="_rss_twitter-title"]').val(), 'tw');

				rssSocialPreview.rssDescriptionContainer($('textarea[name="_rss_opengraph-description"]').val(), 'fb' );
				rssSocialPreview.rssDescriptionContainer($('textarea[name="_rss_twitter-description"]').val(), 'tw');

				setTimeout(function(){ 
					$( '.editable-preview .a3_upload_button' ).addClass('button-primary');
					rssSocialPreview.changeUploadButton(); 
					$('body').addClass( 'admin-loaded' ); 
				}, 100);

			});

			$(document).on( 'change', 'input[name="_rss_opengraph-image"]' , function( e ) {
				e.preventDefault();
				rssSocialPreview.rssImageContainer($(this).val(), 'fb' );
				rssSocialPreview.changeUploadButton();
			});

			$(document).on( 'change', 'input[name="_rss_twitter-image"]' , function( e ) {
				e.preventDefault();
				rssSocialPreview.rssImageContainer($(this).val(), 'tw' );
				rssSocialPreview.changeUploadButton();
			});

			var featuredImage = wp.media.featuredImage.frame();

			featuredImage.on("select", function() {
				var featuredImageObject = featuredImage.state().get("selection").first();

				$('input[name="_rss_feature_image_full_url"]').val( featuredImageObject.attributes.url );

				if( $('input[name="_rss_opengraph-image"]').val() == '' ){
					//rssSocialPreview.rssImageContainer( featuredImageObject.get("url") , 'fb' );
					rssSocialPreview.rssImageContainer( featuredImageObject.attributes.url , 'fb' );
				}

				if( $('input[name="_rss_twitter-image"]').val() == '' ){
					//rssSocialPreview.rssImageContainer( featuredImageObject.get("url") , 'tw' );
					rssSocialPreview.rssImageContainer( featuredImageObject.attributes.url , 'tw' );
				}

			});

			$('#postimagediv').on( "click", '#remove-post-thumbnail', function( e ) {
				e.preventDefault();

				$('input[name="_rss_feature_image_full_url"]').val('');

				$( document ).ajaxComplete(function() {
					if( $('input[name="_rss_opengraph-image"]').val() == '' ){
						rssSocialPreview.rssImageContainer( '' , 'fb' );
					}

					if( $('input[name="_rss_twitter-image"]').val() == '' ){
						rssSocialPreview.rssImageContainer( '' , 'tw' );
					}

				});

			});
			
			$(document).on( 'keyup paste click', 'input[name="_rss_opengraph-title"]' , function( e ) {
				var title = $(this);
				setTimeout( function(){
					rssSocialPreview.rssTitleContainer(title.val(), 'fb' );
				}, 1 );
			});

			$(document).on( 'keyup paste click', 'input[name="_rss_twitter-title"]' , function( e ) {
				var title = $(this);
				setTimeout( function(){
					rssSocialPreview.rssTitleContainer(title.val(), 'tw');
				}, 1 );
			});

			$(document).on( 'keyup paste click', 'input[name="post_title"], #edittag input[name="name"]' , function( e ) {
				var title = $(this);
				setTimeout( function(){
					if( '' === $('input[name="_rss_opengraph-title"]').val() ){
						rssSocialPreview.rssTitleContainer(title.val(), 'fb' );
					}
					if( '' === $('input[name="_rss_twitter-title"]').val() ){
						rssSocialPreview.rssTitleContainer(title.val(), 'tw' );
					}
				}, 1 );
			});

			$(document).on( 'keyup paste click', 'textarea[name="_rss_opengraph-description"]' , function( e ) {
				var description = $(this);
				setTimeout( function(){
					rssSocialPreview.rssDescriptionContainer(description.val(), 'fb' );
				}, 1 );
			});

			$(document).on( 'keyup paste click', 'textarea[name="_rss_twitter-description"]' , function( e ) {
				var description = $(this);
				setTimeout( function(){
					rssSocialPreview.rssDescriptionContainer(description.val(), 'tw' );
				}, 1 );
			});

			$(document).on( 'click', '#fbPreview .editable-preview-value-title', function( e ) {
				e.preventDefault();
				$('input[name="_rss_opengraph-title"]').trigger('focus');
			});

			$(document).on( 'click', '#fbPreview .editable-preview-value-description', function( e ) {
				e.preventDefault();
				$('textarea[name="_rss_opengraph-description"]').trigger('focus');
			});

			$(document).on( 'click', '#fbPreview .editable-preview-image', function( e ) {
				e.preventDefault();
				$('input#upload__rss_opengraph-image').trigger('click');
			});

			$(document).on( 'click', '#twPreview .editable-preview-value-title', function( e ) {
				e.preventDefault();
				$('input[name="_rss_twitter-title"]').trigger('focus');
			});

			$(document).on( 'click', '#twPreview .editable-preview-value-description', function( e ) {
				e.preventDefault();
				$('textarea[name="_rss_twitter-description"]').trigger('focus');
			});

			$(document).on( 'click', '#twPreview .editable-preview-image', function( e ) {
				e.preventDefault();
				$('input#upload__rss_twitter-image').trigger('click');
			});

		},

		isFbTooSmallImage : function( img ){
			return img.width < rssSocialPreview.fb_img_small_width || img.height < rssSocialPreview.fb_img_small_height;
		},

		isFbSmallImage : function( img ){
			return img.width < rssSocialPreview.fb_img_large_width || img.height < rssSocialPreview.fb_img_large_height;
		},

		getFbMaxImageWidth : function( img ){
			return isSmallImage( img ) ? rssSocialPreview.fb_img_medium_width : rssSocialPreview.fb_img_medium_height;
		},

		isFbPortrait : function( img ) {
	        return img.height > img.width ? "portrait" : "landscape"
	    },

	    isTwTooSmallImage : function ( img ) {
	    	return false;
	        return img.width < rssSocialPreview.tw_img_small_width || img.height < rssSocialPreview.tw_img_small_width;
	    },

	    isTwSmallImage : function( img ) {
	    	return false;
	        return img.width < rssSocialPreview.tw_img_large_width || img.height < rssSocialPreview.tw_img_large_width;
	    },

	    rssImageContainer : function ( src, type ){
    	
	    	var featureImagesrc;

	    	featureImagesrc = '';

	    	if( $('#postimagediv #set-post-thumbnail img').length > 0 ) {
				//featureImagesrc = $('#postimagediv #set-post-thumbnail img').attr( 'src' );
				featureImagesrc = $( 'input[name="_rss_feature_image_full_url"]' ).val();
			}else{
				featureImagesrc = '';
		    }

	    	if( type == 'fb' ){
	    		if( '' === src ){
	    			if( '' !== featureImagesrc ){
	    				src = featureImagesrc;
	    			}else{
					    src = _rss_social_preview.fb_default_img;
				    }
		    	}
	    	}
	 		
	 		if( type == 'tw' ){

	 			if( '' === src ){
	    			if( '' !== featureImagesrc ){
	    				src = featureImagesrc;
	    			}else{
					    src = _rss_social_preview.tw_default_img;
				    }
		    	}

	    	}

	    	if( '' != src ){
	    		var img = new Image();
			    img.src = src;
			    img.onload = function() {

					if( type == 'fb' ){

						$( '#fbPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );
						$( '#liPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );
						$( '#dcPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );

						if( rssSocialPreview.isFbTooSmallImage(this) ) {
							$( '#fbPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_error );
							$( '#fbPreview .social-preview-inner .social-image-placeholder' ).addClass('social-image-placeholder-error').show();
							$( '#fbPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

							$( '#liPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_error );
							$( '#liPreview .social-preview-inner .social-image-placeholder' ).addClass('social-image-placeholder-error').show();
							$( '#liPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

							$( '#dcPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_error );
							$( '#dcPreview .social-preview-inner .social-image-placeholder' ).addClass('social-image-placeholder-error').show();
							$( '#dcPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

							return 'error';
						}

						if ( rssSocialPreview.isFbSmallImage(this) ){
							if( "portrait" === rssSocialPreview.isFbPortrait(this) ){
								$( '#fbPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#fbPreview .social-preview-inner' ).addClass( 'editable-preview-portrait' );
								$( '#fbPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								$( '#liPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#liPreview .social-preview-inner' ).addClass( 'editable-preview-portrait' );
								$( '#liPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								$( '#dcPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#dcPreview .social-preview-inner' ).addClass( 'editable-preview-portrait' );
								$( '#dcPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								return 'portrait';
							}else{
								$( '#fbPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#fbPreview .social-preview-inner' ).addClass( 'editable-preview-small' );
								$( '#fbPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								$( '#liPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#liPreview .social-preview-inner' ).addClass( 'editable-preview-small' );
								$( '#liPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								$( '#dcPreview .social-preview-inner .social-image-placeholder' ).hide();
								$( '#dcPreview .social-preview-inner' ).addClass( 'editable-preview-small' );
								$( '#dcPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

								return 'small';
							}
						}else{
							$( '#fbPreview .social-preview-inner .social-image-placeholder' ).hide();
							$( '#fbPreview .social-preview-inner' ).addClass( 'editable-preview-large' );
							$( '#fbPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

							$( '#liPreview .social-preview-inner .social-image-placeholder' ).hide();
							$( '#liPreview .social-preview-inner' ).addClass( 'editable-preview-large' );
							$( '#liPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

							$( '#dcPreview .social-preview-inner .social-image-placeholder' ).hide();
							$( '#dcPreview .social-preview-inner' ).addClass( 'editable-preview-large' );
							$( '#dcPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });

							return 'large';
						}
						
			       	}

			       	if( type == 'tw' ){

			       		$( '#twPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small' );
						
						if( rssSocialPreview.isTwTooSmallImage(this) ) {
							$( '#twPreview .social-preview-inner .social-image-placeholder' ).show();
							$( '#twPreview .social-preview-inner' ).addClass( 'editable-preview-small' );
							$( '#twPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });
							return 'error';
						}

						if ( rssSocialPreview.isTwSmallImage(this) ){
							$( '#twPreview .social-preview-inner .social-image-placeholder' ).hide();
							$( '#twPreview .social-preview-inner' ).addClass( 'editable-preview-small' );
							$( '#twPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )','background-size' : 'cover','background-position' : 'center' });
							return 'small';
						}else{
							$( '#twPreview .social-preview-inner .social-image-placeholder' ).hide();
							$( '#twPreview .social-preview-inner' ).addClass( 'editable-preview-large' );
							$( '#twPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : 'url( '+src+' )' });
							return 'large';
						}
			       	}
			    }

			    return 'large';

	    	}else{

	    		if( type == 'fb' ){
	    			$( '#fbPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );
					$( '#fbPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_msg );
					$( '#fbPreview .social-preview-inner .social-image-placeholder' ).removeClass('social-image-placeholder-error').show();
					$( '#fbPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

					$( '#liPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );
					$( '#liPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_msg );
					$( '#liPreview .social-preview-inner .social-image-placeholder' ).removeClass('social-image-placeholder-error').show();
					$( '#liPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

					$( '#dcPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-small editable-preview-portrait' );
					$( '#dcPreview .social-preview-inner .social-image-placeholder' ).html( _rss_social_preview.fb_placeholder_msg );
					$( '#dcPreview .social-preview-inner .social-image-placeholder' ).removeClass('social-image-placeholder-error').show();
					$( '#dcPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });

	    			return 'error';
	    		}

	    		if( type == 'tw' ){
	    			$( '#twPreview .social-preview-inner' ).removeClass( 'editable-preview-large editable-preview-large' );
	    			$( '#twPreview .social-preview-inner .social-image-placeholder' ).show();
	    			$( '#twPreview .social-preview-inner' ).addClass( 'editable-preview-large' );
					$( '#twPreview .social-preview-inner .editable-preview-image' ).css({ 'background-image' : '' });
	    			return 'error';
	    		}

	    		return 'error';
	    	}
	    },

	    rssTitleContainer : function( title, type ){

	    	var default_title = 'Custom share title';

	    	if( $( 'input[name="post_title"]' ).length > 0 ){
	    		default_title = $( 'input[name="post_title"]' ).val();
	    	}

	    	if( $( '#edittag input[name="name"]' ).length > 0 ){
	    		default_title = $( '#edittag input[name="name"]' ).val();
	    	}

	    	if( '' === title ){
	    		title = default_title;
	    	}

	    	if( type == 'fb' ){
	    		$('#fbPreview .social-preview-inner .editable-preview-value-title').html( title );
	    		$('#liPreview .social-preview-inner .editable-preview-value-title').html( title );
	    		$('#dcPreview .social-preview-inner .editable-preview-value-title').html( title );
	    	}

	    	if( type == 'tw' ){
	    		$('#twPreview .social-preview-inner .editable-preview-value-title').html( title );
	    	}

	    },

	    rssDescriptionContainer : function( desc, type ){

	    	if( type == 'fb' ){
	    		if( '' === desc ){
		    		desc = 'This is the share description text.';
		    	}
	    		$('#fbPreview .social-preview-inner .editable-preview-value-description').html( desc );
	    		$('#liPreview .social-preview-inner .editable-preview-value-description').html( desc );
	    		$('#dcPreview .social-preview-inner .editable-preview-value-description').html( desc );
	    	}

	    	if( type == 'tw' ){
	    		if( '' === desc ){
		    		desc = _rss_social_preview.tw_default_desc;
		    	}
	    		$('#twPreview .social-preview-inner .editable-preview-value-description').html( desc );
	    	}

	    },

	    changeUploadButton : function(){

	    	if( $('input[name="_rss_opengraph-image"]').val() != '' ){
	    		$( '._rss_opengraph-remove-image' ).removeClass('hidden');
	    		$( '#upload__rss_opengraph-image' ).val( _rss_social_preview.upload_button_other_text );
	    	}else{
	    		$( '._rss_opengraph-remove-image' ).addClass('hidden');
	    		$( '#upload__rss_opengraph-image' ).val( _rss_social_preview.upload_button_text );
	    	}

	    	if( $('input[name="_rss_twitter-image"]').val() != '' ){
	    		$( '._rss_twitter-remove-image' ).removeClass('hidden');
	    		$( '#upload__rss_twitter-image' ).val( _rss_social_preview.upload_button_other_text );
	    	}else{
	    		$( '._rss_twitter-remove-image' ).addClass('hidden');
	    		$( '#upload__rss_twitter-image' ).val( _rss_social_preview.upload_button_text );
	    	}
	    }
	}

	rssSocialPreview.init();

})(jQuery);
