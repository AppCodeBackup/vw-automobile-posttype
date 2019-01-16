<?php
/*
 Plugin Name: VW Automobile Posttype
 Plugin URI: http://www.vwthemes.com/
 Description: Creating new post type for VW Automobile Theme.
 Author: VWThemes
 Version: 0.1
 Author URI: http://www.vwthemes.com/
*/

//custom car type
add_action( 'init','vw_automobile_posttype_create_car_type' );
add_action( 'init', 'vw_automobile_posttype_createcar', 0 );
add_action( 'createcar_add_form_fields', 'vw_automobile_posttype_taxonomy_add_new_meta_field', 10, 2 );
add_action( 'createcar_edit_form_fields', 'vw_automobile_posttype_taxonomy_edit_new_meta_field', 10, 2 );
add_action( 'edited_createcar', 'vw_automobile_posttype_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_createcar', 'vw_automobile_posttype_save_taxonomy_custom_meta', 10, 2 );
add_action( 'add_meta_boxes', 'vw_automobile_posttype_cs_custom_meta' );
add_action( 'save_post', 'vw_automobile_posttype_bn_meta_save' );
add_action('save_thumbnail','vw_automobile_posttype_bn_save_taxonomy_custom_meta');


function vw_automobile_posttype_create_car_type() {
  register_post_type( 'cars',
    array(
		'labels' => array(
			'name' => __( 'Cars','vw-automobile-posttype' ),
			'singular_name' => __( 'Cars','vw-automobile-posttype' ),
			'add_new_item' =>  __( 'Cars','vw-automobile-posttype' )
		),
		'menu_icon'  => 'dashicons-performance',
		'public' => true,
		'has_archive' => true,
        'supports' => array(
    	'title',
    	'thumbnail',
    	'revisions',
    	'editor',
        )
    )
  );
}

function vw_automobile_posttype_createcar() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => __( 'Car Brands', 'vw-automobile-posttype' ),
		'singular_name'     => __( 'Car Brand', 'vw-automobile-posttype' ),
		'search_items'      => __( 'Search Ccats','vw-automobile-posttype' ),
		'all_items'         => __( 'All car Categories','vw-automobile-posttype' ),
		'parent_item'       => __( 'Parent car Category','vw-automobile-posttype' ),
		'parent_item_colon' => __( 'Parent car Category:','vw-automobile-posttype' ),
		'edit_item'         => __( 'Edit car Category','vw-automobile-posttype' ),
		'update_item'       => __( 'Update car Category','vw-automobile-posttype' ),
		'add_new_item'      => __( 'Add New car Category','vw-automobile-posttype' ),
		'new_item_name'     => __( 'New car Category Name','vw-automobile-posttype' ),
		'menu_name'         => __( 'Car Brand','vw-automobile-posttype' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'vw_automobile_posttype_createcar' ),
	);

	register_taxonomy( 'vw_automobile_posttype_createcar', array( 'cars' ), $args );
}

/**
 * Add the new custom fields
 */
function vw_automobile_posttype_taxonomy_add_new_meta_field(){
	wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
	if(function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
    }else{
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }?>
	<div class="form-field">
        <img src="" title="Brand Thumbnail" alt="Brand Thumbnail" width="100" height="100" id="fetched_file">
	   	<input type="hidden" name="term_meta[thumbnail]" class="course_logo" id="term_meta[thumbnail]"><br>
	    <input class="upload_image_button button" name="course_files" id="user_file" type="button" value="Select/Upload Image"/>
	</div>
    <script>
    	jQuery(document).ready(function(){
	        jQuery('#user_file').click(function() { // Code for custom wp file upload
                wp.media.editor.send.attachment = function(props, attachment) {
                    jQuery('#fetched_file').attr("src",attachment.url);
                    jQuery('.course_logo').val(attachment.url);
                }  
                wp.media.editor.open(this);                     
	        });
        }); 
    </script>
    <?php
}

/**
 * Add the edit custom fields
 */
function vw_automobile_posttype_taxonomy_edit_new_meta_field( $term ){

    // put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$termMeta = get_option( "taxonomy_$t_id" );

  
    $thumbnail_url = esc_url($termMeta['thumbnail']);

    wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
	if(function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
    }else{
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }

    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[thumbnail]"><?php _e( 'Thumbnail','vw-automobile-posttype'); ?></label></th>
        <td>
        	<img src="<?php if(!empty($thumbnail_url)){ echo esc_url($thumbnail_url); } ?>" title="Brand Thumbnail" alt="Brand Thumbnail" width="100" height="100" id="fetched_file">
            <input type="hidden" name="term_meta[thumbnail]" class="course_logo" id="term_meta[thumbnail]" value="<?php if(!empty($thumbnail_url)){ echo esc_url($thumbnail_url); } ?>"> <br>
	    	<input class="upload_image_button button" name="course_files" id="user_file" type="button" value="Select/Upload Image"/>
        </td>
    </tr>
    <script>
    	jQuery(document).ready(function(){
	        jQuery('#user_file').click(function() { // Code for custom wp file upload
	                wp.media.editor.send.attachment = function(props, attachment) {
	                    jQuery('#fetched_file').attr("src",attachment.url);
	                    jQuery('.course_logo').val(attachment.url);
	                }  
	                wp.media.editor.open(this);                     
	        });
        }); 
    </script>
    <?php
}

/**
 * Save the taxonomy custom meta
 */
function vw_automobile_posttype_save_taxonomy_custom_meta($term_id){
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
		
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
    }
}

/* Adds a meta box to the post editing screen */
function vw_automobile_posttype_cs_custom_meta() {
    add_meta_box( 'cs_meta', __( 'Settings', 'vw-automobile-posttype' ),  'vw_automobile_posttype_cs_meta_callback' , 'cars');
}

/**
 * Outputs the content of the meta box
*/
function vw_automobile_posttype_cs_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'cs_nonce' );
    $cs_stored_meta = get_post_meta( $post->ID );
    ?>
	<div id="postcustomstuff">
		<div class="price">
	        <label for="price"><?php esc_html_e( 'Price','vw-automobile-posttype' ); ?></label>
	        <input type="text" name="minimum_price" id="minimum_price" value="<?php echo esc_html(get_post_meta($post->ID, "minimum_price", true)); ?>">											
    	</div>
    	<div class=" year">
	    	<label for="year"><?php esc_html_e( 'Make(Year)','vw-automobile-posttype' ); ?></label>
		    <input type="text" name="year" id="year" value="<?php echo esc_html(get_post_meta($post->ID, "year", true)); ?>">	
    	</div>
    	<div class="city">
	        <label for="city"><?php esc_html_e( 'city','vw-automobile-posttype' ); ?></label>
		    <input type="text" name="city" id="city" value="<?php echo esc_html(get_post_meta($post->ID, "city", true)); ?>">
        </div>
    	<div class="cc_field">
	        <label for="cc_field"><?php esc_html_e( 'cc_field','vw-automobile-posttype' ); ?></label>
		    <input type="text" name="cc" id="cc" value="<?php echo esc_html(get_post_meta($post->ID, "cc", true)); ?>">
        </div>
    	<div class="auto_field">
	        <label for="auto_field"><?php esc_html_e( 'auto_field','vw-automobile-posttype' ); ?></label>
		    <input type="text" name="auto" id="auto" value="<?php echo esc_html(get_post_meta($post->ID, "auto", true)); ?>">
        </div>                

	</div>
    <?php
}

/* Saves the custom meta input */
function vw_automobile_posttype_bn_meta_save( $post_id ) {

	// Save sequence of dripping for cars
	if( isset( $_POST[ 'sequence_of_dripping' ] )) {
	    update_post_meta( $post_id, 'sequence_of_dripping', $_POST[ 'sequence_of_dripping' ] );
	}
	// Save dripping time
	if( isset( $_POST[ 'dripping_time' ] )) {
	    update_post_meta( $post_id, 'dripping_time', $_POST[ 'dripping_time' ] );
	}

	if( isset( $_POST[ 'minimum_price' ] )) {
	    update_post_meta( $post_id, 'minimum_price', esc_attr($_POST[ 'minimum_price' ]));
	}

	if( isset( $_POST[ 'year' ] )) {
	    update_post_meta( $post_id, 'year', esc_attr($_POST[ 'year' ]));
	}
	if( isset( $_POST[ 'city' ] )) {
	    update_post_meta( $post_id, 'city', esc_attr($_POST[ 'city' ]));
	}
	if( isset( $_POST[ 'cc' ] )) {
	    update_post_meta( $post_id, 'cc', esc_attr($_POST[ 'cc' ]));
	}
	if( isset( $_POST[ 'auto' ] )) {
	    update_post_meta( $post_id, 'auto', esc_attr($_POST[ 'auto' ]));
	}
	if( isset( $_POST[ 'up_cars' ] )) {
	    update_post_meta( $post_id, 'up_cars', 1);
	}else{
		update_post_meta( $post_id, 'up_cars', 0);
	}
	if( isset( $_POST[ 'brand_zone' ] )) {
	    update_post_meta( $post_id, 'brand_zone', esc_attr($_POST[ 'brand_zone' ]));
	}
	if( isset( $_POST[ 'meta-radiotabs' ] ) ) {
		print_r($_POST[ 'meta-radiotabs' ]);
    	update_post_meta( $post_id, 'meta-radiotabs', $_POST[ 'meta-radiotabs' ] );
	}
}
/*---------------- Cars Shortcode ---------------------*/
function vw_automobile_posttype_cars_func( $atts ) {
    $cars = ''; 
    $cars = '<div id="featured-car-update" class="row about-inner">';
      $new = new WP_Query( array( 'post_type' => 'cars') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'small' );
          $url = $thumb['0'];
          $excerpt = vw_automobile_pro_string_limit_words(get_the_excerpt(),18);
          $price = get_post_meta(get_the_ID(),'minimum_price',true);
          $place = get_post_meta(get_the_ID(),'city',true);
          $cars .= '<div class="col-lg-4 col-md-4 col-sm-6 mt-4">
                <div class="images-car">
                  	<div class="row project-box">
	                    <div class="col-md-12 p-t-0 m-t-2">
	                      	<div class="projects-img">
	                        	<img src="'.$url.' " alt=""/>
	                      	</div>
	                       	<div class="featured-car_hover_sec">
	                        	<div class="row">
		                          <p class="col-md-6"> '.$price.'</p>
		                          <p class="col-md-6"><i class="fa fa-map-marker featured-map"></i>'.$place.'</p>
	                        	</div>
	                       	</div>
	                    </div> 
	                    <div class="col-md-12">
	                      	<div class="price-featured-car">
	                      		<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>              
	                        	<p>'.$excerpt.'</p>
	                      	</div>
	                    </div>                       
                  	</div>
                </div>
          	</div>
          <div class="clearfix"></div>';
          $k++;         
        endwhile;
  		wp_reset_postdata(); 
      	else :
        $cars = '<h5 class="text-center">'.__('Not Found','vw-automobile-posttype').'</h5>';
      	endif;
      	$cars.= '</div>';
    	return $cars;
}
add_shortcode( 'vw-automobile-pro-cars', 'vw_automobile_posttype_cars_func' );

			