<?php
/**
 * Plugin Name: Anylitics Only
 * Plugin URI: https://github.com/WolfEsq/analytics-only
 * Description: Adds Google Analytics tag. Configurable for any WordPress website from settings subpage.
 * Version: 2.1.0
 * Author: WolfEsq
 * Author URI: https://github.com/WolfEsq
 */


 class esq_google_analytics_page {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init_settings'  ) );

	}

	public function add_admin_menu() {

		add_options_page(
			esc_html__( 'Google Analytics', 'text_domain' ),
			esc_html__( 'Analytics', 'text_domain' ),
			'manage_options',
			'google-analytics',
			array( $this, 'esq_google_analytics_callback' )
		);

	}

	public function init_settings() {

		register_setting(
			'esq_google_analytics_settings',
			'esq_google_analytics_javascript'
		);

		add_settings_section(
			'esq_google_analytics_javascript_section',
			'',
			false,
			'esq_google_analytics_javascript'
		);

		add_settings_field(
			'google_analytics_script',
			__( 'Google Analytics Tracking ID', 'text_domain' ),
			array( $this, 'render_google_analytics_script_field' ),
			'esq_google_analytics_javascript',
			'esq_google_analytics_javascript_section'
		);

	}

	public function esq_google_analytics_callback() {

		// Check required user capability
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'text_domain' ) );
		}

		// Admin Page Layout
		echo '<div class="wrap">' . "\n";
		echo '	<h1>' . get_admin_page_title() . '</h1>' . "\n";
		echo '	<form action="options.php" method="post">' . "\n";

		settings_fields( 'esq_google_analytics_settings' );
		do_settings_sections( 'esq_google_analytics_javascript' );
		submit_button();

		echo '	</form>' . "\n";
		echo '</div>' . "\n";

	}

	function render_google_analytics_script_field() {

		// Retrieve data from the database.
		$options = get_option( 'esq_google_analytics_javascript' );

		// Set default value.
		$value = isset( $options['google_analytics_script'] ) ? $options['google_analytics_script'] : '';

		// Field output.
        echo '<input type="text" name="esq_google_analytics_javascript[google_analytics_script]" class="regular-text google_analytics_script_field" placeholder="' . esc_attr__( '', 'text_domain' ) . '" value="' . $value . '"></input>';
		echo '<p class="description">' . __( 'Paste in the Tracking ID provided by google for your website.', 'text_domain' ) . '</p>';

	}

}

new esq_google_analytics_page;


function esq_analytics_script_output () {

	// Retrieve data from the database.
	$options = get_option( 'esq_google_analytics_javascript' );

	// Set default value.
	$value = isset( $options['google_analytics_script'] ) ? $options['google_analytics_script'] : '';
	
	ob_start();
	?>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $value; ?>"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		
		gtag('config', '<?php echo $value; ?>');
	</script>

<?php
$content = ob_get_clean();
echo $content;   
}
add_action ( 'wp_head', 'esq_analytics_script_output' );
