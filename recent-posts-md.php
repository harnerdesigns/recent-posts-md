<?php
/*
Plugin Name: Recent Posts Markdown
Plugin URI: https://github.com/harnerdesigns/recent-posts-md/
description: Generate a markdown list of recent wordpress posts.
Version: 0.1
Author: Harner Designs
Author URI: https://harnerdesigns.com
License: GPL2
 */

class RecentPostMD
{

    public function __construct()
    {
// Hook for adding admin menus

        add_action('admin_menu', array($this, 'add_pages'));
        add_action('admin_enqueue_scripts', array($this, 'add_css'));

    }

    public function add_css()
    {

        wp_enqueue_style("style", plugins_url("recent-posts-md.css", __FILE__));
        wp_enqueue_script("js", plugins_url("recent-posts-md.js", __FILE__), "jquery");

    }

    public function add_pages()
    {

        add_management_page(__('Recent Posts Markdown', 'recentpostmd'), __('Recent Posts Markdown', 'recentpostmd'), 'manage_options', 'recentpostmd', array($this, 'tools_page'));

    }

    public function tools_page()
    {

        if (isset($_POST['recentpostmd']) && !empty($_POST['recentpostmd'])) {

            $recentposts = new WP_Query([
                'posts_per_page' => $_POST['count'],
                'post_type' => $_POST['postType'],
                'post_status' => 'publish',
                'order' => 'DESC',
                'orderby' => 'date',
            ]);

            if ($recentposts->have_posts()) {
                $markdownContent = "## Recent Posts" . PHP_EOL;
                while ($recentposts->have_posts()) {
                    $recentposts->the_post();

                    $markdownContent .= "* " . $this->markdown_link(get_the_title(), get_the_permalink()) . PHP_EOL;

                }

            } else {

                $markdownContent = "No Posts";
            }

        }

        echo "<h1>" . __('Recent Posts Markdown Generator', 'recentpostmd') . "</h1>";

        $types = $this->get_post_types();
        echo "<form method='post' id='recentpostmd'>";
        echo "<section class='half'>";
        echo "<h2>What Post Type</h2>";

        echo "<select name='postType'>";
        foreach ($types as $type) {

            echo sprintf("<option %s value='%s'>%s</option>", ($type->name == $_POST['postType'] ? "selected" : ''), $type->name, $type->label);
        }
        echo "</select>";
        echo "</section>";

        echo "<section class='half'>";

        echo "<h2>How Many Posts</h2>";
        echo "<input type='number' name='count' min=1 value=" . (isset($_POST['count']) ? $_POST['count'] : '3') . ">";
        echo "</section>";
        echo "<input type='submit' name='recentpostmd' value='Get Markdown'>";

        echo "</form>";

        if ($markdownContent) {

            echo "<textarea id='markdownContent'>" . $markdownContent . "</textarea>";

        }

        echo "<h5 class='finePrint'>Tool Developed by <a href='https://harnerdesigns.com/?utm_source=recent-posts-md' target='_blank'>Harner Designs</a> | <a href='https://github.com/harnerdesigns/recent-posts-md' target='_blank'>Plugin Github</a> | <a href='https://harnerdesigns.com/support-us?utm_source=recent-posts-md' target='_blank'>Buy Us A Beer</a></h5>";

    }

    public function get_post_types()
    {

        $args = array(
            "public" => true);
        return get_post_types($args, 'objects');
    }

    public function markdown_link($text, $url)
    {

        return sprintf("[%s](%s)", $text, $url);

    }
}

$recentPost = new RecentPostMD;
