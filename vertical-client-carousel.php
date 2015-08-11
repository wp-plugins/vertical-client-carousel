<?php
/*
Plugin Name: Vertical Client Carousel
Plugin URI: http://www.netattingo.com/
Description: This plugin will add vertical client carousel slider in your wordpress site.
Author: NetAttingo Technologies
Version: 1.0.0
Author URI: http://www.netattingo.com/
*/

define('WP_DEBUG',true);
define('REGISTRATION_PAGE_DIR', plugin_dir_path(__FILE__).'pages/');
define('REGISTRATION_INCLUDE_URL', plugin_dir_url(__FILE__).'includes/');

//Include menu
function vccs_logo_plugin_menu() {
	add_menu_page("", "", "administrator", "vertical_client_setting", "vccs_logo_plugin_pages", '' ,30);
	add_submenu_page("edit.php?post_type=vertical_client", "Carousel Setting", "Carousel Setting", "administrator", "carousel-page-setting", "vccs_logo_plugin_pages");
	add_submenu_page("edit.php?post_type=vertical_client", "About Us", "About Us", "administrator", "about-us", "vccs_logo_plugin_pages");
}

add_action("admin_menu", "vccs_logo_plugin_menu");
function vccs_logo_plugin_pages() {

   $itm = REGISTRATION_PAGE_DIR.$_GET["page"].'.php';
   include($itm);
}

//add admin css
function vccs_admin_css() {
  wp_register_style('admin_css', plugins_url('includes/admin-style.css',__FILE__ ));
  wp_enqueue_style('admin_css');
}

add_action( 'admin_init','vccs_admin_css');


function vccs_slider_trigger(){
	//include carousel css and js
	wp_enqueue_style('vccs_caro_css_and_js', REGISTRATION_INCLUDE_URL."front-style.css", false, "1.0", "all"); 
	wp_register_script('vccs_caro_css_and_js', REGISTRATION_INCLUDE_URL."jquery.jcarousellite.js" );
	wp_enqueue_script('vccs_caro_css_and_js');
?>
<?php
}
add_action('wp_footer','vccs_slider_trigger');



// Add Shortcode
function vccs_shortcode( $atts ) {
	//getting all settings
	$auto_slide= get_option('vccs_auto_slide');
	$slide_speed= get_option('vccs_slide_speed');

	//if setting is naull then initial setting
	if($auto_slide == ''){ $auto_slide= 'false';}
	if($slide_speed == ''){ $slide_speed= 1000;}
	
	// Attributes
	extract( shortcode_atts(
		array(
			'posts' => "-1",
			'order' => '',
			'orderby' => '',
			'title' => 'yes',
		), $atts )
	);
	
	
	query_posts(array('orderby' => 'date', 'order' => 'DESC' , 'showposts' => $posts, 'post_type' => 'vertical_client'));
	$return_string ='<div id="vccs-carousel">';
	if (have_posts()) {

		$return_string .='<div class="custom-container vertical">';
		$return_string .='<a href="#" class="prev">&lsaquo;</a>';
		$return_string .='<div class="carousel">';
		$return_string .=	'<ul>';				

		while (have_posts()) : the_post();
			$post_id = get_the_ID();
			$logo_id = get_post_thumbnail_id();
			$logo_url = wp_get_attachment_image_src($logo_id,'full',true);
			$logo_mata = get_post_meta($logo_id,'_wp_attachment_image_alt',true);
			// Client Link
			$logo_link = get_post_meta( $post_id, 'vccs_logo_url', true );
				
			$return_string .= 	'<li>';
			if($logo_link) : 
			   $return_string .= '<a href="'.$logo_link.'">'; // client url
			endif;
			$return_string .=  '<img  src="'. $logo_url[0] .'" alt="'. $logo_mata .'" />';
			if($logo_link) :
			   $return_string .=  '</a>'; // client url end
			endif;
			$return_string .= 	'</li>';		
		endwhile;
		wp_reset_query();
						
		$return_string .=	'</ul>';
		$return_string .='</div>';
		$return_string .='<a href="#" class="next">&rsaquo;</a>';
		$return_string .=	'<div class="clear"></div>';
		$return_string .=	'</div>';

	}else{
	$return_string .=  '<strong>No client yet.</strong>';
	}

	$return_string .= '  </div>';
	$return_string .=' <script type="text/javascript">
		jQuery(function() {
			jQuery(".vertical .carousel").jCarouselLite({
				btnNext: ".vertical .next",
				btnPrev: ".vertical .prev",
				auto:'.$auto_slide.',
				speed: '.$slide_speed.',
				vertical: true
			});
		});
	</script>';
	  
return $return_string;

}
add_shortcode( 'vertical-client-carousel', 'vccs_shortcode' );

// Register logo Custom Post Type
function vccs_post_type() {

	$labels = array(
		'name'                => _x( 'Vertical Client', 'Post Type General Name', '' ),
		'singular_name'       => _x( 'Vertical Client', 'Post Type Singular Name', '' ),
		'menu_name'           => __( 'Vertical Client ', '' ),
		'parent_item_colon'   => __( 'Parent Client :', '' ),
		'all_items'           => __( 'All Clients', '' ),
		'view_item'           => __( 'View Client ', '' ),
		'add_new_item'        => __( 'Add New Client ', '' ),
		'add_new'             => __( 'Add New Client', '' ),
		'edit_item'           => __( 'Edit Client ', '' ),
		'update_item'         => __( 'Update Client ', '' ),
		'search_items'        => __( 'Search Client ', '' ),
		'not_found'           => __( 'Not found', '' ),
		'not_found_in_trash'  => __( 'Not found in Trash', '' ),
	);
	$args = array(
		'label'               => __( 'vertical_client', '' ),
		'description'         => __( 'Client Slider post type.', '' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 80,
		'menu_icon'           => 'dashicons-images-alt',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'vertical_client', $args );

}

// Hook into the 'init' action
add_action( 'init', 'vccs_post_type', 0 );


//set featured image in normal  position   
add_action('do_meta_boxes', 'vccs_featured_image_move_meta_box');
function vccs_featured_image_move_meta_box(){
    remove_meta_box( 'postimagediv', 'vertical_client', 'side' );
    add_meta_box('postimagediv', __('Client Image'), 'post_thumbnail_meta_box', 'vertical_client', 'normal', 'high');
}



// Fire our meta box setup function on the post editor screen. 
add_action( 'load-post.php', 'vccs_meta_boxes_setup' );
add_action( 'load-post-new.php', 'vccs_meta_boxes_setup' );

// Meta box setup function. /
function vccs_meta_boxes_setup() {
  // Add meta boxes on the 'add_meta_boxes' hook. /
  add_action( 'add_meta_boxes', 'vccs_add_logo_meta_boxes' );

  // Save post meta on the 'save_post' hook. 
  add_action( 'save_post', 'vccs_save_logo_class_meta', 10, 2 );
}

// Create one or more meta boxes to be displayed on the post editor screen. 
function vccs_add_logo_meta_boxes() {

  add_meta_box(
    'tls-logo-url',      // Unique ID
    esc_html__( 'Client URL', '' ),    // Title
    'vccs_logo_url_meta_box',   // Callback function
    'vertical_client',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
  );
}


// Display the logo meta box. 
function vccs_logo_url_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'vccs_logo_url_nonce' ); ?>

  <p>
    <label for="tls-logo-url"><?php _e( "Client Website Url ", '' ); ?></label>
    <br />
    <input style="max-width:470px;" class="widefat" type="text" name="tls-logo-url" id="tls-logo-url" value="<?php echo esc_attr( get_post_meta( $object->ID, 'vccs_logo_url', true ) ); ?>"/>
  </p>
<?php } 



// Save the meta box's post metadata. 
function vccs_save_logo_class_meta( $post_id, $post ) {

  // Verify the nonce before proceeding. 
  if ( !isset( $_POST['vccs_logo_url_nonce'] ) || !wp_verify_nonce( $_POST['vccs_logo_url_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  // Get the post type object. 
  $post_type = get_post_type_object( $post->post_type );

  // Check if the current user has permission to edit the post. 
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  // Get the posted data and sanitize it for use as an HTML class. 
  $new_meta_value = sanitize_text_field($_POST['tls-logo-url']);

  // Get the meta key. /
  $meta_key = 'vccs_logo_url';

  // Get the meta value of the custom field key. 
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  // If a new meta value was added and there was no previous value, add it. 
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  // If the new meta value does not match the old value, update it.
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  // If there is no new meta value but an old value exists, delete it. 
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

// Add the posts and pages columns filter. They can both use the same function.
add_filter('manage_posts_columns', 'vccs_add_post_thumbnail_column', 5);

// Add the column
function vccs_add_post_thumbnail_column($cols){
  $cols['vccs_post_thumb'] = __('Client Image');
  return $cols;
}

// Hook into the posts an pages column managing. 
add_action('manage_posts_custom_column', 'vccs_display_post_thumbnail_column', 5, 2);
   
// Grab featured-thumbnail size post thumbnail and display it.
function vccs_display_post_thumbnail_column($col, $id){
  switch($col){
	case 'vccs_post_thumb':
	  if( function_exists('the_post_thumbnail') )
	  echo the_post_thumbnail( array(100, 100) );

	  else
		echo 'No Image';
	  break;
  }
}
?>