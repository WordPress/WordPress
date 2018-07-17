(function () {
var charmap = (function () {
  'use strict';

  var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

  var fireInsertCustomChar = function (editor, chr) {
    return editor.fire('insertCustomChar', { chr: chr });
  };
  var $_ce2ncy9qjjgwebhk = { fireInsertCustomChar: fireInsertCustomChar };

  var insertChar = function (editor, chr) {
    var evtChr = $_ce2ncy9qjjgwebhk.fireInsertCustomChar(editor, chr).chr;
    editor.execCommand('mceInsertContent', false, evtChr);
  };
  var $_el68bd9pjjgwebhj = { insertChar: insertChar };

  var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

  var getCharMap = function (editor) {
    return editor.settings.charmap;
  };
  var getCharMapAppend = function (editor) {
    return editor.settings.charmap_append;
  };
  var $_5e4nos9tjjgwebhr = {
    getCharMap: getCharMap,
    getCharMapAppend: getCharMapAppend
  };

  var isArray = global$1.isArray;
  var getDefaultCharMap = function () {
    return [
      [
        '160',
        'no-break space'
      ],
      [
        '173',
        'soft hyphen'
      ],
      [
        '34',
        'quotation mark'
      ],
      [
        '162',
        'cent sign'
      ],
      [
        '8364',
        'euro sign'
      ],
      [
        '163',
        'pound sign'
      ],
      [
        '165',
        'yen sign'
      ],
      [
        '169',
        'copyright sign'
      ],
      [
        '174',
        'registered sign'
      ],
      [
        '8482',
        'trade mark sign'
      ],
      [
        '8240',
        'per mille sign'
      ],
      [
        '181',
        'micro sign'
      ],
      [
        '183',
        'middle dot'
      ],
      [
        '8226',
        'bullet'
      ],
      [
        '8230',
        'three dot leader'
      ],
      [
        '8242',
        'minutes / feet'
      ],
      [
        '8243',
        'seconds / inches'
      ],
      [
        '167',
        'section sign'
      ],
      [
        '182',
        'paragraph sign'
      ],
      [
        '223',
        'sharp s / ess-zed'
      ],
      [
        '8249',
        'single left-pointing angle quotation mark'
      ],
      [
        '8250',
        'single right-pointing angle quotation mark'
      ],
      [
        '171',
        'left pointing guillemet'
      ],
      [
        '187',
        'right pointing guillemet'
      ],
      [
        '8216',
        'left single quotation mark'
      ],
      [
        '8217',
        'right single quotation mark'
      ],
      [
        '8220',
        'left double quotation mark'
      ],
      [
        '8221',
        'right double quotation mark'
      ],
      [
        '8218',
        'single low-9 quotation mark'
      ],
      [
        '8222',
        'double low-9 quotation mark'
      ],
      [
        '60',
        'less-than sign'
      ],
      [
        '62',
        'greater-than sign'
      ],
      [
        '8804',
        'less-than or equal to'
      ],
      [
        '8805',
        'greater-than or equal to'
      ],
      [
        '8211',
        'en dash'
      ],
      [
        '8212',
        'em dash'
      ],
      [
        '175',
        'macron'
      ],
      [
        '8254',
        'overline'
      ],
      [
        '164',
        'currency sign'
      ],
      [
        '166',
        'broken bar'
      ],
      [
        '168',
        'diaeresis'
      ],
      [
        '161',
        'inverted exclamation mark'
      ],
      [
        '191',
        'turned question mark'
      ],
      [
        '710',
        'circumflex accent'
      ],
      [
        '732',
        'small tilde'
      ],
      [
        '176',
        'degree sign'
      ],
      [
        '8722',
        'minus sign'
      ],
      [
        '177',
        'plus-minus sign'
      ],
      [
        '247',
        'division sign'
      ],
      [
        '8260',
        'fraction slash'
      ],
      [
        '215',
        'multiplication sign'
      ],
      [
        '185',
        'superscript one'
      ],
      [
        '178',
        'superscript two'
      ],
      [
        '179',
        'superscript three'
      ],
      [
        '188',
        'fraction one quarter'
      ],
      [
        '189',
        'fraction one half'
      ],
      [
        '190',
        'fraction three quarters'
      ],
      [
        '402',
        'function / florin'
      ],
      [
        '8747',
        'integral'
      ],
      [
        '8721',
        'n-ary sumation'
      ],
      [
        '8734',
        'infinity'
      ],
      [
        '8730',
        'square root'
      ],
      [
        '8764',
        'similar to'
      ],
      [
        '8773',
        'approximately equal to'
      ],
      [
        '8776',
        'almost equal to'
      ],
      [
        '8800',
        'not equal to'
      ],
      [
        '8801',
        'identical to'
      ],
      [
        '8712',
        'element of'
      ],
      [
        '8713',
        'not an element of'
      ],
      [
        '8715',
        'contains as member'
      ],
      [
        '8719',
        'n-ary product'
      ],
      [
        '8743',
        'logical and'
      ],
      [
        '8744',
        'logical or'
      ],
      [
        '172',
        'not sign'
      ],
      [
        '8745',
        'intersection'
      ],
      [
        '8746',
        'union'
      ],
      [
        '8706',
        'partial differential'
      ],
      [
        '8704',
        'for all'
      ],
      [
        '8707',
        'there exists'
      ],
      [
        '8709',
        'diameter'
      ],
      [
        '8711',
        'backward difference'
      ],
      [
        '8727',
        'asterisk operator'
      ],
      [
        '8733',
        'proportional to'
      ],
      [
        '8736',
        'angle'
      ],
      [
        '180',
        'acute accent'
      ],
      [
        '184',
        'cedilla'
      ],
      [
        '170',
        'feminine ordinal indicator'
      ],
      [
        '186',
        'masculine ordinal indicator'
      ],
      [
        '8224',
        'dagger'
      ],
      [
        '8225',
        'double dagger'
      ],
      [
        '192',
        'A - grave'
      ],
      [
        '193',
        'A - acute'
      ],
      [
        '194',
        'A - circumflex'
      ],
      [
        '195',
        'A - tilde'
      ],
      [
        '196',
        'A - diaeresis'
      ],
      [
        '197',
        'A - ring above'
      ],
      [
        '256',
        'A - macron'
      ],
      [
        '198',
        'ligature AE'
      ],
      [
        '199',
        'C - cedilla'
      ],
      [
        '200',
        'E - grave'
      ],
      [
        '201',
        'E - acute'
      ],
      [
        '202',
        'E - circumflex'
      ],
      [
        '203',
        'E - diaeresis'
      ],
      [
        '274',
        'E - macron'
      ],
      [
        '204',
        'I - grave'
      ],
      [
        '205',
        'I - acute'
      ],
      [
        '206',
        'I - circumflex'
      ],
      [
        '207',
        'I - diaeresis'
      ],
      [
        '298',
        'I - macron'
      ],
      [
        '208',
        'ETH'
      ],
      [
        '209',
        'N - tilde'
      ],
      [
        '210',
        'O - grave'
      ],
      [
        '211',
        'O - acute'
      ],
      [
        '212',
        'O - circumflex'
      ],
      [
        '213',
        'O - tilde'
      ],
      [
        '214',
        'O - diaeresis'
      ],
      [
        '216',
        'O - slash'
      ],
      [
        '332',
        'O - macron'
      ],
      [
        '338',
        'ligature OE'
      ],
      [
        '352',
        'S - caron'
      ],
      [
        '217',
        'U - grave'
      ],
      [
        '218',
        'U - acute'
      ],
      [
        '219',
        'U - circumflex'
      ],
      [
        '220',
        'U - diaeresis'
      ],
      [
        '362',
        'U - macron'
      ],
      [
        '221',
        'Y - acute'
      ],
      [
        '376',
        'Y - diaeresis'
      ],
      [
        '562',
        'Y - macron'
      ],
      [
        '222',
        'THORN'
      ],
      [
        '224',
        'a - grave'
      ],
      [
        '225',
        'a - acute'
      ],
      [
        '226',
        'a - circumflex'
      ],
      [
        '227',
        'a - tilde'
      ],
      [
        '228',
        'a - diaeresis'
      ],
      [
        '229',
        'a - ring above'
      ],
      [
        '257',
        'a - macron'
      ],
      [
        '230',
        'ligature ae'
      ],
      [
        '231',
        'c - cedilla'
      ],
      [
        '232',
        'e - grave'
      ],
      [
        '233',
        'e - acute'
      ],
      [
        '234',
        'e - circumflex'
      ],
      [
        '235',
        'e - diaeresis'
      ],
      [
        '275',
        'e - macron'
      ],
      [
        '236',
        'i - grave'
      ],
      [
        '237',
        'i - acute'
      ],
      [
        '238',
        'i - circumflex'
      ],
      [
        '239',
        'i - diaeresis'
      ],
      [
        '299',
        'i - macron'
      ],
      [
        '240',
        'eth'
      ],
      [
        '241',
        'n - tilde'
      ],
      [
        '242',
        'o - grave'
      ],
      [
        '243',
        'o - acute'
      ],
      [
        '244',
        'o - circumflex'
      ],
      [
        '245',
        'o - tilde'
      ],
      [
        '246',
        'o - diaeresis'
      ],
      [
        '248',
        'o slash'
      ],
      [
        '333',
        'o macron'
      ],
      [
        '339',
        'ligature oe'
      ],
      [
        '353',
        's - caron'
      ],
      [
        '249',
        'u - grave'
      ],
      [
        '250',
        'u - acute'
      ],
      [
        '251',
        'u - circumflex'
      ],
      [
        '252',
        'u - diaeresis'
      ],
      [
        '363',
        'u - macron'
      ],
      [
        '253',
        'y - acute'
      ],
      [
        '254',
        'thorn'
      ],
      [
        '255',
        'y - diaeresis'
      ],
      [
        '563',
        'y - macron'
      ],
      [
        '913',
        'Alpha'
      ],
      [
        '914',
        'Beta'
      ],
      [
        '915',
        'Gamma'
      ],
      [
        '916',
        'Delta'
      ],
      [
        '917',
        'Epsilon'
      ],
      [
        '918',
        'Zeta'
      ],
      [
        '919',
        'Eta'
      ],
      [
        '920',
        'Theta'
      ],
      [
        '921',
        'Iota'
      ],
      [
        '922',
        'Kappa'
      ],
      [
        '923',
        'Lambda'
      ],
      [
        '924',
        'Mu'
      ],
      [
        '925',
        'Nu'
      ],
      [
        '926',
        'Xi'
      ],
      [
        '927',
        'Omicron'
      ],
      [
        '928',
        'Pi'
      ],
      [
        '929',
        'Rho'
      ],
      [
        '931',
        'Sigma'
      ],
      [
        '932',
        'Tau'
      ],
      [
        '933',
        'Upsilon'
      ],
      [
        '934',
        'Phi'
      ],
      [
        '935',
        'Chi'
      ],
      [
        '936',
        'Psi'
      ],
      [
        '937',
        'Omega'
      ],
      [
        '945',
        'alpha'
      ],
      [
        '946',
        'beta'
      ],
      [
        '947',
        'gamma'
      ],
      [
        '948',
        'delta'
      ],
      [
        '949',
        'epsilon'
      ],
      [
        '950',
        'zeta'
      ],
      [
        '951',
        'eta'
      ],
      [
        '952',
        'theta'
      ],
      [
        '953',
        'iota'
      ],
      [
        '954',
        'kappa'
      ],
      [
        '955',
        'lambda'
      ],
      [
        '956',
        'mu'
      ],
      [
        '957',
        'nu'
      ],
      [
        '958',
        'xi'
      ],
      [
        '959',
        'omicron'
      ],
      [
        '960',
        'pi'
      ],
      [
        '961',
        'rho'
      ],
      [
        '962',
        'final sigma'
      ],
      [
        '963',
        'sigma'
      ],
      [
        '964',
        'tau'
      ],
      [
        '965',
        'upsilon'
      ],
      [
        '966',
        'phi'
      ],
      [
        '967',
        'chi'
      ],
      [
        '968',
        'psi'
      ],
      [
        '969',
        'omega'
      ],
      [
        '8501',
        'alef symbol'
      ],
      [
        '982',
        'pi symbol'
      ],
      [
        '8476',
        'real part symbol'
      ],
      [
        '978',
        'upsilon - hook symbol'
      ],
      [
        '8472',
        'Weierstrass p'
      ],
      [
        '8465',
        'imaginary part'
      ],
      [
        '8592',
        'leftwards arrow'
      ],
      [
        '8593',
        'upwards arrow'
      ],
      [
        '8594',
        'rightwards arrow'
      ],
      [
        '8595',
        'downwards arrow'
      ],
      [
        '8596',
        'left right arrow'
      ],
      [
        '8629',
        'carriage return'
      ],
      [
        '8656',
        'leftwards double arrow'
      ],
      [
        '8657',
        'upwards double arrow'
      ],
      [
        '8658',
        'rightwards double arrow'
      ],
      [
        '8659',
        'downwards double arrow'
      ],
      [
        '8660',
        'left right double arrow'
      ],
      [
        '8756',
        'therefore'
      ],
      [
        '8834',
        'subset of'
      ],
      [
        '8835',
        'superset of'
      ],
      [
        '8836',
        'not a subset of'
      ],
      [
        '8838',
        'subset of or equal to'
      ],
      [
        '8839',
        'superset of or equal to'
      ],
      [
        '8853',
        'circled plus'
      ],
      [
        '8855',
        'circled times'
      ],
      [
        '8869',
        'perpendicular'
      ],
      [
        '8901',
        'dot operator'
      ],
      [
        '8968',
        'left ceiling'
      ],
      [
        '8969',
        'right ceiling'
      ],
      [
        '8970',
        'left floor'
      ],
      [
        '8971',
        'right floor'
      ],
      [
        '9001',
        'left-pointing angle bracket'
      ],
      [
        '9002',
        'right-pointing angle bracket'
      ],
      [
        '9674',
        'lozenge'
      ],
      [
        '9824',
        'black spade suit'
      ],
      [
        '9827',
        'black club suit'
      ],
      [
        '9829',
        'black heart suit'
      ],
      [
        '9830',
        'black diamond suit'
      ],
      [
        '8194',
        'en space'
      ],
      [
        '8195',
        'em space'
      ],
      [
        '8201',
        'thin space'
      ],
      [
        '8204',
        'zero width non-joiner'
      ],
      [
        '8205',
        'zero width joiner'
      ],
      [
        '8206',
        'left-to-right mark'
      ],
      [
        '8207',
        'right-to-left mark'
      ]
    ];
  };
  var charmapFilter = function (charmap) {
    return global$1.grep(charmap, function (item) {
      return isArray(item) && item.length === 2;
    });
  };
  var getCharsFromSetting = function (settingValue) {
    if (isArray(settingValue)) {
      return [].concat(charmapFilter(settingValue));
    }
    if (typeof settingValue === 'function') {
      return settingValue();
    }
    return [];
  };
  var extendCharMap = function (editor, charmap) {
    var userCharMap = $_5e4nos9tjjgwebhr.getCharMap(editor);
    if (userCharMap) {
      charmap = getCharsFromSetting(userCharMap);
    }
    var userCharMapAppend = $_5e4nos9tjjgwebhr.getCharMapAppend(editor);
    if (userCharMapAppend) {
      return [].concat(charmap).concat(getCharsFromSetting(userCharMapAppend));
    }
    return charmap;
  };
  var getCharMap$1 = function (editor) {
    return extendCharMap(editor, getDefaultCharMap());
  };
  var $_dc8shd9rjjgwebhl = { getCharMap: getCharMap$1 };

  var get = function (editor) {
    var getCharMap = function () {
      return $_dc8shd9rjjgwebhl.getCharMap(editor);
    };
    var insertChar = function (chr) {
      $_el68bd9pjjgwebhj.insertChar(editor, chr);
    };
    return {
      getCharMap: getCharMap,
      insertChar: insertChar
    };
  };
  var $_gbufu29ojjgwebhf = { get: get };

  var getHtml = function (charmap) {
    var gridHtml, x, y;
    var width = Math.min(charmap.length, 25);
    var height = Math.ceil(charmap.length / width);
    gridHtml = '<table role="presentation" cellspacing="0" class="mce-charmap"><tbody>';
    for (y = 0; y < height; y++) {
      gridHtml += '<tr>';
      for (x = 0; x < width; x++) {
        var index = y * width + x;
        if (index < charmap.length) {
          var chr = charmap[index];
          var charCode = parseInt(chr[0], 10);
          var chrText = chr ? String.fromCharCode(charCode) : '&nbsp;';
          gridHtml += '<td title="' + chr[1] + '">' + '<div tabindex="-1" title="' + chr[1] + '" role="button" data-chr="' + charCode + '">' + chrText + '</div>' + '</td>';
        } else {
          gridHtml += '<td />';
        }
      }
      gridHtml += '</tr>';
    }
    gridHtml += '</tbody></table>';
    return gridHtml;
  };
  var $_6avwgq9wjjgwebi2 = { getHtml: getHtml };

  var getParentTd = function (elm) {
    while (elm) {
      if (elm.nodeName === 'TD') {
        return elm;
      }
      elm = elm.parentNode;
    }
  };
  var open = function (editor) {
    var win;
    var charMapPanel = {
      type: 'container',
      html: $_6avwgq9wjjgwebi2.getHtml($_dc8shd9rjjgwebhl.getCharMap(editor)),
      onclick: function (e) {
        var target = e.target;
        if (/^(TD|DIV)$/.test(target.nodeName)) {
          var charDiv = getParentTd(target).firstChild;
          if (charDiv && charDiv.hasAttribute('data-chr')) {
            var charCodeString = charDiv.getAttribute('data-chr');
            var charCode = parseInt(charCodeString, 10);
            if (!isNaN(charCode)) {
              $_el68bd9pjjgwebhj.insertChar(editor, String.fromCharCode(charCode));
            }
            if (!e.ctrlKey) {
              win.close();
            }
          }
        }
      },
      onmouseover: function (e) {
        var td = getParentTd(e.target);
        if (td && td.firstChild) {
          win.find('#preview').text(td.firstChild.firstChild.data);
          win.find('#previewTitle').text(td.title);
        } else {
          win.find('#preview').text(' ');
          win.find('#previewTitle').text(' ');
        }
      }
    };
    win = editor.windowManager.open({
      title: 'Special character',
      spacing: 10,
      padding: 10,
      items: [
        charMapPanel,
        {
          type: 'container',
          layout: 'flex',
          direction: 'column',
          align: 'center',
          spacing: 5,
          minWidth: 160,
          minHeight: 160,
          items: [
            {
              type: 'label',
              name: 'preview',
              text: ' ',
              style: 'font-size: 40px; text-align: center',
              border: 1,
              minWidth: 140,
              minHeight: 80
            },
            {
              type: 'spacer',
              minHeight: 20
            },
            {
              type: 'label',
              name: 'previewTitle',
              text: ' ',
              style: 'white-space: pre-wrap;',
              border: 1,
              minWidth: 140
            }
          ]
        }
      ],
      buttons: [{
          text: 'Close',
          onclick: function () {
            win.close();
          }
        }]
    });
  };
  var $_3eaa3c9vjjgwebht = { open: open };

  var register = function (editor) {
    editor.addCommand('mceShowCharmap', function () {
      $_3eaa3c9vjjgwebht.open(editor);
    });
  };
  var $_b5cdu19ujjgwebhs = { register: register };

  var register$1 = function (editor) {
    editor.addButton('charmap', {
      icon: 'charmap',
      tooltip: 'Special character',
      cmd: 'mceShowCharmap'
    });
    editor.addMenuItem('charmap', {
      icon: 'charmap',
      text: 'Special character',
      cmd: 'mceShowCharmap',
      context: 'insert'
    });
  };
  var $_19iu2m9xjjgwebi3 = { register: register$1 };

  global.add('charmap', function (editor) {
    $_b5cdu19ujjgwebhs.register(editor);
    $_19iu2m9xjjgwebi3.register(editor);
    return $_gbufu29ojjgwebhf.get(editor);
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
