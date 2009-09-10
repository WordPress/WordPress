/*
 * imgAreaSelect jQuery plugin
 * version 0.9.1
 *
 * Copyright (c) 2008-2009 Michal Wojciechowski (odyniec.net)
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://odyniec.net/projects/imgareaselect/
 *
 */

(function($) {

var abs = Math.abs,
    max = Math.max,
    min = Math.min,
    round = Math.round;

function div() {
    return $('<div/>');
}

$.imgAreaSelect = function (img, options) {
    var

        $img = $(img),

        imgLoaded,

        $box = div(),
        $area = div(),
        $border = div().add(div()).add(div()).add(div()),
        $outer = div().add(div()).add(div()).add(div()),
        $handles = $([]),

        $areaOpera,

        left, top,

        imgOfs,

        imgWidth, imgHeight,

        $parent,

        parOfs,

        zIndex = 0,

        position = 'absolute',

        startX, startY,

        scaleX, scaleY,

        resizeMargin = 10,

        resize,

        aspectRatio,

        shown,

        x1, y1, x2, y2,

        selection = { x1: 0, y1: 0, x2: 0, y2: 0, width: 0, height: 0 },

        $p, d, i, o, w, h, adjusted;

    function viewX(x) {
        return x + imgOfs.left - parOfs.left;
    }

    function viewY(y) {
        return y + imgOfs.top - parOfs.top;
    }

    function selX(x) {
        return x - imgOfs.left + parOfs.left;
    }

    function selY(y) {
        return y - imgOfs.top + parOfs.top;
    }

    function evX(event) {
        return event.pageX - parOfs.left;
    }

    function evY(event) {
        return event.pageY - parOfs.top;
    }

    function getSelection(noScale) {
        var sx = noScale || scaleX, sy = noScale || scaleY;

        return { x1: round(selection.x1 * sx),
            y1: round(selection.y1 * sy),
            x2: round(selection.x2 * sx),
            y2: round(selection.y2 * sy),
            width: round(selection.x2 * sx) - round(selection.x1 * sx),
            height: round(selection.y2 * sy) - round(selection.y1 * sy) };
    }

    function setSelection(x1, y1, x2, y2, noScale) {
        var sx = noScale || scaleX, sy = noScale || scaleY;

        selection = {
            x1: round(x1 / sx),
            y1: round(y1 / sy),
            x2: round(x2 / sx),
            y2: round(y2 / sy)
        };

        selection.width = (x2 = viewX(selection.x2)) - (x1 = viewX(selection.x1));
        selection.height = (y2 = viewX(selection.y2)) - (y1 = viewX(selection.y1));
    }

    function adjust() {
        if (!$img.width())
            return;

        imgOfs = { left: round($img.offset().left), top: round($img.offset().top) };

        imgWidth = $img.width();
        imgHeight = $img.height();

        if ($().jquery == '1.3.2' && $.browser.safari && position == 'fixed') {
            imgOfs.top += max(document.documentElement.scrollTop, $('body').scrollTop());

            imgOfs.left += max(document.documentElement.scrollLeft, $('body').scrollLeft());
        }

        parOfs = $.inArray($parent.css('position'), ['absolute', 'relative']) + 1 ?
            { left: round($parent.offset().left) - $parent.scrollLeft(),
                top: round($parent.offset().top) - $parent.scrollTop() } :
            position == 'fixed' ?
                { left: $(document).scrollLeft(), top: $(document).scrollTop() } :
                { left: 0, top: 0 };

        left = viewX(0);
        top = viewY(0);
    }

    function update(resetKeyPress) {
        if (!shown) return;

        $box.css({ left: viewX(selection.x1), top: viewY(selection.y1) })
            .add($area).width(w = selection.width).height(h = selection.height);

        $area.add($border).add($handles).css({ left: 0, top: 0 });

        $border
            .width(max(w - $border.outerWidth() + $border.innerWidth(), 0))
            .height(max(h - $border.outerHeight() + $border.innerHeight(), 0));

        $($outer[0]).css({ left: left, top: top,
            width: selection.x1, height: imgHeight });
        $($outer[1]).css({ left: left + selection.x1, top: top,
            width: w, height: selection.y1 });
        $($outer[2]).css({ left: left + selection.x2, top: top,
            width: imgWidth - selection.x2, height: imgHeight });
        $($outer[3]).css({ left: left + selection.x1, top: top + selection.y2,
            width: w, height: imgHeight - selection.y2 });

        w -= $handles.outerWidth();
        h -= $handles.outerHeight();

        switch ($handles.length) {
        case 8:
            $($handles[4]).css({ left: w / 2 });
            $($handles[5]).css({ left: w, top: h / 2 });
            $($handles[6]).css({ left: w / 2, top: h });
            $($handles[7]).css({ top: h / 2 });
        case 4:
            $handles.slice(1,3).css({ left: w });
            $handles.slice(2,4).css({ top: h });
        }

        if (resetKeyPress !== false) {
            if ($.imgAreaSelect.keyPress != docKeyPress)
                $(document).unbind($.imgAreaSelect.keyPress,
                    $.imgAreaSelect.onKeyPress);

            if (options.keys)
                $(document)[$.imgAreaSelect.keyPress](
                    $.imgAreaSelect.onKeyPress = docKeyPress);
        }

        if ($.browser.msie && $border.outerWidth() - $border.innerWidth() == 2) {
            $border.css('margin', 0);
            setTimeout(function () { $border.css('margin', 'auto'); }, 0);
        }
    }

    function doUpdate(resetKeyPress) {
        adjust();
        update(resetKeyPress);
        x1 = viewX(selection.x1); y1 = viewY(selection.y1);
        x2 = viewX(selection.x2); y2 = viewY(selection.y2);
    }

    function hide($elem, fn) {
        options.fadeSpeed ? $elem.fadeOut(options.fadeSpeed, fn) : $elem.hide();

    }

    function areaMouseMove(event) {
        var x = selX(evX(event)) - selection.x1,
            y = selY(evY(event)) - selection.y1;

        if (!adjusted) {
            adjust();
            adjusted = true;

            $box.one('mouseout', function () { adjusted = false; });
        }

        resize = '';

        if (options.resizable) {
            if (y <= resizeMargin)
                resize = 'n';
            else if (y >= selection.height - resizeMargin)
                resize = 's';
            if (x <= resizeMargin)
                resize += 'w';
            else if (x >= selection.width - resizeMargin)
                resize += 'e';
        }

        $box.css('cursor', resize ? resize + '-resize' :
            options.movable ? 'move' : '');
        if ($areaOpera)
            $areaOpera.toggle();
    }

    function docMouseUp(event) {
        $('body').css('cursor', '');

        if (options.autoHide || selection.width * selection.height == 0)
            hide($box.add($outer), function () { $(this).hide(); });

        options.onSelectEnd(img, getSelection());

        $(document).unbind('mousemove', selectingMouseMove);
        $box.mousemove(areaMouseMove);
    }

    function areaMouseDown(event) {
        if (event.which != 1) return false;

        adjust();

        if (resize) {
            $('body').css('cursor', resize + '-resize');

            x1 = viewX(selection[/w/.test(resize) ? 'x2' : 'x1']);
            y1 = viewY(selection[/n/.test(resize) ? 'y2' : 'y1']);

            $(document).mousemove(selectingMouseMove)
                .one('mouseup', docMouseUp);
            $box.unbind('mousemove', areaMouseMove);
        }
        else if (options.movable) {
            startX = left + selection.x1 - evX(event);
            startY = top + selection.y1 - evY(event);

            $box.unbind('mousemove', areaMouseMove);

            $(document).mousemove(movingMouseMove)
                .one('mouseup', function () {
                    options.onSelectEnd(img, getSelection());

                    $(document).unbind('mousemove', movingMouseMove);
                    $box.mousemove(areaMouseMove);
                });
        }
        else
            $img.mousedown(event);

        return false;
    }

    function aspectRatioXY() {
        x2 = max(left, min(left + imgWidth,
            x1 + abs(y2 - y1) * aspectRatio * (x2 > x1 || -1)));

        y2 = round(max(top, min(top + imgHeight,
            y1 + abs(x2 - x1) / aspectRatio * (y2 > y1 || -1))));
        x2 = round(x2);
    }

    function aspectRatioYX() {
        y2 = max(top, min(top + imgHeight,
            y1 + abs(x2 - x1) / aspectRatio * (y2 > y1 || -1)));
        x2 = round(max(left, min(left + imgWidth,
            x1 + abs(y2 - y1) * aspectRatio * (x2 > x1 || -1))));
        y2 = round(y2);
    }

    function doResize() {
        if (abs(x2 - x1) < options.minWidth) {
            x2 = x1 - options.minWidth * (x2 < x1 || -1);

            if (x2 < left)
                x1 = left + options.minWidth;
            else if (x2 > left + imgWidth)
                x1 = left + imgWidth - options.minWidth;
        }

        if (abs(y2 - y1) < options.minHeight) {
            y2 = y1 - options.minHeight * (y2 < y1 || -1);

            if (y2 < top)
                y1 = top + options.minHeight;
            else if (y2 > top + imgHeight)
                y1 = top + imgHeight - options.minHeight;
        }

        x2 = max(left, min(x2, left + imgWidth));
        y2 = max(top, min(y2, top + imgHeight));

        if (aspectRatio)
            if (abs(x2 - x1) / aspectRatio > abs(y2 - y1))
                aspectRatioYX();
            else
                aspectRatioXY();

        if (abs(x2 - x1) > options.maxWidth) {
            x2 = x1 - options.maxWidth * (x2 < x1 || -1);
            if (aspectRatio) aspectRatioYX();
        }

        if (abs(y2 - y1) > options.maxHeight) {
            y2 = y1 - options.maxHeight * (y2 < y1 || -1);
            if (aspectRatio) aspectRatioXY();
        }

        selection = { x1: selX(min(x1, x2)), x2: selX(max(x1, x2)),
            y1: selY(min(y1, y2)), y2: selY(max(y1, y2)),
            width: abs(x2 - x1), height: abs(y2 - y1) };

        update();

        options.onSelectChange(img, getSelection());
    }

    function selectingMouseMove(event) {
        x2 = resize == '' || /w|e/.test(resize) || aspectRatio ? evX(event) : viewX(selection.x2);
        y2 = resize == '' || /n|s/.test(resize) || aspectRatio ? evY(event) : viewY(selection.y2);

        doResize();

        return false;

    }

    function doMove(newX1, newY1) {
        x2 = (x1 = newX1) + selection.width;
        y2 = (y1 = newY1) + selection.height;

        selection = $.extend(selection, { x1: selX(x1), y1: selY(y1),
            x2: selX(x2), y2: selY(y2) });

        update();

        options.onSelectChange(img, getSelection());
    }

    function movingMouseMove(event) {
        x1 = max(left, min(startX + evX(event), left + imgWidth - selection.width));
        y1 = max(top, min(startY + evY(event), top + imgHeight - selection.height));

        doMove(x1, y1);

        event.preventDefault();

        return false;
    }

    function startSelection() {
        adjust();

        x2 = x1;
        y2 = y1;

        doResize();

        resize = '';

        if ($outer.is(':not(:visible)'))
            $box.add($outer).hide().fadeIn(options.fadeSpeed||0);

        shown = true;

        $(document).unbind('mouseup', cancelSelection)
            .mousemove(selectingMouseMove).one('mouseup', docMouseUp);
        $box.unbind('mousemove', areaMouseMove);

        options.onSelectStart(img, getSelection());
    }

    function cancelSelection() {
        $(document).unbind('mousemove', startSelection);
        hide($box.add($outer));

        selection = { x1: selX(x1), y1: selY(y1), x2: selX(x1), y2: selY(y1),
                width: 0, height: 0 };

        options.onSelectChange(img, getSelection());
        options.onSelectEnd(img, getSelection());
    }

    function imgMouseDown(event) {
        if (event.which != 1 || $outer.is(':animated')) return false;

        adjust();
        startX = x1 = evX(event);
        startY = y1 = evY(event);

        $(document).one('mousemove', startSelection)
            .one('mouseup', cancelSelection);

        return false;
    }

    function parentScroll() {
        doUpdate(false);
    }

    function imgLoad() {
        imgLoaded = true;

        setOptions(options = $.extend({
            classPrefix: 'imgareaselect',
            movable: true,
            resizable: true,
            parent: 'body',
            onInit: function () {},
            onSelectStart: function () {},
            onSelectChange: function () {},
            onSelectEnd: function () {}
        }, options));

        $box.add($outer).css({ visibility: '' });

        if (options.show) {
            shown = true;
            adjust();
            update();
            $box.add($outer).hide().fadeIn(options.fadeSpeed||0);
        }

        setTimeout(function () { options.onInit(img, getSelection()); }, 0);
    }

    var docKeyPress = function(event) {
        var k = options.keys, d, t, key = event.keyCode || event.which;

        d = !isNaN(k.alt) && (event.altKey || event.originalEvent.altKey) ? k.alt :
            !isNaN(k.ctrl) && event.ctrlKey ? k.ctrl :
            !isNaN(k.shift) && event.shiftKey ? k.shift :
            !isNaN(k.arrows) ? k.arrows : 10;

        if (k.arrows == 'resize' || (k.shift == 'resize' && event.shiftKey) ||
            (k.ctrl == 'resize' && event.ctrlKey) ||
            (k.alt == 'resize' && (event.altKey || event.originalEvent.altKey)))
        {
            switch (key) {
            case 37:
                d = -d;
            case 39:
                t = max(x1, x2);
                x1 = min(x1, x2);
                x2 = max(t + d, x1);
                if (aspectRatio) aspectRatioYX();
                break;
            case 38:
                d = -d;
            case 40:
                t = max(y1, y2);
                y1 = min(y1, y2);
                y2 = max(t + d, y1);
                if (aspectRatio) aspectRatioXY();
                break;
            default:
                return;
            }

            doResize();
        }
        else {
            x1 = min(x1, x2);
            y1 = min(y1, y2);

            switch (key) {
            case 37:
                doMove(max(x1 - d, left), y1);
                break;
            case 38:
                doMove(x1, max(y1 - d, top));
                break;
            case 39:
                doMove(x1 + min(d, imgWidth - selX(x2)), y1);
                break;
            case 40:
                doMove(x1, y1 + min(d, imgHeight - selY(y2)));
                break;
            default:
                return;
            }
        }

        return false;
    };

    function styleOptions($elem, props) {
        for (option in props)
            if (options[option] !== undefined)
                $elem.css(props[option], options[option]);
    }

    function setOptions(newOptions) {
        if (newOptions.parent)
            ($parent = $(newOptions.parent)).append($box.add($outer));

        options = $.extend(options, newOptions);

        adjust();

        if (newOptions.handles != null) {
            $handles.remove();
            $handles = $([]);

            i = newOptions.handles ? newOptions.handles == 'corners' ? 4 : 8 : 0;

            while (i--)
                $handles = $handles.add(div());

            $handles.addClass(options.classPrefix + '-handle').css({
                position: 'absolute',
                fontSize: 0,
                zIndex: zIndex + 1 || 1
            });

            if (!parseInt($handles.css('width')))
                $handles.width(5).height(5);

            if (o = options.borderWidth)
                $handles.css({ borderWidth: o, borderStyle: 'solid' });

            styleOptions($handles, { borderColor1: 'border-color',
                borderColor2: 'background-color',
                borderOpacity: 'opacity' });
        }

        scaleX = options.imageWidth / imgWidth || 1;
        scaleY = options.imageHeight / imgHeight || 1;

        if (newOptions.x1 != null) {
            setSelection(newOptions.x1, newOptions.y1, newOptions.x2,
                    newOptions.y2);
            newOptions.show = !newOptions.hide;
        }

        if (newOptions.keys)
            options.keys = $.extend({ shift: 1, ctrl: 'resize' },
                newOptions.keys);

        $outer.addClass(options.classPrefix + '-outer');
        $area.addClass(options.classPrefix + '-selection');
        for (i = 0; i++ < 4;)
            $($border[i-1]).addClass(options.classPrefix + '-border' + i);

        styleOptions($area, { selectionColor: 'background-color',
            selectionOpacity: 'opacity' });
        styleOptions($border, { borderOpacity: 'opacity',
            borderWidth: 'border-width' });
        styleOptions($outer, { outerColor: 'background-color',
            outerOpacity: 'opacity' });
        if (o = options.borderColor1)
            $($border[0]).css({ borderStyle: 'solid', borderColor: o });
        if (o = options.borderColor2)
            $($border[1]).css({ borderStyle: 'dashed', borderColor: o });

        $box.append($area.add($border).add($handles).add($areaOpera));

        if ($.browser.msie) {
            if (o = $outer.css('filter').match(/opacity=([0-9]+)/))
                $outer.css('opacity', o[1]/100);
            if (o = $border.css('filter').match(/opacity=([0-9]+)/))
                $border.css('opacity', o[1]/100);
        }

        if (newOptions.hide)
            hide($box.add($outer));
        else if (newOptions.show && imgLoaded) {
            shown = true;
            $box.add($outer).fadeIn(options.fadeSpeed||0);
            doUpdate();
        }

        aspectRatio = (d = (options.aspectRatio || '').split(/:/))[0] / d[1];

        if (options.disable || options.enable === false) {
            $box.unbind('mousemove', areaMouseMove).unbind('mousedown', areaMouseDown);
            $img.add($outer).unbind('mousedown', imgMouseDown);
            $(window).unbind('resize', parentScroll);
            $img.add($img.parents()).unbind('scroll', parentScroll);
        }
        else if (options.enable || options.disable === false) {
            if (options.resizable || options.movable)
                $box.mousemove(areaMouseMove).mousedown(areaMouseDown);

            if (!options.persistent)
                $img.add($outer).mousedown(imgMouseDown);
            $(window).resize(parentScroll);
            $img.add($img.parents()).scroll(parentScroll);
        }

        options.enable = options.disable = undefined;
    }

    this.getOptions = function () { return options; };

    this.setOptions = setOptions;

    this.getSelection = getSelection;

    this.setSelection = setSelection;

    this.update = doUpdate;

    $p = $img;

    while ($p.length && !$p.is('body')) {
        if (!isNaN($p.css('z-index')) && $p.css('z-index') > zIndex)
            zIndex = $p.css('z-index');
        if ($p.css('position') == 'fixed')
            position = 'fixed';

        $p = $p.parent();
    }

    if (!isNaN(options.zIndex))
        zIndex = options.zIndex;

    if ($.browser.msie)
        $img.attr('unselectable', 'on');

    $.imgAreaSelect.keyPress = $.browser.msie ||
        $.browser.safari ? 'keydown' : 'keypress';

    if ($.browser.opera)
        $areaOpera = div().css({ width: '100%', height: '100%',
            position: 'absolute', zIndex: zIndex + 2 || 2 });

    $box.add($outer).css({ visibility: 'hidden', position: position,
        overflow: 'hidden', zIndex: zIndex || '0' });
    $box.css({ zIndex: zIndex + 2 || 2 });
    $area.add($border).css({ position: 'absolute' });

    img.complete || img.readyState == 'complete' || !$img.is('img') ?
        imgLoad() : $img.one('load', imgLoad);

};

$.fn.imgAreaSelect = function (options) {
    options = options || {};

    this.each(function () {
        if ($(this).data('imgAreaSelect'))
            $(this).data('imgAreaSelect').setOptions(options);
        else {
            if (options.enable === undefined && options.disable === undefined)
                options.enable = true;

            $(this).data('imgAreaSelect', new $.imgAreaSelect(this, options));
        }
    });

    if (options.instance)
        return $(this).data('imgAreaSelect');

    return this;
};

})(jQuery);
