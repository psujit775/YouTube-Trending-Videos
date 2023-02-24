# YouTube Trending Videos

This plugin displays a list of YouTube trending videos on your website. The plugin provides an admin page where you can enter your YouTube API key and country. You can get a key from the Google Cloud Console.

Google Cloud Console : https://console.developers.google.com/apis/credentials


# Installation

    Download the plugin zip file from the GitHub repository.
    Login to your WordPress dashboard and navigate to Plugins > Add New > Upload Plugin.
    Upload the zip file and click Install Now.
    Activate the plugin.

# Usage

You can display the trending videos on any page or post using the [yt_trending_videos] shortcode. By default, the shortcode displays the trending videos for the country specified in the plugin settings. You can override the country by passing the country parameter in the shortcode. For example, to display the trending videos for India, use the shortcode [yt_trending_videos country="IN"].


# Settings

To access the plugin settings, go to Settings > YouTube Trending Videos. Here you can enter your YouTube API key and country. The plugin will use the country specified here to display the trending videos.

## Shortcode Parameters

The `[yt_trending_videos]` shortcode supports the following parameters:

country - The country to display the trending videos for. This parameter overrides the country specified in the plugin settings. 

Example: `[yt_trending_videos country="IN"]`.

# Examples

To display the trending videos on a page or post, use the [yt_trending_videos] shortcode. Here are some examples:

    [yt_trending_videos] - Displays the trending videos for the country specified in the plugin settings.
    [yt_trending_videos country="US"] - Displays the trending videos for the United States.
    [yt_trending_videos country="GB"] - Displays the trending videos for the United Kingdom.
    [yt_trending_videos country="IN"] - Displays the trending videos for India.
