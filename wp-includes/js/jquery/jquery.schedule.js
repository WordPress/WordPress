/*
**  jquery.schedule.js -- jQuery plugin for scheduled/deferred actions
**  Copyright (c) 2007 Ralf S. Engelschall <rse@engelschall.com> 
**  Licensed under GPL <http://www.gnu.org/licenses/gpl.txt>
**
**  $LastChangedDate$
**  $LastChangedRevision$
*/

/*
 *  <div id="button">TEST BUTTON</div>
 *  <div id="test"></div>
 *
 *  <script type="text/javascript">
 *     $(document).ready(
 *     function(){
 *         $('#button').click(function () {
 *             $(this).css("color", "blue").schedule(2000, function (x) {
 *                 $(this).css("color", "red");
 *                 $("#test").html("test: x = " + x);
 *             }, 42);
 *         });
 *     });
 *  </script>
 */

(function($) {

    /*  object constructor  */
    $.scheduler = function () {
        this.bucket = {};
        return;
    };

    /*  object methods  */
    $.scheduler.prototype = {
        /*  schedule a task  */
        schedule: function () {
            /*  schedule context with default parameters */
            var ctx = {
                "id":         null,         /* unique identifier of high-level schedule */
                "time":       1000,         /* time in milliseconds after which the task is run */
                "repeat":     false,        /* whether schedule should be automatically repeated */
                "protect":    false,        /* whether schedule should be protected from double scheduling */
                "obj":        null,         /* function context object ("this") */
                "func":       function(){}, /* function to call */
                "args":       []            /* function arguments to pass */
            };

            /*  helper function: portable checking whether something is a function  */
            function _isfn (fn) {
                return (
                       !!fn
                    && typeof fn != "string"
                    && typeof fn[0] == "undefined"
                    && RegExp("function", "i").test(fn + "")
                );
            };
            
            /*  parse arguments into context parameters (part 1/4):
                detect an override object (special case to support jQuery method) */
            var i = 0;
            var override = false;
            if (typeof arguments[i] == "object" && arguments.length > 1) {
                override = true;
                i++;
            }

            /*  parse arguments into context parameters (part 2/4):
                support the flexible way of an associated array */
            if (typeof arguments[i] == "object") {
                for (var option in arguments[i])
                    if (typeof ctx[option] != "undefined")
                        ctx[option] = arguments[i][option];
                i++;
            }

            /*  parse arguments into context parameters (part 3/4):
                support: schedule([time [, repeat], ]{{obj, methodname} | func}[, arg, ...]); */
            if (   typeof arguments[i] == "number"
                || (   typeof arguments[i] == "string" 
                    && arguments[i].match(RegExp("^[0-9]+[smhdw]$"))))
                ctx["time"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["repeat"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["protect"] = arguments[i++];
            if (   typeof arguments[i] == "object"
                && typeof arguments[i+1] == "string"
                && _isfn(arguments[i][arguments[i+1]])) {
                ctx["obj"] = arguments[i++];
                ctx["func"] = arguments[i++];
            }
            else if (   typeof arguments[i] != "undefined"
                     && (   _isfn(arguments[i]) 
                         || typeof arguments[i] == "string"))
                ctx["func"] = arguments[i++];
            while (typeof arguments[i] != "undefined")
                ctx["args"].push(arguments[i++]);

            /*  parse arguments into context parameters (part 4/4):
                apply parameters from override object */
            if (override) {
                if (typeof arguments[1] == "object") {
                    for (var option in arguments[0])
                        if (   typeof ctx[option] != "undefined"
                            && typeof arguments[1][option] == "undefined")
                            ctx[option] = arguments[0][option];
                }
                else {
                    for (var option in arguments[0])
                        if (typeof ctx[option] != "undefined")
                            ctx[option] = arguments[0][option];
                }
                i++;
            }

            /*  annotate context with internals */
            ctx["_scheduler"] = this; /* internal: back-reference to scheduler object */
            ctx["_handle"]    = null; /* internal: unique handle of low-level task */

            /*  determine time value in milliseconds */
            var match = String(ctx["time"]).match(RegExp("^([0-9]+)([smhdw])$"));
            if (match && match[0] != "undefined" && match[1] != "undefined")
                ctx["time"] = String(parseInt(match[1]) *
                    { s: 1000, m: 1000*60, h: 1000*60*60,
                      d: 1000*60*60*24, w: 1000*60*60*24*7 }[match[2]]);

            /*  determine unique identifier of task  */
            if (ctx["id"] == null)
                ctx["id"] = (  String(ctx["repeat"])  + ":"
                             + String(ctx["protect"]) + ":"
                             + String(ctx["time"])    + ":"
                             + String(ctx["obj"])     + ":"
                             + String(ctx["func"])    + ":"
                             + String(ctx["args"])         );

            /*  optionally protect from duplicate calls  */
            if (ctx["protect"])
                if (typeof this.bucket[ctx["id"]] != "undefined")
                    return this.bucket[ctx["id"]];

            /*  support execution of methods by name and arbitrary scripts  */
            if (!_isfn(ctx["func"])) {
                if (   ctx["obj"] != null
                    && typeof ctx["obj"] == "object"
                    && typeof ctx["func"] == "string"
                    && _isfn(ctx["obj"][ctx["func"]]))
                    /*  method by name  */
                    ctx["func"] = ctx["obj"][ctx["func"]];
                else
                    /*  arbitrary script  */
                    ctx["func"] = eval("function () { " + ctx["func"] + " }");
            }

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  store context into bucket of scheduler object  */
            this.bucket[ctx["id"]] = ctx;

            /*  return context  */
            return ctx;
        },

        /*  re-schedule a task  */
        reschedule: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  return context  */
            return ctx;
        },

        /*  internal scheduling operation  */
        _schedule: function (ctx) {
            /*  closure to act as the call trampoline function  */
            var trampoline = function () {
                /*  jump into function  */
                var obj = (ctx["obj"] != null ? ctx["obj"] : ctx);
                (ctx["func"]).apply(obj, ctx["args"]);

                /*  either repeat scheduling and keep in bucket or
                    just stop scheduling and delete from scheduler bucket  */
                if (   /* not cancelled from inside... */
                       typeof (ctx["_scheduler"]).bucket[ctx["id"]] != "undefined"
                    && /* ...and repeating requested */
                       ctx["repeat"])
                    (ctx["_scheduler"])._schedule(ctx);
                else
                    delete (ctx["_scheduler"]).bucket[ctx["id"]];
            };

            /*  schedule task and return handle  */
            return setTimeout(trampoline, ctx["time"]);
        },

        /*  cancel a scheduled task  */
        cancel: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  cancel scheduled task  */
            if (typeof ctx == "object") {
                clearTimeout(ctx["_handle"]);
                delete this.bucket[ctx["id"]];
            }
        }
    };

    /* integrate a global instance of the scheduler into the global jQuery object */
    $.extend({
        scheduler$: new $.scheduler(),
        schedule:   function () { return $.scheduler$.schedule.apply  ($.scheduler$, arguments) },
        reschedule: function () { return $.scheduler$.reschedule.apply($.scheduler$, arguments) },
        cancel:     function () { return $.scheduler$.cancel.apply    ($.scheduler$, arguments) }
    });

    /* integrate scheduling convinience method into all jQuery objects */
    $.fn.extend({
        schedule: function () {
            var a = [ {} ];
            for (var i = 0; i < arguments.length; i++)
                a.push(arguments[i]);
            return this.each(function () {
                a[0] = { "id": this, "obj": this };
                return $.schedule.apply($, a);
            });
        }
    });

})(jQuery);

/*
**  jquery.schedule.js -- jQuery plugin for scheduled/deferred actions
**  Copyright (c) 2007 Ralf S. Engelschall <rse@engelschall.com> 
**  Licensed under GPL <http://www.gnu.org/licenses/gpl.txt>
**
**  $LastChangedDate$
**  $LastChangedRevision$
*/

/*
 *  <div id="button">TEST BUTTON</div>
 *  <div id="test"></div>
 *
 *  <script type="text/javascript">
 *     $(document).ready(
 *     function(){
 *         $('#button').click(function () {
 *             $(this).css("color", "blue").schedule(2000, function (x) {
 *                 $(this).css("color", "red");
 *                 $("#test").html("test: x = " + x);
 *             }, 42);
 *         });
 *     });
 *  </script>
 */

(function($) {

    /*  object constructor  */
    $.scheduler = function () {
        this.bucket = {};
        return;
    };

    /*  object methods  */
    $.scheduler.prototype = {
        /*  schedule a task  */
        schedule: function () {
            /*  schedule context with default parameters */
            var ctx = {
                "id":         null,         /* unique identifier of high-level schedule */
                "time":       1000,         /* time in milliseconds after which the task is run */
                "repeat":     false,        /* whether schedule should be automatically repeated */
                "protect":    false,        /* whether schedule should be protected from double scheduling */
                "obj":        null,         /* function context object ("this") */
                "func":       function(){}, /* function to call */
                "args":       []            /* function arguments to pass */
            };

            /*  helper function: portable checking whether something is a function  */
            function _isfn (fn) {
                return (
                       !!fn
                    && typeof fn != "string"
                    && typeof fn[0] == "undefined"
                    && RegExp("function", "i").test(fn + "")
                );
            };
            
            /*  parse arguments into context parameters (part 1/4):
                detect an override object (special case to support jQuery method) */
            var i = 0;
            var override = false;
            if (typeof arguments[i] == "object" && arguments.length > 1) {
                override = true;
                i++;
            }

            /*  parse arguments into context parameters (part 2/4):
                support the flexible way of an associated array */
            if (typeof arguments[i] == "object") {
                for (var option in arguments[i])
                    if (typeof ctx[option] != "undefined")
                        ctx[option] = arguments[i][option];
                i++;
            }

            /*  parse arguments into context parameters (part 3/4):
                support: schedule([time [, repeat], ]{{obj, methodname} | func}[, arg, ...]); */
            if (   typeof arguments[i] == "number"
                || (   typeof arguments[i] == "string" 
                    && arguments[i].match(RegExp("^[0-9]+[smhdw]$"))))
                ctx["time"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["repeat"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["protect"] = arguments[i++];
            if (   typeof arguments[i] == "object"
                && typeof arguments[i+1] == "string"
                && _isfn(arguments[i][arguments[i+1]])) {
                ctx["obj"] = arguments[i++];
                ctx["func"] = arguments[i++];
            }
            else if (   typeof arguments[i] != "undefined"
                     && (   _isfn(arguments[i]) 
                         || typeof arguments[i] == "string"))
                ctx["func"] = arguments[i++];
            while (typeof arguments[i] != "undefined")
                ctx["args"].push(arguments[i++]);

            /*  parse arguments into context parameters (part 4/4):
                apply parameters from override object */
            if (override) {
                if (typeof arguments[1] == "object") {
                    for (var option in arguments[0])
                        if (   typeof ctx[option] != "undefined"
                            && typeof arguments[1][option] == "undefined")
                            ctx[option] = arguments[0][option];
                }
                else {
                    for (var option in arguments[0])
                        if (typeof ctx[option] != "undefined")
                            ctx[option] = arguments[0][option];
                }
                i++;
            }

            /*  annotate context with internals */
            ctx["_scheduler"] = this; /* internal: back-reference to scheduler object */
            ctx["_handle"]    = null; /* internal: unique handle of low-level task */

            /*  determine time value in milliseconds */
            var match = String(ctx["time"]).match(RegExp("^([0-9]+)([smhdw])$"));
            if (match && match[0] != "undefined" && match[1] != "undefined")
                ctx["time"] = String(parseInt(match[1]) *
                    { s: 1000, m: 1000*60, h: 1000*60*60,
                      d: 1000*60*60*24, w: 1000*60*60*24*7 }[match[2]]);

            /*  determine unique identifier of task  */
            if (ctx["id"] == null)
                ctx["id"] = (  String(ctx["repeat"])  + ":"
                             + String(ctx["protect"]) + ":"
                             + String(ctx["time"])    + ":"
                             + String(ctx["obj"])     + ":"
                             + String(ctx["func"])    + ":"
                             + String(ctx["args"])         );

            /*  optionally protect from duplicate calls  */
            if (ctx["protect"])
                if (typeof this.bucket[ctx["id"]] != "undefined")
                    return this.bucket[ctx["id"]];

            /*  support execution of methods by name and arbitrary scripts  */
            if (!_isfn(ctx["func"])) {
                if (   ctx["obj"] != null
                    && typeof ctx["obj"] == "object"
                    && typeof ctx["func"] == "string"
                    && _isfn(ctx["obj"][ctx["func"]]))
                    /*  method by name  */
                    ctx["func"] = ctx["obj"][ctx["func"]];
                else
                    /*  arbitrary script  */
                    ctx["func"] = eval("function () { " + ctx["func"] + " }");
            }

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  store context into bucket of scheduler object  */
            this.bucket[ctx["id"]] = ctx;

            /*  return context  */
            return ctx;
        },

        /*  re-schedule a task  */
        reschedule: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  return context  */
            return ctx;
        },

        /*  internal scheduling operation  */
        _schedule: function (ctx) {
            /*  closure to act as the call trampoline function  */
            var trampoline = function () {
                /*  jump into function  */
                var obj = (ctx["obj"] != null ? ctx["obj"] : ctx);
                (ctx["func"]).apply(obj, ctx["args"]);

                /*  either repeat scheduling and keep in bucket or
                    just stop scheduling and delete from scheduler bucket  */
                if (   /* not cancelled from inside... */
                       typeof (ctx["_scheduler"]).bucket[ctx["id"]] != "undefined"
                    && /* ...and repeating requested */
                       ctx["repeat"])
                    (ctx["_scheduler"])._schedule(ctx);
                else
                    delete (ctx["_scheduler"]).bucket[ctx["id"]];
            };

            /*  schedule task and return handle  */
            return setTimeout(trampoline, ctx["time"]);
        },

        /*  cancel a scheduled task  */
        cancel: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  cancel scheduled task  */
            if (typeof ctx == "object") {
                clearTimeout(ctx["_handle"]);
                delete this.bucket[ctx["id"]];
            }
        }
    };

    /* integrate a global instance of the scheduler into the global jQuery object */
    $.extend({
        scheduler$: new $.scheduler(),
        schedule:   function () { return $.scheduler$.schedule.apply  ($.scheduler$, arguments) },
        reschedule: function () { return $.scheduler$.reschedule.apply($.scheduler$, arguments) },
        cancel:     function () { return $.scheduler$.cancel.apply    ($.scheduler$, arguments) }
    });

    /* integrate scheduling convinience method into all jQuery objects */
    $.fn.extend({
        schedule: function () {
            var a = [ {} ];
            for (var i = 0; i < arguments.length; i++)
                a.push(arguments[i]);
            return this.each(function () {
                a[0] = { "id": this, "obj": this };
                return $.schedule.apply($, a);
            });
        }
    });

})(jQuery);

/*
**  jquery.schedule.js -- jQuery plugin for scheduled/deferred actions
**  Copyright (c) 2007 Ralf S. Engelschall <rse@engelschall.com> 
**  Licensed under GPL <http://www.gnu.org/licenses/gpl.txt>
**
**  $LastChangedDate$
**  $LastChangedRevision$
*/

/*
 *  <div id="button">TEST BUTTON</div>
 *  <div id="test"></div>
 *
 *  <script type="text/javascript">
 *     $(document).ready(
 *     function(){
 *         $('#button').click(function () {
 *             $(this).css("color", "blue").schedule(2000, function (x) {
 *                 $(this).css("color", "red");
 *                 $("#test").html("test: x = " + x);
 *             }, 42);
 *         });
 *     });
 *  </script>
 */

(function($) {

    /*  object constructor  */
    $.scheduler = function () {
        this.bucket = {};
        return;
    };

    /*  object methods  */
    $.scheduler.prototype = {
        /*  schedule a task  */
        schedule: function () {
            /*  schedule context with default parameters */
            var ctx = {
                "id":         null,         /* unique identifier of high-level schedule */
                "time":       1000,         /* time in milliseconds after which the task is run */
                "repeat":     false,        /* whether schedule should be automatically repeated */
                "protect":    false,        /* whether schedule should be protected from double scheduling */
                "obj":        null,         /* function context object ("this") */
                "func":       function(){}, /* function to call */
                "args":       []            /* function arguments to pass */
            };

            /*  helper function: portable checking whether something is a function  */
            function _isfn (fn) {
                return (
                       !!fn
                    && typeof fn != "string"
                    && typeof fn[0] == "undefined"
                    && RegExp("function", "i").test(fn + "")
                );
            };
            
            /*  parse arguments into context parameters (part 1/4):
                detect an override object (special case to support jQuery method) */
            var i = 0;
            var override = false;
            if (typeof arguments[i] == "object" && arguments.length > 1) {
                override = true;
                i++;
            }

            /*  parse arguments into context parameters (part 2/4):
                support the flexible way of an associated array */
            if (typeof arguments[i] == "object") {
                for (var option in arguments[i])
                    if (typeof ctx[option] != "undefined")
                        ctx[option] = arguments[i][option];
                i++;
            }

            /*  parse arguments into context parameters (part 3/4):
                support: schedule([time [, repeat], ]{{obj, methodname} | func}[, arg, ...]); */
            if (   typeof arguments[i] == "number"
                || (   typeof arguments[i] == "string" 
                    && arguments[i].match(RegExp("^[0-9]+[smhdw]$"))))
                ctx["time"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["repeat"] = arguments[i++];
            if (typeof arguments[i] == "boolean")
                ctx["protect"] = arguments[i++];
            if (   typeof arguments[i] == "object"
                && typeof arguments[i+1] == "string"
                && _isfn(arguments[i][arguments[i+1]])) {
                ctx["obj"] = arguments[i++];
                ctx["func"] = arguments[i++];
            }
            else if (   typeof arguments[i] != "undefined"
                     && (   _isfn(arguments[i]) 
                         || typeof arguments[i] == "string"))
                ctx["func"] = arguments[i++];
            while (typeof arguments[i] != "undefined")
                ctx["args"].push(arguments[i++]);

            /*  parse arguments into context parameters (part 4/4):
                apply parameters from override object */
            if (override) {
                if (typeof arguments[1] == "object") {
                    for (var option in arguments[0])
                        if (   typeof ctx[option] != "undefined"
                            && typeof arguments[1][option] == "undefined")
                            ctx[option] = arguments[0][option];
                }
                else {
                    for (var option in arguments[0])
                        if (typeof ctx[option] != "undefined")
                            ctx[option] = arguments[0][option];
                }
                i++;
            }

            /*  annotate context with internals */
            ctx["_scheduler"] = this; /* internal: back-reference to scheduler object */
            ctx["_handle"]    = null; /* internal: unique handle of low-level task */

            /*  determine time value in milliseconds */
            var match = String(ctx["time"]).match(RegExp("^([0-9]+)([smhdw])$"));
            if (match && match[0] != "undefined" && match[1] != "undefined")
                ctx["time"] = String(parseInt(match[1]) *
                    { s: 1000, m: 1000*60, h: 1000*60*60,
                      d: 1000*60*60*24, w: 1000*60*60*24*7 }[match[2]]);

            /*  determine unique identifier of task  */
            if (ctx["id"] == null)
                ctx["id"] = (  String(ctx["repeat"])  + ":"
                             + String(ctx["protect"]) + ":"
                             + String(ctx["time"])    + ":"
                             + String(ctx["obj"])     + ":"
                             + String(ctx["func"])    + ":"
                             + String(ctx["args"])         );

            /*  optionally protect from duplicate calls  */
            if (ctx["protect"])
                if (typeof this.bucket[ctx["id"]] != "undefined")
                    return this.bucket[ctx["id"]];

            /*  support execution of methods by name and arbitrary scripts  */
            if (!_isfn(ctx["func"])) {
                if (   ctx["obj"] != null
                    && typeof ctx["obj"] == "object"
                    && typeof ctx["func"] == "string"
                    && _isfn(ctx["obj"][ctx["func"]]))
                    /*  method by name  */
                    ctx["func"] = ctx["obj"][ctx["func"]];
                else
                    /*  arbitrary script  */
                    ctx["func"] = eval("function () { " + ctx["func"] + " }");
            }

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  store context into bucket of scheduler object  */
            this.bucket[ctx["id"]] = ctx;

            /*  return context  */
            return ctx;
        },

        /*  re-schedule a task  */
        reschedule: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  pass-through to internal scheduling operation  */
            ctx["_handle"] = this._schedule(ctx);

            /*  return context  */
            return ctx;
        },

        /*  internal scheduling operation  */
        _schedule: function (ctx) {
            /*  closure to act as the call trampoline function  */
            var trampoline = function () {
                /*  jump into function  */
                var obj = (ctx["obj"] != null ? ctx["obj"] : ctx);
                (ctx["func"]).apply(obj, ctx["args"]);

                /*  either repeat scheduling and keep in bucket or
                    just stop scheduling and delete from scheduler bucket  */
                if (   /* not cancelled from inside... */
                       typeof (ctx["_scheduler"]).bucket[ctx["id"]] != "undefined"
                    && /* ...and repeating requested */
                       ctx["repeat"])
                    (ctx["_scheduler"])._schedule(ctx);
                else
                    delete (ctx["_scheduler"]).bucket[ctx["id"]];
            };

            /*  schedule task and return handle  */
            return setTimeout(trampoline, ctx["time"]);
        },

        /*  cancel a scheduled task  */
        cancel: function (ctx) {
            if (typeof ctx == "string")
                ctx = this.bucket[ctx];

            /*  cancel scheduled task  */
            if (typeof ctx == "object") {
                clearTimeout(ctx["_handle"]);
                delete this.bucket[ctx["id"]];
            }
        }
    };

    /* integrate a global instance of the scheduler into the global jQuery object */
    $.extend({
        scheduler$: new $.scheduler(),
        schedule:   function () { return $.scheduler$.schedule.apply  ($.scheduler$, arguments) },
        reschedule: function () { return $.scheduler$.reschedule.apply($.scheduler$, arguments) },
        cancel:     function () { return $.scheduler$.cancel.apply    ($.scheduler$, arguments) }
    });

    /* integrate scheduling convinience method into all jQuery objects */
    $.fn.extend({
        schedule: function () {
            var a = [ {} ];
            for (var i = 0; i < arguments.length; i++)
                a.push(arguments[i]);
            return this.each(function () {
                a[0] = { "id": this, "obj": this };
                return $.schedule.apply($, a);
            });
        }
    });

})(jQuery);

