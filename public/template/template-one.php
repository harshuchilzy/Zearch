<!-- <label for="my-modal-3" class="btn modal-button">open modal</label> -->

<!-- Put this part before </body> tag -->

<?php
$settings = get_option('ezearch');
?>

<section class="container mx-auto dayz-products-dropdown p-5 zearch-wrapper overflow-y-auto" style="display: znone;">
    <div class="">
        <div class="bg-white shadow-lg rounded-lg ">
            <div class="flex gap-4 p-5 ">
                <div class="dayz-search-sidebar w-1/6 p-5">

                    <div class="text-2xl ezearch-sidebar-title font-medium mb-4">
                        Price
                    </div>
                    <div class="mb-6">
                        <?php $price_range = ezearch_get_price_range(); ?>
                        <input id="ezearch_price_ranger" type="range" min="<?php echo $price_range['min'] ?>" max="<?php echo $price_range['max'] ?>" value="" class="range" step="1" />
                        <div class="w-full flex justify-between text-xs px-2">
                            <span><?php echo wc_price($price_range['min']) ?></span>
                            <span><?php echo wc_price($price_range['max']) ?></span>
                        </div>
                    </div>

                    <?php
                    $post_type = 'product';
                    $taxonomies = get_object_taxonomies($post_type, 'objects');

                    foreach ($taxonomies as $taxonomy) { 
                        if(!isset($settings['weighting'][$post_type]['terms.'.$taxonomy->name.'.name']['enabled'])){
                            continue;
                        }
                        ?>
                        <div class="text-2xl ezearch-sidebar-title font-medium mb-4">
                            <?php echo $taxonomy->label; ?>
                        </div>
                        <div class="mb-6">
                            <?php
                            $args = array(
                                'taxonomy' => $taxonomy->name,
                                'hide_empty' => true,
                                'parent'   => 0
                            );
                            $parent_terms = get_terms($args); ?>
                            <ul class="list-none mx-auto">

                                <?php foreach ($parent_terms as $parent_term) {

                                    echo '<li><div class="form-control">
                                    <label class="label cursor-pointer justify-start" for="ezearch_' . $taxonomy->name . '_' . $parent_term->slug . '">
                                    <input type="checkbox" name="terms.' . $taxonomy->name . '.name" class="ezearch_terms_filter checkbox mr-2" id="ezearch_' . $taxonomy->name . '_' . $parent_term->slug . '" value="' . $parent_term->name . '"/>
                                    <span class="label-text text-black">' . $parent_term->name . '</span></label>';
                                    $child_args = array(
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => false,
                                        'parent'   => $parent_term->term_id
                                    );
                                    $child_taxo_terms = get_terms($child_args);
                                    if ($child_taxo_terms) {
                                        echo '<ul class="list-none ml-3">';
                                        foreach ($child_taxo_terms as $child_term) {
                                            echo '<li><div class="form-control">
                                            <label class="label cursor-pointer justify-start" for="ezearch_' . $taxonomy->name . '_' . $child_term->slug . '">
                                            <input type="checkbox" name="terms.' . $taxonomy->name . '.name" class="ezearch_terms_filter checkbox mr-2" id="ezearch_' . $taxonomy->name . '_' . $child_term->slug . '" value="' . $child_term->name . '"/>
                                            <span class="label-text text-black">' . $child_term->name . '</span></label>
                                        </div></li>';
                                        }
                                        echo '</ul>';
                                    }

                                    echo '</div></li>';
                                } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
                <div class="dayz-search-result w-5/6 p-5 overflow-auto">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div id="ezearch-form" class="p-4 col-span-2">
                            <input type="text" placeholder="Search here" name="s" class="input input-bordered w-full" />
                        </div>
                        <div class="count p-4 text-right">
                            <b class="mt-5 text-right">Results: <span id="zearch_result_count">0</span></b>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-4 h-auto" id="zearch_results"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/html" id="tmpl-ezearch-template">
    <div>
        <div class="bg-black shadow-md m-3 rounded-md result-item">
            <div class="image-area flex justify-center bg-white">
                <img src="{{ data.thumbnail }}" alt="" class="w-auto">
            </div>
            <div class="details-area bg-black text-white p-4">
                <a href="{{ data.permalink }}" class="font-bold text-[#f99e41]">{{ data.post_title }}</a>
                <p class="m-0">{{{ data.description }}}</p>
            </div>
            <div class="add-to-cart grid grid-cols-3 gap-4 items-center p-4">
                <p class="text-white font-bold m-0">{{{ data.price_html}}}</p>
                <a href="?add-to-cart={{ data.product_id }}" class="btn col-span-2 bg-[#f99e41] text-white text-lg product_type_simple add_to_cart_button ajax_add_to_cart" data-quantity="1" data-product_sku="{{data.sku}}" data-product_id="{{ data.product_id }}">Add to Cart</a>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-ezearch-loader">
    <div class="loaders">
        <div class="bg-black shadow-md m-3 rounded-md result-item animate-pulse">
            <div class="image-area flex justify-center h-40">
                <div class="w-full rounded h-full bg-slate-700"></div>
            </div>
            <div class="details-area bg-black text-white p-4">
                <div class="bg-gray-200 h-6 rounded dark:bg-gray-600 w-full mb-2"></div>
                <div class="bg-gray-300 h-12 rounded dark:bg-gray-700 w-full"></div>
            </div>
            <div class="add-to-cart grid grid-cols-3 gap-4 items-center p-4">
                <div class=" bg-gray-200 rounded h-6 dark:bg-gray-700 w-auto"></div>
                <div class="bg-gray-300 rounded h-12 dark:bg-gray-600 col-span-2"></div>
            </div>
        </div>
    </div>
</script>