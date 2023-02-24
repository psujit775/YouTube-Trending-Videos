<?php
/**
 * Plugin Name: YouTube Trending Videos
 * Plugin URI: https://github.com/psujit775/YouTube-Trending-Videos.git
 * Description: Displays a list of YouTube trending videos.
 * Version: 1.0
 * Author: Sujit Patel
 * Author URI:
 * License: GPL3
 */

function yt_trending_videos_settings_init() {
  add_settings_section(
    'yt_trending_videos_api_settings',
    'API Settings',
    'yt_trending_videos_settings_section_callback',
    'yt_trending_videos'
  );

  add_settings_field(
    'yt_trending_videos_api_key',
    'YouTube API Key',
    'yt_trending_videos_api_key_callback',
    'yt_trending_videos',
    'yt_trending_videos_api_settings'
  );

  add_settings_field(
    'yt_trending_videos_country',
    'Country (you can use shorcode to get data country wise example: [yt_trending_videos country="IN"])',
    'yt_trending_videos_country_callback',
    'yt_trending_videos',
    'yt_trending_videos_api_settings'
  );

  register_setting(
    'yt_trending_videos',
    'yt_trending_videos_api_key'
  );

  register_setting(
    'yt_trending_videos',
    'yt_trending_videos_country'
  );
}
add_action('admin_init', 'yt_trending_videos_settings_init');

function yt_trending_videos_settings_section_callback() {
  echo '<p>Enter your YouTube API key and country below. You can get a key from the <a href="https://console.developers.google.com/apis/credentials" target="_blank">Google Cloud Console</a>.</p>';
}

function yt_trending_videos_api_key_callback() {
  $api_key = get_option('yt_trending_videos_api_key');
  echo '<input type="text" name="yt_trending_videos_api_key" value="' . esc_attr($api_key) . '" />';
}

function yt_trending_videos_country_callback() {
  $country = get_option('yt_trending_videos_country');
  echo '<input type="text" name="yt_trending_videos_country" value="' . esc_attr($country) . '" />';
}

function yt_trending_videos_settings_page() {
  ?>
  <div class="wrap">
    <h1>YouTube Trending Videos Settings</h1>
    <form action="options.php" method="post">
      <?php settings_fields('yt_trending_videos'); ?>
      <?php do_settings_sections('yt_trending_videos'); ?>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

function yt_trending_videos_admin_menu() {
  add_options_page(
    'YouTube Trending Videos Settings',
    'YouTube Trending Videos',
    'manage_options',
    'yt_trending_videos',
    'yt_trending_videos_settings_page'
  );
}
add_action('admin_menu', 'yt_trending_videos_admin_menu');

function convert_count($view_count) {
    if ($view_count >= 1000000) {
        return round($view_count / 1000000, 1) . 'M';
    } else if ($view_count >= 1000) {
        return round($view_count / 1000, 1) . 'K';
    } else {
        return $view_count;
    }
}

function yt_trending_videos_shortcode( $atts ) {
  $atts = shortcode_atts( array(
    'country' => get_option( 'yt_trending_videos_country', 'US' ),
  ), $atts );

  $api_key = get_option( 'yt_trending_videos_api_key', '' );
  $cache_key = 'yt_trending_videos_' . $atts['country'];

  // Check if results are already cached
  $videos = get_transient( $cache_key );

  if ( $videos === false ) {
    // Results not cached, make API request
    $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&maxResults=50&regionCode=' . $atts['country'] . '&key=' . $api_key;
    $response = wp_remote_get( $url );

    if ( is_wp_error( $response ) ) {
      return '<p>Error retrieving trending videos</p>';
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body );

    if ( ! isset( $data->items ) ) {
      return '<p>No trending videos found</p>';
    }

    // Cache the results for 15 minutes
    set_transient( $cache_key, $data->items, 15 * MINUTE_IN_SECONDS );

    $videos = $data->items;
  }
  $output = '<div class="yt-trending-videos">';
  $output .= '<ul>';

  foreach ( $videos as $video ) {
    $title = $video->snippet->title;
    $thumbnail_url = $video->snippet->thumbnails->medium->url;
    $video_url = "https://www.youtube.com/watch?v=" . $video->id;
    $channelTitle = $video->snippet->channelTitle;
    $publishTime = $video->snippet->publishedAt;
    $likes = convert_count($video->statistics->likeCount);
    $views = convert_count($video->statistics->viewCount);
    $commentCount = convert_count($video->statistics->commentCount);

    $dateTime = new DateTime($publishTime, new DateTimeZone('UTC'));
    $dateTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
    $formattedDate = $dateTime->format('M d, Y h:i A');

    $output .= '<li>';
    $output .= '<img src="' . $thumbnail_url . '" alt="' . $title . '" />';
    $output .= '<p class="yt-h4" ><b><a href="' . $video_url . '">' . $title . '</a></b></p>';
    $output .= '<p class="yt-p1"><b>Uploaded By:</b> ' . $channelTitle . '</p>';
    $output .= '<p class="yt-p1"><b>Uploaded At:</b> ' . $formattedDate .'</p>';
    $output .= '<p class="yt-p2"><b>Likes:</b> ' . $likes . ', <b>Views:</b> ' . $views . ', <b>Comments:</b> ' . $commentCount .'</p>';
    $output .= '</li>';
  }

  $output .= '</ul>';
  $output .= '</div>';

  return $output;
}

function yt_trending_videos_enqueue_scripts() {
  wp_enqueue_style( 'yt-trending-videos-style', plugins_url( 'style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'yt_trending_videos_enqueue_scripts' );

function yt_trending_videos_shortcode_init() {
  add_shortcode( 'yt_trending_videos', 'yt_trending_videos_shortcode' );
}

add_action( 'init', 'yt_trending_videos_shortcode_init' );
