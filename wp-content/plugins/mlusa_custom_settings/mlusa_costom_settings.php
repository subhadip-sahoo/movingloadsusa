<?php
/* 
 * Plugin Name: MLUSA Custom Settings
 * Plugin URI: http://qss.in/
 * Description: Moving Loads USA Custom Settings options.
 * Version: 1.0
 * Author: Quintessential Software Solutions Private Limited (QSS)
 * Author URI: http://qss.in/
 * Licence: GPL2
*/
class MLUSA_Custom_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Moving Loads USA Custom Settings', 
            'MLUSA Custom Settings', 
            'manage_options', 
            'mlusa_settings', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'mlusa_custom_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>MLUSA Custom Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'mlusa_custom_options_group' );   
                do_settings_sections( 'mlusa_settings' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'mlusa_custom_options_group', // Option group
            'mlusa_custom_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'mlusa_settings' // Page
        );  

        add_settings_field(
            'trail_period', // ID
            "Free Trial Period (In Days)", // Title 
            array( $this, 'trail_period_callback' ), // Callback
            'mlusa_settings', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'paypal_email_address', 
            'PayPal Email Address', 
            array( $this, 'paypal_email_address_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'paypal_environment', 
            'Payment Environment', 
            array( $this, 'paypal_environment_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        ); 
        add_settings_field(
            'paypal_subscription_amount', 
            'Subscription Amount (In USD)', 
            array( $this, 'paypal_subscription_amount_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        ); 
        add_settings_field(
            'paypal_subscription_period_no', 
            'Subscription Period', 
            array( $this, 'paypal_subscription_period_no_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'braintree_merchant_ID', 
            'Braintree Merchant ID', 
            array( $this, 'braintree_merchant_ID_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'braintree_private_key', 
            'Braintree Private Key', 
            array( $this, 'braintree_private_key_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'braintree_public_key', 
            'Braintree Public Key', 
            array( $this, 'braintree_public_key_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'braintree_plan_ID', 
            'Braintree Plan ID',
            array( $this, 'braintree_plan_ID_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
        add_settings_field(
            'braintree_plan_name', 
            'Braintree Plan Name', 
            array( $this, 'braintree_plan_name_callback' ), 
            'mlusa_settings', 
            'setting_section_id'
        );
//        add_settings_field(
//            'email_format', 
//            'Custom Email Format', 
//            array( $this, 'email_format_callback' ), 
//            'mlusa_settings', 
//            'setting_section_id'
//        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['trail_period'] )){
            $new_input['trail_period'] = absint( $input['trail_period'] );
        }

        if( isset( $input['paypal_email_address'] ) )
            $new_input['paypal_email_address'] = sanitize_text_field( $input['paypal_email_address'] );
        
        if( isset( $input['paypal_environment'] ) )
            $new_input['paypal_environment'] = sanitize_text_field( $input['paypal_environment'] );
        
        if( isset( $input['paypal_subscription_amount'] ) )
            $new_input['paypal_subscription_amount'] = sanitize_text_field( $input['paypal_subscription_amount'] );
        
        if( isset( $input['paypal_subscription_period_no'] ) )
            $new_input['paypal_subscription_period_no'] = absint( $input['paypal_subscription_period_no'] );
         
        if( isset( $input['paypal_subscription_period_days'] ) )
            $new_input['paypal_subscription_period_days'] = sanitize_text_field( $input['paypal_subscription_period_days'] );
        
        if( isset( $input['braintree_merchant_ID'] ) )
            $new_input['braintree_merchant_ID'] = $input['braintree_merchant_ID'];
        
        if( isset( $input['braintree_private_key'] ) )
            $new_input['braintree_private_key'] = $input['braintree_private_key'];
        
        if( isset( $input['braintree_public_key'] ) )
            $new_input['braintree_public_key'] = $input['braintree_public_key'];
        
        if( isset( $input['braintree_plan_ID'] ) )
            $new_input['braintree_plan_ID'] = $input['braintree_plan_ID'];
        
        if( isset( $input['braintree_plan_name'] ) )
            $new_input['braintree_plan_name'] = $input['braintree_plan_name'];
        
//        if( isset( $input['email_format'] ) )
//            $new_input['email_format'] =  $input['email_format'];
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        //print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function trail_period_callback()
    {
        printf(
            '<input type="number" id="trail_period" name="mlusa_custom_options[trail_period]" value="%s" step="1"/> <br/><p class="description">Only put the number in days. Example: 30</p>',
            isset( $this->options['trail_period'] ) ? esc_attr( $this->options['trail_period']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function paypal_email_address_callback()
    {
        printf(
            '<input type="email" id="paypal_email_address" name="mlusa_custom_options[paypal_email_address]" value="%s" size="50"/><br/><p class="description">Be sure that this is the correct paypal email address. All transactions will occure through this email id.</p>',
            isset( $this->options['paypal_email_address'] ) ? esc_attr( $this->options['paypal_email_address']) : ''
        );
    }
    public function paypal_environment_callback(){
    ?>
        <input type="radio" id="paypal_environment" name="mlusa_custom_options[paypal_environment]" value="production" <?php if(isset($this->options['paypal_environment']) && esc_attr( $this->options['paypal_environment']) == 'production') {echo 'checked';}?>/>Production 
        <input type="radio" id="paypal_environment" name="mlusa_custom_options[paypal_environment]" value="sandbox" <?php if(isset($this->options['paypal_environment']) && esc_attr( $this->options['paypal_environment']) == 'sandbox') {echo 'checked';}?>/> Sandbox
    <?php
    }
    public function paypal_subscription_amount_callback(){
        printf(
            '<input type="number" id="paypal_subscription_amount" name="mlusa_custom_options[paypal_subscription_amount]" value="%s" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"/><br/><p class="description">Only put the amount upto two decimal number. Example: 100.50</p>',
            isset( $this->options['paypal_subscription_amount'] ) ? esc_attr( $this->options['paypal_subscription_amount']) : ''
        );
    }
    public function paypal_subscription_period_no_callback(){ ?>
            <input type="number" id="paypal_subscription_period_no" name="mlusa_custom_options[paypal_subscription_period_no]" value="<?php echo isset( $this->options['paypal_subscription_period_no'] ) ? esc_attr( $this->options['paypal_subscription_period_no']) : '';?>" step="1"/>
            <select id="paypal_subscription_period_days" name="mlusa_custom_options[paypal_subscription_period_days]">
                <option value="D" <?php if(isset($this->options['paypal_subscription_period_days']) && esc_attr( $this->options['paypal_subscription_period_days']) == 'D') {echo 'selected="selected"';}?>>Day</option>
                <option value="W" <?php if(isset($this->options['paypal_subscription_period_days']) && esc_attr( $this->options['paypal_subscription_period_days']) == 'W') {echo 'selected="selected"';}?>>Week</option>
                <option value="M" <?php if(isset($this->options['paypal_subscription_period_days']) && esc_attr( $this->options['paypal_subscription_period_days']) == 'M') {echo 'selected="selected"';}?>>Month</option>
                <option value="Y" <?php if(isset($this->options['paypal_subscription_period_days']) && esc_attr( $this->options['paypal_subscription_period_days']) == 'Y') {echo 'selected="selected"';}?>>Year</option>
            </select>
            <br/><p class="description">Only put the number. Example: 1 Day / 1 Month etc.</p>
    <?php
    }
    public function paypal_subscription_period_days_callback(){
        printf(
            '<input type="number" id="paypal_subscription_period_days" name="mlusa_custom_options[paypal_subscription_period_days]" value="%s" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"/><br/><p class="description">Only put the amount upto two decimal number. Example: 100.50</p>',
            isset( $this->options['paypal_subscription_period_days'] ) ? esc_attr( $this->options['paypal_subscription_period_days']) : ''
        );
    }
    
//     public function email_format_callback(){
//        printf(
//            '<textarea id="email_format" name="mlusa_custom_options[email_format]" rows="10" cols="60">%s</textarea>
//                <br/><p class="description">These variables can be used: [user_name], [user_login], [user_pass]</p>',
//            isset( $this->options['email_format'] ) ? esc_attr( $this->options['email_format']) : ''
//        );
//    }
    public function braintree_merchant_ID_callback()
    {
        printf(
            '<input type="text" id="braintree_merchant_ID" name="mlusa_custom_options[braintree_merchant_ID]" value="%s" size="50"/>',
            isset( $this->options['braintree_merchant_ID'] ) ? esc_attr( $this->options['braintree_merchant_ID']) : ''
        );
    }
    public function braintree_private_key_callback()
    {
        printf(
            '<input type="text" id="braintree_private_key" name="mlusa_custom_options[braintree_private_key]" value="%s" size="50"/>',
            isset( $this->options['braintree_private_key'] ) ? esc_attr( $this->options['braintree_private_key']) : ''
        );
    }
    public function braintree_public_key_callback()
    {
        printf(
            '<input type="text" id="braintree_public_key" name="mlusa_custom_options[braintree_public_key]" value="%s" size="50"/>',
            isset( $this->options['braintree_public_key'] ) ? esc_attr( $this->options['braintree_public_key']) : ''
        );
    }
    public function braintree_plan_ID_callback()
    {
        printf(
            '<input type="text" id="braintree_plan_ID" name="mlusa_custom_options[braintree_plan_ID]" value="%s" size="50"/>',
            isset( $this->options['braintree_plan_ID'] ) ? esc_attr( $this->options['braintree_plan_ID']) : ''
        );
    }
    public function braintree_plan_name_callback()
    {
        printf(
            '<input type="text" id="braintree_plan_name" name="mlusa_custom_options[braintree_plan_name]" value="%s" size="50"/>',
            isset( $this->options['braintree_plan_name'] ) ? esc_attr( $this->options['braintree_plan_name']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new MLUSA_Custom_Settings();
?>