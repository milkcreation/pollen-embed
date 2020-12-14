'use strict'

import jQuery from 'jquery'
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widget'
import '../partial/embed'

jQuery(function ($) {
  $.widget('pollen.pollenEmbedField', {
    widgetEventPrefix: 'embed-field:',
    id: undefined,
    xhr: undefined,
    options: {},
    controls: {
      wrapper: '.FieldEmbed-wrapper',
      preview: '.FieldEmbed-preview'
    },
    player: undefined,

    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this._initOptions(this)
      this._initControls(this)
      this._initEvents(this)
    },
    // Initialisation des attributs de configuration.
    _initOptions: (self) => {
      $.extend(
          true,
          self.options,
          self.el.data('options') && $.parseJSON(decodeURIComponent(self.el.data('options'))) || {}
      )
    },
    // Initialisation des agents de contrôle.
    _initControls: (self) => {
      self.wrapper = $(self.controls.wrapper)
      if (!self.wrapper.length) {
        self.wrapper = self.el.wrap('<div class="FieldEmbed-wrapper"/>').parent()
      }

      self.preview = $(self.controls.preview)
      if (!self.preview.length) {
        self.preview = $('<div class="FieldEmbed-preview"/>').appendTo(self.wrapper)
      }
    },
    // Initialisation des événements.
    _initEvents: (self) => {
      let events = {
        'keyup': (e) => {
          self._keyUpdelay(self._fetchPreview(e, self), 500)
        },
        'loaded': (e) => {
          self._fetchPreview(e, self)
        }
      }
      self._on(self.el, events)

      self.el.trigger('loaded')
    },
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    _keyUpdelay: (fn, ms) => {
      let timer = 0

      return function(...args) {
        clearTimeout(timer)
        timer = setTimeout(fn.bind(this, ...args), ms || 0)
      }
    },
    _fetchPreview: (e, self) => {
      self.preview.empty()

      let value = self.el.val(),
          ajax = $.extend(self.option('ajax'), {data: {value: value}})

      $.ajax(ajax).done((resp) => {
        self.preview.html(resp.data)
      })
    }
    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------

    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------

  })

  $(document).ready(function () {
    $('[data-control="embed-field"]').pollenEmbedField()
    /*
    $.tify.observe('[data-control="embed"]', function (i, target) {
      $(target).pollenEmbed()
    }) */
  })
})