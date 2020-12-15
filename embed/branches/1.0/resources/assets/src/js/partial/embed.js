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
    handler: undefined,
    provider:undefined,

    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this.el.attr('data-control', 'embed-loaded');

      this._initOptions(this)
      this._initEvents(this)
      this._initControls(this)
    },
    // Initialisation des attributs de configuration.
    _initOptions: (self) => {
      $.extend(
          true,
          self.options,
          self.el.data('options') && $.parseJSON(decodeURIComponent(self.el.data('options'))) || {}
      )
    },
    _initEvents: (self) => {
      let events = {}

      self._on(self.el, events)
    },
    _initControls: (self) => {
      self.provider = self.el.data('provider')

      switch (self.provider) {
        case 'defered' :
          let ajax = self.option('ajax')

          $.ajax(ajax).done((resp) => {
            self.el.replaceWith(resp.data)

            if (resp.success) {
              self.provider = self.el.data('provider')
              self._doProviderInit(self)
            }
          })

          break;
        default :
          self._doProviderInit(self)
          break
      }
    },
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------

    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------
    _doProviderInit: (self) => {
      let id = self.el.attr('id'),
          params = self.el.data('params') || undefined

      params = params ? $.parseJSON(decodeURIComponent(params)) || {} : {}

      if (!id) {
        id = 'Embed--' + self.uuid
        self.el.attr('id', id)
      }

      switch(self.provider) {
        case 'video' :
          self.handler = new VideoJs(id, params)
          /**
           * @see https://blog.videojs.com/autoplay-best-practices-with-video-js/
           */
          self.handler.ready(() => {
            if (params.autoplay) {
              self.handler.muted(true)

              canAutoPlay.video().then(({result, error}) => {
                if (result === true) {
                  self.handler.play()
                } else {
                  console.log(error)
                }
              })
            }
          })
          break;
        case 'youtube' :
          let videoId = self.el.data('video-id')

          self.handler = new YouTubePlayer(id, {videoId: videoId, playerVars: params})
          self.handler.on('ready', (e) => {
            let player = e.target

            if (params.autoplay) {
              player.mute()
              player.playVideo()
            }
          })

          if (params.loop) {
            self.handler.on('stateChange', function (state) {
              if (state.data === 0) {
                self.handler.playVideo()
              }
            })
          }
          break
      }
    }
    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------

  })

  $(document).ready(function () {
    $('[data-control="embed"]').pollenEmbed()

    $.tify.observe('[data-control="embed"]', function (i, target) {
      $(target).pollenEmbed()
    })
  })
})