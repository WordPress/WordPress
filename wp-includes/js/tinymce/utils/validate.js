/**
 * validate.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
  // String validation:

  if (!Validator.isEmail('myemail'))
    alert('Invalid email.');

  // Form validation:

  var f = document.forms['myform'];

  if (!Validator.isEmail(f.myemail))
    alert('Invalid email.');
*/

var Validator = {
  isEmail : function (s) {
    return this.test(s, '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$');
  },

  isAbsUrl : function (s) {
    return this.test(s, '^(news|telnet|nttp|file|http|ftp|https)://[-A-Za-z0-9\\.]+\\/?.*$');
  },

  isSize : function (s) {
    return this.test(s, '^[0-9.]+(%|in|cm|mm|em|ex|pt|pc|px)?$');
  },

  isId : function (s) {
    return this.test(s, '^[A-Za-z_]([A-Za-z0-9_])*$');
  },

  isEmpty : function (s) {
    var nl, i;

    if (s.nodeName == 'SELECT' && s.selectedIndex < 1) {
      return true;
    }

    if (s.type == 'checkbox' && !s.checked) {
      return true;
    }

    if (s.type == 'radio') {
      for (i = 0, nl = s.form.elements; i < nl.length; i++) {
        if (nl[i].type == "radio" && nl[i].name == s.name && nl[i].checked) {
          return false;
        }
      }

      return true;
    }

    return new RegExp('^\\s*$').test(s.nodeType == 1 ? s.value : s);
  },

  isNumber : function (s, d) {
    return !isNaN(s.nodeType == 1 ? s.value : s) && (!d || !this.test(s, '^-?[0-9]*\\.[0-9]*$'));
  },

  test : function (s, p) {
    s = s.nodeType == 1 ? s.value : s;

    return s == '' || new RegExp(p).test(s);
  }
};

var AutoValidator = {
  settings : {
    id_cls : 'id',
    int_cls : 'int',
    url_cls : 'url',
    number_cls : 'number',
    email_cls : 'email',
    size_cls : 'size',
    required_cls : 'required',
    invalid_cls : 'invalid',
    min_cls : 'min',
    max_cls : 'max'
  },

  init : function (s) {
    var n;

    for (n in s) {
      this.settings[n] = s[n];
    }
  },

  validate : function (f) {
    var i, nl, s = this.settings, c = 0;

    nl = this.tags(f, 'label');
    for (i = 0; i < nl.length; i++) {
      this.removeClass(nl[i], s.invalid_cls);
      nl[i].setAttribute('aria-invalid', false);
    }

    c += this.validateElms(f, 'input');
    c += this.validateElms(f, 'select');
    c += this.validateElms(f, 'textarea');

    return c == 3;
  },

  invalidate : function (n) {
    this.mark(n.form, n);
  },

  getErrorMessages : function (f) {
    var nl, i, s = this.settings, field, msg, values, messages = [], ed = tinyMCEPopup.editor;
    nl = this.tags(f, "label");
    for (i = 0; i < nl.length; i++) {
      if (this.hasClass(nl[i], s.invalid_cls)) {
        field = document.getElementById(nl[i].getAttribute("for"));
        values = { field: nl[i].textContent };
        if (this.hasClass(field, s.min_cls, true)) {
          message = ed.getLang('invalid_data_min');
          values.min = this.getNum(field, s.min_cls);
        } else if (this.hasClass(field, s.number_cls)) {
          message = ed.getLang('invalid_data_number');
        } else if (this.hasClass(field, s.size_cls)) {
          message = ed.getLang('invalid_data_size');
        } else {
          message = ed.getLang('invalid_data');
        }

        message = message.replace(/{\#([^}]+)\}/g, function (a, b) {
          return values[b] || '{#' + b + '}';
        });
        messages.push(message);
      }
    }
    return messages;
  },

  reset : function (e) {
    var t = ['label', 'input', 'select', 'textarea'];
    var i, j, nl, s = this.settings;

    if (e == null) {
      return;
    }

    for (i = 0; i < t.length; i++) {
      nl = this.tags(e.form ? e.form : e, t[i]);
      for (j = 0; j < nl.length; j++) {
        this.removeClass(nl[j], s.invalid_cls);
        nl[j].setAttribute('aria-invalid', false);
      }
    }
  },

  validateElms : function (f, e) {
    var nl, i, n, s = this.settings, st = true, va = Validator, v;

    nl = this.tags(f, e);
    for (i = 0; i < nl.length; i++) {
      n = nl[i];

      this.removeClass(n, s.invalid_cls);

      if (this.hasClass(n, s.required_cls) && va.isEmpty(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.number_cls) && !va.isNumber(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.int_cls) && !va.isNumber(n, true)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.url_cls) && !va.isAbsUrl(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.email_cls) && !va.isEmail(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.size_cls) && !va.isSize(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.id_cls) && !va.isId(n)) {
        st = this.mark(f, n);
      }

      if (this.hasClass(n, s.min_cls, true)) {
        v = this.getNum(n, s.min_cls);

        if (isNaN(v) || parseInt(n.value) < parseInt(v)) {
          st = this.mark(f, n);
        }
      }

      if (this.hasClass(n, s.max_cls, true)) {
        v = this.getNum(n, s.max_cls);

        if (isNaN(v) || parseInt(n.value) > parseInt(v)) {
          st = this.mark(f, n);
        }
      }
    }

    return st;
  },

  hasClass : function (n, c, d) {
    return new RegExp('\\b' + c + (d ? '[0-9]+' : '') + '\\b', 'g').test(n.className);
  },

  getNum : function (n, c) {
    c = n.className.match(new RegExp('\\b' + c + '([0-9]+)\\b', 'g'))[0];
    c = c.replace(/[^0-9]/g, '');

    return c;
  },

  addClass : function (n, c, b) {
    var o = this.removeClass(n, c);
    n.className = b ? c + (o !== '' ? (' ' + o) : '') : (o !== '' ? (o + ' ') : '') + c;
  },

  removeClass : function (n, c) {
    c = n.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' ');
    return n.className = c !== ' ' ? c : '';
  },

  tags : function (f, s) {
    return f.getElementsByTagName(s);
  },

  mark : function (f, n) {
    var s = this.settings;

    this.addClass(n, s.invalid_cls);
    n.setAttribute('aria-invalid', 'true');
    this.markLabels(f, n, s.invalid_cls);

    return false;
  },

  markLabels : function (f, n, ic) {
    var nl, i;

    nl = this.tags(f, "label");
    for (i = 0; i < nl.length; i++) {
      if (nl[i].getAttribute("for") == n.id || nl[i].htmlFor == n.id) {
        this.addClass(nl[i], ic);
      }
    }

    return null;
  }
};
