<?php
function teyutowp_api_settings_page() {
  if(isset($_POST['teyuto_options']['api_key'])):
    update_option("teyuto_api_key", sanitize_text_field($_POST['teyuto_options']['api_key']));
    update_option("teyuto_channel", sanitize_text_field($_POST['teyuto_options']['channel']));
  endif; ?>
  <h2>Teyuto API key</h2>
  <p>Discover <a target="_blank" href="https://teyuto.com">Teyuto</a>, a platform dedicated to empowering product creators with robust video infrastructure.</p>
  <p>Leverage <a target="_blank" href="https://teyuto.com">Teyuto</a>'s lightning-fast video APIs to seamlessly integrate, scale, and manage on-demand and low-latency live streaming functionalities within your WordPress website.</p>
  <p>If you're yet to obtain an API key, sign up for a complimentary account on <a target="_blank" href="https://teyuto.com">Teyuto's website</a> today.</p>
  <form action="" method="post">
    <p>Channel</p>
    <input id='teyuto-settings-channel' name='teyuto_options[channel]' type='text' value='<?php echo(esc_html(get_option("teyuto_channel"))) ?>' />
    <p>Api Key</p>
    <input id='teyuto-settings-key' name='teyuto_options[api_key]' type='text' value='<?php echo(esc_html(get_option("teyuto_api_key"))) ?>' />
    <br><br>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
  </form>
  <?php 
  // Get client
  $client = teyutowp_get_client();
  if(!$client): ?>
    <p class="api-error-message">Api key is not added/valid</p>
    <?php return;
  endif;
  ?>
<?php }
