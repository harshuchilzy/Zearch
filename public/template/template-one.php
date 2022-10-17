<?php 
echo '<pre>';
// print_r(get_option('ezearch')['weighting']);
echo '</pre>';
$searchables = '';
foreach(get_option('ezearch')['weighting'] as $post_type => $data){
    foreach($data as $field => $settings){
        if($settings['enabled'] == 'on'){
            $searchables .= '"' . $field . '^' . $settings['weight'] . '",';
        }
    }
}

echo $searchables;

?>
<section class="dayz-products-dropdown p-5 zearch-wrapper" style="display: none">

    <div class="container bg-white shadow-lg rounded-lg mx-auto">
        <div class="flex gap-4 p-5">
            <div class="dayz-search-sidebar w-1/6 p-5">
                <div tabindex="0" class="collapse collapse-open collapse-arrow  rounded-box">
                    <div class="collapse-title text-2xl font-medium">
                      Price
                    </div>
                    <div class="collapse-content">
                    <?php $price_range = ezearch_get_price_range(); ?>
                        <input id="ezearch_price_ranger" type="range" min="<?php $price_range['min'] ?>" max="<?php $price_range['max'] ?>" value="" class="range" step="1" />
                    </div>
                </div>
                <div tabindex="0" class="collapse collapse-arrow  rounded-box">
                    <div class="collapse-title text-2xl font-medium">
                        Category
                    </div>
                    <div class="collapse-content"> 
                       <?php  
                        echo do_shortcode( '[dayz_product_cats]' );
                       ?>
                    </div>
                </div>
                <div tabindex="0" class="collapse collapse-arrow  rounded-box">
                    <div class="collapse-title text-2xl font-medium">
                        Brand
                    </div>
                    <div class="collapse-content"> 
                       <?php  
                        echo do_shortcode( '[dayz_product_brands]' );
                       ?>
                    </div>
                </div>
                <div tabindex="0" class="collapse collapse-arrow  rounded-box">
                    <div class="collapse-title text-2xl font-medium">
                        Size
                    </div>
                    <div class="collapse-content"> 
                        <p>tabindex="0" attribute is necessary to make the div focusable</p>
                    </div>
                </div>
            </div>
            <div class="dayz-search-result w-5/6 p-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="count p-4">
                        <b class="mt-5">Results: <span id="zearch_result_count">0</span></b>
                    </div>
                    <div class="sort p-4">
                        <select class="select select-bordered w-full max-w-xs bg-white float-right">
                            <option disabled selected>Sort by</option>
                            <option>Han Solo</option>
                            <option>Greedo</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4" id="zearch_results"></div>
            </div>
        </div>
    </div>
</section>
<script type="text/html" id="tmpl-ezearch-template">
    <div class="bg-black shadow-md m-3 rounded-md">
        <div class="image-area flex justify-center">
            <img src="{{ data.thumbnail }}" alt="" width="100%">
        </div>
        <div class="details-area bg-black text-white p-4">
            <p class="font-bold text-[#f99e41]">{{ data.post_title }}</p>
            <p class="m-0">{{ data.description }}</p>
        </div>
        <div class="add-to-cart grid grid-cols-3 gap-4 items-center p-4">
            <p class="text-white font-bold m-0">{{{ data.price_html}}}</p>
            <a class="btn col-span-2 bg-[#f99e41] text-white text-lg add_to_cart_button ajax_add_to_cart" data-quantity="1" data-pproduct="{{ data.product_id }}">Add to Cart</a>
        </div>
    </div>
</script>