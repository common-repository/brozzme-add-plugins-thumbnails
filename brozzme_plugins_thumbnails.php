<?php
/*
  Plugin Name: Brozzme add plugins thumbnails
  Plugin URI: https://brozzme.com/plugins-thumbnails/
  Description: Add thumbnail column to plugins list table in the admin plugins page.
  Version: 1.4.5
  Author: benoti
  Author URI: https://brozzme.com
  License: GPL2
 */

if(!class_exists('brozzme_plugins_thumbnails')){

    class brozzme_plugins_thumbnails {

        public function __construct() {

            $this->basename = plugin_basename(__FILE__);
            $this->directory_path = plugin_dir_path(__FILE__);
            $this->directory_url = plugins_url(dirname($this->basename));

            // group menu ID
            $this->plugin_dev_group = 'Brozzme';
            $this->plugin_dev_group_id = 'brozzme-plugins';

            // plugin info
            $this->plugin_name = 'Brozzme Plugins Thumbnails';
            $this->plugin_slug = 'brozzme-plugins-thumbnails';
            $this->settings_page_slug = 'brozzme-plugins-thumbnails';
            $this->plugin_version = '1.0';
            $this->plugin_txt_domain = 'brozzme-add-plugins-thumbnails';

            $this->_define_constants();

            // Run our activation and deactivation hooks
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook( __FILE__, array($this, 'deactivate') );
            register_uninstall_hook( __DIR__ .'/uninstall.php', 'brozzme_plugins_thumbnails_plugin_uninstall' );

            $this->options = get_option('b7epi_settings');
            $this->options_specials = get_option('b7epi_my_plugin_thumbnails');
            /* init */
            add_action( 'admin_enqueue_scripts', array( $this, '_add_settings_styles') );

            $this->_init();
            add_action( 'plugins_loaded', array($this, '_load_textdomain') );
            $this->color = array('#4679BD', '#03b775', '#ffac30', '#e82e2e');
        }

        public function _define_constants(){

            defined('BFSL_PLUGINS_DEV_GROUPE')    or define('BFSL_PLUGINS_DEV_GROUPE', $this->plugin_dev_group);
            defined('BFSL_PLUGINS_DEV_GROUPE_ID') or define('BFSL_PLUGINS_DEV_GROUPE_ID', $this->plugin_dev_group_id);
            defined('BFSL_PLUGINS_URL') or define('BFSL_PLUGINS_URL', $this->directory_url);
            defined('BFSL_PLUGINS_SLUG') or define('BFSL_PLUGINS_SLUG', $this->plugin_slug);

            defined('B7EPI')    or define('B7EPI', $this->plugin_name);
            defined('B7EPI_BASENAME')    or define('B7EPI_BASENAME', $this->basename);
            defined('B7EPI_DIR')    or define('B7EPI_DIR', $this->directory_path);
            defined('B7EPI_DIR_URL')    or define('B7EPI_DIR_URL', $this->directory_url);
            defined('B7EPI_SETTINGS_SLUG')  or define('B7EPI_SETTINGS_SLUG', $this->settings_page_slug);
            defined('B7EPI_PLUGIN_SLUG')  or define('B7EPI_PLUGIN_SLUG', $this->plugin_slug);
            defined('B7EPI_VERSION')        or define('B7EPI_VERSION', $this->plugin_version);
            defined('B7EPI_TEXT_DOMAIN')    or define('B7EPI_TEXT_DOMAIN', $this->plugin_txt_domain);
        }

        /**
         *
         */
        public function _init() {

            add_action('admin_head', array($this, '_empty_thumb_style'));
            add_filter('manage_plugins_columns', array($this, '_columns_head'));
            add_action('manage_plugins_custom_column', array($this, '_thumbnail_column'), 10, 3);

            if (is_admin() && !class_exists('brozzme_plugins_page')){
                include_once ($this->directory_path . 'includes/brozzme_plugins_page.php');
            }

            include_once $this->directory_path . 'includes/brozzme_plugins_thumbnails_settings.php';
            new brozzme_plugins_thumbnails_settings();

        }
        /**
         * load text domain
         */
        public function _load_textdomain() {
            load_plugin_textdomain( $this->plugin_txt_domain, false, $this->plugin_txt_domain . '/languages' );
        }

        /**
         * @param $hook
         */
        public function _add_settings_styles($hook) {
            if($hook === 'toplevel_page_brozzme-plugins' || $hook == 'brozzme_page_' . $this->settings_page_slug){
                wp_enqueue_style('b7epi', plugin_dir_url( __FILE__ ) . 'css/brozzme-admin-css.css', false, '');
                wp_enqueue_style('b7epi-2', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '');
            }
        }

        /**
         * @param $defaults
         * @return array
         */
        public function _columns_head($defaults) {

            $defaults = array_slice($defaults, 0, 1, true) +
                array("brozzme_plugin_icon" => "") +
                array_slice($defaults, 1, count($defaults) - 1, true);

            return $defaults;
        }

        /**
         * @param $column_name
         * @param $plugin_file
         * @param $plugin_data
         * @set transient
         */
        public function _thumbnail_column($column_name, $plugin_file, $plugin_data) {

            if ( 'brozzme_plugin_icon' == $column_name ) {
                $plugin_slug = $this->_get_plugin_slug($plugin_file);
                $plugin_first_letters = strtoupper($this->_get_word_first_letter($plugin_data));


                if ( false === ( $plugin_icon = get_transient( $plugin_slug.'_plugin_icon_url' ) ) ) {
                    $transient_expiration = (empty($this->options['b7epi_plugins_thumbnails_transients_expiration']))? 7 * DAY_IN_SECONDS : ((int)$this->options['b7epi_plugins_thumbnails_transients_expiration'] * DAY_IN_SECONDS);

                    /* */
                    $special = $this->_check_for_specials($plugin_slug);

                    if($special != NULL){
                        $plugin_icon = apply_filters($plugin_slug.'_brozzme_plugin_icon_url', $special[1], $plugin_slug);
                    }else{
                        $plugin_icon = apply_filters($plugin_slug.'_brozzme_plugin_icon_url', $this->_remote_plugin_image_url($plugin_slug), $plugin_slug);
                    }

                    set_transient( $plugin_slug.'_plugin_icon_url', $plugin_icon, $transient_expiration );
                }

                $color = $this->color;
                $rand_color = array_rand($color, 1);

                echo '<span class="wps-ext-img" data-src="' . $plugin_icon . '" data-pluginName="'. $plugin_data['Name'] .'" data-pluginfl="' . strtoupper($plugin_slug[0]) .'" data-pluginfls="' . $plugin_first_letters . '" data-color="'. $color[$rand_color] .'">
        <div class="square-box" >
                <div class="square-content"><div><div class="fl">' . strtoupper($plugin_slug[0]) . '</div><div class="fls">' . $plugin_first_letters . '</div></div></div>
            </div></span>';

            }
        }

        /**
         * @param $plugin
         * @return string
         */
        public function get_plugin_slug($plugin) {

            $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $plugin_slug = sanitize_title($plugin['Name']);

            return $plugin_slug;
        }

        /**
         * @param $plugin
         * @return mixed
         */
        public function _get_plugin_slug($plugin) {

            $plugin_folder = explode('/', $plugin);

            return $plugin_folder[0];
        }

        /**
         * @param $plugin_data
         * @return string
         */
        public function _get_word_first_letter($plugin_data){

            $words = explode(' ', $plugin_data['Name']);
            $i=0;
            $first_letter = '';
            foreach ($words as $word) {
                if($i<=2){
                    $first_letter .= $word[0];
                    $i++;
                }
            }

            return $first_letter;

        }

        /**
         * @param $plugin_name
         * @return string
         */
        public function _remote_plugin_image_url($plugin_name){
            /* need improvment to get revision thumbnail */
            $image_url = "https://ps.w.org/{$plugin_name}/assets/icon-128x128";

            $response_png = wp_remote_get( $image_url.'.png' );
            if(wp_remote_retrieve_response_code( $response_png ) == '200'){
                return $image_url . '.png';
            }else{
                $response_jpg = wp_remote_get( $image_url.'.jpg' );
                if(wp_remote_retrieve_response_code( $response_jpg ) == '200'){
                    return $image_url . '.jpg';
                }else{
                    return 'null';
                }
            }

            return 'null';
        }


        public function _check_for_specials($plugin_slug){

            if(isset($this->options_specials['b7epi_specials']) && is_array($this->options_specials['b7epi_specials'])){
                $slug_exists = array_column($this->options_specials['b7epi_specials'], 'slug');
                $key = array_search($plugin_slug, array_column($this->options_specials['b7epi_specials'], 'slug'));

                if(in_array($plugin_slug, $slug_exists)){
                    $url = $this->options_specials['b7epi_specials'][$key]['url'];

                    return array($plugin_slug, $url);
                }
            }

            return null;
        }

        /**
         *
         */
        public function _empty_thumb_style() {

            $icon_width = (empty($this->options['b7epi_plugins_thumbnails_width']))? "96" : $this->options['b7epi_plugins_thumbnails_width'];
            $icon_height = (empty($this->options['b7epi_plugins_thumbnails_height']))? "96" : $this->options['b7epi_plugins_thumbnails_height'];

            $size_base = 96;
            $line_height = '1.177';
            $fl_base = '48';
            $fls_base = '26';

            $margin_top = '4px';
            $round_setup = '';
            if($icon_height < 80){
                // (78x3.5)/96
                $fl_font_size = ($icon_height*$fl_base)/ $size_base;
                $fls_font_size = ($icon_height*$fls_base)/ $size_base;
                if($icon_height <= 50){
                    $line_height = 0.5;
                    $margin_top = '10px';
                }else{
                    $line_height = 0.8;
                    $margin_top = '10px';
                }
            }else{
                $fl_font_size = $fl_base;
                $fls_font_size = $fls_base;
                if(isset($this->options['b7epi_plugins_thumbnails_round']) && $this->options['b7epi_plugins_thumbnails_round'] != false){
                    $line_height = 0.980;
                    $margin_top = '6px';
                }
            }

            if(isset($this->options['b7epi_plugins_thumbnails_round']) && $this->options['b7epi_plugins_thumbnails_round'] != false){
                $round_setup = 'border-radius:50%;';
            }

            ?>
            <style>
                .square-box{
                    position: relative;
                    width: <?php echo $icon_width; ?>px;
                    overflow: hidden;
                    background: #4679BD;
                <?php echo $round_setup;?>
                }
                .wps-ext-img img{
                <?php echo $round_setup;?>
                }
                .square-box:before{
                    content: "";
                    display: block;
                    padding-top: 100%;
                }
                .square-content{
                    position:  absolute;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    right: 0;
                    color: white;
                }
                .square-content div {
                    display: table;
                    width: 100%;
                    height: 100%;
                    margin-left: auto;
                    margin-right: auto;
                    width: <?php echo $icon_width; ?>px;;
                }
                .square-content div {
                    display: table-cell;
                    text-align: center;
                    vertical-align: middle;
                    color: white;
                }
                .square-content div.fl{
                    line-height: <?php echo $line_height; ?>;
                    font-size: <?php echo $fl_font_size; ?>px;
                    float: left;
                    margin-top: <?php echo $margin_top;?>;
                }
                .square-content div.fls{
                    float: right;
                    font-size: <?php echo $fls_font_size; ?>px;
                    width: 100%;
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    var aSpans = document.querySelectorAll(".wps-ext-img");

                    for (var i = 0; i < aSpans.length; i++) {
                        var src = aSpans[i].getAttribute('data-src');
                        if ('null' !== src) {
                            aSpans[i].innerHTML = '<img src="' + src + '" title="' + aSpans[i].getAttribute('data-pluginName') + '" width="<?php echo $icon_width; ?>" height="<?php echo $icon_height; ?>"/>';
                        }else{
                            aSpans[i].innerHTML = '<div class="square-box" title="' + aSpans[i].getAttribute('data-pluginName') + '" style="background-color:' + aSpans[i].getAttribute('data-color') +';"><div class="square-content"><div><div class="fl">'  + aSpans[i].getAttribute('data-pluginfl') + '</div><div class="fls">'  + aSpans[i].getAttribute('data-pluginfls') + '</div></div></div> </div></span>';
                        }
                    }
                }, false);
            </script>
            <?php

        }

        /**
         * @return array
         */
        public function _get_plugins(){

            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $install_plugins = get_plugins();

            $plugin_array = array();
            foreach ($install_plugins as $install_plugin => $plugin_data) {
                $plugin_array[] = $this->_get_plugin_slug($install_plugin);
            }
            return $plugin_array;
        }

        public function activate(){

            if (false === get_option('b7epi_settings')) {
                $arg = array(
                    'b7epi_enable'                => 'true',
                    'b7epi_plugins_thumbnails_width'    => 96,
                    'b7epi_plugins_thumbnails_height'   => 96,
                    'b7epi_plugins_thumbnails_transients_expiration' => 7 * DAY_IN_SECONDS
                );
                add_option( 'b7epi_settings', $arg, '', 'no' );

                // get all plugins array
                $plugins = $this->_get_plugins();
                // create transients for all files
                foreach($plugins as $k=>$plugin_slug){
                    if ( false === ( $plugin_icon = get_transient( $plugin_slug.'_plugin_icon_url' ) ) ) {
                        $plugin_icon = $this->_remote_plugin_image_url($plugin_slug);
                        set_transient( $plugin_slug.'_plugin_icon_url', $plugin_icon, 7 * DAY_IN_SECONDS );
                    }
                }
            }
        }

        public function deactivate(){

           // delete_option('b7epi_settings');
            $plugins = $this->_get_plugins();

            foreach ($plugins as $k=>$plugin_slug){
                delete_transient($plugin_slug.'_plugin_icon_url');
            }
        }
    }

    new brozzme_plugins_thumbnails();
}



