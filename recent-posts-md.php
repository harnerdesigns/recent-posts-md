<?php
   /*
   Plugin Name: Recent Posts Markdown
   Plugin URI: https://harnerdesigns.com
   description: Generate a markdown list of recent wordpress posts.
   Version: 0.1
   Author: Harner Designs
   Author URI: https://harnerdesigns.com
   License: GPL2
   */
// Hook for adding admin menus
add_action('admin_menu', 'recentpostmd_add_pages');

function recentpostmd_add_pages(){

add_management_page( __('Recent Posts Markdown','recentpostmd'), __('Recent Posts Markdown','recentpostmd'), 'manage_options', 'recentpostmd', 'recentpostmd_tools_page');



}


function recentpostmd_tools_page() {

   if(isset($_POST['recentpostmd']) && !empty($_POST['recentpostmd'])){

         $recentposts = new WP_Query([
      'posts_per_page' => $_POST['count'],
      'post_type'      => $_POST['postType'],
      'post_status' => 'publish',
      'order'=>'DESC',
                    'orderby'=>'date',
   ]);



         if($recentposts->have_posts()){
            $markdownContent = "## Recent Posts" .  PHP_EOL;
            while ($recentposts->have_posts()){
               $recentposts->the_post();



               $markdownContent .= "* " . recentpostmd_markdown_link(get_the_title(), get_the_permalink()) . PHP_EOL;

               
            }


         }

   }



    echo "<h1>" . __( 'Recent Posts Markdown Generator', 'recentpostmd' ) . "</h1>";

    $types =  recentpostmd_get_post_types();
    echo "<form method='post' id='recentpostmd'>";
    
    echo "<h2>What Post Type</h2>";


    echo "<select name='postType'>";
    foreach($types as $type){

      echo sprintf("<option %s value='%s'>%s</option>",($type->name == $_POST['postType'] ? "selected" : ''), $type->name, $type->label);
    }
    echo "</select>";

    echo "<h2>How Many Posts</h2>";
    echo "<input type='number' name='count' min=1 value=".(isset($_POST['count']) ? $_POST['count'] : '3').">";
    echo "<input type='submit' name='recentpostmd' value='Get Markdown'>";

    echo "</form>";

    if($markdownContent){

      echo "<textarea>" . $markdownContent . "</textarea>";

    }

}

function recentpostmd_get_post_types() {

$args = array(
   "public" => true);
   return get_post_types($args, 'objects');
}

function recentpostmd_markdown_link($text, $url){


   return sprintf("[%s](%s)", $text, $url);

}