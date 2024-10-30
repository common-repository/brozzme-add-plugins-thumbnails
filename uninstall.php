<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 21/06/2017
 * Time: 10:09
 */


if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function brozzme_plugins_thumbnails_plugin_uninstall(){

    delete_option('b7epi_settings');
    if((is_multisite() || is_network_admin()) && current_user_can('install_plugins')){
        delete_site_option('b7epi_settings');
        delete_site_option('b7epi_my_plugin_thumbnails');
    }

    /* delete plugin transient */

    //require_once(admin_url() . '/plugins.php');

    $plugins = get_plugins();

    foreach ($plugins as $plugin){
            $plugin_folder = explode('/', $plugin);
            $plugin_slug = $plugin_folder[0];
            delete_transient($plugin_slug . '_plugin_icon_url');
    }
}