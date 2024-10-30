<?php
/**
 * Created by PhpStorm.
 * User: benoti
 * Date: 15/12/2017
 * Time: 21:50
 */

class brozzme_plugins_thumbnails_settings
{
    public function __construct()
    {
        $this->options = get_option('b7epi_settings');
        $this->options_specials = get_option('b7epi_my_plugin_thumbnails');
        add_action('admin_menu', array($this, 'add_admin_pages'), 110);
        add_action('admin_init', array($this, 'settings_fields'));

        /* since 1.4 */
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_js'));
        add_action('updated_option', array($this, '_reset_saved_specials'), 10, 3);


    }

    /**
     *
     */
    public function add_admin_pages()
    {
        add_submenu_page(BFSL_PLUGINS_DEV_GROUPE_ID,
            __('Plugins Thumbnails', B7EPI_TEXT_DOMAIN),
            __('Plugins Thumbnails', B7EPI_TEXT_DOMAIN),
            'manage_options',
            B7EPI_SETTINGS_SLUG,
            array($this, 'settings_page')
        );

    }

    /**
     *
     */
    public function settings_page(){

        ?>
        <div class="wrap">
            <h2>Brozzme Plugins Thumbnails</h2>
            <?php

            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';
            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo B7EPI_SETTINGS_SLUG;?>&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', B7EPI_TEXT_DOMAIN );?></a>
                <a href="?page=<?php echo B7EPI_SETTINGS_SLUG;?>&tab=add_my_plugins_thubnails" class="nav-tab <?php echo $active_tab == 'add_my_plugins_thubnails' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Add your thumbnails', B7EPI_TEXT_DOMAIN );?></a>
                <a href="admin.php?page=brozzme-plugins" class="nav-tab <?php echo $active_tab == 'brozzme' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Brozzme', B7EPI_TEXT_DOMAIN );?></a>

            </h2>

            <form id="brozzme-theme-panel-form" action='options.php' method='post'>
            <?php
            if($active_tab == 'add_my_plugins_thubnails') {
                settings_fields('plugins-thumbnails-specials');
                do_settings_sections('plugins-thumbnails-specials');
                submit_button();
            }else{
                settings_fields('plugins-thumbnails-settings');
                do_settings_sections('plugins-thumbnails-settings');
                submit_button();
            }

            ?>
            </form>
        </div>
        <!-- slider button -->
        <style>
            #slidecontainer {
                width: 100%; /* Width of the outside container */
            }

            /* The slider itself */
            .slider {
                -webkit-appearance: none;  /* Override default CSS styles */
                appearance: none;
                width: 50%; /* Full-width */
                height: 25px; /* Specified height */
                background: #FFFFFF; /* Grey background */
                outline: none; /* Remove outline */
                opacity: 0.7; /* Set transparency (for mouse-over effects on hover) */
                -webkit-transition: .2s; /* 0.2 seconds transition on hover */
                transition: opacity .2s;
                border-radius:5px;
                border: 1px solid #CCCCCC;
            }

            /* Mouse-over effects */
            .slider:hover {
                opacity: 1; /* Fully shown on mouse-over */
            }

            /* The slider handle (use webkit (Chrome, Opera, Safari, Edge) and moz (Firefox) to override default look) */
            .slider::-webkit-slider-thumb {
                -webkit-appearance: none; /* Override default look */
                appearance: none;
                width: 25px; /* Set a specific slider handle width */
                height: 25px; /* Slider handle height */
                background: #4CAF50; /* Green background */
                cursor: pointer; /* Cursor on hover */
            }

            .slider::-moz-range-thumb {
                width: 25px; /* Set a specific slider handle width */
                height: 25px; /* Slider handle height */
                background: #4CAF50; /* Green background */
                cursor: pointer; /* Cursor on hover */
            }
        </style>
        <script>
            var sliders = document.querySelectorAll('.slider');

            for (i = 0, x = sliders.length; i < x; i++) {
                var range = sliders[i];
                var id = (sliders[i].id) + "-value";
                var sortie = document.getElementById(id);
                sortie.innerHTML = range.value;

            }
            function adaptator(id, val) {
                var rangeSlide = document.getElementById(id);

                var id_adapt = id + "-value";
                var adapt = document.getElementById(id_adapt);
                rangeSlide.value = val;
                if(id_adapt == 'b7epi-plugins-thumbnails-width-value'){
                    var adapt2 = document.getElementById('b7epi-plugins-thumbnails-height-value');
                    adapt.innerHTML = val;
                    adapt2.innerHTML = val;
                }else{
                    var adapt2 = document.getElementById('b7epi-plugins-thumbnails-width-value');
                    adapt.innerHTML = val;
                    adapt2.innerHTML = val;
                }

            }
        </script>
        <?php
    }

    /**
     *
     */
    public function settings_fields(){
        register_setting( 'plugins-thumbnails-settings', 'b7epi_settings' );

        /* GENERAL TAB */
        add_settings_section(
            'b7epi_main',
            __('Settings', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_settings_section'),
            'plugins-thumbnails-settings'
        );

        add_settings_field(
            'b7epi_enable',
            __( 'Enable Brozzme Plugin Icons', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_enable_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );

        add_settings_field(
            'b7epi_plugins_thumbnails_width',
            __( 'Width', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_plugins_thumbnails_width_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );
        add_settings_field(
            'b7epi_plugins_thumbnails_height',
            __( 'Height', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_plugins_thumbnails_height_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );

        add_settings_field(
            'b7epi_plugins_thumbnails_round',
            __( 'Round icons', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_plugins_thumbnails_round_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );

        add_settings_field(
            'b7epi_plugins_thumbnails_transients_expiration',
            __( 'Transients expiration', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_plugins_thumbnails_transients_expiration_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );
        add_settings_field(
            'b7epi_plugins_thumbnails_reset_transients',
            __( 'Reset transients', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_plugins_thumbnails_reset_transients_render'),
            'plugins-thumbnails-settings',
            'b7epi_main'
        );

        /* SINCE 1.4 */

        register_setting( 'plugins-thumbnails-specials', 'b7epi_my_plugin_thumbnails' );

        add_settings_section(
            'b7epi_specials_panel',
            'Add plugins thumbnails url', array($this, 'b7epi_specials_section'),
            'plugins-thumbnails-specials'
        );

        add_settings_field(
            'b7epi_specials',
            __( 'Add your own plugins thumbnails', B7EPI_TEXT_DOMAIN),
            array($this, 'b7epi_specials_render'),
            'plugins-thumbnails-specials',
            'b7epi_specials_panel'
        );
    }

    /**
     *
     */
    public function b7epi_settings_section(){

    }

    /**
     *
     */
    public function b7epi_specials_section(){

        ?>
        <p><?php _e('Customize plugins thumbnails with any ones of your choice if they are not available in the WordPress repository (premium, self developed plugin).', B7EPI_TEXT_DOMAIN);?></p>
        <p><?php _e('Select plugin in the select dropdown and choose or upload the thumbnail for the choosen plugin. ', B7EPI_TEXT_DOMAIN);?></p>
        <?php
    }

    /**
     *
     */
    public function b7epi_enable_render(){
        ?>
        <div id="b7e-bounds">
            <label class="on"><input type="radio" name="b7epi_settings[b7epi_enable]" value="true"  <?php checked( $this->options['b7epi_enable'], 'true' ); ?>>
                <span><?php _e( 'Yes', B7EPI_TEXT_DOMAIN);?></span></label>
            <label class="off"><input type="radio" name="b7epi_settings[b7epi_enable]" value="false"  <?php checked( $this->options['b7epi_enable'], 'false' ); ?>>
                <span><?php _e( 'No', B7EPI_TEXT_DOMAIN);?></span></label>
        </div>
        <?php
    }

    /**
     *
     */
    public function b7epi_plugins_thumbnails_width_render(){
        ?>
        <div id="slidecontainer">
            <input type="range" class="slider" name="b7epi_settings[b7epi_plugins_thumbnails_width]" id="b7epi-plugins-thumbnails-width" onchange="adaptator('b7epi-plugins-thumbnails-height', this.value)" min="32" max="96" value="<?= (empty($this->options['b7epi_plugins_thumbnails_width']))? "96" : $this->options['b7epi_plugins_thumbnails_width']; ?>" >
        </div>
        <p>Value: <span id="b7epi-plugins-thumbnails-width-value"></span> px</p>
        <?php
    }

    /**
     *
     */
    public function b7epi_plugins_thumbnails_height_render(){
        ?>
        <div id="slidecontainer_1">
            <input type="range" class="slider" name="b7epi_settings[b7epi_plugins_thumbnails_height]" id="b7epi-plugins-thumbnails-height" onchange="adaptator('b7epi-plugins-thumbnails-width', this.value)" min="32" max="96" value="<?= (empty($this->options['b7epi_plugins_thumbnails_height']))? "96" : $this->options['b7epi_plugins_thumbnails_height']; ?>" >
        </div>
        <p>Value: <span id="b7epi-plugins-thumbnails-height-value"></span> px</p>
        <?php
    }

    /**
     *
     */
    public function b7epi_plugins_thumbnails_round_render(){

        if(isset($this->options['b7epi_plugins_thumbnails_round']) && $this->options['b7epi_plugins_thumbnails_round'] != ''){
            $checked = 'checked="checked"';
        }
        else{
            $checked = '';
        }
        ?>
        <input type="checkbox" name="b7epi_settings[b7epi_plugins_thumbnails_round]" value="true" <?php echo $checked;?>>
        <?php
    }

    /**
     *
     */
    public function b7epi_plugins_thumbnails_transients_expiration_render(){
        
        ?>
        <select id="dropdown_transient" name="b7epi_settings[b7epi_plugins_thumbnails_transients_expiration]">
            <option class="transient_expires" value="1 * DAY_IN_SECONDS" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '1 * DAY_IN_SECONDS')?>>1 <?php _e('day', B7EPI_TEXT_DOMAIN);?></option>
            <option class="transient_expires" value="7 * DAY_IN_SECONDS" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '7 * DAY_IN_SECONDS')?>>1 <?php _e('week', B7EPI_TEXT_DOMAIN);?></option>
            <option class="transient_expires" value="30 * DAY_IN_SECONDS" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '30 * DAY_IN_SECONDS')?>>1 <?php _e('month', B7EPI_TEXT_DOMAIN);?></option>
            <option class="transient_expires" value="90 * DAY_IN_SECONDS" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '90 * DAY_IN_SECONDS')?>>3 <?php _e('month', B7EPI_TEXT_DOMAIN);?></option>
            <option class="transient_expires" value="365 * DAY_IN_SECONDS" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '365 * DAY_IN_SECONDS')?>>1 <?php _e('year', B7EPI_TEXT_DOMAIN);?></option>
            <option class="transient_expires" value="0" <?php selected($this->options['b7epi_plugins_thumbnails_transients_expiration'], '0')?>><?php _e('Never', B7EPI_TEXT_DOMAIN);?></option>
        </select>
        <script>
            (function( $ ) {
                $(function() {
                    $('#dropdown_transient').change(function() {
                        $('input[class=reset_transient]').attr('checked', true);
                    });
                });
            })( jQuery );
        </script>
        <?php
    }

    /**
     *
     */
    public function b7epi_plugins_thumbnails_reset_transients_render(){

        if(isset($this->options['b7epi_plugins_thumbnails_reset_transients']) && $this->options['b7epi_plugins_thumbnails_reset_transients'] != ''){

            $instal_plugins = $this->_get_install_plugins();

            foreach ($instal_plugins as $k=>$plugin_slug){
                delete_transient($plugin_slug.'_plugin_icon_url');
            }

            $_options = $this->options;
            $_options['b7epi_plugins_thumbnails_reset_transients'] = '';
            update_option('b7epi_settings', $_options);

            _e('Transients deleted', B7EPI_TEXT_DOMAIN);
        }
        else{
            ?>
            <input class="reset_transient" type="checkbox" name="b7epi_settings[b7epi_plugins_thumbnails_reset_transients]" value="true">
            <?php
        }
    }

    /**
     * @return array
     */
    public function _get_install_plugins(){
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
    /**
     * @param $plugin
     * @return mixed
     */
    public function _get_plugin_slug($plugin){

        $plugin_name = explode('/', $plugin);

        if(is_array($plugin_name)){
            return $plugin_name[0];
        }
    }

    /* since 1.4 */
    /**
     * @param $hook
     */
    public function enqueue_media_js($hook){

        if($hook != 'brozzme_page_'. B7EPI_PLUGIN_SLUG)
            return;

        wp_enqueue_media();

        wp_register_script( 'media-bbss-uploader-js', B7EPI_DIR_URL .'/js/bapt-media-upload.js', array('jquery') );
        wp_localize_script(
            'media-bbss-uploader-js',
            'screenHelp',
            array(
                'title' => __('Select or upload new file for thumbnail', B7EPI_TEXT_DOMAIN),
                'buttomText' => __('Add thumbnail', B7EPI_TEXT_DOMAIN),
                'uploadButtonText' => __('Modify thumbnail', B7EPI_TEXT_DOMAIN)
            )
        );
        wp_enqueue_script( 'media-bbss-uploader-js' );
    }

    /**
     *
     */
    public function b7epi_specials_render(){
        ?>
        <div class="input_fields_wrap">
            <button class="add_field_button button button-primary"><?php _e('Add More Fields', B7EPI_TEXT_DOMAIN);?></button><br/><br/>
            <?php
            $i = 0;
            $plugins = get_plugins();
            foreach ( $plugins as $plugin => $plugin_data ) {
                $slug = $this->_get_plugin_slug($plugin);

                $active_plugin = (in_array($plugin, get_option('active_plugins')) ) ? true : false;
                $array[] = array( 'name' => $plugin_data['Name'], 'slug' => $slug, 'active' => $active_plugin );
            }

            if(isset($this->options_specials['b7epi_specials']) && is_array($this->options_specials['b7epi_specials']) && $this->options_specials['b7epi_specials'] != null){

                $i = 0;

                foreach ($this->options_specials['b7epi_specials'] as $k=>$bwpps_plugin_slug) {
                    ?>
            <div id="thumb_info_<?php echo $i;?>" class="bloc-thumb-info">
                <a style="float: right;" class="remove_field" href="#"><span class="dashicons dashicons-dismiss"></span></a>
                <?php
                $file_headers = @get_headers($bwpps_plugin_slug['url']);
                if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
                    $exists = false;
                }
                else {
                    $exists = true;
                }
                if($exists === true){
                    ?><img class="custom-img-container" src="<?php echo $bwpps_plugin_slug['url']; ?>" width="96px" height="auto" style="float:right;margin-right:10px;"/><?php
                }

                ?>
                <select class="b7epi_tmp_slug" name="b7epi_my_plugin_thumbnails_temp">
                    <?php
                    foreach ($array as $data) {
                        $checked = '';
                        if( $bwpps_plugin_slug['slug'] === $data['slug'] ) {
                            $checked = 'selected';
                        }
                        echo  '<option value="' . $data['slug'] . '" '. $checked . '>' . $data['name'] . '</option>';
                    } ?>
                </select>
                <input type="text" readonly class="be7pi-final-slug" name="b7epi_my_plugin_thumbnails[b7epi_specials][<?php echo $i;?>][slug]" data-key="<?php echo $i;?>" value="<?php echo $bwpps_plugin_slug['slug'];?>" size="70"> </br>
                <input id="thumb_file_url_<?php echo $i;?>" type="text" name="b7epi_my_plugin_thumbnails[b7epi_specials][<?php echo $i;?>][url]" value="<?php echo $bwpps_plugin_slug['url'];?>" size="70">
                <input id="upload-button" key="<?php echo $i;?>" type="button" class="upload-custom-img button-primary " value="<?php _e('Modify thumbnail', B7EPI_TEXT_DOMAIN);?>" style="margin: 4px 2px;"/>
            </div>
                    <?php
                    $i++;
                }
            }
            else{
                ?>
            <div id="thumb_info_0" class="bloc-thumb-info">
                <a class="remove_field" style="float: right;" href="#"><span class="dashicons dashicons-dismiss"></span></a>
                <img class="custom-img-container hidden" src="" width="96px" height="auto" style="float:right;margin-right:10px;"/>
                <select class="b7epi_tmp_slug" name="b7epi_my_plugin_thumbnails_temp">
                    <?php
                    foreach ($array as $data) {
                        $checked = '';
                        echo  '<option value="' . $data['slug'] . '" '. $checked . '>' . $data['name'] . '</option>';
                    } ?>
                </select>
                <input type="text" readonly class="be7pi-final-slug" name="b7epi_my_plugin_thumbnails[b7epi_specials][0][slug]" value="" placeholder="<?php _e('Slug (ie, for WPS Hide Login,slug is wps-hide-login, usually the plugin folder name)', B7EPI_TEXT_DOMAIN);?>" size="70"> </br>
                <input id="thumb_file_url_0" type="text" name="b7epi_my_plugin_thumbnails[b7epi_specials][0][url]" value="" size="70">
                <input id="upload-button" key="0" type="button" class="upload-custom-img button-primary" value="<?php _e('Add thumbnail', B7EPI_TEXT_DOMAIN);?>"  style="margin: 4px 2px;"/>
            </div>
                <?php
            }

            if($i == 0){
                $js_i = 0;
            }
            else{
                $js_i = $i-1;
            }
            ?>

        </div>
        <style>.custom-img-container.hidden{display:none;}
            .custom-img-container.hidden{display:block;}</style>
        <script>
            jQuery(document).ready(function($){

                $(document).ready(function() {
                    var max_fields      = 15; //maximum input boxes allowed
                    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
                    var add_button      = $(".add_field_button"); //Add button ID

                    var x = <?php echo $js_i;?>; //initial text box count
                    $(add_button).click(function(e){ //on add input button click
                        e.preventDefault();
                        if(x < max_fields){ //max input box allowed
                            x++; //text box increment
                            $(wrapper).append('<div id="thumb_info_' + x + '" class="bloc-thumb-info newbloc">' +
                                '<a class="remove_field" style="float: right;" href="#"><span class="dashicons dashicons-dismiss"></span></a>' +
                                '<img class="custom-img-container hidden" src="" width="96px" height="auto" style="float:right;margin-right:10px;"/>' +
                                '<select class="b7epi_tmp_slug newsel" name="b7epi_my_plugin_thumbnails_temp"><?php echo $this->_select_plugins_without_selected($array);?></select>' +
                                '<input type="text" readonly class="be7pi-final-slug" name="b7epi_my_plugin_thumbnails[b7epi_specials][' + x + '][slug]" placeholder="<?php _e('Plugin slug (ie, for WPS Hide Login, slug is wps-hide-login, usually the plugin folder name)', B7EPI_TEXT_DOMAIN);?>" size="70"/>' +
                                '</br><input type="text" name="b7epi_my_plugin_thumbnails[b7epi_specials]['+ x +'][url]" value="" placeholder="<?php _e('Url of the thumbnail', B7EPI_TEXT_DOMAIN);?>" size="70">' +
                                '<input id="upload-button" key="'+ x +'" type="button" class="upload-custom-img new button-primary" value="<?php _e('Add plugin thumbnail', B7EPI_TEXT_DOMAIN);?>"  style="margin: 4px 2px;"/></div>');
                        }
                        var target = $('#thumb_info_' + x );
                        $('html, body').animate({
                            scrollTop: target.offset().top
                        }, 1000);

                    });

                    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                        e.preventDefault(); $(this).parent('div').remove(); x--;
                    })

                });
            });
        </script>
        <style>
            .bloc-thumb-info.newbloc{
                -webkit-animation: comein 3s; /* Safari 4+ */
                -moz-animation:    comein 3s; /* Fx 5+ */
                -o-animation:      comein 3s; /* Opera 12+ */
                animation:         comein 3s; /* IE 10+, Fx 29+ */
            }
            @keyframes comein {
                from {
                    border: 3px solid steelblue;
                    box-shadow:10px 10px 15px steelblue;
                    opacity: 0.3;
                }

                to {
                    border: unset;
                    box-shadow: unset;
                    opacity: 1;
                }
            }
        </style>
        <script>
            (function( $ ) {
                $(function() {

                    $('.b7epi_tmp_slug').change(function() {

                        $(this).next('.be7pi-final-slug').val($(this).find("option:selected").attr('value'));
                    });

                    $('.input_fields_wrap').live('click', '.b7epi_tmp_slug.newsel', get_selector);


                    function get_selector(){
                        $('.b7epi_tmp_slug.newsel').change(function() {
                            $(this).next('.be7pi-final-slug').val($(this).find("option:selected").attr('value'));
                        });
                    }
                });
            })( jQuery );
        </script>
        <?php
    }

    /**
     * @param $array
     * @return string
     */
    public function _select_plugins_without_selected($array){

        ob_start();
        foreach ($array as $data) {
            $checked = '';
            echo  '<option value="' . $data['slug'] . '" '. $checked . '>' . $data['name'] . '</option>';
        }

        $output = ob_get_clean();

        return $output;

    }

    /**
     * @param $option
     * @param $old_value
     * @param $value
     */
    public function _reset_saved_specials($option, $old_value, $value ) {

        if($option == 'b7epi_my_plugin_thumbnails'){

            $delete_slug_transient = array();
            $transient_expiration = (empty($this->options['b7epi_plugins_thumbnails_transients_expiration']))? 7 * DAY_IN_SECONDS : ((int)$this->options['b7epi_plugins_thumbnails_transients_expiration'] * DAY_IN_SECONDS);

            $i = 0;
            foreach (maybe_unserialize($value["b7epi_specials"]) as $k=>$bwpps_plugin_slug) {
                $slug = $bwpps_plugin_slug['slug'];
                $old_slug = $old_value["b7epi_specials"][$i]['slug'];
                $url = $bwpps_plugin_slug['url'];
                $old_url = $old_value["b7epi_specials"][$i]['url'];

                if($slug != $old_slug || $url != $old_url){
                    $delete_slug_transient[]= true;
                    delete_transient($old_slug .'_plugin_icon_url');
                    set_transient($slug . '_plugin_icon_url', $url, $transient_expiration);
                }

                $i++;
            }
        }
    }

    /**
     * @param $image_url
     * @return mixed
     */
    public function _get_image_id($image_url) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));

        return $attachment[0];
    }
}