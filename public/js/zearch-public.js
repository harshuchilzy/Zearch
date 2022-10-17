jQuery(document).ready(function ($) {
  $('input[name="s"]').keyup(function (e) {
    e.preventDefault();
    search_values = $(this).val();
    if (this.value.length > 3) {
      $("#zearch_results").html("");
      $.ajax({
        type: "post",
        url: DayzAjax.dayz_ajaxurl,
        data: { action: "query_ezearch", search_values: search_values },
        success: function (response) {
          var zearchTemplate = wp.template("ezearch-template");
        //   $(".zearch-wrapper").show();
          var hits = JSON.parse(response);

          const hit_results = hits;
          $("#zearch_result_count").text(hit_results.length);
          hit_results.forEach(function (hit_result) {
			$('#ezearch-model-toggle').prop('checked',true);
            console.log(hit_result);

            $("#zearch_results").append(
              zearchTemplate({
                post_title: hit_result._source.post_title,
                thumbnail: hit_result._source.thumbnail.src,
                description: hit_result._source.post_excerpt.substring(0, 75),
                price_html: hit_result._source.price_html,
                product_id: hit_result._source.post_id,
				sku: hit_result._source.meta._sku[0].value
              })
            );
          });
        },
      });
    } else {
      console.log("type more than 3 letters");
    }
  });

  $('#ezearch_price_ranger').on('change', function(){
    search_values = $(this).val();
console.log(search_values);
	$.ajax({
        type: "post",
        url: DayzAjax.dayz_ajaxurl,
        data: { action: "query_ezearch_by", search_values: search_values },
		success: function (response) {
			$("#zearch_results").html('');
			var zearchTemplate = wp.template("ezearch-template");
          var hits = JSON.parse(response);

          const hit_results = hits;
          $("#zearch_result_count").text(hit_results.length);
          hit_results.forEach(function (hit_result) {
			$('#ezearch-model-toggle').prop('checked',true);
            console.log(hit_result);

            $("#zearch_results").append(
              zearchTemplate({
                post_title: hit_result._source.post_title,
                thumbnail: hit_result._source.thumbnail.src,
                description: hit_result._source.post_excerpt.substring(0, 75),
                price_html: hit_result._source.price_html,
                product_id: hit_result._source.post_id,
				sku: hit_result._source.meta._sku[0].value
              })
            );
          });
		}
	});
  });

  //filter products cats
  jQuery(".dayz_product_cat").click(function () {
    alert(jQuery(this).val());
  });
});
