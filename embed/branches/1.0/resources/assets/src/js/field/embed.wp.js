'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import './embed';
import 'presstify-framework/partial/media-library/js/scripts';

jQuery(function ($) {
  /** @param {Object} $.pollen */
  $.widget('pollen.pollenEmbedField', $.pollen.pollenEmbedField, {
    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation.
    _create: function () {
      this._super();
    },
    // INITIALISATIONS.
    // -----------------------------------------------------------------------------------------------------------------
    // Initialisation des événements.
    _initControls: (self) => {
      self._super(self);

      $.extend(true, self.controls, {mediaButton: '.FieldEmbed-mediaButton'})

      self.mediaButton = $(self.controls.mediaButton)
      if (!self.mediaButton.length) {
        self.mediaButton = $('<button class="FieldEmbed-mediaButton">+</button>').appendTo(self.inputWrapper)
      }
      self.mediaLibrary = self.mediaButton.tifyMediaLibrary({multiple: false, library: {type: 'video'}})
      self.mediaButton.on('media-library:select', function (e, selection) {
        let video = selection[0] || {};

        self.el.val(video.url);
        self._fetchPreview(e, self)
      });

    },
    // Initialisation des événements.
    _initEvents: (self) => {
      self._super(self);

      let events = {
        'click': (e) => {
          self._getFromMediaLibrary(e, self)
        }
      }
      self._on(self.mediaButton, events)
    },
    // Récupération de la vidéo d'un élément depuis la médiathèque.
    _getFromMediaLibrary: (e, self) => {
      e.preventDefault();

      self.mediaLibrary.tifyMediaLibrary('open');
    },
  });
});