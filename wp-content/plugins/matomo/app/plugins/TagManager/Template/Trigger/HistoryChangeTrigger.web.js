(function () {
    return function (parameters, TagManager) {

        function getCurrentUrl()
        {
            return parameters.window.location.href;
        }
        function getEventUrl(event)
        {
            if (event && event.target && event.target.location && event.target.location.href) {
                return event.target.location.href;
            }
            return getCurrentUrl();
        }

        this.setUp = function (triggerEvent) {
            var initialUrl = getCurrentUrl();
            var url = TagManager.url;
            var origin = url.parseUrl(initialUrl, 'origin');

            var lastEvent = {
                eventType: null,
                hash: url.parseUrl(initialUrl, 'hash'),
                search: url.parseUrl(initialUrl, 'search'),
                path: url.parseUrl(initialUrl, 'pathname'),
                state: parameters.window.state || null
            };

            function trigger(eventType, newUrl, newState)
            {
                var newEvent = {
                    eventType: eventType,
                    hash: url.parseUrl(newUrl, 'hash'),
                    search: url.parseUrl(newUrl, 'search'),
                    path: url.parseUrl(newUrl, 'pathname'),
                    state: newState
                };

                var shouldForceEvent = (lastEvent.eventType === 'popstate' && newEvent.eventType === 'hashchange') || (lastEvent.eventType === 'hashchange' && newEvent.eventType === 'popstate') || (lastEvent.eventType === 'hashchange' && newEvent.eventType === 'hashchange') || (lastEvent.eventType === 'popstate' && newEvent.eventType === 'popstate');
                shouldForceEvent = !shouldForceEvent;

                var oldUrl = lastEvent.path;
                if (lastEvent.search) {
                    oldUrl += '?' + lastEvent.search;
                }
                if (lastEvent.hash) {
                    oldUrl += '#' + lastEvent.hash;
                }
                var nowUrl = newEvent.path;
                if (newEvent.search) {
                    nowUrl += '?' + newEvent.search;
                }
                if (newEvent.hash) {
                    nowUrl += '#' + newEvent.hash;
                }
                if (shouldForceEvent || oldUrl !== nowUrl) {
                    var tmpLast = lastEvent;
                    lastEvent = newEvent; // overwrite as early as possible in case event gets triggered again

                    triggerEvent({
                        event: 'mtm.HistoryChange', 'mtm.historyChangeSource': newEvent.eventType,
                        'mtm.oldUrl': origin + oldUrl, 'mtm.newUrl': origin + nowUrl,
                        'mtm.oldUrlHash': tmpLast.hash, 'mtm.newUrlHash': newEvent.hash,
                        'mtm.oldUrlPath': tmpLast.path, 'mtm.newUrlPath': newEvent.path,
                        'mtm.oldUrlSearch': tmpLast.search, 'mtm.newUrlSearch': newEvent.search,
                        'mtm.oldHistoryState': tmpLast.state, 'mtm.newHistoryState': newEvent.state
                    });
                }
            }

            function replaceHistoryMethod(methodNameToReplace)
            {
                TagManager.utils.setMethodWrapIfNeeded(parameters.window.history, methodNameToReplace, function(state, title, urlParam) {
                    trigger(methodNameToReplace, getCurrentUrl(), state);
                });
            }

            replaceHistoryMethod('replaceState');
            replaceHistoryMethod('pushState');

            TagManager.dom.addEventListener(parameters.window, 'hashchange', function (event) {
                var newUrl = getEventUrl(event);
                trigger('hashchange', newUrl, null);
            }, false);
            TagManager.dom.addEventListener(parameters.window, 'popstate', function (event) {
                var newUrl = getEventUrl(event);
                trigger('popstate', newUrl, event.state);
            }, false);

        };
    };
})();