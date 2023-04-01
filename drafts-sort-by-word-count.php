<?php
/**
 * Plugin Name: Drafts Sort By Word Count
 * Plugin URI: https://github.com/pixlflip/wp-plugin-sort-drafts-by-word-count
 * Description: This plugin sorts drafts in the draft tab of the posts page by word count.
 * Version: 1.0.0
 * Author: PixlFlip
 * Author URI: https://www.pixlflip.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function ds_sort_drafts_by_word_count($query) {
    if (is_admin() && $query->is_main_query() && $query->get('post_status') === 'draft') {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', '_word_count');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'ds_sort_drafts_by_word_count');

function ds_save_word_count($post_id) {
    $post = get_post($post_id);
    if ($post->post_status === 'draft') {
        $word_count = str_word_count(strip_tags($post->post_content));
        update_post_meta($post_id, '_word_count', $word_count);
    }
}
add_action('save_post', 'ds_save_word_count', 10, 1);

function ds_add_word_count_column($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['word_count'] = __('Word Count', 'drafts-sorter');
        }
    }
    return $new_columns;
}
add_filter('manage_posts_columns', 'ds_add_word_count_column');

function ds_display_word_count_column($column_name, $post_id) {
    if ($column_name === 'word_count') {
        $word_count = get_post_meta($post_id, '_word_count', true);
        echo intval($word_count);
    }
}
add_action('manage_posts_custom_column', 'ds_display_word_count_column', 10, 2);

