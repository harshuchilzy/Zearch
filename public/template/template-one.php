<section class="dayz-products-dropdown p-5">
    <div class="container bg-white shadow-lg rounded-lg">
        <div class="flex gap-4 p-5">
            <div class="dayz-search-sidebar w-1/6 p-5">
                <div tabindex="0" class="collapse collapse-open collapse-arrow  rounded-box">
                    <div class="collapse-title text-2xl font-medium">
                      Price
                    </div>
                    <div class="collapse-content"> 
                    <input type="range" min="0" max="100" value="25" class="range" step="25" />
                        <div class="w-full flex justify-between text-xs px-2">
                            <span>$45</span>
                            <span>$50</span>
                            <span>$55</span>
                            <span>$65</span>
                            <span>$75</span>
                        </div>
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
                <div class="grid grid-cols-2 gap-4 flex">
                    <div class="count p-4">
                        <b class="mt-5">Results:04</b>
                    </div>
                    <div class="sort p-4">
                        <select class="select select-bordered w-full max-w-xs bg-white float-right">
                            <option disabled selected>Sort by</option>
                            <option>Han Solo</option>
                            <option>Greedo</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4 flex" id="dayz_elastic_results">
                    <!-- <div class="bg-black shadow-md m-3 rounded-md">
                        <div class="image-area flex justify-center">
                            <img src="https://itslondon.s3.amazonaws.com/p/xxl/EIN4513501.jpg" alt="" width="100%">
                        </div>
                        <div class="details-area bg-black text-white p-4">
                            <p class="font-bold text-[#f99e41]">Dewalt DCF622N 18V</p>
                            <p class="m-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae repellendus amet opti</p>
                            
                        </div>
                        <div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4">
                            <p class="text-white font-bold m-0">$13.00</p>
                            <a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a>
                        </div>
                    </div>
                    <div class="bg-black shadow-md m-3 rounded-md">
                        <div class="image-area flex justify-center">
                            <img src="https://itslondon.s3.amazonaws.com/p/xxl/EIN4513501.jpg" alt="" width="100%">
                        </div>
                        <div class="details-area bg-black text-white p-4">
                            <p class="font-bold text-[#f99e41]">Dewalt DCF622N 18V</p>
                            <p class="m-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae repellendus amet opti</p>
                            
                        </div>
                        <div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4">
                            <p class="text-white font-bold m-0">$13.00</p>
                            <a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a>
                        </div>
                    </div>
                    <div class="bg-black shadow-md m-3 rounded-md">
                        <div class="image-area flex justify-center">
                            <img src="https://itslondon.s3.amazonaws.com/p/xxl/EIN4513501.jpg" alt="" width="100%">
                        </div>
                        <div class="details-area bg-black text-white p-4">
                            <p class="font-bold text-[#f99e41]">Dewalt DCF622N 18V</p>
                            <p class="m-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae repellendus amet opti</p>
                            
                        </div>
                        <div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4">
                            <p class="text-white font-bold m-0">$13.00</p>
                            <a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a>
                        </div>
                    </div>
                    <div class="bg-black shadow-md m-3 rounded-md">
                        <div class="image-area flex justify-center">
                            <img src="https://itslondon.s3.amazonaws.com/p/xxl/EIN4513501.jpg" alt="" width="100%">
                        </div>
                        <div class="details-area bg-black text-white p-4">
                            <p class="font-bold text-[#f99e41]">Dewalt DCF622N 18V</p>
                            <p class="m-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae repellendus amet opti</p>
                            
                        </div>
                        <div class="add-to-cart grid grid-cols-2 gap-4 flex items-end p-4">
                            <p class="text-white font-bold m-0">$13.00</p>
                            <a class="btn bg-[#f99e41] text-white text-lg">Add to Cart</a>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>