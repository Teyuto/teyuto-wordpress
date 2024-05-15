<?php
function teyutowp_library() { ?>
  
  <?php
  $url_components = wp_parse_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
  $query_params = array();
  if (isset($url_components['query'])) {
      parse_str($url_components['query'], $query_params);
  }
  $currentPageNum = isset($query_params['paget']) && is_numeric($query_params['paget']) && $query_params['paget'] > 0 ? intval($query_params['paget']) : 1;

  ?>
 
  <?php 
  // Get client
  $client = teyutowp_get_client();
  if(!$client): ?>
    <p class="api-error-message">Api key is not added/valid, please go to <a href="admin.php?page=settings-teyuto">settings</a> page and update it with correct one</p>
    <?php return;
  endif;
  ?>

  <!-- Wrap section -->
  <div class="wrap">
    <h1 class="wp-heading-inline">Library</h1>
    <a href="admin.php?page=add-new-video" class="page-title-action aria-button-if-js">Add New</a>
    <hr class="wp-header-end">

    <input id="library-search-value" value="<?php echo esc_attr($_GET['searcht']); ?>" type="text" placeholder="Search name or id">

    <?php

      // Handle if delete button is clicked
      if (isset($_POST["deleteavideo"])) {
        teyutowp_delete_video($client, $_POST['deletevideoid']);
      }

      // Handle if Save button is clicked!
      if (isset($_POST['submit-button'])) :
        $public = true;

        $privacy = 'hidden';
        if ($_POST['input_public']) : $privacy = 'public';
        endif;

        $patch_data = array(
          'title' => $_POST['input_title'],
          'description' => $_POST['input_description'],
          'privacy' => $privacy,
        );
        
        teyutowp_patch_video($client, $_POST['videoid'], $patch_data);
      endif;

      // Check if videos exist
      $videos = [];
      $searchParam = isset($_GET['searcht']) ? urlencode($_GET['searcht']) : null;
      try{
        $currentPage = teyutowp_get_videos($client, $currentPageNum, $searchParam);
        $videos = array_merge($videos, $currentPage->videos);
      }catch(Exception $e){
        $videos = null;
      }
        
      if (!$videos) :
        echo "<h4>No videos.</h4>"; 
      endif; ?>
  </div>
  <!-- End of Wrap section -->

  <!-- Video section -->
  <div class="videos-wrapper">
    <?php
      // Get videos from newest to oldest
      // Loop for every video and check if there is extra class
      foreach ($videos as $video) :
        if ($query_params['id'] == $video->id) : $extra_class = " active";
        else : $extra_class = "";
        endif; 
      ?>

      <!-- Single video popup -->
      <div class="video-info-frame<?php echo(esc_attr($extra_class)) ?>" data-videoid="<?php echo(esc_attr($video->id)) ?>">
        <!-- Single video header -->
        <div class="video-frame-top-section">
          <div class="video-frame-header-text">
            <h1>Video details</h1>
          </div>
          <div class="edit-media-header">
            <a onclick="teyuto_change_url()" class="close-video-trig">X</a>
          </div>
        </div>
        <!-- End of Single video header -->

        <!-- Video popup main section -->
        <div class="video-frame-bottom-section">
          <!-- Video -->
          <div class="video-info-left">
            <div class="custom-iframe">
            <?php 
              $atts['id'] = $video->id;
              echo teyutowp_shortcode($atts); 
            ?>
            </div>
          </div>
          <!-- End of Video -->

          <!-- Video general wrapper -->
          <div class="video-info-right">
            <!-- Video info -->
            <div class="video-info-right-data">
              <div><span class="video-info-bold-text">Video id: </span><span><?php echo(esc_html($video->id)) ?></span></div>
              <div><span class="video-info-bold-text">Video created at: </span><span><?php echo(esc_html(teyutowp_get_video_date($video->date))) ?></span></div>
            </div>
            <!-- End of Video info-->

            <!-- General form -->
            <form method="POST" id="head-form" action="admin.php?page=teyuto-library&videoid=<?php echo(esc_attr($video->id)) ?>">
              <div class="teyuto-settings">
                <!-- Text for video -->
                <div class="custom-private-text">
                  <?php if ($video->privacy == 'hidden') : ?>
                    <p style="color:red;">The video is hidden, only you can see the video with the administrator token. </p>
                  <?php endif; ?>
                </div>

                <!-- Labels and inputs -->
                <div class="labels-and-inputs-wrapper">
                  <ul class="labels-and-inputs-ul">
                    <li class="labels-and-inputs-li">
                      <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Hidden: </label>
                      <input class="teyuto-custom-input custom-copy-item" name="input_public" id="clickable-item-3" <?php if ($video->privacy == 'hidden') { echo "checked"; } ?> type="checkbox" class="attachment-details-copy-link" value="yes">
                    </li>
                  </ul>
                </div>

                <!-- Title -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-title" class="name custom-label-item">Title: </label>
                  <input class="custom-input-margin teyuto-custom-input custom-copy-item" id="clickable-item-1" name="input_title" required type="text" value="<?php echo(esc_attr($video->title)) ?>">
                </div>

                <!-- Description -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Description: </label>
                  <textarea class="custom-input-margin teyuto-custom-input custom-copy-item" id="clickable-item-2" name="input_description" type="text" class="attachment-details-copy-link"><?php echo(esc_textarea($video->description)) ?></textarea>
                </div>

                <!-- Video URL -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Video URL: </label>
                  <input class="teyuto-custom-input custom-copy-item" name="input_video_url" id="clickable-item-7" type="text" class="attachment-details-copy-link" readonly value="<?php echo(esc_attr($video->embeed)) ?>">
                  <div class="clickable-item" title="Copy"></div>
                </div>

                <!-- Video Hls -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Video HLS: </label>
                  <input class="teyuto-custom-input custom-copy-item" name="input_video_url" id="clickable-item-7" type="text" class="attachment-details-copy-link" readonly value="<?php echo(esc_attr($video->hls_url)) ?>">
                  <div class="clickable-item" title="Copy"></div>
                </div>

                <!-- Shortcode -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Shortcode: </label>
                  <input class="custom-copy-item custom-input-width-75" id="clickable-item-8" type="text" readonly value="[teyuto id=<?php echo esc_attr($video->id); ?>]">
                  <div class="clickable-item" title="Copy"></div>
                </div>
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">16/9: </label>
                  <input class="custom-copy-item custom-input-width-75" id="clickable-item-8" type="text" readonly value="[teyuto id=<?php echo esc_attr($video->id) ?> aspect-ratio=16/9]">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">9/16: </label>
                  <input class="custom-copy-item custom-input-width-75" id="clickable-item-8" type="text" readonly value="[teyuto id=<?php echo esc_attr($video->id) ?> aspect-ratio=9/16]">
                  <div class="clickable-item" title="Copy"></div>
                </div>
               

                <!-- Save button for form -->
                <input type="submit" name="submit-button" id="form-button-trigger" class="button button-primary" value="Save">
                <input type="text" id="hidden-video-id" name="videoid" value="<?php echo esc_attr($video->id) ?>">
              </div>
            </form>
            <!-- End of General form -->

            <!-- Settings bar -->
            <div class="actions settings-bar">
              <form method="POST" class="action-delete-form" onsubmit="return customfunction()">
                <?php if ($video->assets->mp4) : ?>
                  <a target="_blank" href="<?php echo(esc_attr($video->assets->mp4)) ?>">Download</a>
                  <span class="links-separator">|</span>
                <?php endif; ?>
                <a class="custom-edit-link" target="_blank" href="https://teyuto.tv/dashboard#open_video=<?php echo esc_attr($video->id) ?>">Edit on Teyuto</a>
                <span class="links-separator">|</span>
                <input type="submit" class="button-link custom-delete-link wp-delete-permanently" name="deleteavideo" value="Delete permanently" />
                <input type="text" class="form-hidden-text-input" name="deletevideoid" value="<?php echo esc_attr($video->id) ?>" />
              </form>
              <a class="button-form-trigger button button-primary">Save</a>
            </div>
            <!-- End of Settings bar -->
          </div>
          <!-- End of Video general wrapper -->
        </div>
        <!-- Video popup right section -->
      </div>
      <!-- End of Single video popup -->

      <div class="single-video teyuto-trig" style="background-image: url('<?php echo isset($video->img_preview) && !empty($video->img_preview) ? esc_attr($video->img_preview) : 'https://placehold.co/800?text=x'; ?>');">
        <div class="filenamee"><?php echo(esc_html($video->title)) ?> <span style="background:#383838; font-size:11px; color:white; padding:3px; border-radius:4px;"><?php echo(esc_html($video->id)) ?></span></div>
      </div>
    <?php endforeach; ?>
  </div>
  <!-- End of Video section -->

  <!-- Pagination -->
    <div class="pagination-container">
    <?php
      // Generare i link di paginazione per le pagine precedenti e successive
      $prevPage = $currentPageNum - 1;
      $nextPage = $currentPageNum + 1;
    ?>
    Current page: <?php echo esc_attr($currentPageNum); ?>
    <?php
    $searcht = isset($_GET['searcht']) ? '&searcht=' . urlencode($_GET['searcht']) : '';

    $prevPageLink = "admin.php?page=teyuto-library&paget=$prevPage$searcht";
    $nextPageLink = "admin.php?page=teyuto-library&paget=$nextPage$searcht";
    ?>

    <a href="<?php echo esc_attr($prevPageLink); ?>" class="pagination-link">Previous</a>
    <a href="<?php echo esc_attr($nextPageLink); ?>" class="pagination-link">Next</a>

  </div>
  <!-- Fine della pagination -->
  
  <div onclick="teyuto_change_url()" id="bg-for-videos"></div>
<?php } ?>
