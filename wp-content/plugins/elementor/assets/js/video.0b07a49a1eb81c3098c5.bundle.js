"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["video"],{

/***/ "../assets/dev/js/frontend/handlers/video.js":
/*!***************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/video.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
class Video extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        imageOverlay: '.elementor-custom-embed-image-overlay',
        video: '.elementor-video',
        videoIframe: '.elementor-video-iframe',
        playIcon: '.elementor-custom-embed-play'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $imageOverlay: this.$element.find(selectors.imageOverlay),
      $video: this.$element.find(selectors.video),
      $videoIframe: this.$element.find(selectors.videoIframe),
      $playIcon: this.$element.find(selectors.playIcon)
    };
  }
  handleVideo() {
    if (this.getElementSettings('lightbox')) {
      return;
    }
    if ('youtube' === this.getElementSettings('video_type')) {
      this.apiProvider.onApiReady(apiObject => {
        this.elements.$imageOverlay.remove();
        this.prepareYTVideo(apiObject, true);
      });
    } else {
      this.elements.$imageOverlay.remove();
      this.playVideo();
    }
  }
  playVideo() {
    if (this.elements.$video.length) {
      // This.youtubePlayer exists only for YouTube videos, and its play function is different.
      if (this.youtubePlayer) {
        this.youtubePlayer.playVideo();
      } else {
        this.elements.$video[0].play();
      }
      return;
    }
    const $videoIframe = this.elements.$videoIframe,
      lazyLoad = $videoIframe.data('lazy-load');
    if (lazyLoad) {
      $videoIframe.attr('src', lazyLoad);
    }
    if (this.getElementSettings('autoplay')) {
      $videoIframe.attr('allow', 'autoplay');
      $videoIframe[0].src = this.apiProvider.getAutoplayURL($videoIframe[0].src);
    }
  }
  async animateVideo() {
    const lightbox = await elementorFrontend.utils.lightbox;
    lightbox.setEntranceAnimation(this.getCurrentDeviceSetting('lightbox_content_animation'));
  }
  async hideLightbox() {
    const lightbox = await elementorFrontend.utils.lightbox;
    lightbox.getModal().hide();
  }
  prepareYTVideo(YT, onOverlayClick) {
    const elementSettings = this.getElementSettings(),
      playerOptions = {
        videoId: this.videoID,
        events: {
          onReady: () => {
            if (elementSettings.mute) {
              this.youtubePlayer.mute();
            }
            if (elementSettings.autoplay || onOverlayClick) {
              this.youtubePlayer.playVideo();
            }
          },
          onStateChange: event => {
            if (event.data === YT.PlayerState.ENDED && elementSettings.loop) {
              this.youtubePlayer.seekTo(elementSettings.start || 0);
            }
          }
        },
        playerVars: {
          controls: elementSettings.controls ? 1 : 0,
          rel: elementSettings.rel ? 1 : 0,
          playsinline: elementSettings.play_on_mobile ? 1 : 0,
          cc_load_policy: elementSettings.cc_load_policy ? 1 : 0,
          autoplay: elementSettings.autoplay ? 1 : 0,
          start: elementSettings.start,
          end: elementSettings.end
        }
      };

    // To handle CORS issues, when the default host is changed, the origin parameter has to be set.
    if (elementSettings.yt_privacy) {
      playerOptions.host = 'https://www.youtube-nocookie.com';
      playerOptions.origin = window.location.hostname;
    }
    this.youtubePlayer = new YT.Player(this.elements.$video[0], playerOptions);
  }
  bindEvents() {
    this.elements.$imageOverlay.on('click', this.handleVideo.bind(this));
    this.elements.$playIcon.on('keydown', event => {
      const playKeys = [13,
      // Enter key.
      32 // Space bar key.
      ];
      if (playKeys.includes(event.keyCode)) {
        this.handleVideo();
      }
    });
  }
  onInit() {
    super.onInit();
    const elementSettings = this.getElementSettings();
    if (elementorFrontend.utils[elementSettings.video_type]) {
      this.apiProvider = elementorFrontend.utils[elementSettings.video_type];
    } else {
      this.apiProvider = elementorFrontend.utils.baseVideoLoader;
    }
    if ('youtube' !== elementSettings.video_type) {
      // Currently the only API integration in the Video widget is for the YT API
      return;
    }
    this.videoID = this.apiProvider.getVideoIDFromURL(elementSettings.youtube_url);

    // If there is an image overlay, the YouTube video prep method will be triggered on click
    if (!this.videoID) {
      return;
    }

    // If the user is using an image overlay, loading the API happens on overlay click instead of on init.
    if (elementSettings.show_image_overlay && elementSettings.image_overlay.url) {
      return;
    }
    if (elementSettings.lazy_load) {
      this.intersectionObserver = elementorModules.utils.Scroll.scrollObserver({
        callback: event => {
          if (event.isInViewport) {
            this.intersectionObserver.unobserve(this.elements.$video.parent()[0]);
            this.apiProvider.onApiReady(apiObject => this.prepareYTVideo(apiObject));
          }
        }
      });

      // We observe the parent, since the video container has a height of 0.
      this.intersectionObserver.observe(this.elements.$video.parent()[0]);
      return;
    }
    this.apiProvider.onApiReady(apiObject => this.prepareYTVideo(apiObject));
  }
  onElementChange(propertyName) {
    if (0 === propertyName.indexOf('lightbox_content_animation')) {
      this.animateVideo();
      return;
    }
    const isLightBoxEnabled = this.getElementSettings('lightbox');
    if ('lightbox' === propertyName && !isLightBoxEnabled) {
      this.hideLightbox();
    }
  }
}
exports["default"] = Video;

/***/ })

}]);
//# sourceMappingURL=video.0b07a49a1eb81c3098c5.bundle.js.map