=== Videojs HTML5 Player ===
Contributors: naa986
Donate link: https://wphowto.net/
Tags: video, wpvideo, flash, html5, iPad, iphone, ipod, mobile, playlists, embed video, videojs, flash player, player, video player, embed, lightweight, minimal, myvideo, responsive  
Requires at least: 4.2
Tested up to: 4.9
Stable tag: 1.1.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Embed video file beautifully in WordPress using Videojs HTML5 Player. Embed HTML5 compatible responsive video in your post/page with video js.

== Description ==

[Videojs HTML5 Player](https://wphowto.net/videojs-html5-player-for-wordpress-757) is a user-friendly plugin that supports video playback on desktops and mobile devices. It makes super easy for you to embed both self-hosted video files or video files that are externally hosted using Videojs library.

= Requirements =

* A self-hosted website running on [WordPress hosting](https://wphowto.net/best-cheap-wordpress-hosting-1689)

= Videojs HTML5 Player Features =

* Embed MP4 video files into a post/page or anywhere on your WordPress site
* Embed responsive videos for a better user experience while viewing from a mobile device
* Embed HTML5 videos which are compatible with all major browsers
* Embed videos with poster images
* Embed videos using videojs player
* Automatically play a video when the page is rendered
* Embed videos uploaded to your WordPress media library using direct links in the shortcode
* No setup required, simply install and start embedding videos
* Lightweight and compatible with the latest version of WordPress
* Clean and sleek player with no watermark
* fallbacks for other HTML5-supported filetypes (WebM, Ogv)

= Videojs HTML5 Player Plugin Usage =

In order to embed a video create a new post/page and use the following shortcode:

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4"]`

Here, "url" is the location of the MP4 video source file (H.264 encoded). You need to replace the sample URL with the actual URL of the video file.

= Video Shortcode Options =

The following options are supported in the shortcode.

**WebM**

You can specify a WebM video file in addition to the source MP4 video file. This parameter is optional.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" webm="http://example.com/wp-content/uploads/videos/myvid.webm"]`

**Ogv**

You can specify a Ogv video file in addition to the source MP4 & WebM video files. This parameter is optional.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" webm="http://example.com/wp-content/uploads/videos/myvid.webm" ogv="http://example.com/wp-content/uploads/videos/myvid.ogv"]`

**Width**

Defines the width of the video file (Height is automatically calculated). This option is not required unless you want to limit the maximum width of the video.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" width="480"]`

**Preload**

Specifies if and how the video should be loaded when the page loads. Defaults to "auto" (the video should be loaded entirely when the page loads). Other options:

* "metadata" - only metadata should be loaded when the page loads
* "none" - the video should not be loaded when the page loads

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" preload="metadata"]`

**Controls**

Specifies that video controls should be displayed. Defaults to "true". In order to hide controls set this parameter to "false".

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" controls="false"]`

When you disable controls users will not be able to interact with your videos. So It is recommended that you enable autoplay for a video with no controls.

**Autoplay**

Causes the video file to automatically play when the page loads.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" autoplay="true"]`

**Poster**

Defines image to show as placeholder before the video plays.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" poster="http://example.com/wp-content/uploads/poster.jpg"]`

**Loop**

Causes the video file to loop to beginning when finished and automatically continue playing.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" loop="true"]`

**Muted**

Specifies that the audio output of the video should be muted.

`[videojs_video url="http://example.com/wp-content/uploads/videos/myvid.mp4" muted="true"]`

For detailed documentation please visit the [Videojs HTML5 Player](https://wphowto.net/videojs-html5-player-for-wordpress-757) plugin page

== Installation ==

1. Go to the Add New plugins screen in your WordPress Dashboard
1. Click the upload tab
1. Browse for the plugin file (videojs-html5-player.zip) on your computer
1. Click "Install Now" and then hit the activate button

== Frequently Asked Questions ==

= Can this plugin be used to embed videos in WordPress? =

Yes.

= Are the videos embedded by this plugin playable on iOS devices? =

Yes.

= Can I embed responsive videos using this plugin? =

Yes.

== Screenshots ==

1. Videojs Player Demo

== Upgrade Notice ==
none

== Changelog ==

= 1.1.2 =

* Videojs HTML5 Player is now compatible with WordPress 4.9.

= 1.1.1 =

* Added support for playsinline attribute which allows a video to play inline on iOS (the video will not automatically enter fullscreen mode when playback begins).

= 1.1.0 =

* Videojs script is now enqueued in the footer to avoid a JavaScript setup error.

= 1.0.9 =

* Made jQuery a dependency for the videojs script.

= 1.0.8 =

* Updated the translation files so the plugin can take advantage of language packs.
* Videojs HTML5 Player is now compatible with WordPress 4.4.

= 1.0.7 =

* Added a new shortcode parameter to accept Ogv as a video source format.

= 1.0.6 =

* Added a new shortcode parameter to accept WebM as a video source format.

= 1.0.5 =

* Updated the Videojs library to 5.0.0

= 1.0.4 =

* Videojs HTML5 Player is now compatible with WordPress 4.3

= 1.0.3 =

* Added an option to mute the audio output of a video
* Added an option to loop a video

= 1.0.2 =

* Added an option to show/hide controls
* Added an option to set preload attribute

= 1.0.1 =

* First commit
