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
    controls: {
      wrapper: '.Embed-wrapper'
    },
    handler: undefined,
    provider: undefined,

    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      if (this.el.data('control') === 'embed') {
        this.el.attr('data-control', 'embed-loaded')
      }

      this._initOptions(this)
      this._initEvents(this)
      this._initControls(this)
      this._initLoad(this)
    },
    // Initialisation des attributs de configuration.
    _initOptions: (self) => {
      $.extend(
          true,
          self.options,
          self.el.data('options') && $.parseJSON(decodeURIComponent(self.el.data('options'))) || {}
      )

      self.ready = self.option('ready') || function (self) {
      }

      self.change = self.option('change') || function (self) {
      }
    },
    _initEvents: (self) => {
      let events = {}

      self._on(self.el, events)
    },
    _initControls: (self) => {
      self.id = self.el.attr('id')

      if (!self.id) {
        self.id = 'Embed--' + self.uuid
        self.el.attr('id', self.id)
      }

      self.wrapper = $(self.controls.wrapper)
      if (!self.wrapper.length) {
        self.wrapper = self.el.wrap('<div class="Embed-wrapper"/>').parent()
      }

      self.container = self.wrapper.wrap('<div class="Embed-container"/>').parent()
    },
    _initLoad: (self) => {
      self.provider = self.el.data('provider')

      switch (self.provider) {
        case 'defered' :
          let ajax = self.option('ajax')

          $.ajax(ajax).done((resp) => {
            if (resp.success) {
              self.container.html(resp.data)
              self.el = $('#' + self.id)

              self.provider = self.el.data('provider')
              self._doProviderInit(self)
            }
          })
          break
        default :
          self._doProviderInit(self)
          break
      }
    },
    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------
    _doProviderInit: (self) => {
      let params = self.el.data('params') || undefined
      params = params ? $.parseJSON(decodeURIComponent(params)) || {} : {}

      switch (self.provider) {
        case 'video' :
          self.handler = new VideoJs(self.id, params)

          self.handler.ready(() => {
            self.ready(self)

            if (params.autoplay) {
              self._doAutoplayVideo(self.handler, self)
            }
          })

          self.handler.on('statechanged', function (e) {
              // @todo
          })
          break
        case 'youtube' :
          self.handler = new YouTubePlayer(self.id, {videoId: self.el.data('video-id'), playerVars: params})

          self.handler.on('ready', () =>  {
            self.ready(self)

            if (params.autoplay) {
              self._doAutoplayVideo(self.handler, self)
            }
          })

          /**
           * Etats
           * -1 : non démarré
           * 0 : arrêté
           * 1 : en lecture
           * 2 : en pause
           * 3 : en mémoire tampon
           * 5 : en file d'attente
           */
          self.handler.on('stateChange', (state) => {
            self.change(state, self)

            if (params.loop) {
              self._doLoopVideo(state, self.handler, self)
            }
          })
          break
      }
    },
    _doAutoplayVideo: (player, self) => {
      switch (self.provider) {
        case 'video' :
          $('#' + self.id).ready(() => {
            self._doMuteVideo(player, self)

            canAutoPlay.video().then(({result, error}) => {
              if (result === true) {
                self._doPlayVideo(player, self)
              } else {
                //console.log(error)
              }
            })
          })
          break
        case 'youtube' :
          self._doMuteVideo(player, self)
          self._doPlayVideo(player, self)
          break
      }
    },
    _doFullScreenVideo: (player, self) => {
      switch (self.provider) {
        case 'video' :
          player.requestFullscreen()
          break
        case 'youtube' :
          let iframe = $('#' + self.id).get(0)

          if (iframe.requestFullScreen) {
            iframe.requestFullScreen()
          } else if (iframe.mozRequestFullScreen) {
            iframe.mozRequestFullScreen()
          } else if (iframe.webkitRequestFullScreen) {
            iframe.webkitRequestFullScreen()
          }
          break
      }
    },
    _doLoopVideo: (state, player, self) => {
      switch (self.provider) {
        case 'youtube' :
          if (state.data === 0) {
            self._doPlayVideo(player, self)
          }
          break
      }
    },
    _doMuteVideo: (player, self) => {
      switch (self.provider) {
        case 'video' :
          player.muted(true)
          break
        case 'youtube' :
          player.mute()
          break
      }
    },
    _doPlayVideo: (player, self) => {
      switch (self.provider) {
        case 'video' :
          player.play()
          break
        case 'youtube' :
          player.playVideo()
          break
      }
    },
    _doSeekToVideo(to = 0, player, self) {
      switch (self.provider) {
        case 'video' :
          player.currentTime(to)
          break
        case 'youtube' :
          player.seekTo(to)
          break
      }
    },
    _doUnmuteVideo: (player, self) => {
      switch (self.provider) {
        case 'video' :
          player.muted(false)
          break
        case 'youtube' :
          player.unMute()
          break
      }
    },
    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------
    play: function () {
      this._doPlayVideo(this.handler, this)

      return this
    },
    fs: function () {
      return this.fullscreen()
    },
    fullscreen: function () {
      this._doFullScreenVideo(this.handler, this)

      return this
    },
    mute: function () {
      this._doMuteVideo(this.handler, this)

      return this
    },
    restart: function () {
      return this.seekTo()
    },
    seekTo: function (to = 0) {
      this._doSeekToVideo(to, this.handler, this)

      return this
    },
    unmute: function () {
      this._doUnmuteVideo(this.handler, this)

      return this
    }
  })

  // Auto-chargement
  $(document).ready(function () {
    $('[data-control="embed"]').pollenEmbed()

    $.tify.observe('[data-control="embed"]', function (i, target) {
      $(target).pollenEmbed()
    })
  })
})