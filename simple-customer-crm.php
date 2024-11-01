<?php
/*
* Plugin Name: Simple Customer CRM
* Plugin URI:  https://wordpress.org/plugins/simple-customer-crm/
* Description: The Plugin will collect customer data and build customer profiles
inside of the client’s WordPress Dashboard.
* Version: 1.0.0
* Author: Tristup Ghosh
* Author URI: http://www.tristupghosh.com
* Text Domain: simple_customer_crm
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
{
  exit;
}
if ( ! defined( 'SIMPLE_CUSTOMER_CRM_VERSION' ) ) {
    define( 'SIMPLE_CUSTOMER_CRM_VERSION', '1.0.0' );
}
if ( ! defined( 'SIMPLE_CUSTOMER_CRM_PLUGIN_CSS_URI' ) ) {
    define( 'SIMPLE_CUSTOMER_CRM_PLUGIN_CSS_URI', plugins_url( 'css/',__FILE__ ) );
}
if ( ! defined( 'SIMPLE_CUSTOMER_CRM_PLUGIN_JS_URI' ) ) {
    define( 'SIMPLE_CUSTOMER_CRM_PLUGIN_JS_URI', plugins_url( 'js/',__FILE__ ) );
}
if ( ! defined( 'SIMPLE_CUSTOMER_CRM_PLUGIN_IMAGE_URI' ) ) {
    define( 'SIMPLE_CUSTOMER_CRM_PLUGIN_IMAGE_URI', plugins_url( 'images/',__FILE__ ) );
}
class Simple_Customer_Crm
{
  function __construct()
  {
    add_action('wp_enqueue_scripts', array($this,'simple_customer_crm_scripts'),50);
    add_shortcode("sccrm",array($this,"simple_customer_crm_callback")); 
    add_action( 'init', array($this,'simple_customer_crm_cpt'), 10 ); //customer post type
    add_action( 'init', array($this,'simple_customer_crm_taxonomies'), 11 );
    /****** Adding Meta Boxes to Post Type ****/
    add_action( 'add_meta_boxes_sccrm_customer', array($this,'simple_customer_crm_meta_boxes') );
    add_action( 'save_post', array($this,'simple_customer_crm_metabox_save_details'), 10, 2 );
    
    /****** Adding New Columns to the list **/
    add_filter ( 'manage_edit-sccrm_customer_columns', array($this,'simple_customer_crm_columns') );
    /****** Making Custom Column Sortable **/
    add_filter( 'manage_edit-sccrm_customer_sortable_columns', array($this,'simple_customer_crm_sortable_columns') );
    /****** Showing Custom data in Column ***/
    add_action( 'manage_sccrm_customer_posts_custom_column', array($this,'simple_customer_crm_custom_columns'), 10, 2 );
    
    /****** Removed View Options ***/
    add_filter( 'post_row_actions', array($this,'simple_customer_crm_remove_row_actions'), 10, 1 );

    add_action( 'wp_ajax_submitCustomerForm', array( $this, 'simple_customer_crm_submitCustomerForm' ) ); 
    add_action( 'wp_ajax_nopriv_submitCustomerForm', array( $this, 'simple_customer_crm_submitCustomerForm' ) );
  }//end of function
  function simple_customer_crm_scripts() 
  {
    if (!wp_style_is( 'bootstrap', 'enqueued' ))
    {
        wp_enqueue_style( 'bootstrap', SIMPLE_CUSTOMER_CRM_PLUGIN_CSS_URI . 'bootstrap.min.css',array(),SIMPLE_CUSTOMER_CRM_VERSION,'screen');
    }
    wp_enqueue_style( 'simple_customer_crm-main-css', SIMPLE_CUSTOMER_CRM_PLUGIN_CSS_URI. 'main.css',array(),SIMPLE_CUSTOMER_CRM_VERSION,'screen' );
    wp_enqueue_script( 'jquery-validation', SIMPLE_CUSTOMER_CRM_PLUGIN_JS_URI . 'jquery.validate.js',array('jquery'),SIMPLE_CUSTOMER_CRM_VERSION );
    wp_enqueue_script( 'simple_customer_crm-main-script', SIMPLE_CUSTOMER_CRM_PLUGIN_JS_URI . 'main.js',array('jquery-validation'),SIMPLE_CUSTOMER_CRM_VERSION );
    wp_localize_script( 'simple_customer_crm-main-script', 'sccrmsettings', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
  }//end of function
  function simple_customer_crm_callback($atts)
  {
      extract( shortcode_atts( array(
        'form_title'=>"Customer Form",
        /** NAME FIELD ***/
        'name_field_label'=>"Name",
        'name_field_min_length'=>"",
        'name_field_max_length'=>"",
        /** PHONE FIELD **/
        'phone_field_label'=>"Phone No.",
        'phone_field_min_length'=>"",
        'phone_field_max_length'=>"",
        /** Email FIELD **/
        'email_field_label'=>"Email Address",
        /** Budget FIELD **/
        'budget_field_label'=>"Your Budget",        
        /** Message FIELD **/
        'message_field_label'=>"Your Message",
        'message_field_height'=>"3",   //rows
        'message_field_width'=>"50",   //cols
        /** Submit **/
        'submit_field_label'=>"Send Data"
      ), $atts,'sccrm' ) );
      /**** TO GET UTC DATE &TIME **/
      $response=wp_remote_get('http://worldclockapi.com/api/json/utc/now');
      $current_date_time='';
      if ( !is_wp_error( $response ) ) 
      {
        $current_date_time_json=json_decode(wp_remote_retrieve_body( $response ));        
        $current_date_time=$current_date_time_json->currentDateTime; 
      }
      if(!is_admin())
      {
        require "template/customer-form.php";        
      }
  }//end of function
  function simple_customer_crm_cpt()
  {
      $labels = array(
        'name' => _x( 'Customers', 'simple_customer_crm' ),
        'singular_name' => _x( 'Customer', 'simple_customer_crm' ),
        'edit_item' => _x( 'Edit Customer', 'simple_customer_crm' ),
        'add_new' => _x( 'Add New Customer', 'simple_customer_crm' ),
        'add_new_item' => _x( 'Add New Customer', 'simple_customer_crm' ),
        'new_item' => _x( 'New Customer', 'simple_customer_crm' ),
        'view_item' => _x( 'View Customer', 'simple_customer_crm' ),
        'search_items' => _x( 'Search Customer', 'simple_customer_crm' ),
        'not_found' => _x( 'No Customer found', 'simple_customer_crm' ),
        'not_found_in_trash' => _x( 'No Customer found in Trash', 'simple_customer_crm' ),
        'menu_name' => _x( 'Customers', 'simple_customer_crm' ),
      );
      $args = array(
          'labels'        => $labels,
          'public'        => true,
          'publicly_queryable'  => true,
          'show_ui'         => true,
          'query_var'       => true,
          'rewrite'         => apply_filters( 'sccrm_customer_cpt_rewrite_args', array( 'slug' => 'cpt', 'with_front' => false ) ),
          'hierarchical'      => false,
          'menu_position'     => null,
          'exclude_from_search' => true,
          'menu_icon'       =>'dashicons-groups',      
          'supports'        => array( 'title','editor'),
          'capability_type'     => 'post',
          'capabilities' => array(
              'create_posts' => false, //revoked add new option
          ),
          'map_meta_cap'       => true
      );
      register_post_type( 'sccrm_customer', $args ); 
  }//end of function

  function simple_customer_crm_taxonomies()
  {
      /**** Adding Category to Customer Post Type ****/
      $labels = array(
        'name'        => _x( 'Customer Categories', 'taxonomy general name', 'simple_customer_crm' ),
        'singular_name'   => _x( 'Category', 'taxonomy singular name', 'simple_customer_crm' ),
        'search_items'    => __( 'Search Categories', 'simple_customer_crm' ),
        'all_items'     => __( 'All Categories', 'simple_customer_crm' ),
        'parent_item'     => __( 'Parent Category', 'simple_customer_crm' ),
        'parent_item_colon' => __( 'Parent Category:', 'simple_customer_crm' ),
        'edit_item'     => __( 'Edit Category', 'simple_customer_crm' ),
        'update_item'     => __( 'Update Category', 'simple_customer_crm' ),
        'add_new_item'    => __( 'Add New Customer Category', 'simple_customer_crm' ),
        'new_item_name'   => __( 'New Category Name', 'simple_customer_crm' ),
        'menu_name'     => __( 'Categories', 'simple_customer_crm' )
      );
      register_taxonomy( 'customer_category', array('sccrm_customer'), array(
        'hierarchical'  => true,
        'labels'    => $labels,
        'show_ui'     => true,
        'query_var'   => true,
        'rewrite'     => apply_filters( 'sccrm_customer_customer_category_rewrite_args', array( 'slug' => 'customer' ) )
      ));
      /**** Adding Tag to Customer Post Type ****/
      $labels = array(
        'name'        => _x( 'Customer Tags', 'taxonomy general name', 'simple_customer_crm' ),
        'singular_name'   => _x( 'Tag', 'taxonomy singular name', 'simple_customer_crm' ),
        'search_items'    => __( 'Search Tags', 'simple_customer_crm' ),
        'all_items'     => __( 'All Tags', 'simple_customer_crm' ),
        'parent_item'     => __( 'Parent Tag', 'simple_customer_crm' ),
        'parent_item_colon' => __( 'Parent Tag:', 'simple_customer_crm' ),
        'edit_item'     => __( 'Edit Tag', 'simple_customer_crm' ),
        'update_item'     => __( 'Update Tag', 'simple_customer_crm' ),
        'add_new_item'    => __( 'Add New Customer Tag', 'simple_customer_crm' ),
        'new_item_name'   => __( 'New Tag Name', 'simple_customer_crm' ),
        'menu_name'     => __( 'Tags', 'simple_customer_crm' )
      );
      register_taxonomy( 'customer_tag', array('sccrm_customer'), array(
        'hierarchical'  => true,
        'labels'    => $labels,
        'show_ui'     => true,
        'query_var'   => true,
        'rewrite'     => apply_filters( 'et_customer_tag_rewrite_args', array( 'slug' => 'customer' ) )
      ));
  }//end of fucntion
  function simple_customer_crm_meta_boxes()
  {
     add_meta_box('customer_personal_meta', __( 'Personal Informations', 'simple_customer_crm' ), array($this,'simple_customer_crm_meta_callback'), 'sccrm_customer'); 
  }//end of function
  function simple_customer_crm_meta_callback($post)
  {
      wp_nonce_field( 'customer_personal_meta_box', 'customer_personal_meta_box_nonce' ); 
      /*** Phone Field */
      $outline='<div class="customer_phone">';
        $outline.='<label for="sccrm_customer_phone" style="width:150px; display:inline-block;">'. esc_html__('Customer Phone No.', 'simple_customer_crm') .'</label>';
        $sccrm_customer_phone = get_post_meta( $post->ID, 'sccrm_customer_phone', true );
        $outline.= '<input type="text" name="sccrm_customer_phone" id="sccrm_customer_phone" class="title_field" value="'. esc_attr($sccrm_customer_phone) .'" style="width:300px;"/>';
      $outline.='</div>';
      /*** EMail Field*/
      $outline.='<div class="customer_email">';
        $outline.='<label for="sccrm_customer_email" style="width:150px; display:inline-block;">'. esc_html__('Customer Email.', 'simple_customer_crm') .'</label>';
        $sccrm_customer_email = get_post_meta( $post->ID, 'sccrm_customer_email', true );
        $outline.= '<input type="text" name="sccrm_customer_email" id="sccrm_customer_email" class="title_field" value="'. esc_attr($sccrm_customer_email) .'" style="width:300px;"/>';
      $outline.='</div>';
      /*** Budget Field */
      $outline.='<div class="sccrm_customer_budget">';
        $outline.='<label for="sccrm_customer_budget" style="width:150px; display:inline-block;">'. esc_html__('Customer Budget.', 'simple_customer_crm') .'</label>';
        $sccrm_customer_currency = get_post_meta( $post->ID, 'sccrm_customer_currency', true );
        $sccrm_customer_budget = get_post_meta( $post->ID, 'sccrm_customer_budget', true );
        $outline.= '<select name="sccrm_customer_currency" id="sccrm_customer_currency">';
          $outline.= '<option value="usd" '.selected( $sccrm_customer_currency, 'usd', false).'>'.esc_html__('USD','simple_customer_crm').' &dollar;</option>';
          $outline.= '<option value="euro" '.selected( $sccrm_customer_currency, 'euro', false ).'>'.esc_html__('Euro','simple_customer_crm').' &euro;</option>';
        $outline.= '</select>';
        $outline.= '<input type="text" name="sccrm_customer_budget" id="sccrm_customer_budget" class="title_field" value="'. esc_attr($sccrm_customer_budget) .'" style="width:300px;"/>';
      $outline.='</div>';     

      echo $outline;
  }//end of function
  function simple_customer_crm_metabox_save_details( $post_id, $post )
  {
      global $pagenow;  
      // Check if our nonce is set.
      if ( ! isset( $_POST['customer_personal_meta_box_nonce'] ) ) {
        return $post_id;
      }
      if ( ! wp_verify_nonce( $_POST['customer_personal_meta_box_nonce'], 'customer_personal_meta_box' ) ) {
        return $post_id;
      }
      if ( 'post.php' != $pagenow ) return $post_id;

      if ( 'sccrm_customer' != $post->post_type ){
        return $post_id;
      }
      if ( in_array( $_POST['post_type'], array( 'sccrm_customer' ) ) ) 
      {

        if ( isset( $_POST['sccrm_customer_phone'] ) && !empty( $_POST['sccrm_customer_phone'] ) )
        {
          update_post_meta( $post_id, 'sccrm_customer_phone', sanitize_text_field($_POST['sccrm_customer_phone'] ));
        }
        else
        {
          delete_post_meta( $post_id, 'sccrm_customer_phone' );       
        }

        if ( isset( $_POST['sccrm_customer_email'] ) && !empty( $_POST['sccrm_customer_email'] ) )
        {
          update_post_meta( $post_id, 'sccrm_customer_email', sanitize_email($_POST['sccrm_customer_email'] ));
        }
        else
        {
          delete_post_meta( $post_id, 'sccrm_customer_email' );       
        }

        if ( isset( $_POST['sccrm_customer_currency'] ) && !empty( $_POST['sccrm_customer_currency'] ) )
        {
          update_post_meta( $post_id, 'sccrm_customer_currency', sanitize_text_field($_POST['sccrm_customer_currency'] ));
        }
        else
        {
          delete_post_meta( $post_id, 'sccrm_customer_currency' );       
        }

        if ( isset( $_POST['sccrm_customer_budget'] ) && !empty( $_POST['sccrm_customer_budget'] ) )
        {
          update_post_meta( $post_id, 'sccrm_customer_budget', sanitize_text_field($_POST['sccrm_customer_budget'] ));
        }
        else
        {
          delete_post_meta( $post_id, 'sccrm_customer_budget' );       
        }

      }
  
  }//end of function

  function simple_customer_crm_columns ( $columns ) 
  {
    unset($columns['title']); //removing title column from existing column array
    unset($columns['date']);  //removing date column from existing column array
    return array_merge ( $columns, array ( 
       'title' => esc_html__('Name','simple_customer_crm'),
       'phone' => esc_html__( 'Phone Number','simple_customer_crm' ),
       'email'   => esc_html__( 'Email Address','simple_customer_crm' ),
       'customer_date' => esc_html__('Date','simple_customer_crm')
    ) );
    return $columns;
  }//end of function
  function simple_customer_crm_sortable_columns( $columns ) 
  {
    $columns['phone'] = 'phone'; //adding phone as sortable
    $columns['email'] = 'email'; //adding email as sortable
    $columns['customer_date'] = 'customer_date'; //adding email as sortable
    return $columns;
  } //end of function
  function simple_customer_crm_custom_columns($column, $post_id )
  {
    global $post;
    switch( $column ) 
    {
        case 'phone' :
            /*** Get the post meta for phone number ***/
            $phone = get_post_meta( $post_id, 'sccrm_customer_phone', true );
            /* If no phone no. is found, output a default message. */
            if ( empty( $phone ) )
            {              
              echo esc_html__( 'Unknown','simple_customer_crm' );
            }
            else
            {
              printf( __( '%s','simple_customer_crm' ), $phone );
            }
            break;

        case 'email' :
            /*** Get the post meta for email ***/
            $email = get_post_meta( $post_id, 'sccrm_customer_email', true );
            /* If no email is found, output a default message. */
            if ( empty( $email ) )
            {              
              echo esc_html__( 'Unknown' ,'simple_customer_crm');
            }
            else
            {
              printf( __( '%s','simple_customer_crm' ), $email );
            }
            break;
        case 'customer_date' :
            /*** Get the post meta for email ***/
            $customer_date_utc = get_post_meta( $post_id, 'sccrm_customer_create_date', true );
            $customer_date_time = date("Y-m-d H:i:s",  strtotime($customer_date_utc));

            /* If no email is found, output a default message. */
            if ( empty( $customer_date_time ) )
            {              
              echo esc_html__( 'Unknown' ,'simple_customer_crm');
            }
            else
            {
              printf( __( '%s','simple_customer_crm' ), $customer_date_time );
            }
            break;

    }//end of switch

  }//end of function
  /**** To remove view link ***/
  function simple_customer_crm_remove_row_actions( $actions )
  {
      if( get_post_type() === 'sccrm_customer' )
      {
          unset( $actions['view'] );
      }
      return $actions;
  }//end of function
  /**** AJAX Funtion called **/
  function simple_customer_crm_submitCustomerForm()
  {
    parse_str($_POST['formData'], $formData);
    $post_id = wp_insert_post(array (
        'post_type' => 'sccrm_customer',
        'post_title' => sanitize_text_field($formData['sccrm_customer_name']),
        'post_content' => sanitize_text_field($formData['sccrm_customer_message']),
        'post_status' => 'private',     //status set private
        'comment_status' => 'closed',   // closed comment
        'ping_status' => 'closed',      // ping option closed
    ));

    if ($post_id) {
        // insert post meta
        add_post_meta($post_id, 'sccrm_customer_phone', sanitize_text_field($formData['sccrm_customer_phone']));
        add_post_meta($post_id, 'sccrm_customer_email', sanitize_text_field($formData['sccrm_customer_email']));
        add_post_meta($post_id, 'sccrm_customer_currency',sanitize_text_field( $formData['sccrm_customer_currency']));
        add_post_meta($post_id, 'sccrm_customer_budget', sanitize_text_field($formData['sccrm_customer_budget']));
        add_post_meta($post_id, 'sccrm_customer_create_date', sanitize_text_field($formData['sccrm_customer_create_date']));
    }
    die();
  }//end of function
    
}//end of class
new Simple_Customer_Crm();


