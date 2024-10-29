<?php
   /*
   Plugin Name: Autotag Posts by SHIFT1
   Plugin URI: https://www.shift1.com/plugins/auto-tags/
   description: A plugin to automatically add tags to a blog post by scanning the post content and adding tags for all included phrases.
   Version: 1.0
   Author: Dennis Consorte
   Author URI: https://www.shift1.com
   License: GPL2
   */

   function shiftone_auto_tag_posts( $post_id ) {
	$append = true; //add tags to existing list for post

	$post_data = get_post($post_id);
	$new_tags = array();

	$title = $post_data->post_title;
	$content = $post_data->post_content;
	$title = " ${title} ";
	$content = " ${content} ";
	
	$title_clean = preg_replace("/[^a-zA-Z 0-9]+/", " ", $title);
	$content_clean = preg_replace("/[^a-zA-Z 0-9]+/", " ", strip_tags( preg_replace("`\[[^\]]*\]`"," ",$content) ));
	
	$all_tags = get_tags(array('hide_empty'=>false));
	foreach ( $all_tags as $tag ){
		$tag_name = $tag->name;
		$tag_name_padded = " ${tag_name} ";
		if ((stripos($title, $tag_name_padded) !== false) || (stripos($content, $tag_name_padded) !== false) || (stripos($title_clean, $tag_name_padded) !== false) || (stripos($content_clean, $tag_name_padded) !== false)) {
			$new_tags[] = $tag_name;
		}
	}
	if (count($new_tags) > 0) wp_set_post_tags( $post_id, $new_tags, $append );
}
  
// add checkbox to post update meta box

add_action( 'post_submitbox_misc_actions', 'shiftone_auto_tag_posts_field' );
function shiftone_auto_tag_posts_field()
{
    global $post;

    if (get_post_type($post) != 'post') return false;
	
	$autopop_tags = get_post_meta($post->ID, 'shiftone_autopopulate_tags', true);
	
    ?>
        <div class="misc-pub-section">
            <?php //if there is a value (1), check the checkbox ?>
            
			<label><input type="checkbox"<?php echo (!empty($value) ? ' checked="checked"' : null) ?> value="1" name="shiftone_autopopulate_tags" /> Autopopulate Tags</label>
			
        </div>
    <?php
}

add_action( 'save_post', 'shiftone_auto_tag_posts_save_postdata');
function shiftone_auto_tag_posts_save_postdata($postid)
{
	global $post;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;
    if ( !current_user_can( 'edit_page', $postid ) ) return false;
    if(empty($postid) || $_POST['post_type'] != 'post' ) return false;

    if(isset($_POST['shiftone_autopopulate_tags'])){
        shiftone_auto_tag_posts( $postid );
    }
    else{
        
    }
	
}   
   
   
   
   
/**
 * Add plugin action links.
 *
 * Add a link to the settings page on the plugins.php page.
 *
 * @since 1.0.0
 *
 * @param  array  $links List of existing plugin action links.
 * @return array         List of modified plugin action links.
 */
function shiftone_auto_tag_posts_action_links( $links ) {
	
/*
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>'
	), $links );
*/

$links = array_merge( array(
		'<a href="https://www.shift1.com">Get More Plugins</a>'
	), $links );
		
	
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'shiftone_auto_tag_posts_action_links' );
   
   
   
   
   
   
   
   
   
   
   
   
?>