<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://bobachat.app/
 * @since      1.0.6
 *
 */
?>
<?php echo "<script>jQuery(document).ready(function($) {
        $('#bobachatActive').click(function () {
          window.open($(this).attr('value'), '_blank');
        });
        $('#bobachatChange').click(function () {
          window.open($(this).attr('value'), '_blank');
        });
    });</script>"?>
<form method="post" action="options.php" id="myForm">
  <?php settings_fields( "bobachat_custom_setting" );do_settings_sections( "bobachat_custom_setting"); ?>
  <?php $bobachat_uniq_key = get_option( 'bobachat_uniq_key' ); ?>
  <?php $integration = $this->bobachat_api->getIntegration() ?>
  <?php if(Empty($integration) || Empty($integration -> userId)): ?>
  <div class="alert alert-danger mt-3 mx-3" role="alert">
    <h1 class="boba_alert_title">Activate your Bobachat account</h1>
    <p class="boba_alert_description">You need a Bobachat account before you can use the plugin. Click here to activate for free
      <button type="button" id="bobachatActive"
        value=<?php echo 'https://admin.bobachat.app/initIntegration?domain='.get_bloginfo('url').'&uniq_key='.$bobachat_uniq_key.'' ?>
        style="background: none!important;
      border: none;
      padding: 0!important;
      font-family: arial, sans-serif;
      color: #069;
      text-decoration: underline;
      cursor: pointer;">Active Now</button>
    </p>
  </div>
  <div class="mt-0  flex-fill mx-3 p-0" style="background: #fff; border: 1px solid #cecaca ">
    <div class="card-header">
      <h5>Bobachat Account</h5>
    </div>
    <div class="card-body">
      <p class="card-text">You are signed as a guest.</p>
      <a href="https://admin.bobachat.app/signin">Sign in</a> or <a href="https://admin.bobachat.app/signup">Sign up</a>
    </div>
  </div>
  </div>
  <?php else: 
    $imageList = (object) array(
      'TopBar Light'=> '/img/topbar-light.png',
      'TopBar Dark'=>  '/img/topbar-dark.png',
      'BottomBar Light'=>  '/img/bottombar-light.png',
      'BottomBar Dark'=>  '/img/bottombar-dark.png',
      'Exit Popup Light'=>  '/img/popup-light.png',
      'Exit Popup Dark'=> '/img/popup-dark.png',
      'ScrollBox Light'=>  '/img/scrollbox-light.png',
      'ScrollBox Dark'=>  '/img/scrollbox-dark.png',);
  ?>
    <div class="boba-header my-3 mt-4" style="display: flex; flex-direction: row;">
      <h3>Preview</h3>
      <button type="button" id="bobachatChange" class="btn btn-outline-primary ml-3"
        value=<?php echo 'https://admin.bobachat.app/manageIntegration?id='.$integration -> id.'' ?>>Manage Settings</button>
    </div>
    <div class="d-flex">
      <h5>Form Type: &nbsp;</h5> <?php echo $integration -> subscriptionFormTemplate -> name ?>
    </div>
    <div class="row row-wrap">
      <div class="col-md col-lg col-sm mt-3" style="border: 1px solid #cececa">
        <img
          src=<?php echo plugins_url($imageList->{$integration -> subscriptionFormTemplate -> name}, dirname(__FILE__) ) ?>
          style="max-width: 100%" />
      </div>
      <div class="col-md-3 col-lg-3 col-sm mt-3">
        <div class="card mt-0 p-0">
          <div class="card-header">
            <h5>Your Account</h5>
          </div>
          <div class="card-body">
            <p>Account email: <?php echo $integration -> userEmail?></p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</form>