<?php
defined( 'ABSPATH' ) || exit;
include_once OLIVER_POS_ABSPATH . 'includes/class-pos-bridge-install.php';
$udid = get_option('oliver_pos_subscription_udid'); // replace by client id
$login_auth = get_option('oliver_pos_subscription_autologin_token'); // replace by login token
$app_url = ASP_TRY_ONBOARD;

$encode_url_auth = "";
if ($login_auth) {
    $encode_url_auth = urlencode($login_auth);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        dataLayer = [{
            'oliverpos-client-guid': `<?php echo get_option("oliver_pos_subscription_email"); ?>`,
            'oliverpos-client-url': `<?php echo home_url(); ?>`,
            'oliverpos-client-email': `<?php echo wp_get_current_user()->user_email; ?>`
        }];
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TQBJ7K8');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Oliver Version <?php echo OLIVER_POS_PLUGIN_VERSION_NUMBER; ?> -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="">
    <title>Oliver - login</title>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQBJ7K8" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- id="oliver-fullHeight" -->
<div class="on-content" >
    <div class="on-header">
        <header>
            <img src="<?php echo plugins_url('public/resource/img/logo.svg', dirname(dirname(dirname(__FILE__)))); ?>" alt="">
        </header>
    </div>
    <!-- id="oliver-ContentHeight" -->
    <section class="bridge_content_outer bridge_content_container">
        <div class="bridge_content_table">
            <div class="bridge_content_cell">
                <div class="op-column op-fail" style="display: none">
                    <div class="op-portlet op-portlet-fluid">
                        <div class="op-portlet-head">
                            Oops!
                        </div>
                        <div class="op-portlet-body">
                            <ul class="list-errors">
                                <li class="op-error">
                                    <div>
                                        <span class="octagonal"></span>
                                        <span class="server-error"></span>
                                    </div>
                                    <a href="#" id="op-reload" class="list-errors-fix-fixed op-button-transparent">Try Again</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="connection-pane" style="display:none">
                    <main class="op-inner-content2">
                        <!-- Registration banner start -->
                        <div class="op-row" id="op-register-banner">
                            <div class="op-column-left" >
                                <div class="op-column">
                                    <div class="op-portlet op-portlet-fluid op-portlet-newUser">
                                        <div class="op-portlet-body">
                                            <div class="op-row">
                                                <div class="op-column-left">
                                                    <div id="register-pane-url" class="op-column op-column-in">
                                                        <h1>👋 Hi. Welcome to Oliver POS.</h1>
                                                        <p>From here, you can access everything you need to start selling in-store.  </p>
                                                        <a href="<?php echo esc_url_raw( $app_url); ?>" target="_blank">
                                                            <button class="op-button-transparent op-button-connect"> Create an Account </button>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="op-column-right">
                                                    <div class="op-column op-column-in">
                                                        <img src="<?php echo plugins_url('public/resource/img/create.png', dirname(dirname(dirname(__FILE__)))); ?>" class="create-to-connect" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Registration banner end -->
                        <div class="op-row">
                            <div class="op-column-left">
                                <div class="op-column op-order-1 op-register-url">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">Oliver Register</div>
                                        <div class="op-portlet-body">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide register-url" style="opacity: 0;">
                                                    <div class="op-portlet-background" style="background: #2797E8;"> </div>
                                                    <div class="op-portlet-inner">
                                                        <div class="op-portlet-inner-image">
                                                            <img src="<?php echo plugins_url('public/resource/img/blog.png', dirname(dirname(dirname(__FILE__)))); ?>" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="op-portlet-footbg" style="background: #2797E8">
                                                        Start selling with our uncluttered and intuitive WooCommerce POS register.
                                                        <div class="content-form">
                                                            <button class="op-button-transparent">
                                                                <a href="#" target="_blank" id="op-transparent">
                                                                    Launch Register</a>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="s-loader stop_s_loader register-url">
                                                    <div class="s-lInner s-lInner-list-bg">
                                                        <div class="s-lInner-list s-lInner-list-bg">
                                                            <div class="s-lInner-list-bottom">
                                                                <div>
                                                                    <h1></h1>
                                                                    <p></p>
                                                                </div>
                                                                <p class="last-child"></p>
                                                            </div>
                                                        </div>
                                                        <div class="s-lInner-animate"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="op-column op-order-3 op-hub-url">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">
                                            Oliver Hub
                                        </div>
                                        <div class="op-portlet-body">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide" style="opacity: 0;">
                                                    <div class="op-portlet-background" style="background: #47D2A5;"></div>
                                                    <div class="op-portlet-inner">
                                                        <div class="op-portlet-inner-image">
                                                            <img src="<?php echo plugins_url('public/resource/img/hub.png', dirname(dirname(dirname(__FILE__)))); ?>" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="op-portlet-footbg" style="background: #47D2A5">
                                                        Manage staff, receipts, reports, account settings and more..
                                                        <div id="connection-pane-url">
                                                            <a type="button" href="<?php echo esc_url_raw( $app_url); ?>" target="_blank" class="btn-connect">Launch Hub</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="s-loader <?php if(!empty($app_url)){ echo 'stop_s_loader';}?>">
                                                    <div class="s-lInner-list s-lInner-list-bg">
                                                        <div class="s-lInner-list-bottom">
                                                            <div>
                                                                <h1></h1>
                                                                <p></p>
                                                            </div>
                                                            <p class="last-child"></p>
                                                        </div>
                                                    </div>
                                                    <div class="s-lInner-animate"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="op-column op-order-5">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">
                                            Need a Hand?
                                        </div>
                                        <div class="op-portlet-body">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide" style="opacity: 0;">
                                                    <div class="op-portlet-need">
                                                        <ul>
                                                            <li>
                                                                <img src="<?php echo plugins_url('public/resource/img/meeting.svg', dirname(dirname(dirname(__FILE__)))); ?>"
                                                                     alt="" srcset="">
                                                            </li>
                                                            <li>
                                                                <h6>Schedule a Meeting</h6>
                                                                <p>Book a Demo with our Sales Team</p>
                                                            </li>
                                                            <li>
                                                                <button class="op-button-transparent">
                                                                    <a href="https://oliverpos.com/resources/support/" target="_blank">Book Now</a>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="op-portlet-need">
                                                        <ul>
                                                            <li>
                                                                <img src="<?php echo plugins_url('public/resource/img/Phone.svg', dirname(dirname(dirname(__FILE__)))); ?>"
                                                                     alt="" srcset="">
                                                            </li>
                                                            <li>
                                                                <h6>Call our Support Line</h6>
                                                                <p>Give our office a call </p>
                                                            </li>
                                                            <li>
                                                                <button class="op-button-transparent">
                                                                    <a href="tel:8336620633">Call +1-833-662-0633</a>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="op-portlet-need">
                                                        <ul>
                                                            <li>
                                                                <img src="<?php echo plugins_url('public/resource/img/KnowledgeBase.svg', dirname(dirname(dirname(__FILE__)))); ?>"
                                                                     alt="" srcset="">
                                                            </li>
                                                            <li>
                                                                <h6>Ask Our Knowledge Base</h6>
                                                                <p>Get help right away</p>
                                                            </li>
                                                            <li>
                                                                <button class="op-button-transparent">
                                                                    <a href="https://help.oliverpos.com/" target="_blank">Open Knowledge Base</a>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="op-portlet-need">
                                                        <ul>
                                                            <li>
                                                                <img src="<?php echo plugins_url('public/resource/img/LiveChat.svg', dirname(dirname(dirname(__FILE__)))); ?>"
                                                                     alt="" srcset="">
                                                            </li>
                                                            <li>
                                                                <h6>Live Chat</h6>
                                                                <p>Talk to a support expert </p>
                                                            </li>
                                                            <li>
                                                                <button class="op-button-transparent">
                                                                    <a href="https://oliverpos.com/contact-oliver-pos/#hs-chat-open" target="_blank">Open Website</a>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="s-loader stop_s_loader">
                                                    <div class="s-lInner">
                                                        <div class="s-lInner-list">
                                                            <div class="op-portlet-need">
                                                                <ul class="op-portlet-need-loading">
                                                                    <li>
                                                                        <span class="img"></span>
                                                                    </li>
                                                                    <li>
                                                                        <h6>&nbsp;</h6>
                                                                        <p>&nbsp;</p>
                                                                    </li>
                                                                    <li>
                                                                        <button class="op-button-transparent">
                                                                            &nbsp;
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="op-portlet-need">
                                                                <ul class="op-portlet-need-loading">
                                                                    <li>
                                                                        <span class="img"></span>
                                                                    </li>
                                                                    <li>
                                                                        <h6>&nbsp;</h6>
                                                                        <p>&nbsp;</p>
                                                                    </li>
                                                                    <li>
                                                                        <button class="op-button-transparent">
                                                                            &nbsp;
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="op-portlet-need">
                                                                <ul class="op-portlet-need-loading">
                                                                    <li>
                                                                        <span class="img"></span>
                                                                    </li>
                                                                    <li>
                                                                        <h6>&nbsp;</h6>
                                                                        <p>&nbsp;</p>
                                                                    </li>
                                                                    <li>
                                                                        <button class="op-button-transparent">
                                                                            &nbsp;
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="s-lInner-animate"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="op-column-right">
                                <div class="op-column op-order-2 op-system-check-url">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">
                                            System Check<span class="critical-issues"></span>
                                            <button class="op-button-transparent op-button-head btn-refresh">
                                                Refresh
                                            </button>
                                        </div>
                                        <div class="op-portlet-body btn-refresh-content">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide systemcheck" style="opacity: 0;">
                                                    <ul class="list-errors">
                                                        <li class="op-error op-ssl">
                                                            <div>
                                                                <span class="octagonal"></span>
                                                                Error: Outdated SSL Certification
                                                            </div>
                                                            <a href="https://help.oliverpos.com/can-oliver-connect-to-an-unsecure-website" target="_blank" class="list-errors-fix-fixed op-button-transparent">Learn More</a>
                                                        </li>
                                                        <li class="op-error op-Permalinks">
                                                            <div>
                                                                <span class="octagonal"></span>
                                                                Permalinks Not Set
                                                            </div>
                                                            <a href="https://help.oliverpos.com/what-permalinks-settings-do-i-need-to-connect-to-oliver" class="list-errors-fix-fixed op-button-transparent" target="_blank">Learn More</a>
                                                        </li>
                                                        <li class="op-error op-localhost">
                                                            <div>
                                                                <span class="octagonal"></span>
                                                                Error: Oliver Pos not support local servers. Please use a Public
                                                            </div>
                                                            <a href="https://help.oliverpos.com/can-oliver-connect-to-a-locally-hosted-website" class="list-errors-fix-fixed op-button-transparent" target="_blank">Learn More</a>
                                                        </li>
                                                        <li class="op-error op-error-res">
                                                            <div><span class=""></span></div>
                                                            <a href="#" target="_blank" class="list-errors-fix-fixed op-button-transparent"></a>
                                                        </li>
                                                        <li class="op-error-warning op-error-res">
                                                            <div><span class=""></span></div>
                                                            <a href="" target="_blank" class="list-errors-fix-fixed op-button-transparent"></a>
                                                        </li>
                                                        <li class="op-error-success op-no_errors">
                                                            <div>
                                                                <span class="checked"></span>
                                                                No Errors to Report
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="s-loader stop_s_loader systemcheck">
                                                    <div class="s-lInner">
                                                        <div class="s-lInner-list">
                                                            <div class="s-lInner-lists-errors">
                                                                <div>
                                                                    <div class="error-img"></div>
                                                                </div>
                                                                <div class="error-content">
                                                                    <h1></h1>
                                                                    <p></p>
                                                                </div>
                                                                <div class="error-button"></div>
                                                            </div>
                                                            <div class="s-lInner-lists-errors">
                                                                <div>
                                                                    <div class="error-img"></div>
                                                                </div>
                                                                <div class="error-content">
                                                                    <h1></h1>
                                                                    <p></p>
                                                                </div>
                                                                <div class="error-button"></div>
                                                            </div>
                                                            <div class="s-lInner-lists-errors">
                                                                <div>
                                                                    <div class="error-img"></div>
                                                                </div>
                                                                <div class="error-content">
                                                                    <h1></h1>
                                                                    <p></p>
                                                                </div>
                                                                <div class="error-button"></div>
                                                            </div>
                                                            <div class="s-lInner-lists-errors">
                                                                <div>
                                                                    <div class="error-img"></div>
                                                                </div>
                                                                <div class="error-content">
                                                                    <h1></h1>
                                                                    <p></p>
                                                                </div>
                                                                <div class="error-button"></div>
                                                            </div>
                                                        </div>
                                                        <div class="s-lInner-animate"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="op-column op-order-4 op-view-plan">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">
                                            Your Plan
                                        </div>
                                        <div class="op-portlet-body">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide op-view-plan-hide" style="opacity: 0;">
                                                    <div class="op-portlet-plan op-portlet-plan-space">
                                                        <div>
                                                            <h1 class="op-plan-name"></h1>
                                                            <p>Unlimited Transactions, Unlimited Customers</p>
                                                            <a href="<?php echo esc_url_raw( $app_url ); ?>&ReturnUrl=%2FSubscription%2FIndex" class="op-button-transparent" id="op-viewplans" target="_blank">View Plans</a>
                                                        </div>
                                                        <img src="<?php echo plugins_url('public/resource/img/o.svg', dirname(dirname(dirname(__FILE__)))); ?>" alt="">
                                                    </div>
                                                    <div class="op-portlet-plan op-portlet-plan2 op-no-padding">
                                                        Payment
                                                        <p class="op-next-payment"></p>
                                                    </div>
                                                </div>
                                                <div class="s-loader stop_s_loader op-view-plan-hide">
                                                    <div class="s-lInner">
                                                        <div class="s-lInner-list">
                                                            <div class="op-portlet-plan op-portlet-plan-space">
                                                                <div>
                                                                    <h1></h1>
                                                                    <p>Unlimited Transactions, Unlimited Customers</p>
                                                                    <a href="#" class="op-button-transparent"></a>
                                                                </div>
                                                                <span class="img"></span>
                                                            </div>
                                                            <div class="op-portlet-plan op-portlet-plan2">
                                                                Payment
                                                                <p>Your next bill is for 00 USD</p>
                                                            </div>
                                                        </div>
                                                        <div class="s-lInner-animate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="op-column op-order-6">
                                    <div class="op-portlet op-portlet-fluid">
                                        <div class="op-portlet-head">
                                            Announcement
                                        </div>
                                        <div class="op-portlet-body">
                                            <div class="op-portlet-content">
                                                <div class="s-loader-content-hide" style="opacity: 0;">
                                                    <div class="op-portlet-inner">
                                                        <iframe height="300px" width="100%" frameborder="0" seamless="true" scrolling="no" class="kt-portlet kt-margin-b-0" src="https://wordpress-715812-2640674.cloudwaysapps.com/bridge-announcements-banner-2"></iframe>
                                                    </div>
                                                </div>
                                                <div class="s-loader stop_s_loader">
                                                    <div class="s-lInner-list s-lInner-list-bg">
                                                        <div class="s-lInner-list-bottom">
                                                            <div>
                                                                <h1></h1>
                                                                <p></p>
                                                            </div>
                                                            <p class="last-child"></p>
                                                        </div>
                                                    </div>
                                                    <div class="s-lInner-animate"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
                <!-- ========= this is the first form used for rendering connected ========== -->
                <div class="oliver_info" style="margin-top: 5px; display: none;" id="error-panel"></div>
    </section>
</div>
<script type="text/javascript">
    function loading(){
        jQuery(".s-loader-content-hide").css("opacity", 1);
        jQuery('.stop_s_loader').css({"display":"none", "opacity":0});
        jQuery('.systemcheck.s-loader-content-hide').css({"opacity":0});
        jQuery('.systemcheck.stop_s_loader').css({"display":"block", "opacity":1});
        jQuery('.op-view-plan-hide.s-loader-content-hide').css({"opacity":0});
        jQuery('.op-view-plan-hide.stop_s_loader').css({"display":"block", "opacity":1});
        jQuery('.register-url.s-loader-content-hide').css({"opacity":0});
        jQuery('.register-url.stop_s_loader').css({"display":"block", "opacity":1});
    }
    var JQueryAjaxUrl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';

    var UDID = "<?php echo $udid; ?>";
    jQuery(document).ready(function() {
        // start loader
        oliver_pos_initLoader();
        // invoke try connect
        oliver_pos_try_connect();
        // On Click refresh button
        jQuery(".btn-refresh").click(function(){
            oliver_pos_systemCheck();
        });
        jQuery(document).on('click', '#op-reload', function() {
            window.location.reload();
        });
    });

    /**
     * For establish connection with oliver pos
     * @since 2.2.5.0
     * @return boolena true : false (errors).
     */
    function oliver_pos_try_connect() {
        oliver_pos_startLoader();
        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_try_connect',
                'service'   : '1',
            },
            function(result){
                let msg = "Oliver POS did not respond in a timely manner";
                if (result) {
                    loading();
                    let response = JSON.parse(result);
                    if (response.is_success) {
                        let cId = response.content.client_id;
                        let tId = response.content.server_token;
                        let autoToken = null;
                        if (response.content.auth_token) {
                            autoToken = response.content.auth_token;
                            jQuery('#op-register-banner').css({"display": "none"});
                        }
                        oliver_pos_displayConnectionPanel(cId, tId, autoToken);
                        oliver_pos_systemCheck();
                        oliver_pos_getRegisterUrl();
                        oliver_pos_getViewPlans();
                    } else {
                        if (response.Message) {
                            msg = response.Message;
                        }
                        oliver_pos_displayError(msg);
                    }
                } else {
                    oliver_pos_displayError(msg);
                }
            }
        ).fail(function() {
            msg='Error: Server is not responding!';
            oliver_pos_displayError(msg);
        })
            .always(function() {
                oliver_pos_stopLoader();
            });
    }
    function oliver_pos_displayConnectionPanel(cId, tId, autoToken){
        let appURL = '<?php echo $app_url ?>';
        let encodeUrlAuth = '<?php echo $encode_url_auth ?>';
        let serverURL = `${appURL}?_client=${cId}`;
        if (autoToken) {
            serverURL += `&_token=${autoToken}`;
        }
        if ( ! serverURL.includes("&_token=")) {
            if (encodeUrlAuth != "") {
                serverURL += `&_token=${encodeUrlAuth}`;
            }
        }
        jQuery('#connection-pane').css({"display": "block"});
        jQuery('#connection-panel').css({"display": "block"});
        jQuery("#connection-pane-url a").attr("href", decodeURI(serverURL));
        jQuery("#register-pane-url a").attr("href", decodeURI(serverURL));
        var opDecodeUrl = decodeURI(serverURL+'&_returnUrl=%2FSubscription%2FIndex');
        jQuery("a#op-viewplans").attr("href", opDecodeUrl);
    }
    jQuery(".confirm-deactivate").click( function() {
        jQuery(this).find('a').text("Disconnecting...");
        jQuery(this).css({
            'pointer-events' : "none"
        });
        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_disconnect_subscription',
                'service'   : '1',
            },
            function( response ){
                let res = JSON.parse( response );
                if (res.IsSuccess) {
                    oliver_pos_delete_subscription();
                }
            }
        );
    });
    function oliver_pos_delete_subscription() {
        // body...
        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_delete_subscription',
                'service'   : '1',
            },
            function( response ){
                res = JSON.parse( response );
                if ( res.status ) {
                    location.reload(true);
                }
            }
        );
    }
    /**
     * display error
     * @since 2.2.5.0
     * @return html|message.
     */
    function oliver_pos_displayError(eMsg) {
        jQuery('.server-error').text(eMsg);
        jQuery('.op-fail').css({"display": "block"});
        jQuery('.toplevel_page_oliver-pos div#wpwrap').css({"background": "transparent"});
    }
    /**
     * init loader
     * @since 2.2.5.0
     * @return html|loader.
     */
    function oliver_pos_initLoader() {
        jQuery("body").append(`<?php oliver_pos_loader(); ?>`);
    }
    /**
     * start loader
     * @since 2.2.5.0
     * @return html|loader.
     */
    function oliver_pos_startLoader() {
        jQuery('#image_loading').css({"display": "flex"});
    }
    /**
     * stop loader
     * @since 2.2.5.0
     * @return html|loader.
     */
    function oliver_pos_stopLoader() {
        jQuery('#image_loading').css({"display": "none"});
    }

    function oliver_pos_getViewPlans() {
        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_view_plans',
            },
            function(plans_result){
                let plans_response = JSON.parse(plans_result);
                if(plans_response.op_plan=='noResponse'){
                    jQuery('.op-view-plan').addClass("op-hide");
                }
                else{
                    jQuery('.op-next-payment').text('Your next bill is for '+plans_response.PlanAmount.toFixed(2)+' '+plans_response.next_payment);
                    jQuery('.op-plan-name').text(plans_response.op_plan);
                    jQuery('.op-view-plan').removeClass("op-hide");
                }
                jQuery('.op-view-plan-hide.s-loader-content-hide').css({"opacity":1});
                jQuery('.op-view-plan-hide.stop_s_loader').css({"display":"none", "opacity":0});
            }
        );
    }
    function oliver_pos_getRegisterUrl() {
        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_register_url',
            },
            function(reg_result){
                let reg_response = JSON.parse(reg_result);
                if(reg_response.register_url=='not_register'){
                    jQuery('.op-register-url').addClass("op-hide");
                    jQuery('.op-system-check-url').addClass("op-hide");
                    jQuery('.op-hub-url').addClass("op-hide");
                }
                else{
                    jQuery("a#op-transparent").attr("href", reg_response.register_url);
                    jQuery('.op-register-url').removeClass("op-hide");
                }
                jQuery('.register-url.s-loader-content-hide').css({"opacity":1});
                jQuery('.register-url.stop_s_loader').css({"display":"none", "opacity":0});
            }
        );
    }
    function oliver_pos_systemCheck() {
        jQuery('.systemcheck.s-loader-content-hide').css({"opacity":0});
        jQuery('.systemcheck.stop_s_loader').css({"display":"block", "opacity":1});

        jQuery.post(JQueryAjaxUrl ,{
                'action'    : 'oliver_pos_system_check',
            },
            function(op_result){
                let op_response = JSON.parse(op_result);

                if(op_response.permalink=='notset'){
                    jQuery('.op-Permalinks').removeClass("op-hide");
                }
                else{
                    jQuery('.op-Permalinks').addClass("op-hide");
                }
                if(op_response.ssl_result=='yes'){
                    jQuery('.op-ssl').addClass("op-hide");
                }
                else{
                    jQuery('.op-ssl').removeClass("op-hide");
                }
                if(op_response.localhost=='localhost'){
                    jQuery('.op-localhost').removeClass("op-hide");
                }
                else{
                    jQuery('.op-localhost').addClass("op-hide");
                }
                if(op_response.no_errors=='yes'){
                    jQuery('.op-no_errors').addClass("op-hide");
                }
                else{
                    jQuery('.op-no_errors').removeClass("op-hide");
                }
                if(op_response.issue_count !=0){
                    jQuery('.critical-issues').text(' ( '+op_response.issue_count+' critical issues)');
                }
                if(op_response.HardBloker !='')
                {
                    jQuery('.systemcheck ul.list-errors .op-error.op-error-res').removeClass("op-hide");
                    jQuery('.systemcheck ul.list-errors .op-error.op-error-res').remove();
                    jQuery('.systemcheck ul.list-errors').append(op_response.HardBloker);
                }
                else{
                    jQuery('.systemcheck ul.list-errors .op-error.op-error-res').addClass("op-hide");
                }
                if(op_response.SoftBloker !='')
                {
                    jQuery('.systemcheck ul.list-errors .op-error-warning').removeClass("op-hide");
                    jQuery('.systemcheck ul.list-errors .op-error-warning').remove();
                    jQuery('.systemcheck ul.list-errors').append(op_response.SoftBloker);
                }
                else{
                    jQuery('.systemcheck ul.list-errors .op-error-warning').addClass("op-hide");
                }
                jQuery('.systemcheck.s-loader-content-hide').css({"opacity":1});
                jQuery('.systemcheck.stop_s_loader').css({"display":"none", "opacity":0});
            }
        );
    }
</script>
</body>
</html>