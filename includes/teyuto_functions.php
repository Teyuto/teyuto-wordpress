<?php

// getVideos
function teyutowp_get_videos($client, $page = 1, $search = null) {

    $url = 'https://api.teyuto.tv/v2/videos?page=' . $page;
    
    if ($search !== null) {
        $url .= '&search=' . urlencode($search);
    }

    $data = wp_remote_get($url, array(
        'headers' => array(
            'channel' => $client->channel, 
            'Authorization' => $client->apiKey,
            'Content-Type' => 'application/json; charset=utf-8'
        ),
    ));

    return json_decode(wp_remote_retrieve_body($data));
}

// deleteVideo
function teyutowp_delete_video($client, $id=null) {
    if($id){
        $url = 'https://api.teyuto.tv/v2/videos/' . $id;
        $response = wp_remote_request($url, array(
            'method'  => 'DELETE',
            'headers' => array(
                'channel'        => $client->channel,
                'Authorization'  => $client->apiKey,
                'Content-Type'   => 'application/json; charset=utf-8'
            ),
        ));
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return false;
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code === 200) {
               return true;
            } else {
               return false;
            }
        }
    }
}

// patchVideo
function teyutowp_patch_video($client, $id=null, $patch_data=array()) {
    echo $id;
    if($id){
        $url = 'https://api.teyuto.tv/v2/videos/' . $id;

        $form_data = array();
        foreach ($patch_data as $key => $value) {
            $form_data[$key] = $value;
        }

        $form_data_encoded = http_build_query($form_data);

        $response = wp_remote_request($url, array(
            'method'    => 'PATCH',
            'headers'   => array(
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization'=> $client->apiKey
            ),
            'body' => $form_data_encoded,
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return false;
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code === 200) {
                return true;
            } else {
                return false;
            }
        }
    }
}


// Get client function
function teyutowp_get_client()
{
    $apikey = get_option("teyuto_api_key");
    $channel = get_option("teyuto_channel");

    $data = wp_remote_post('https://api.teyuto.tv/v2/videos', array(
        'headers' => array('channel' => $channel, 'Authorization' => $apiKey,'Content-Type' => 'application/json; charset=utf-8'),
        'method' => 'GET',
        'data_format' => 'body',
    ));
    $result = json_decode(wp_remote_retrieve_body($data));

    $data2 = wp_remote_post('https://api.teyuto.tv/v1/user/?f=settings_company&cid='.$channel, array(
        'method' => 'GET',
        'data_format' => 'body',
    ));
    $result2 = json_decode(wp_remote_retrieve_body($data2));
    update_option("teyuto_domain", sanitize_text_field($result2->domain));

    if($result->status != '401'){
        $client = array("channel" => $channel, "apiKey" => $apikey, "domain" => $result2->domain);
        $client = json_encode($client);
        $client = json_decode($client, false);
    }else{
        $client = false;
    }

    return $client;
}

// convert video date and return it
function teyutowp_get_video_date($date)
{
    $video_created_at = substr($date, 0, 10);
    $video_created_at = date_create($video_created_at);
    return date_format($video_created_at, 'F j, Y');
}

// For fancy tags
function teyutowp_tags($arr)
{
    $numItems = count($arr);
    $i = 0;
    $value = $arr;
    foreach ($value as $tag):
        if (++$i === $numItems) {
            echo esc_html($tag);
        } else {
            echo esc_html($tag) . ",";
        }
    endforeach;
}

// function for generate shortcode
function teyutowp_shortcode($atts)
{
    $videoId = $atts['id']; 
    $aspectRatio = $atts['aspect-ratio']; 
    if(preg_match_all('/^[a-zA-Z0-9]+$/', $videoId) == 0) {
        return "";
    } 

    $domain = get_option("teyuto_domain");
    if($domain=='' || !$domain){
        return "";
    }  

    $urlVideo = "https://$domain/video/player?w=$videoId";

    if($aspectRatio == '16/9'){
        return '<div class="teyuto-video-iframe" style="position: relative; width: 100%; padding-top: 56.25%;">
        <iframe loading="lazy" src="'.$urlVideo.'" frameborder="0" scrolling="no" allowfullscreen="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
        </div>';
    }else if($aspectRatio == '9/16'){
        return '<div class="teyuto-video-iframe" style="position: relative; width: 100%; padding-top: 177.7777777778%;">
        <iframe loading="lazy" src="'.$urlVideo.'" frameborder="0" scrolling="no" allowfullscreen="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
        </div>';
    }else{
        return '<div class="teyuto-video-iframe"><iframe loading="lazy" src="'.$urlVideo.'" width="100%" height="100%" frameborder="0" scrolling="no" allowfullscreen="true"></iframe></div>';
    }
}

add_shortcode('teyuto', 'teyutowp_shortcode');

// Register Settings in plugin list
function teyutowp_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=settings-teyuto">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter("plugin_action_links_teyuto/teyuto.php", 'teyutowp_settings_link');

// teyuto register css for backend
function teyutowp_include_css()
{
    wp_register_style('teyuto-style', plugins_url('../assets/style.css', __FILE__));
    wp_enqueue_style('teyuto-style');
}
add_action('admin_init', 'teyutowp_include_css');

// teyuto register script for backend
function teyutowp_scripts()
{
    wp_register_script( 'Tus', 'https://cdn.jsdelivr.net/npm/tus-js-client@latest/dist/tus.min.js', null, null, true );
    wp_enqueue_script('Tus');
    wp_enqueue_script('teyuto_script', plugins_url('../assets/script.js', __FILE__));
    wp_enqueue_script('jquery-ui-core');
}
add_action('admin_enqueue_scripts', 'teyutowp_scripts');
