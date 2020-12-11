'use strict'

import jQuery from 'jquery'
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widget'
import canAutoPlay from 'can-autoplay'
import VideoJs from 'video.js'
import YouTubePlayer from 'youtube-player'
import 'presstify-framework/observer/js/scripts'

jQuery(function ($) {
  $.widget('pollen.pollenEmbed', {
    widgetEventPrefix: 'embed:',
    id: undefined,
    xhr: undefined,
    options: {},
    controls: {},
    player: undefined,

    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this._initOptions()
      this._initEvents()
      this._initControls()
    },
    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      )
    },
    _initEvents: function () {
      let events = {}

      this._on(this.el, events)
    },
    _initControls: function () {
      let self = this

      if (self.player === undefined) {
        let playerID = this.el.attr('id'),
            provider = this.el.data('provider'),
            videoParams = this.el.data('video-params') || undefined

        if (!playerID) {
          playerID = 'Embed--' + this.uuid
          this.el.attr('id', playerID)
        }

        if (videoParams) {
          videoParams = $.parseJSON(decodeURIComponent(videoParams)) || {}
        } else {
          videoParams = {}
        }

        switch (provider) {
          case 'video' :
            self.player = new VideoJs(playerID, videoParams)
            /**
             * @see https://blog.videojs.com/autoplay-best-practices-with-video-js/
             */
            self.player.ready(() => {
              if (videoParams.autoplay) {
                canAutoPlay.video().then(({result, error}) => {
                  if (result === true) {
                    self.player.play()
                  } else {
                    console.log(error)
                  }
                })
              }
            })
            break;
          case 'youtube':
            let videoId = this.el.data('video-id')

            self.player = new YouTubePlayer(playerID, {videoId: videoId, playerVars: videoParams})
            self.player.on('ready', (e) => {
              let player = e.target
              player.mute()
              if (videoParams.autoplay) {
                player.playVideo()
              }
            })
            break
        }


      }
    }
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------

    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------

    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------

  })

  $(document).ready(function () {
    $('[data-control="embed"]').pollenEmbed()
    /*
    $.tify.observe('[data-control="embed"]', function (i, target) {
      $(target).pollenEmbed()
    }) */
  })
})