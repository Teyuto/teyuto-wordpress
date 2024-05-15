<?php
function teyutowp_add_new_video() {
  $client = teyutowp_get_client();
  if(!$client): ?>
    <p class="api-error-message">Api key is not added/valid, please go to <a href="admin.php?page=settings-teyuto">settings</a> page and update it with correct one</p>
    <?php return;
  endif;
  ?>

  <div class="wrap">
    <h2><?php echo(esc_html(get_admin_page_title())) ?></h2>
  </div>

  <div id="dropContainer" class="dropContainerWrapper">
    <div id="custom-bg-grey-containter"></div>
    <h2 class="upload-instructions drop-instructions">Drop files to upload</h2>
    <p class="upload-instructions drop-instructions">or</p>
    <label for="fileInput" id="video-file-label">
		  Select files
    </label>
  </div>
  <input type="file" id="fileInput" />
  <input type="text" id="teyuto_apiKey" style="display:none;" value="<?php echo esc_attr($client->apiKey); ?>"/>
  <input type="text" id="teyuto_channel" style="display:none;" value="<?php echo esc_attr($client->channel); ?>"/>

  <div id="action-upload">
	<input type="file" id="video-file">
  </div>
  <div id="chunk-information"></div>
  <div id="teyuto-progress-bar">
    <progress id="progress-tracker" value="0" max="100"></progress>
  </div>
  <div id="video-information"></div>

<?php }