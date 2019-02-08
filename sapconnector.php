<?php

/*
  Plugin Name: SAP Conversational AI connector
  Description: Connect your SAP Conversational AI chatbot with Wordpress
  Author: SAP Conversational AI
  Version: 1.0.0
*/

class Smashing_Fields_Plugin {
    public function __construct() {
      add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
      add_action( 'admin_init', array( $this, 'setup_sections' ) );
      add_action( 'admin_init', array( $this, 'setup_fields' ) );
    }
    public function create_plugin_settings_page() {
      $page_title = 'SAP Conversational AI chatbot connector - Settings Page';
      $menu_title = 'Bot connector';
      $capability = 'manage_options';
      $slug = 'smashing_fields';
      $callback = array( $this, 'plugin_settings_page_content' );
      $icon = 'dashicons-admin-plugins';
      $position = 100;
      add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }
    public function plugin_settings_page_content() {?>
      <div class="wrap">
        <h2>SAP Conversational AI chatbot connector - Settings Page</h2><?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  $this->admin_notice();
            } ?>
        <form method="POST" action="options.php">
                <?php
                    settings_fields( 'smashing_fields' );
                    do_settings_sections( 'smashing_fields' );
                    submit_button();
                ?>
        </form>
      </div> <?php
    }
    
    public function admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>Your settings have been updated!</p>
        </div><?php
    }
    public function setup_sections() {
        add_settings_section( 'our_first_section', "Welcome to your chatbot's connector settings page. Don't worry, it'll be easy!", array( $this, 'section_callback' ), 'smashing_fields' );
    }
    public function section_callback( $arguments ) {
      switch( $arguments['id'] ){
        case 'our_first_section':
          echo "We strongly advise you to avoid any code modification. We take care of everything.<br/>Just go on your SAP Conversational AI bot's connector page, choose Webchat > Wordpress in the Connect tab and follow the steps!";
          break;
      }
    }
    public function setup_fields() {
        $fields = array(
          array(
            'uid' => 'channelid',
            'label' => 'Channel ID',
            'section' => 'our_first_section',
            'type' => 'text',
            'placeholder' => 'Some text',
            'supplimental' => 'See the Connect > Webchat > Wordpress tab on your SAP CAI chatbot page to get your Channel ID',
          ),
      
        array(
            'uid' => 'tokenid',
            'label' => 'Token ID',
            'section' => 'our_first_section',
            'type' => 'text',
            'placeholder' => 'Some text',  
            'supplimental' => 'See the Connect > Webchat > Wordpress tab on your SAP CAI chatbot page to get your Token ID',
          ),  
      
          array(
            'uid' => 'awesome_checkboxes',
            'label' => 'Where does your chatbot should appear?',
            'section' => 'our_first_section',
            'type' => 'checkbox',
            'options' => array(
            'option1' => 'Index',
            'option2' => 'Articles',
            'option3' => 'Pages',
            'option4' => 'Archives (author, category,...)',
            'option5' => 'Search results',
            ),
            'default' => array()
          ),

           array(
            'uid' => 'awesome_checkboxes2',
            'label' => 'Do you want your chabot to be mobile responsive?',
            'section' => 'our_first_section',
            'type' => 'radio',
            'options' => array(
            'option1' => 'Yes',
            'option2' => 'No',
            ),
            'default' => array(),
            'supplimental' => "Is you choose 'Yes', your chatbot will be full screen on mobile. This option may already be activated on your website. Check your template's documentation.<br/>If you're not sure
            which option to choose, use the 'No' button.",
          )
        );
      foreach( $fields as $field ){
          add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'smashing_fields', $field['section'], $field );
            register_setting( 'smashing_fields', $field['uid'] );
      }
    }
    public function field_callback( $arguments ) {

        $value = get_option( $arguments['uid'] );
        if( ! $value ) {
            $value = $arguments['default'];
        }
        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $attributes = '';
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper );
        }
        if( $supplimental = $arguments['supplimental'] ){
            printf( '<p class="description">%s</p>', $supplimental );
        }
    }
}

new Smashing_Fields_Plugin();

$moncodebot = '<script src="https://cdn.cai.tools.sap/webchat/webchat.js" channelId="'.get_option('channelid').'" token="'.get_option('tokenid').'" id="cai-webchat"></script>';

if(in_array("option1", get_option('awesome_checkboxes2'))) { 
      function hook_css() {
          ?>
              <meta name="viewport" content="width=device-width"> 
          <?php
      }
      add_action('wp_head', 'hook_css');
}

if(in_array("option1", get_option('awesome_checkboxes'))) {
      add_action("wp_footer", "mfp_Add_Text1");  
      function mfp_Add_Text1()
          {
            if(is_home()) {
            echo $GLOBALS["moncodebot"];
          }}}

if(in_array("option2", get_option('awesome_checkboxes'))) {
      add_action("wp_footer", "mfp_Add_Text");  
      function mfp_Add_Text()
          {
            if(is_single()) {
            echo $GLOBALS["moncodebot"];
          }}}

if(in_array("option3", get_option('awesome_checkboxes'))) {
      add_action("wp_footer", "mfp_Add_Text3");  
      function mfp_Add_Text3()
          {
            if(is_page()) {
             echo $GLOBALS["moncodebot"];
          }}}

if(in_array("option4", get_option('awesome_checkboxes'))) {
      add_action("wp_footer", "mfp_Add_Text4");  
      function mfp_Add_Text4()
          {
            if(is_archive()) {
            echo $GLOBALS["moncodebot"];
          }}}

if(in_array("option5", get_option('awesome_checkboxes'))) {
      add_action("wp_footer", "mfp_Add_Text5");  
      function mfp_Add_Text5()
          {
            if(is_search()) {
            echo $GLOBALS["moncodebot"];
          }}}

?>
