(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

 $( window ).load(function() {
	 //$('button.components-button[aria-label="Save draft"]').addClass("editor-post-save-draft is-tertiary");
 });


	$( document ).on("click", "#wpcgai_load_draft_settings", function(event) {
		event.preventDefault();
		var wpai_preview_title = $("#wpai_preview_title").val();
		$(".editor-post-title").text(wpai_preview_title); 
		$("input#title").val(wpai_preview_title);  		
		
			jQuery('.editor-post-title').focus();
			
			setTimeout(function(){ 
				$("input#save-post").click();  
				$(".editor-post-save-draft").click(); 
				setTimeout(function(){
					if($('#editor').hasClass('block-editor__container')){
					   //location.reload(true); 
					   var post_id___ = $('#post_ID').val();
						var con__ = $("#wpcgai_preview_box").val()
						var data__ = {
							'action' : 'wpaicg_set_post_content_',
							'content':con__,
							'post_id':post_id___
						}
						$.post(ajaxurl, data__, function(response__) {

							location.reload(true);

						});
					}
					
				}, 1000); 
			}, 500);  
  
	});

})( jQuery );
