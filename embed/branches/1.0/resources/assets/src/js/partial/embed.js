'use strict'

import jQuery from 'jquery'
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widget'
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
            videoId = this.el.data('video-id'),
            playerVars = $.parseJSON(decodeURIComponent(this.el.data('player-vars'))) || {}

        if (!playerID) {
          playerID = 'Embed--' + this.uuid
          this.el.attr('id', playerID)
        }

        self.player = new YouTubePlayer(playerID, {
          videoId: videoId,
          playerVars: playerVars
        })

        self.player.on('ready', (e) => {
          let player = e.target
          player.mute()
          if (playerVars.autoplay) {
            player.playVideo()
          }
        })

        $('.ArticleTitle-content').click(function () {
          self.player.playVideo()
        })
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

    $.tify.observe('[data-control="embed"]', function (i, target) {
      $(target).pollenEmbed()
    })
  })
})