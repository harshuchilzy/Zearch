jQuery(document).ready(function ($) {
  $('input[name="s"]').keyup(function (e) {
    e.preventDefault();
    $(".zearch-wrapper").hide();
    search_values = $(this).val();
    if (this.value.length > 3) {
      $("#zearch_results").html("");
      $.ajax({
        type: "post",
        url: DayzAjax.dayz_ajaxurl,
        data: { action: "query_zearch", search_values: search_values },
        success: function (response) {
          var zearchTemplate = wp.template("ezearch-template");
          console.log(zearchTemplate);
          $(".zearch-wrapper").show();
          var hits = JSON.parse(response);

          const hit_results = hits;
          $("#zearch_result_count").text(hit_results.length);
          hit_results.forEach(function (hit_result) {
            console.log(hit_result);

            $("#zearch_results").append(
              zearchTemplate({
                post_title: hit_result._source.post_title,
                thumbnail: hit_result._source.thumbnail.src,
                description: hit_result._source.post_excerpt.substring(0, 75),
                price_html: hit_result._source.price_html,
                product_id: hit_result._source.post_id,
              })
            );
          });
        },
      });
    } else {
      console.log("type more than 3 letters");
    }
  });

  //filter products cats
  jQuery(".dayz_product_cat").click(function () {
    alert(jQuery(this).val());
  });
});
