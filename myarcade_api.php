<?php
/**
 * MyArcadePlugin Theme API  - Helps theme developers to create MyArcadePlugin compatible themes.
 *
 * @package MyArcadePlugin Theme API
 * @author Daniel Bakovic - http://myarcadeplugin.com
 *
 * @version 1.0.0
 */


/**
 * Display or retrieve the title of the current post/game. The title can be cutted after x characters. 
 * Words will not be cutted off (wordwrap).
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $chars Optional. Max. length of the title
 * @param bool $echo Optional. default to true. Whether to display or return.
 * @return string $title String if $echo parameter is false.
 */
function myarcade_title ($chars = 0, $echo = true) {
  
  $title = strip_tags( the_title('', '', FALSE) );
  
  if ( $chars > 0 ) {
    if ( (strlen($title) > $chars) ) {
      $title = mb_substr($title, 0, $chars);
      $title = mb_substr($title, 0, -strlen(strrchr($title, ' ')));  // Wordwrap
    
      if ( strlen($title) < 4 ) {
        $title = mb_substr( the_title('', '',FALSE), 0, $chars );
      }
    
      $title .= ' ..';
    }
  } 
  
  if ($echo == true) { echo $title; } else { return $title; }
}


/**
 * Display or retrieve the excerpt of a game post. All tags will be removed.
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $length Character length of the excerpt
 * @param bool $echo Optional. Return or echo the result
*/
function myarcade_excerpt($length = false, $echo = true) {
  global $post;
  
  // Get post excerpt
  $text = strip_shortcodes( $post->post_content );
  $text = apply_filters('the_content', $text);
  $text = str_replace(']]>', ']]&gt;', $text);
  $text = wp_trim_words( $text, 100, '' );  

  if ( $length ) {
    if ( strlen($text) > $length ) {
      $text = mb_substr($text, 0, $length).' [...]';
    }
  }
  
  if ($echo) { echo $text; } else { return $text; }
}


/**
 * Display the game thumbnail of the current game. 
 * If no thumbnail is available the function will display a default thumbnail located in the template directory.
 *
 * default thumb: /template_directory/images/def_thumb.png
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $width Optional. Width of the thumbnail in px. Default: 100
 * @param int $height Optional. Height of the thumbnail in px. Default: 100
 * @param string $class Optional. CSS class for the image tag
 */
function myarcade_thumbnail ($width = 100, $height = 100, $class = '') {
  global $post;
  
  if ( !empty($class) ) { $class = 'class="'.$class.'"'; }
  
  $thumbnail = get_post_meta($post->ID, "mabp_thumbnail_url", true);  
  
  if ( preg_match('|^(http).*|i', $thumbnail) == 0 ) {
    // No Thumbail available.. get the default thumb
    $thumbnail = get_bloginfo('template_directory').'/images/def_thumb.png';
  }
  
  $args = array( 'before' => '', 'after' => '', 'echo' => false );
  
  echo '<img src="'.$thumbnail.'" width="'.$width.'" height="'.$height.'" '.$class.' alt="'.the_title_attribute( $args ).'" />';
}


/**
 * Get the url of the current game thumbnail
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 */
function myarcade_get_thumbnail_url() {
  global $post;  
  return get_post_meta($post->ID, "mabp_thumbnail_url", true); 
}


/**
 * Get the number of available screenshots for the current game.
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @return int Number of screenshots
 */
function myarcade_count_screenshots () {
  global $post;
  
  $screen_count = 0;
  
  for ($screen_nr = 1; $screen_nr <= 4; $screen_nr++) {   
    if ( preg_match('|^(http).*|i', get_post_meta($post->ID, "mabp_screen".$screen_nr."_url", true)) ) {
      $screen_count++;
    }
  }
  
  return intval($screen_count);
}


/**
 * Display the given screenshot of the current game.
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $width Optional. Width of the screen shot in px. Default: 450
 * @param int $height Optional. Height of the screen shot in px. Default: 350
 * @param int $number Optional. The number of the screenshot (1..4). Default 1
 * @param string $class Optional. CSS class fot the image tag
 */
function myarcade_screenshot ($width = 450, $height = 300, $number = 1, $class = '') {
  global $post;
  
  if ( !empty($class) ) { $class = 'class="'.$class.'"'; }
  
  $screenshot = get_post_meta($post->ID, "mabp_screen".$number."_url", true);
  
  if ( preg_match('|^(http).*|i', $screenshot) ) {
    $args = array( 'before' => '', 'after' => '', 'echo' => false );
    echo '<img src="'.$screenshot.'"  width="'.$width.'" height="'.$height.'" '.$class.' alt="'.the_title_attribute( $args ).'" />';
  }
}


/**
 * Retrieves the screenshot url for the current game
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $number Optional. The number of the screenshot (1..4). Default 1
 * @param bool $echo Optional. Return or echo the result
 */
function myarcade_get_screenshot_url ($number = 1, $echo = true) {
  global $post;
     
  $screenshot = get_post_meta($post->ID, "mabp_screen".$number."_url", true);
  
  if ( $echo == true ) { echo $screenshot; } else { return $screenshot; }
}


/**
 * Display all available screenshots of the current game.
 *
 * @usage Use this function only in the WordPress post loop
 *
 * @since 1.0
 *
 * @param int $width Optional. Width of the screen shot in px. Default: 450
 * @param int $height Optional. Height of the screen shot in px. Default: 350
 * @param int $screen_nr Optional. The number of the screen (1..4). Default 1
 * @param string $class Optional. CSS class fot the image tag
 */
function myarcade_all_screenshots ($width = 450, $height = 300, $class = '') {
  global $post;
  
  $args = array( 'before' => '', 'after' => '', 'echo' => false );
  
  if ( !empty($class) ) { $class = 'class="'.$class.'"'; }  
  
  for ($screen_nr = 1; $screen_nr <= 4; $screen_nr++) {
    $screenshot = get_post_meta($post->ID, "mabp_screen".$screen_nr."_url", true);
    
    if ( preg_match('|^(http).*|i', $screenshot) ) {      
      echo '<a href="'.$screenshot.'" title="'.the_title_attribute( $args ).'" rel="lightbox"><img src="'.$screenshot.'"  width="'.$width.'" height="'.$height.'" '.$class.' alt="'.the_title_attribute( $args ).'" /></a>';
    }
  }
}
?>