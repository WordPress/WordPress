/* global wp */

/**
 * media-library.js
 *
 * Adapted from WordPress
 *
 * @copyright 2017 by the WordPress contributors.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * This program incorporates work covered by the following copyright and
 * permission notices:
 *
 * b2 is (c) 2001, 2002 Michel Valdrighi - m@tidakada.com - http://tidakada.com
 *
 * b2 is released under the GPL
 *
 * WordPress - Web publishing software
 *
 * Copyright 2003-2010 by the contributors
 *
 * WordPress is released under the GPL
 */

var Select = wp.media.view.MediaFrame.Select,
  Library = wp.media.controller.Library,
  l10n = wp.media.view.l10n;

wp.media.view.MediaFrame.ETSelect = wp.media.view.MediaFrame.Select.extend({
  initialize: function() {
    _.defaults( this.options, {
      multiple: true,
      editing: false,
      state: 'insert',
      metadata: {},
      title: l10n.insertMediaTitle,
      button: {
        text: l10n.insertIntoPost
      },
    });

    // Call 'initialize' directly on the parent class.
    Select.prototype.initialize.apply( this, arguments );
    this.createIframeStates();
  },

  /**
   * Create the default states.
   */
  createStates: function() {
    var options = this.options;

    this.states.add([
      // Main states.
      new Library({
        id:         'insert',
        title:      options.title,
        priority:   20,
        toolbar:    'main-insert',
        filterable: 'all',
        library:    wp.media.query( options.library ),
        multiple:   options.multiple ? 'reset' : false,
        editable:   true,
        allowLocalEdits: true,
        displaySettings: true,
        displayUserSettings: true
      }),

      // Embed states.
      new wp.media.controller.Embed( { metadata: options.metadata } ),
    ]);
  },

  bindHandlers: function() {
    var handlers;

    Select.prototype.bindHandlers.apply( this, arguments );

    this.on( 'toolbar:create:main-insert', this.createToolbar, this );
    this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

    handlers = {
      content: {
        'embed': 'embedContent',
      },

      toolbar: {
        'main-insert': 'mainInsertToolbar',
      }
    };

    _.each( handlers, function( regionHandlers, region ) {
      _.each( regionHandlers, function( callback, handler ) {
        this.on( region + ':render:' + handler, this[ callback ], this );
      }, this );
    }, this );
  },

  // Content
  embedContent: function() {
    var view = new wp.media.view.Embed({
      controller: this,
      model:      this.state()
    }).render();

    this.content.set( view );

    if ( ! wp.media.isTouchDevice ) {
      view.url.focus();
    }
  },

  // Toolbars
  mainInsertToolbar: function( view ) {
    var options = this.options;
    var controller = this;

    view.set( 'insert', {
      style:    'primary',
      priority: 80,
      text:     options.button.text,
      requires: { selection: true },

      /**
       * @fires wp.media.controller.State#insert
       */
      click: function() {
        var state = controller.state(),
          selection = state.get('selection');

        controller.close();
        state.trigger( 'insert', selection ).reset();
      }
    });
  },

  mainEmbedToolbar: function( toolbar ) {
    toolbar.view = new wp.media.view.Toolbar.Embed({
      controller: this
    });
  }
});

// export default wp.media.view.MediaFrame.ETSelect;