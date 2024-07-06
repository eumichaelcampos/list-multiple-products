jQuery(document).ready(function($) {
    // Handle quantity change to update total
    $('.lmp-quantity').on('input', function() {
        var $row = $(this).closest('tr');
        var price = parseFloat($row.find('.lmp-product-total').data('product-price'));
        var quantity = parseInt($(this).val());
        var total = price * quantity;
        $row.find('.lmp-product-total').text(total.toFixed(2));
        updateCartTotal();
    });

    // Update cart total
    function updateCartTotal() {
        var cartTotal = 0;
        $('.lmp-product-total').each(function() {
            cartTotal += parseFloat($(this).text());
        });
        $('.lmp-cart-total').text(cartTotal.toFixed(2));
    }

    // Handle Add to Cart for individual products
    $('.lmp-add-to-cart').on('click', function(e) {
        e.preventDefault();
        var product_id = $(this).data('product-id');
        var quantity = $(this).closest('tr').find('.lmp-quantity').val();

        addToCart(product_id, quantity);
    });

    // Handle Add All to Cart
    $('#lmp-add-all-to-cart').on('click', function(e) {
        e.preventDefault();
        var products = [];

        $('.list-multiple-products-table tbody tr').each(function() {
            var product_id = $(this).find('.lmp-add-to-cart').data('product-id');
            var quantity = $(this).find('.lmp-quantity').val();

            if (quantity > 0) {
                products.push({ product_id: product_id, quantity: quantity });
            }
        });

        if (products.length > 0) {
            addMultipleToCart(products);
        }
    });

    // Handle Reset individual product quantity
    $('.lmp-reset-quantity').on('click', function(e) {
        e.preventDefault();
        $(this).closest('tr').find('.lmp-quantity').val(0).trigger('input');
    });

    // Handle Reset all quantities
    $('#lmp-reset-all-quantities').on('click', function(e) {
        e.preventDefault();
        $('.lmp-quantity').val(0).trigger('input');
    });

    // Function to add product to cart
    function addToCart(product_id, quantity) {
        $.ajax({
            url: lmp_params.ajax_url,
            type: 'POST',
            data: {
                action: 'lmp_add_to_cart',
                product_id: product_id,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    // Atualiza o ícone do carrinho WooCommerce
                    $(document.body).trigger('wc_fragment_refresh');
                    showNotification(response.message, [response.image]);
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Function to add multiple products to cart
    function addMultipleToCart(products) {
        var requests = [];
        var productImages = [];

        $.each(products, function(index, product) {
            requests.push($.ajax({
                url: lmp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'lmp_add_to_cart',
                    product_id: product.product_id,
                    quantity: product.quantity
                },
                success: function(response) {
                    if (response.success) {
                        productImages.push(response.image);
                    }
                }
            }));
        });

        $.when.apply($, requests).done(function() {
            // Atualiza o ícone do carrinho WooCommerce
            $(document.body).trigger('wc_fragment_refresh');
            showNotification(lmp_params.locale == 'en_US' ? 'Products added to cart.' : 'Produtos adicionados ao carrinho.', productImages);
        }).fail(function() {
            alert(lmp_params.locale == 'en_US' ? 'Failed to add some products to cart.' : 'Falha ao adicionar alguns produtos ao carrinho.');
        });
    }

    // Function to show notification
    function showNotification(message, productImages) {
        var $notification = $('#lmp-notification');
        var $imagesContainer = $notification.find('.lmp-notification-images');

        $imagesContainer.empty();

        $.each(productImages, function(index, image) {
            $imagesContainer.append(image);
        });

        $notification.find('.lmp-notification-message').text(message);
        $notification.fadeIn(300).delay(3000).fadeOut(300);
    }
});
