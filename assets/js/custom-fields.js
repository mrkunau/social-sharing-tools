/**
 * Custom Fields JavaScript
 *
 * All JavaScript logic for fields in the post meta box.
 * @since 4.8.0
 *
 */

(function($) {

	sstoolsCustomFields = {
		setup_field_tabber: function() {
			$('.sstools-metabox-tabs').tabs();
		}, // End setup_field_tabber()


	}; // End sstoolsCustomFields Object // Don't remove this, or the sky will fall on your head.

	/**
	 * Execute the above methods in the sstoolsCustomFields object.
	 *
	 * @since 4.8.0
	 */
	$(document).ready(function() {
		sstoolsCustomFields.setup_field_tabber();
	});

})(jQuery);