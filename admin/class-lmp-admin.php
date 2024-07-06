<?php
if ( ! class_exists( 'LMP_Admin' ) ) {
    class LMP_Admin {
        public static function init() {
            add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
            add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_scripts' ] );
        }

        public static function enqueue_admin_scripts() {
            wp_enqueue_style( 'lmp-admin-styles', plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' );
            wp_enqueue_script( 'lmp-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/admin-scripts.js', [ 'jquery' ], null, true );
        }

        public static function add_admin_menu() {
            add_menu_page(
                __( 'List Multiple Products', 'list-multiple-products' ),
                __( 'List Multiple Products', 'list-multiple-products' ),
                'manage_options',
                'list-multiple-products',
                [ __CLASS__, 'admin_page' ],
                'dashicons-list-view'
            );

            add_submenu_page(
                'list-multiple-products',
                __( 'All Lists', 'list-multiple-products' ),
                __( 'All Lists', 'list-multiple-products' ),
                'manage_options',
                'list-multiple-products',
                [ __CLASS__, 'admin_page' ]
            );

            add_submenu_page(
                'list-multiple-products',
                __( 'Add New List', 'list-multiple-products' ),
                __( 'Add New List', 'list-multiple-products' ),
                'manage_options',
                'list-multiple-products-add-new',
                [ __CLASS__, 'add_new_list_page' ]
            );

            add_submenu_page(
                'list-multiple-products',
                __( 'Settings', 'list-multiple-products' ),
                __( 'Settings', 'list-multiple-products' ),
                'manage_options',
                'list-multiple-products-settings',
                [ __CLASS__, 'settings_page' ]
            );
        }

        public static function register_settings() {
            register_setting( 'lmp_options_group', 'lmp_lists', 'sanitize_text_field' );
        }

        public static function admin_page() {
            ?>
            <div class="wrap">
                <h1><?php _e( 'All Lists', 'list-multiple-products' ); ?></h1>
                <a href="<?php echo admin_url( 'admin.php?page=list-multiple-products-add-new' ); ?>" class="button button-primary"><?php _e( 'Add New List', 'list-multiple-products' ); ?></a>
                <div class="mpcdp_settings_content">
                    <div id="general" class="hidden mpcdp_settings_tab active" data-tab="general" style="display: block;">
                        <div class="mpcdp_settings_section">
                            <div class="mpcdp_settings_section_title"><?php _e( 'All Product Tables', 'list-multiple-products' ); ?></div>
                            <div class="mpcdp_settings_toggle mpcdp_container" style="margin-top: 30px;">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <?php
                                            $lists = get_option( 'lmp_lists', [] );
                                            if ( ! empty( $lists ) ) {
                                                ?>
                                                <table class="table-shortcode">
                                                    <thead>
                                                        <tr>
                                                            <th><?php _e( 'ID', 'list-multiple-products' ); ?></th>
                                                            <th><?php _e( 'Title', 'list-multiple-products' ); ?></th>
                                                            <th><?php _e( 'Description', 'list-multiple-products' ); ?></th>
                                                            <th><?php _e( 'Shortcode', 'list-multiple-products' ); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ( $lists as $id => $list ) {
                                                            $product_ids = implode( ',', $list['products'] );
                                                            ?>
                                                            <tr>
                                                                <td><?php echo esc_html( $id ); ?></td>
                                                                <td><?php echo esc_html( $list['title'] ); ?></td>
                                                                <td><?php echo esc_html( $list['description'] ); ?></td>
                                                                <td><code>[list-product id="<?php echo esc_html( $product_ids ); ?>"]</code></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="mpcdp_option_label"><?php _e( 'No shortcodes found.', 'list-multiple-products' ); ?></div>
                                                <div class="mpcdp_option_description">
                                                    <?php _e( 'Create a product table shortcode', 'list-multiple-products' ); ?> <a href="<?php echo admin_url( 'admin.php?page=list-multiple-products-add-new' ); ?>"><?php _e( 'here', 'list-multiple-products' ); ?></a>.
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        public static function add_new_list_page() {
            if ( isset( $_POST['lmp_add_list'] ) ) {
                self::save_list();
            }
            ?>
            <div class="wrap">
                <h1><?php _e( 'Add New List', 'list-multiple-products' ); ?></h1>
                <form method="post">
                    <div id="general" class="hidden mpcdp_settings_tab active" data-tab="general" style="display: block;">
                        <div class="mpcdp_settings_section">
                            <div class="mpcdp_settings_section_title"><?php _e( 'Add New Product Table', 'list-multiple-products' ); ?></div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Table Title', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'For internal use only, it helps admins identify the table and will not be shown to customers.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <input type="text" name="lmp_list_title" id="lmp_list_title" value="" placeholder="<?php _e( 'Product table title', 'list-multiple-products' ); ?>" class="regular-text" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Table Description', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'For internal use only, it helps admins identify the table and will not be shown to customers.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <input type="text" name="lmp_list_description" id="lmp_list_description" value="" placeholder="<?php _e( 'Description', 'list-multiple-products' ); ?>" class="regular-text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Add Product', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Select specific products to display if you want to add only certain items in the product table.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_products[]" id="lmp_list_products" multiple class="regular-text" required>
                                                <?php
                                                $products = wc_get_products( [ 'limit' => -1 ] );
                                                foreach ( $products as $product ) {
                                                    echo '<option value="' . esc_attr( $product->get_id() ) . '">' . esc_html( $product->get_name() ) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Selected Products', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Choose which products should be checked by default when the product table initially loads.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_selected[]" id="lmp_list_selected" multiple class="regular-text">
                                                <?php
                                                foreach ( $products as $product ) {
                                                    echo '<option value="' . esc_attr( $product->get_id() ) . '">' . esc_html( $product->get_name() ) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Categories', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Specify the product categories to be included in the product table.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_categories[]" id="lmp_list_categories" multiple class="regular-text">
                                                <?php
                                                $categories = get_terms( 'product_cat' );
                                                foreach ( $categories as $category ) {
                                                    echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Products Per Page', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Set a limit on the number of products to display per page. This option is functional only when the Pagination setting is Show. Maximum page limit is 100.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <input type="number" name="lmp_list_limit" id="lmp_list_limit" value="10" class="regular-text" min="1" max="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Pagination', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'If Hide is selected, all products will be shown on a single page without pagination.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <div class="input-field" style="display:none;">
                                                <input type="checkbox" name="lmp_list_pagination" id="lmp_list_pagination" data-off-title="Hide" data-on-title="Show" class="hurkanSwitch-switch-input" title="Pagination" checked="">
                                            </div>
                                            <div class="hurkanSwitch hurkanSwitch-switch-plugin-box">
                                                <div class="hurkanSwitch-switch-box switch-animated-on">
                                                    <a class="hurkanSwitch-switch-item active hurkanSwitch-switch-item-color-success  hurkanSwitch-switch-item-status-on" style="width:100px !important">
                                                        <span class="lbl"><?php _e( 'Show', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                    <a class="hurkanSwitch-switch-item  hurkanSwitch-switch-item-color-  hurkanSwitch-switch-item-status-off" style="width:90px !important">
                                                        <span class="lbl"><?php _e( 'Hide', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Order By', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Determine the attribute (like price, name, etc.) upon which the product sorting should be based.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_orderby" id="lmp_list_orderby" class="regular-text">
                                                <option value="date"><?php _e( 'Date', 'list-multiple-products' ); ?></option>
                                                <option value="title"><?php _e( 'Title', 'list-multiple-products' ); ?></option>
                                                <option value="price"><?php _e( 'Price', 'list-multiple-products' ); ?></option>
                                                <option value="id"><?php _e( 'ID', 'list-multiple-products' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Order', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Define the display sequence as ascending, descending, or custom. The custom setting is functional only when you have pre-selected specific products in the Add Product section.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_order" id="lmp_list_order" class="regular-text">
                                                <option value="asc"><?php _e( 'ASC', 'list-multiple-products' ); ?></option>
                                                <option value="desc"><?php _e( 'DESC', 'list-multiple-products' ); ?></option>
                                                <option value="custom"><?php _e( 'Custom', 'list-multiple-products' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Product Description', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'This is the individual product description.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <div class="input-field" style="display:none;">
                                                <input type="checkbox" name="lmp_list_description" id="lmp_list_description" data-off-title="Hide" data-on-title="Show" class="hurkanSwitch-switch-input" title="Product Description">
                                            </div>
                                            <div class="hurkanSwitch hurkanSwitch-switch-plugin-box">
                                                <div class="hurkanSwitch-switch-box switch-animated-off">
                                                    <a class="hurkanSwitch-switch-item  hurkanSwitch-switch-item-color-success  hurkanSwitch-switch-item-status-on" style="width:100px !important">
                                                        <span class="lbl"><?php _e( 'Show', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                    <a class="hurkanSwitch-switch-item active hurkanSwitch-switch-item-color-  hurkanSwitch-switch-item-status-off" style="width:90px !important">
                                                        <span class="lbl"><?php _e( 'Hide', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Product Link', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Choose whether or not product titles should link to their individual product pages.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <div class="input-field" style="display:none;">
                                                <input type="checkbox" name="lmp_list_link" id="lmp_list_link" data-off-title="Hide" data-on-title="Show" class="hurkanSwitch-switch-input" title="Product Link" checked="">
                                            </div>
                                            <div class="hurkanSwitch hurkanSwitch-switch-plugin-box">
                                                <div class="hurkanSwitch-switch-box switch-animated-on">
                                                    <a class="hurkanSwitch-switch-item active hurkanSwitch-switch-item-color-success  hurkanSwitch-switch-item-status-on" style="width:100px !important">
                                                        <span class="lbl"><?php _e( 'Show', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                    <a class="hurkanSwitch-switch-item  hurkanSwitch-switch-item-color-  hurkanSwitch-switch-item-status-off" style="width:90px !important">
                                                        <span class="lbl"><?php _e( 'Hide', 'list-multiple-products' ); ?></span>
                                                        <span class="hurkanSwitch-switch-cursor-selector"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpcdp_settings_toggle mpcdp_container">
                                <div class="mpcdp_settings_option visible">
                                    <div class="mpcdp_row">
                                        <div class="mpcdp_settings_option_description col-md-6">
                                            <div class="mpcdp_option_label"><?php _e( 'Product Type', 'list-multiple-products' ); ?></div>
                                            <div class="mpcdp_option_description"><?php _e( 'Select the types of products you wish to include in the product table.', 'list-multiple-products' ); ?></div>
                                        </div>
                                        <div class="mpcdp_settings_option_field mpcdp_settings_option_field_text col-md-6">
                                            <select name="lmp_list_type[]" id="lmp_list_type" multiple class="regular-text">
                                                <option value="all"><?php _e( 'All', 'list-multiple-products' ); ?></option>
                                                <option value="simple"><?php _e( 'Simple', 'list-multiple-products' ); ?></option>
                                                <option value="variable"><?php _e( 'Variable', 'list-multiple-products' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpca-content new-table">
                                <a href="<?php echo admin_url( 'admin.php?page=mpc-shortcode' ); ?>" class="mpcasc-reset"><span class="button-secondary"><?php _e( 'Reset', 'list-multiple-products' ); ?></span></a>
                            </div>

                            <input type="hidden" id="mpc_opt_sc" name="mpc_opt_sc" value="<?php echo wp_create_nonce( 'mpc_opt_sc' ); ?>">
                            <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=mpc-shortcode">
                        </div>
                    </div>
                    <p class="submit">
                        <input type="submit" name="lmp_add_list" class="button button-primary" value="<?php _e( 'Save List', 'list-multiple-products' ); ?>">
                    </p>
                </form>
            </div>
            <?php
        }

        private static function save_list() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! isset( $_POST['lmp_add_list'] ) || ! wp_verify_nonce( $_POST['mpc_opt_sc'], 'mpc_opt_sc' ) ) {
                return;
            }

            $lists = get_option( 'lmp_lists', [] );

            $list_id = sanitize_key( wp_unique_id( 'lmp_list_' ) );
            $list_title = sanitize_text_field( $_POST['lmp_list_title'] );
            $list_description = sanitize_text_field( $_POST['lmp_list_description'] );
            $list_products = array_map( 'intval', $_POST['lmp_list_products'] );
            $list_selected = isset( $_POST['lmp_list_selected'] ) ? array_map( 'intval', $_POST['lmp_list_selected'] ) : [];
            $list_categories = isset( $_POST['lmp_list_categories'] ) ? array_map( 'intval', $_POST['lmp_list_categories'] ) : [];
            $list_limit = intval( $_POST['lmp_list_limit'] );
            $list_pagination = isset( $_POST['lmp_list_pagination'] ) ? 'show' : 'hide';
            $list_orderby = sanitize_text_field( $_POST['lmp_list_orderby'] );
            $list_order = sanitize_text_field( $_POST['lmp_list_order'] );
            $list_description_display = isset( $_POST['lmp_list_description'] ) ? 'show' : 'hide';
            $list_link = isset( $_POST['lmp_list_link'] ) ? 'show' : 'hide';
            $list_type = isset( $_POST['lmp_list_type'] ) ? array_map( 'sanitize_text_field', $_POST['lmp_list_type'] ) : [];

            $lists[ $list_id ] = [
                'title' => $list_title,
                'description' => $list_description,
                'products' => $list_products,
                'selected' => $list_selected,
                'categories' => $list_categories,
                'limit' => $list_limit,
                'pagination' => $list_pagination,
                'orderby' => $list_orderby,
                'order' => $list_order,
                'description_display' => $list_description_display,
                'link' => $list_link,
                'type' => $list_type,
            ];

            update_option( 'lmp_lists', $lists );
        }

        public static function settings_page() {
            ?>
            <div class="wrap">
                <h1><?php _e( 'Settings', 'list-multiple-products' ); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'lmp_options_group' );
                    do_settings_sections( 'lmp_options_group' );
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Option Name', 'list-multiple-products' ); ?></th>
                            <td><input type="text" name="option_name" value="<?php echo esc_attr( get_option( 'option_name' ) ); ?>" /></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
    }
}

LMP_Admin::init();
