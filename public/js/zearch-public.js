(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	 
})( jQuery );

jQuery( document ).ready(function() {

    jQuery("#search-form-2").keyup( function(e) {
		e.preventDefault();
		search_values = jQuery(this).val();
		if (this.value.length > 3) {
			jQuery.ajax({
				type : "post",
				url : DayzAjax.dayz_ajaxurl,
				data : {action: "query_elasticsearch", search_values : search_values},
				success: function(response) {
					var hits = JSON.parse(response);
					const hit_results = hits.hits.hits;
					var html= '';
				    hit_results.forEach(function(hit_result) {
						console.log(hit_result._source);
						var product = hit_result._source;
						html += '<div class="bg-black shadow-md m-3 rounded-md"><div class="image-area flex justify-center"><img src="'+ product.thumbnail.src +'" alt="" width="100%"></div><div class="details-area bg-black text-white p-4"><p class="font-bold text-[#f99e41]">'+ product.post_title +'</p></div><div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4"><p class="text-white font-bold m-0">'+ product.price_html +'</p><a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a></div></div>';
						// document.getElementById(html).innerHTML += '<div class="bg-black shadow-md m-3 rounded-md"><div class="image-area flex justify-center"><img src="'+ product.thumbnail.src +'" alt="" width="100%"></div><div class="details-area bg-black text-white p-4"><p class="font-bold text-[#f99e41]">'+ product.post_title +'</p></div><div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4"><p class="text-white font-bold m-0">'+ product.price_html +'</p><a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a></div></div>';
					});
					jQuery('#dayz_elastic_results').append(html);
				}
			 });
		} else {
			console.log('type more than 3 letters');
		}
		
	 });
}); 