/**
 * mctabs.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*jshint globals: tinyMCEPopup */

function MCTabs() {
  this.settings = [];
  this.onChange = tinyMCEPopup.editor.windowManager.createInstance('tinymce.util.Dispatcher');
}

MCTabs.prototype.init = function (settings) {
  this.settings = settings;
};

MCTabs.prototype.getParam = function (name, default_value) {
  var value = null;

  value = (typeof (this.settings[name]) == "undefined") ? default_value : this.settings[name];

  // Fix bool values
  if (value == "true" || value == "false") {
    return (value == "true");
  }

  return value;
};

MCTabs.prototype.showTab = function (tab) {
  tab.className = 'current';
  tab.setAttribute("aria-selected", true);
  tab.setAttribute("aria-expanded", true);
  tab.tabIndex = 0;
};

MCTabs.prototype.hideTab = function (tab) {
  var t = this;

  tab.className = '';
  tab.setAttribute("aria-selected", false);
  tab.setAttribute("aria-expanded", false);
  tab.tabIndex = -1;
};

MCTabs.prototype.showPanel = function (panel) {
  panel.className = 'current';
  panel.setAttribute("aria-hidden", false);
};

MCTabs.prototype.hidePanel = function (panel) {
  panel.className = 'panel';
  panel.setAttribute("aria-hidden", true);
};

MCTabs.prototype.getPanelForTab = function (tabElm) {
  return tinyMCEPopup.dom.getAttrib(tabElm, "aria-controls");
};

MCTabs.prototype.displayTab = function (tab_id, panel_id, avoid_focus) {
  var panelElm, panelContainerElm, tabElm, tabContainerElm, selectionClass, nodes, i, t = this;

  tabElm = document.getElementById(tab_id);

  if (panel_id === undefined) {
    panel_id = t.getPanelForTab(tabElm);
  }

  panelElm = document.getElementById(panel_id);
  panelContainerElm = panelElm ? panelElm.parentNode : null;
  tabContainerElm = tabElm ? tabElm.parentNode : null;
  selectionClass = t.getParam('selection_class', 'current');

  if (tabElm && tabContainerElm) {
    nodes = tabContainerElm.childNodes;

    // Hide all other tabs
    for (i = 0; i < nodes.length; i++) {
      if (nodes[i].nodeName == "LI") {
        t.hideTab(nodes[i]);
      }
    }

    // Show selected tab
    t.showTab(tabElm);
  }

  if (panelElm && panelContainerElm) {
    nodes = panelContainerElm.childNodes;

    // Hide all other panels
    for (i = 0; i < nodes.length; i++) {
      if (nodes[i].nodeName == "DIV") {
        t.hidePanel(nodes[i]);
      }
    }

    if (!avoid_focus) {
      tabElm.focus();
    }

    // Show selected panel
    t.showPanel(panelElm);
  }
};

MCTabs.prototype.getAnchor = function () {
  var pos, url = document.location.href;

  if ((pos = url.lastIndexOf('#')) != -1) {
    return url.substring(pos + 1);
  }

  return "";
};


//Global instance
var mcTabs = new MCTabs();

tinyMCEPopup.onInit.add(function () {
  var tinymce = tinyMCEPopup.getWin().tinymce, dom = tinyMCEPopup.dom, each = tinymce.each;

  each(dom.select('div.tabs'), function (tabContainerElm) {
    //var keyNav;

    dom.setAttrib(tabContainerElm, "role", "tablist");

    var items = tinyMCEPopup.dom.select('li', tabContainerElm);
    var action = function (id) {
      mcTabs.displayTab(id, mcTabs.getPanelForTab(id));
      mcTabs.onChange.dispatch(id);
    };

    each(items, function (item) {
      dom.setAttrib(item, 'role', 'tab');
      dom.bind(item, 'click', function (evt) {
        action(item.id);
      });
    });

    dom.bind(dom.getRoot(), 'keydown', function (evt) {
      if (evt.keyCode === 9 && evt.ctrlKey && !evt.altKey) { // Tab
        //keyNav.moveFocus(evt.shiftKey ? -1 : 1);
        tinymce.dom.Event.cancel(evt);
      }
    });

    each(dom.select('a', tabContainerElm), function (a) {
      dom.setAttrib(a, 'tabindex', '-1');
    });

    /*keyNav = tinyMCEPopup.editor.windowManager.createInstance('tinymce.ui.KeyboardNavigation', {
      root: tabContainerElm,
      items: items,
      onAction: action,
      actOnFocus: true,
      enableLeftRight: true,
      enableUpDown: true
    }, tinyMCEPopup.dom);*/
  }
);
});