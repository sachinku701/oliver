<?php
defined( 'ABSPATH' ) || exit;
/**
 * All about installation process of oliver pos bridge plugin
 */
class Pos_Bridge_Install {
    /**
     * Initiate AJAX routes.
     */

    public static function oliver_pos_init() {
        add_action( 'init', array( __CLASS__, 'oliver_pos_install' ), 5 );
        /* Register AJAX routes*/
        add_action('wp_ajax_oliver_pos_try_connect', array(__CLASS__, 'oliver_pos_try_connect'));
        add_action('wp_ajax_oliver_pos_disconnect_subscription', array(__CLASS__, 'oliver_pos_disconnect_subscription'));
        add_action('wp_ajax_oliver_pos_delete_subscription', array(__CLASS__, 'oliver_pos_delete_subscription'));
        add_action('admin_menu', array(__CLASS__, 'oliver_pos_create_admin_menu'));
        add_action('wp_ajax_oliver_pos_deactivate_plugin', array(__CLASS__, 'oliver_pos_deactivate_plugin'));
        //Since 2.3.9.3
        add_action('wp_ajax_oliver_pos_system_check', array(__CLASS__, 'oliver_pos_system_check'));
        add_action('wp_ajax_oliver_pos_register_url', array(__CLASS__, 'oliver_pos_register_url'));
        add_action('wp_ajax_oliver_pos_view_plans', array(__CLASS__, 'oliver_pos_view_plans'));
        /* Register AJAX routes*/
    }

    /**
     * Install Oliver POS.
     */
    public static function oliver_pos_install() {
        self::oliver_pos_create_options();
    }
    public static function oliver_pos_create_options() {
        # used for create options
    }

    /**
     * Create menu page in wordpress admin panel.
     */

    public static function oliver_pos_create_admin_menu() {
        // add menu
        add_menu_page('Oliver POS Bridge', 'Oliver POS Bridge', 'manage_options', 'oliver-pos', array( __CLASS__, 'oliver_pos_load_menu_view' ), plugins_url('public/resource/img/oliver_icon_121.png', dirname(__FILE__)),32);
        // add submenu for menu
        add_submenu_page('oliver-pos' ,'Dashboard', 'Dashboard', 'manage_options', 'oliver-pos',array( __CLASS__, 'oliver_pos_load_menu_view' ));
    }

    public static function oliver_pos_load_menu_view() {
        return require(dirname(__FILE__) . '/views/backend/create-subscription-new.php');
    }
    public static function oliver_pos_try_connect() {
        $site_address = home_url();
        // data to be sent
        if (!get_option('oliver_pos_authorization_token')) {
            self::oliver_pos_set_authorization_token($site_address);
        }
        $token = get_option('oliver_pos_authorization_token');

        // url to call
        $url = ASP_TRY_CONNECT;
        $version = OLIVER_POS_PLUGIN_VERSION_NUMBER;
        $esc_url = esc_url_raw("{$url}?source={$site_address}&token={$token}&version={$version}");
        oliver_log("Try connect url = " . $esc_url);
        // Get cURL resource
        $wp_remote_get = wp_remote_get($esc_url, array(
            'timeout'     => 120,
            'redirection' => 1,
        ));

        if ( is_wp_error( $wp_remote_get ) ) {
            $response = json_encode(array("Message" => $wp_remote_get->get_error_message()));
            oliver_log("Something went wrong: $response");
        } else {
            $response = wp_remote_retrieve_body($wp_remote_get);
            oliver_log("Not occur wp_error");

            if (wp_remote_retrieve_response_code($wp_remote_get) == 200) {
                $decode_response = json_decode( wp_remote_retrieve_body($wp_remote_get) );
                if ( $decode_response->is_success ) {
                    if ( ! empty($decode_response->content) && is_object($decode_response->content)) {
                        $content = $decode_response->content;

                        /*
                        * client id and token used for base 64 authorization
                        * oliver_pos_subscription_email = clientId (super admin)
                        * oliver_pos_subscription_udid = clientId (super admin) for all API
                        * oliver_pos_subscription_token = server_token (super admin)
                        */
                        update_option('oliver_pos_subscription_email', sanitize_text_field($content->client_id), false); //it is client id
                        update_option('oliver_pos_subscription_udid',  sanitize_text_field($content->udid), false);  //it is client id
                        update_option('oliver_pos_subscription_token', sanitize_text_field($content->server_token), false); //it is client token

                        if (isset($content->auth_token) && ! empty($content->auth_token)) {
                            update_option('oliver_pos_subscription_autologin_token', sanitize_text_field($content->auth_token), false); //This token used for auto login get from super admin
                            oliver_log("oliver_pos_subscription_autologin_token = " . get_option("oliver_pos_subscription_autologin_token"));
                        }

                        oliver_log("oliver_pos_subscription_email = " . get_option("oliver_pos_subscription_email"));
                        oliver_log("oliver_pos_subscription_token = " . get_option("oliver_pos_subscription_token"));
                        oliver_log("oliver_pos_subscription_udid = " . get_option("oliver_pos_subscription_udid"));

                        /**
                         * Trigger the service if plugin version chenged
                         * @since 2.3.0.9
                         */
                        if( ! get_option( 'oliver_pos_previouse_version' ) ){
                            add_option( 'oliver_pos_previouse_version', OLIVER_POS_PLUGIN_VERSION_NUMBER );
                            self::oliver_pos_trigger_update_version_number();
                        } else {
                            if (get_option( 'oliver_pos_previouse_version' ) != OLIVER_POS_PLUGIN_VERSION_NUMBER) {
                                update_option( 'oliver_pos_previouse_version', OLIVER_POS_PLUGIN_VERSION_NUMBER );
                                self::oliver_pos_trigger_update_version_number();
                            }
                        }

                        /**
                         * Trigger the service after try connect
                         * @since 2.3.3.1
                         */
                        self::oliver_pos_trigger_bridge_details();
                    }
                }
            }
        }
        print_r($response);
        exit();
    }

    public static function oliver_pos_disconnect_subscription() {
        $url = esc_url_raw( ASP_TRY_DISCONNECT );
        oliver_log("disconnect_subscription = {$url}");

        // Get cURL resource
        wp_remote_get($url, array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => false,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
        exit();
    }

    public static function oliver_pos_delete_subscription() {
        oliver_log("delete_subscription");
        // bridge
        delete_option( 'oliver_pos_authorization_token' );

        // super admin
        delete_option( 'oliver_pos_subscription_udid' );
        delete_option( 'oliver_pos_subscription_email' );
        delete_option( 'oliver_pos_subscription_token' );
        delete_option( 'oliver_pos_subscription_autologin_token' );

        echo json_encode( array(
            'status'   => true,
            'message'  => 'success',
        ) );

        exit();
    }

    public static function oliver_pos_deactivate_plugin() {
        delete_option( 'pos_bridge_plugin_do_deactivation_redirection' );
        deactivate_plugins( '/oliver-pos/oliver-pos.php' );
        //@since 2.3.8.3
        $all_installed_plugins = get_plugins();
        $op_plugin_slug = 'oliver-pos-points-and-rewards/oliver-pos-points-and-rewards.php';
        if(array_key_exists( $op_plugin_slug, $all_installed_plugins ))
        {
            deactivate_plugins( '/oliver-pos-points-and-rewards/oliver-pos-points-and-rewards.php' );
        }
        echo json_encode(array( 'status'   => true, 'message'  => 'success' ));
        exit;
    }

    public static function oliver_pos_set_authorization_token($url) {
        $rand = $url . mt_rand(); //generates a random integer using the Mersenne Twister algorithm.
        $token = md5($rand); //calculates the MD5 hash of a string.
        update_option( 'oliver_pos_authorization_token', sanitize_text_field($token));
    }

    /**
     * Trigger the service if plugin version chenged
     * @since 2.3.0.9
     * @return void call ASP.Net API's
     */
    public static function oliver_pos_trigger_update_version_number() {
        $method = ASP_TRIGGER_CHANGE_BRIDGE_VERSION;
        $version = OLIVER_POS_PLUGIN_VERSION_NUMBER;
        $client_id = get_option("oliver_pos_subscription_email");
        $url = esc_url_raw("{$method}?clientId={$client_id}&version={$version}");
        oliver_log("trigger_update_version_number = {$url}");

        // Get cURL resource
        wp_remote_get($url, array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => false,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
    }

    /**
     * Trigger the service after try connect
     * @since 2.3.3.1
     * @return void call ASP.Net API's
     */
    public static function oliver_pos_trigger_bridge_details() {
        $method = ASP_TRIGGER_BRIDGE_DETAILS;
        $client_id = get_option("oliver_pos_subscription_email");
        $url = esc_url_raw("{$method}?clientId={$client_id}");
        oliver_log("trigger_bridge_details = {$url}");

        // Get cURL resource
        wp_remote_get($url, array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => false,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
    }

    public static function get_authorization_token() {
        return get_option( 'oliver_pos_authorization_token' );
    }
    //Since 2.3.9.3
    public static function oliver_pos_system_check() {
        $validateVersionUrl = ASP_VALIDATE_VERSION;
        $noErrors ='';
        $issueCount =0;
        $noPermalink='yes';
        $localhost= 'no';
        //permalinks check
        $permalinksSettings =  get_option('permalink_structure');
        if($permalinksSettings=='')
        {
            $noPermalink='notset';
            $issueCount =$issueCount + 1;
            $noErrors='yes';
        }
        //SSL check
        $stream = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
        $siteUrl =  get_home_url();
        $read = fopen($siteUrl , "rb", false, $stream);
        $cont = stream_context_get_params($read);
        $varSsl = ($cont["options"]["ssl"]["peer_certificate"]);
        $sslResult = (!is_null($varSsl)) ? 'yes' : 'no';
        if($sslResult=='no')
        {
            $issueCount =$issueCount + 1;
            $noErrors='yes';
        }
        //Localhost check
        $server_type = $_SERVER['REMOTE_ADDR'];
        $pri_addrs = array (
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.1|127.255.255.255','::1' //Localhost
        );
        if(in_array( $_SERVER['REMOTE_ADDR'], $pri_addrs))
        {
            $localhost= 'localhost';
            $issueCount =$issueCount + 1;
            $noErrors='yes';
        }
        //hard bloker and soft bloker version and plugins api
        $SoftBlokerPlugins='';
        $HardBlokerPlugins='';
        $SoftVersions='';
        $HardVersions='';
	    $SoftBloker='';
	    $HardBloker='';
        $responceHub = wp_remote_get( $validateVersionUrl, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
        if ( is_wp_error($responceHub)) {
            $responceHubData = json_encode(array("Message" => $responceHub->get_error_message()));
            oliver_log("Something went wrong: $responceHubData");
        }
        else
        {
            if (wp_remote_retrieve_response_code($responceHub) == 200)
            {
                $responceHubData = json_decode(wp_remote_retrieve_body($responceHub));
                if ($responceHubData->is_success )
                {
                    if (!empty($responceHubData->content) && is_object($responceHubData->content))
                    {
                        $responceContent = $responceHubData->content;
                        $SoftBlokerPlugins = $responceContent->SoftBlokerPlugins;
                        $HardBlokerPlugins = $responceContent->HardBlokerPlugins;
                        $SoftVersions = $responceContent->SoftVersion;
                        $HardVersions = $responceContent->HardVersion;
                        if(!empty($HardBlokerPlugins)){
                            $HardBlokerPluginsCount = count($HardBlokerPlugins);
                            if(!empty($HardBlokerPluginsCount)){
                                $issueCount =$issueCount + $HardBlokerPluginsCount;
                                $noErrors='yes';
                            }
                        }
                        if(!empty($SoftBlokerPlugins)){
                            $noErrors='yes';
                        }
                        if(!empty($HardVersions)){
                            $HardVersionsCount = count($HardVersions);
                            if(!empty($HardVersionsCount)){
                                $issueCount =$issueCount + $HardVersionsCount;
                                $noErrors='yes';
                            }
                        }
                        if(!empty($SoftVersions)){
                            $noErrors='yes';
                        }
                        $SoftBlokerPlugins = array_merge($SoftBlokerPlugins,$SoftVersions);
                        if(!empty($SoftBlokerPlugins))
                        {
                            foreach($SoftBlokerPlugins as $SoftBlokerPlugin)
                            {
                                $SoftBloker .= '<li class="op-error-warning op-error-res"><div><span class="triangle"></span>'. $SoftBlokerPlugin->Messages. '</div>';

                                if(!empty($SoftBlokerPlugin->Link))
                                {
                                    $SoftBloker .= '<a href="'. $SoftBlokerPlugin->Link .'" target="_blank" class="list-errors-fix-fixed op-button-transparent">Learn More</a>';
                                }
                                $SoftBloker .= '</li>';
                            }
                        }
                        $HardBlokerPlugins = array_merge($HardBlokerPlugins,$HardVersions);
                        if(!empty($HardBlokerPlugins))
                        {
                            foreach($HardBlokerPlugins as $HardBlokerPlugin)
                            {
                                $HardBloker .= '<li class="op-error op-error-res"><div><span class="octagonal"></span>'. $HardBlokerPlugin->Messages. '</div>';

                                if(!empty($HardBlokerPlugin->Link))
                                {
                                    $HardBloker .= '<a href="'. $HardBlokerPlugin->Link .'" target="_blank" class="list-errors-fix-fixed op-button-transparent">Learn More</a>';
                                }
                                $HardBloker .= '</li>';
                            }
                        }
                    }
                }
            }
        }
        echo json_encode(array("permalink"=>$noPermalink, "ssl_result"=>$sslResult, "localhost"=>$localhost, "issue_count"=>$issueCount, "SoftBloker"=>$SoftBloker, "HardBloker"=>$HardBloker, "no_errors"=>$noErrors));
        exit();
    }
    //Since 2.3.9.3
    public static function oliver_pos_register_url() {
        $registerUrl='not_register';
        $registerApiUrl = ASP_LAUNCH_REGISTER;
        $responceReg = wp_remote_get( $registerApiUrl, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
        if ( is_wp_error($responceReg))
        {
            $responceRegData = json_encode(array("Message" => $responceReg->get_error_message()));
            oliver_log("Something went wrong: $responceRegData");
        }
        else
        {
            if (wp_remote_retrieve_response_code($responceReg) == 200)
            {
                $responceRegData = json_decode(wp_remote_retrieve_body($responceReg));
                if ( $responceRegData->is_success )
                {
                    if (!empty($responceRegData->content))
                    {
                        $registerUrl = $responceRegData->content;
                    }
                }
            }
        }
        echo json_encode(array("register_url"=>$registerUrl));
        exit();
    }
    //Since 2.3.9.3
    public static function oliver_pos_view_plans() {
        $nextPayment='';
        $PlanAmount=0;
        $opPlan='noResponse';
        $planInfoUrl = ASP_PLANINFO;
        $op_plan='';
        $planInfoRes = wp_remote_get($planInfoUrl, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( get_option( 'oliver_pos_subscription_email' ).":".get_option( 'oliver_pos_subscription_token' ) ),
            ),
        ));
        if ( is_wp_error($planInfoRes)) {
            $responsePlan = json_encode(array("Message" => $planInfoRes->get_error_message()));
            oliver_log("Something went wrong: $responsePlan");
        }
        else
        {
            if (wp_remote_retrieve_response_code($planInfoRes) == 200)
            {
                $responsePlans = json_decode(wp_remote_retrieve_body($planInfoRes));
                if ( $responsePlans->is_success )
                {
                    if (!empty($responsePlans->content) && is_object($responsePlans->content))
                    {
                        $responsePlan = $responsePlans->content;
                        if(!empty($responsePlan))
                        {
                            switch ($responsePlan->Plan)
                            {
                                case 'oliverpos-pro':
                                    $opPlan='Oliver Pos Professional';
                                    break;
                                case 'oliverpos-enterprise':
                                    $opPlan='Oliver Pos Enterprise';
                                    break;
                                case 'oliverpos-basic':
                                    $opPlan='Oliver Pos Basic';
                                    break;
                                default:
                                    $opPlan='Oliver Pos Free';
                                    break;
                            }
                            $nextPayment.= $responsePlan->currency.' + tax on ';
                            $PlanAmount= $responsePlan->Amount;
                            $ExpriryDate = explode("T",$responsePlan->ExpriryDate);
                            $nextPayment.= $ExpriryDate[0];
                        }
                    }
                }
            }
        }
        echo json_encode(array("op_plan"=>$opPlan, "next_payment"=>$nextPayment, "PlanAmount"=>$PlanAmount));
        exit();
    }
}
Pos_Bridge_Install::oliver_pos_init();