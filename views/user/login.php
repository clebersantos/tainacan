<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/login_js.php');
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');
session_start();
?>

<!---?php
$loginUrl = $facebook->getLoginUrl(
        array(
            'scope' => 'email,user_birthday',
            'redirect_uri' => get_bloginfo(template_directory) . '/controllers/user/user_controller.php?collection_id=' . $collection_id . '&operation=return_login_fb'
        )
);

//$logoutUrl = $facebook->getLogoutUrl();
?-->
<?php
    $config = get_option('socialdb_theme_options');
    $app['app_id'] = $config['socialdb_fb_api_id'];
    $app['app_secret'] = $config['socialdb_fb_api_secret'];

    if (!empty($app['app_id']) && !empty($app['app_secret'])) {
        $fb = new Facebook\Facebook([
            'app_id' => $app['app_id'],
            'app_secret' => $app['app_secret'],
            'default_graph_version' => 'v2.3',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email', 'user_birthday']; // optional
        $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/user/user_controller.php?collection_id=' . $collection_id . '&operation=return_login_fb', $permissions);
    }
//echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>
<input type="hidden" id="src_login" name="src" value="<?php echo get_template_directory_uri() ?>">
<div class="container" style="margin-bottom: 15%;">

    <form action="" id="LoginForm" name="LoginForm" class="form-signin">
        <input type="hidden" id="operation_log" name="operation" value="login_regular">
        <input type="hidden" id="collection_id_login" name="collection_id" value="<?php echo $collection_id; ?>">
        <h2 class="form-signin-heading"><?php _e("Please sign in",'tainacan'); ?></h2>
        <label for="inputEmail" class="sr-only"><?php _e("Username",'tainacan'); ?></label>
        <input type="text" id="inputUsername" name="username" class="form-control" placeholder="<?php _e('Username','tainacan') ?>" required="" autofocus="">
        <br />
        <label for="inputPassword" class="sr-only"><?php _e("Password",'tainacan'); ?></label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php _e('Password','tainacan') ?>" required="">
        <div class="checkbox">
            <?php if ($facebook_option['api_id'] && $facebook_option['api_secret']) { ?>
                <a href="<?php echo $loginUrl; ?>"><img src="<?php echo get_template_directory_uri(); ?>/libraries/images/fb_login.png" style="max-width: 150px;" /></a>
            <?php } ?>
            <?php if (isset($authUrl)) { ?>
                <a href="<?php echo $authUrl; ?>"><img src="<?php echo get_template_directory_uri(); ?>/libraries/images/plus_login.png" style="max-width: 150px;" /></a>
            <?php } ?>
            <!--label>
                <input type="checkbox" value="remember-me"> Remember me
            </label-->
        </div>
        <label><a href="#" id="open_myModalForgotPassword"><?php _e("Forgot password?",'tainacan'); ?></a></label>
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php _e("Login",'tainacan'); ?></button>
    </form>

</div>

<div class="modal fade" id="myModalForgotPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="formUserForgotPassword" name="formUserForgotPassword" >  
                <input type="hidden" name="operation" value="forgot_password">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Forgot Password?','tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_login"><?php _e('Username or Email','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="text" required="required" class="form-control" name="user_login_forgot" id="user_login_forgot" placeholder="<?php _e('Type here the username that you will use for login or your email','tainacan'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Send','tainacan'); ?></button>
                </div>
            </form>    
        </div>
    </div>
</div>