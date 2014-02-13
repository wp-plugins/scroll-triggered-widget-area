<?php
require_once dirname( __FILE__ ) . '/includes/class.settings-api-stwa.php';
class stwa_Admin {
	protected static $instance = null;
	
	protected $plugin_screen_hook_suffix = null;

	private function __construct() {
		$plugin = Scroll_Triggered_Widget::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

        // Instantiates the Settings API
	    $this->settings_api = new stwa_Settings_API();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		
		add_action( 'admin_init', array( $this, 'admin_init' ) );		
	}

	/**
	 * Return an instance of this class.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *	 
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Scroll_Triggered_Widget::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *	 
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts( $hook_suffix) {

		wp_enqueue_style( 'wp-color-picker' );
    	wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    	wp_enqueue_script( 'jQuery-Cookie' );

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Scroll_Triggered_Widget::VERSION );
		}


	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *	 
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Scroll Triggered Widget Area', $this->plugin_slug ),
			__( 'Scroll Triggered Widget Area', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *	 
	 */
	public function display_plugin_admin_page() {		
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *	 
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}


	public function admin_init() {	
		 //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
			  		 
	}

	public function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'stwa_settings',
                'title' => __( '<h3 id="stwa-settings-title">Scroll Triggered Widget Area - Settings</h3>', 'stwa' )
            )           
        );
        return $sections;
    }

     function get_settings_fields() {
        $settings_fields = array(
            'stwa_settings' => array(    

                array(
                    'name' => 'stwa_show',
                    'label' => __( 'Restrict Visibility', 'stwa' ),
                    'desc' => __( 'Restrict visbility of the widget area to specific content types', 'stwa' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'page' => 'Page',
                        'post' => 'Post',
                        'frontpage' => 'FrontPage'                        
                    )
                ),           

                array(
                    'name' => 'stwa_placement',
                    'label' => __( 'Placement', 'stwa' ),
                    'desc' => __( 'Where to place the widget area? Left or Right?', 'stwa' ),
                    'type' => 'select',
                    'default' => 'right',
                    'options' => array(
                        'left' => 'Left',
                        'right' => 'Right'
                    )
                ),

                array(
                    'name' => 'stwa_width',
                    'label' => __( 'Width in px', 'stwa' ),
                    'desc' => __( 'Width of the widget area in px', 'stwa' ),
                    'type' => 'text',
                    'default' => '400',
                    'sanitize_callback' => 'intval'
                ),

                array(
                    'name' => 'stwa_display_height',
                    'label' => __( 'Display Height in px', 'stwa' ),
                    'desc' => __( 'How many px from bottom the widget area should appear?', 'stwa' ),
                    'type' => 'text',
                    'default' => '100',
                    'sanitize_callback' => 'intval'
                ),

                array(
                    'name' => 'stwa_animation',
                    'label' => __( 'Animation', 'stwa' ),
                    'desc' => __( 'Select the animation type', 'stwa' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(  
                       
                        "fadeIn fadeOut" => "fade",   
                        "fadeInUp fadeOutUp" => "fadeUp", 
                        "fadeInDown fadeOutDown" => "fadeDown",
                        "fadeInLeft fadeOutLeft"  => "fadeLeft" , 
                        "fadeInRight fadeOutRight" => "fadeRight", 
                        "fadeInUpBig fadeOutUpBig" => "fadeUpBig",
                        "fadeInDownBig fadeOutDownBig" => "fadeDownBig" , 
                        "fadeInLeftBig fadeOutLeftBig" => "fadeLeftBig",
                        "fadeInRightBig fadeOutRightBig" => "fadeRightBig", 
                        "bounceIn bounceOut" => "bounce", 
                        "bounceInUp bounceOutUp" => "bounceUp",
                        "bounceInDown bounceOutDown" => "bounceDown", 
                        "bounceInLeft bounceOutLeft" => "bounceLeft", 
                        "bounceInRight bounceOutRight" => "bounceRight",                       
                        "slideInDown slideOutUp" => "slide-down",
                        "slideInLeft slideOutLeft" => "slideLeft", 
                        "slideInRight slideOutRight" => "slideRight",
                        "rotateIn rotateOut" => "rotate", 
                        "rotateInDownLeft rotateOutDownLeft" => "rotateDownLeft",
                        "rotateInDownRight rotateOutDownRight" => "rotateDownRight", 
                        "rotateInUpLeft rotateOutUpLeft"  => "rotateUpLeft",
                        "rotateInUpRight rotateOutUpRight" => "rotateUpRight", 
                        "lightSpeedIn lightSpeedOut" => "lightSpeed",
                        "flipInX flipOutX" => "flip-X", 
                        "flipInY flipOutY" => "flip-Y",     
                        )
                ),

                array(
                    'name' => 'stwa_bgcolor',
                    'label' => __( 'Background Color', 'stwa' ),
                    'desc' => __( 'Set background color for the widget area.', 'stwa' ),
                    'type' => 'color',
                    
                ),

                array(
                    'name' => 'stwa_bordercolor',
                    'label' => __( 'Border Color', 'stwa' ),
                    'desc' => __( 'Set border color for the widget area.', 'stwa' ),
                    'type' => 'color',
                    
                ),

                array(
                    'name' => 'stwa_title_color',
                    'label' => __( 'Title Font Color', 'stwa' ),
                    'desc' => __( 'Set color for the widget title text.', 'stwa' ),
                    'type' => 'color',
                    
                ),                              

                array(
                    'name' => 'stwa_border_width',
                    'label' => __( 'Border width in px', 'stwa' ),
                    'desc' => __( 'Border width of the widget area in px', 'stwa' ),
                    'type' => 'text',
                    'default' => '2',
                    'sanitize_callback' => 'intval'
                ),

                array(
                    'name' => 'stwa_cookie',
                    'id' => 'stwa-cookie',
                    'label' => __( 'Cookie expiration time in days', 'stwa' ),
                    'desc' => __( 'How many days you wish the widget area to be hidden when closed', 'stwa' ),
                    'type' => 'text',
                    'default' => '1',
                    'sanitize_callback' => 'intval'
                )


                
            )
        );

        return $settings_fields;
    }

}
