<?php
if ( ! class_exists( 'LMP_Shortcode' ) ) {
    class LMP_Shortcode {
        public static function init() {
            add_shortcode( 'list-product', [ __CLASS__, 'render_shortcode' ] );
            add_action( 'wp_ajax_lmp_add_to_cart', [ __CLASS__, 'add_to_cart' ] );
            add_action( 'wp_ajax_nopriv_lmp_add_to_cart', [ __CLASS__, 'add_to_cart' ] );
        }

        public static function render_shortcode( $atts ) {
            $atts = shortcode_atts( [
                'products-id' => '',
                'id'          => '',
                'header'      => 'true',
                'options'     => 'Image,Product,Categories,Variations,Price,Quantity,Total,Add to Cart,Reset',
            ], $atts, 'list-product' );

            $list_id = $atts['id'];
            $header = filter_var($atts['header'], FILTER_VALIDATE_BOOLEAN);
            $options = explode(',', $atts['options']);

            if ( $list_id ) {
                $lists = get_option( 'lmp_lists', [] );
                $list = isset( $lists[ $list_id ] ) ? $lists[ $list_id ] : null;

                if ( $list ) {
                    $products = $list['products'];
                    $categories = $list['categories'];
                    $limit = $list['limit'];
                    $pagination = $list['pagination'];
                    $orderby = $list['orderby'];
                    $order = $list['order'];
                    $description_visibility = $list['description_visibility'];
                    $link_visibility = $list['link_visibility'];
                    $product_type = $list['product_type'];

                    return self::render_product_table( $products, $categories, $limit, $pagination, $orderby, $order, $description_visibility, $link_visibility, $product_type, $header, $options );
                } else {
                    return '<p>' . __( 'List not found.', 'list-multiple-products' ) . '</p>';
                }
            } else {
                $products = array_map( 'intval', explode( ',', $atts['products-id'] ) );

                return self::render_product_table( $products, [], 10, 'show', 'date', 'desc', 'hide', 'show', 'all', $header, $options );
            }
        }

        public static function render_product_table( $products = [], $categories = [], $limit = 10, $pagination = 'show', $orderby = 'date', $order = 'desc', $description_visibility = 'hide', $link_visibility = 'show', $product_type = 'all', $header = true, $options = [] ) {
            // Query para obter produtos com base nos parâmetros fornecidos
            $args = [
                'post_type'      => 'product',
                'posts_per_page' => $pagination === 'show' ? $limit : -1,
                'orderby'        => $orderby,
                'order'          => $order,
                'post__in'       => $products,
                'tax_query'      => [],
            ];

            if ( ! empty( $categories ) ) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $categories,
                ];
            }

            if ( $product_type !== 'all' ) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => $product_type,
                ];
            }

            $query = new WP_Query( $args );

            ob_start();
            ?>
            <table class="list-multiple-products-table">
                <?php if ( $header ) : ?>
                <thead>
                    <tr>
                        <?php if ( in_array( 'Image', $options ) ) : ?><th><?php _e( 'Image', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Product', $options ) ) : ?><th><?php _e( 'Product', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Categories', $options ) ) : ?><th><?php _e( 'Categories', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Variations', $options ) ) : ?><th><?php _e( 'Variations', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Price', $options ) ) : ?><th><?php _e( 'Price', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Quantity', $options ) ) : ?><th><?php _e( 'Quantity', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Total', $options ) ) : ?><th><?php _e( 'Total', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Add to Cart', $options ) ) : ?><th><?php _e( 'Add to Cart', 'list-multiple-products' ); ?></th><?php endif; ?>
                        <?php if ( in_array( 'Reset', $options ) ) : ?><th><?php _e( 'Reset', 'list-multiple-products' ); ?></th><?php endif; ?>
                    </tr>
                </thead>
                <?php endif; ?>
                <tbody>
                    <?php
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            global $product;

                            echo '<tr>';
                            if ( in_array( 'Image', $options ) ) { echo '<td class="imagem">' . $product->get_image() . '</td>'; }
                            if ( in_array( 'Product', $options ) ) {
                                echo '<td class="produto">';
                                if ( $link_visibility === 'show' ) {
                                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                                } else {
                                    echo get_the_title();
                                }
                                if ( $description_visibility === 'show' ) {
                                    echo '<div class="product-description">' . get_the_excerpt() . '</div>';
                                }
                                echo '</td>';
                            }
                            if ( in_array( 'Categories', $options ) ) { echo '<td class="categoria">' . wc_get_product_category_list( $product->get_id() ) . '</td>'; }
                            if ( in_array( 'Variations', $options ) ) {
                                echo '<td class="variacao">';
                                if ( $product->is_type( 'variable' ) ) {
                                    $attributes = $product->get_attributes();
                                    foreach ( $attributes as $attribute ) {
                                        echo '<span>' . wc_attribute_label( $attribute->get_name() ) . ': ' . implode( ', ', $attribute->get_options() ) . '</span><br>';
                                    }
                                }
                                echo '</td>';
                            }
                            if ( in_array( 'Price', $options ) ) { echo '<td class="preco">' . $product->get_price_html() . '</td>'; }
                            if ( in_array( 'Quantity', $options ) ) { echo '<td class="quantidade add-carrinho"><input type="number" class="lmp-quantity" name="quantity[' . $product->get_id() . ']" value="0" min="0" max="' . $product->get_stock_quantity() . '">'; }
                            
                            if ( in_array( 'Add to Cart', $options ) ) { echo '<button class="button lmp-add-to-cart" data-product-id="' . $product->get_id() . '">' . __( 'Carrinho', 'list-multiple-products' ) . '</button></td>'; }
                            if ( in_array( 'Total', $options ) ) { echo '<td class="lmp-product-total" data-product-price="' . $product->get_price() . '">0.00</td>'; }
                            if ( in_array( 'Reset', $options ) ) { echo '<td class="reset"><button class="button lmp-reset-quantity">' . __( 'Resetar', 'list-multiple-products' ) . '</button></td>'; }
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9">' . __( 'No products found.', 'list-multiple-products' ) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="lmp-cart-summary">
                <strong><?php _e( 'Total:', 'list-multiple-products' ); ?></strong>
                <span class="lmp-cart-total">0.00</span>
                <button id="lmp-add-all-to-cart" class="button button-primary"><?php _e( 'Adicionar todos ao carrinho', 'list-multiple-products' ); ?></button>
                <button id="lmp-reset-all-quantities" class="button"><?php _e( 'Resetar todos', 'list-multiple-products' ); ?></button>
            </div>
            <div id="lmp-notification" style="display:none;">
                <div class="lmp-notification-content">
                    <span class="lmp-notification-message"></span>
                    <div class="lmp-notification-images"></div>
                </div>
            </div>
            <?php

            if ( $pagination === 'show' && $query->max_num_pages > 1 ) {
                echo '<div class="pagination">';
                echo paginate_links( [
                    'total'   => $query->max_num_pages,
                    'current' => max( 1, get_query_var( 'paged' ) ),
                ] );
                echo '</div>';
            }

            wp_reset_postdata();

            return ob_get_clean();
        }

        public static function add_to_cart() {
            if ( ! isset( $_POST['product_id'], $_POST['quantity'] ) ) {
                wp_send_json_error( [ 'message' => 'Invalid data.' ] );
            }

            $product_id = intval( $_POST['product_id'] );
            $quantity = intval( $_POST['quantity'] );

            if ( $product_id > 0 && $quantity > 0 ) {
                $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

                if ( $cart_item_key ) {
                    // Obter a imagem do produto para a notificação
                    $product = wc_get_product( $product_id );
                    $product_image = $product ? $product->get_image() : '';
                    wp_send_json_success( [ 'message' => 'Product added to cart.', 'image' => $product_image ] );
                } else {
                    wp_send_json_error( [ 'message' => 'Failed to add product to cart.' ] );
                }
            } else {
                wp_send_json_error( [ 'message' => 'Invalid product or quantity.' ] );
            }
        }
    }

    LMP_Shortcode::init();
}


