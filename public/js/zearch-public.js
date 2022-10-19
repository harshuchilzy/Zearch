jQuery(document).ready(function ($) {
  $(".dayz-search-sidebar").accordion();
  var query = "";
  var from = 0;
  var size = 8;
  var taxoTerms = [];
  var loading = true;
  $('input[name="s"]').keyup(function (e) {
    e.preventDefault();
    query = this.value;
    $("#zearch_results").html("");
    from = 0;
    eZearch();
  });

  function eZearch() {
    if (query.length > 3) {
      // $("#zearch_results").html("");
      console.log("Searching " + query + ", from " + from + " Size " + size);
      $.ajax({
        type: "post",
        url: DayzAjax.dayz_ajaxurl,
        data: { action: "query_ezearch", search_values: query, from: from, size: size, taxoTerms: taxoTerms },
        success: function (response) {
          // console.log(response)
          var zearchTemplate = wp.template("ezearch-template");
          var hits = JSON.parse(response);

          const hit_results = hits;

          hit_results.forEach(function (hit_result) {
            console.log(hit_result);

            $("#zearch_results").append(
              zearchTemplate({
                post_title: hit_result._source.post_title,
                thumbnail: hit_result._source.thumbnail.src,
                description: hit_result._source.post_excerpt.substring(0, 75),
                price_html: hit_result._source.price_html,
                product_id: hit_result._source.post_id,
                sku: hit_result._source.meta._sku[0].value,
                permalink: hit_result._source.permalink,
              })
            );
          });
          $("#zearch_result_count").text($(".result-item").length);

          $(".zearch-wrapper").dialog({
            modal: true,
            open: function(){
              // $('.ui-widget-content').not('.dayz-products-dropdown').bind('click',function(){
              //     $('.zearch-wrapper').dialog('close');
              // });
          }
          });
          from = from + size;
          loading = false;
          return true;
        },
      });
      return false;
    } else {
      console.log("type more than 3 letters");
      return false;
    }
  }

  // Price Range Selector
  $("#ezearch_price_ranger").on("change", function () {
    search_values = $(this).val();
    console.log(search_values);
    $.ajax({
      type: "post",
      url: DayzAjax.dayz_ajaxurl,
      data: { action: "query_ezearch_by", search_values: search_values, from: from, size: size, taxoTerms: taxoTerms },
      success: function (response) {
        $("#zearch_results").html("");
        var zearchTemplate = wp.template("ezearch-template");
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
              sku: hit_result._source.meta._sku[0].value,
            })
          );
        });
      },
    });
  });

  //filter products cats
  // jQuery(".dayz_product_cat").click(function () {
  //   alert(jQuery(this).val());
  // });

  var target = '#zearch_results';
  $('.dayz-search-result').on("scroll", function () {
    var mayLoadContent = (Math.ceil($('.dayz-search-result').scrollTop()) + 100) >= ($(target).height() - $('.dayz-search-result').height());
    if(mayLoadContent){
      if ($('.dayz-search-result input[name="s"]').val().length > 3) {
        query = $('.dayz-search-result input[name="s"]').val();
      }
      if(loading == false){
        console.log("Loading more");
        loading = true;
        eZearch();
      }
    }
  });

  $(".ezearch_terms_filter").on("change", function () {
    $(".ezearch_terms_filter:checked").each(function (i, ele) {
      let name = $(ele).attr("name");
      let val = $(ele).val();
      taxoTerms.push({ name: name, value: val });
    });
    $("#zearch_results").html("");

    from = 0;
    eZearch();
  });

  // $('.dayz-search-result').scrollPagination({
  //   url: DayzAjax.dayz_ajaxurl,
  //   data: { action: "query_ezearch", search_values: search_values, from: from, size: size, taxoTerms: taxoTerms },
  //   // 'data': {
  //   //   'page': 1, // which entry to load on init
  //   //   'size': 10, // number of pages
  //   // },
  //   'scroller': $(".dayz-search-result"),
  //   'heightOffset': 50,
  //   'loading':'#loading',
  //   'loadingText':'Wait a moment... it\'s loading!',
  // });
});