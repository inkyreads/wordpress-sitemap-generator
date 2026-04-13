<?php
/**
 * Plugin Name: Inky Sitemap Pro
 * Plugin URI:  https://inkyreads.com/tools/sitemap-generator
 * Description: A high-performance, XML sitemap generator for wordpress websites
 * Version:     1.2.4
 * Author:      Inky Labs
 * Author URI:  https://inkyreads.com
 * License:     GPL2
 */

if (!defined('ABSPATH')) exit;

/**
 * Register the sitemap query var
 */
add_filter('query_vars', function($vars) {
    $vars[] = 'inky_sitemap';
    return $vars;
});

/**
 * Create the rewrite rule so it looks professional: yoursite.com/inky-sitemap.xml
 */
add_action('init', function() {
    add_rewrite_rule('^inky-sitemap\.xml$', 'index.php?inky_sitemap=1', 'top');
});

/**
 * Flush rewrite rules on activation to ensure the .xml URL works immediately
 */
register_activation_hook(__FILE__, 'flush_rewrite_rules');

/**
 * The Generator Engine
 */
add_action('template_redirect', function() {
    if (get_query_var('inky_sitemap')) {
        header('Content-Type: application/xml; charset=utf-8');
        
        $posts = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 500, // Professional default
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?xml-stylesheet type="text/xsl" href="' . includes_url('css/main-sitemap.xsl') . '"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($posts as $post) {
            echo '<url>';
            echo '<loc>' . get_permalink($post->ID) . '</loc>';
            echo '<lastmod>' . get_the_modified_date('c', $post->ID) . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }

        echo '</urlset>';
        exit;
    }
});
