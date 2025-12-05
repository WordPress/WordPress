(function () {
    var cookiename = 'mtm_consent_removed';

    function listen_event(e, type, callback) {
        if (e.addEventListener) {
            return e.addEventListener(type, callback, false);
        }

        if (e.attachEvent) {
            return e.attachEvent('on' + type, callback);
        }

        e['on' + type] = callback;
    }
    function by_id(id) {
        return document.getElementById(id);
    }
    function are_cookies_disabled() {
        return navigator && !navigator.cookieEnabled;
    }
    function set_display(id, status)
    {
        var e = by_id(id);
        if (e) {
            e.style.display = status;
        }
    }
    function is_opted_out() {
        // piwik_ignore check for BC.
        return document.cookie && (document.cookie.indexOf(cookiename + '=1') !== -1 || document.cookie.indexOf('piwik_ignore=') !== -1);
    }
    function update_status()
    {
        if (are_cookies_disabled()) {
            set_display('matomo_outout_err_cookies', 'block');
            set_display('matomo_optout_checkbox', 'none');
        } else if (is_opted_out()) {
            set_display('matomo_opted_out_intro', 'block');
            set_display('matomo_opted_in_intro', 'none');
            set_display('matomo_opted_out_label', 'inline');
            set_display('matomo_opted_in_label', 'none');
            by_id('matomo_optout_checkbox').checked = false;
        } else {
            set_display('matomo_opted_out_intro', 'none');
            set_display('matomo_opted_in_intro', 'block');
            set_display('matomo_opted_out_label', 'none');
            set_display('matomo_opted_in_label', 'inline');
            by_id('matomo_optout_checkbox').checked = true;
        }
    }
    function on_ready(callback) {
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(callback, 1);
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    function set_cookie(name, val, expires, path, domain)
    {
        var cookie = name + '=' + val + ';expires=' + expires + ';SameSite=Lax;path=' + (path || '/');
        if (domain) {
            cookie += ';domain=' + domain;
        }
        document.cookie = cookie;
    }

    on_ready(function () {
        update_status();

        if (are_cookies_disabled()) {
            return;
        }

        listen_event(by_id('matomo_optout_checkbox'),'change', function () {
            var trackers = [];
            if ('object' === typeof window.Piwik && 'function' === typeof Piwik.getAsyncTrackers) {
                trackers = Piwik.getAsyncTrackers();
            }
            var value = 0;
            var expires = 'Thu, 01 Jan 1970 00:00:01 GMT'
            if (is_opted_out()) {
                // for BC additionally remove any set piwik_ignore cookie
                set_cookie('piwik_ignore', 0, expires, '/');
            } else {
                value = 1;
                var expire = new Date();
                expire.setTime(expire.getTime() + (86400 * 365 * 30 * 1000));
                expires = expire.toGMTString();
            }

            if (trackers.length) {
                // respect tracker settings
                for (var i = 0; i < trackers.length; i++) {
                    set_cookie(cookiename, value, expires, trackers[i].getCookiePath(), trackers[i].getCookieDomain());
                }
            } else {
                // fallback
                set_cookie(cookiename, value, expires, '/');
            }

            update_status();
        });
    })
})();
