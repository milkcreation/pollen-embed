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
      inputWrapper: '.FieldEmbed-input',
      wrapper: '.FieldEmbed-wrapper',
      preview: '.FieldEmbed-preview',
      spinner: '.FieldEmbed-spinner'
    },
    player: undefined,
    keyUpTimeOut: null,

    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this._initOptions(this)
      this._initControls(this)
      this._initEvents(this)

      this.el.trigger('loaded')
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

      self.inputWrapper = $(self.controls.inputWrapper)
      if (!self.inputWrapper.length) {
        self.inputWrapper = self.el.wrap('<div class="FieldEmbed-inputWrapper"/>').parent()
      }

      self.spinner = $(self.controls.spinner)
      if (!self.controls.spinner.length) {
        self.controls.spinner = $('<div class="FieldEmbed-spinner"/>').appendTo(self.inputWrapper)
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
          self._keyUpDelay(e, self, 500)
        },
        'loaded': (e) => {
          self._fetchPreview(e, self)
        }
      }
      self._on(self.el, events)
    },
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    // Delai de saisie
    _keyUpDelay: (e, self, ms) => {
      if (self.keyUpTimeOut !== null) {
        clearTimeout(self.keyUpTimeOut)
      }
      self.keyUpTimeOut = setTimeout(() => {
        self.keyUpTimeOut = null
        self._fetchPreview(e, self)
      }, ms)
    },
    // Récupération de l'aperçu
    _fetchPreview: (e, self) => {
      let value = self.el.val()

      if (self.value !== value) {
        self.spinner.show()
        self.el.prop('disabled', true)

        if (self.xhr !== undefined) {
          self.xhr.abort()
          self.xhr = undefined
        }

        self.preview.empty()
        let ajax = $.extend(self.option('ajax'), {data: {value: value}})

        self.xhr = $.ajax(ajax)
            .done((resp) => {
              self.preview.html(resp.data.render)
            })
            .always(() => {
              self.spinner.hide()
              self.el.prop('disabled', false).focus()
            })

        self.value = value
      }
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