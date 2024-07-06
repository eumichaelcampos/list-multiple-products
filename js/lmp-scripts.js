jQuery(document).ready(function($) {
    $('.lmp-add-to-cart').on('click', function() {
        var productId = $(this).data('product-id');

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: productId
            },
            success: function(response) {
                alert('Product added to cart!');
            }
        });
    });
});
