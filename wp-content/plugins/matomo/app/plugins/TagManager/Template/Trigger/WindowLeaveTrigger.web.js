(function () {
    return function (parameters, TagManager) {
        var numTriggers = 0;

        this.setUp = function (triggerEvent) {

            TagManager.dom.onReady(function () {

                if (!parameters.document.documentElement) {
                    return;
                }

                var timerInCaseReturns;

                function cancelTimer()
                {
                    if (timerInCaseReturns) {
                        clearTimeout(timerInCaseReturns);
                        timerInCaseReturns = null;
                    }
                }

                TagManager.dom.addEventListener(parameters.document.documentElement, 'mouseleave', function (event) {
                    if ('undefined' === typeof event.clientY) {
                        return;
                    }
                    if (event.clientY > 3) {
                        return;
                    }

                    if (timerInCaseReturns) {
                        cancelTimer();
                        return;
                    }
                    var timerDelay = 50;

                    timerInCaseReturns = setTimeout(function () {
                        var limit = parameters.get('triggerLimit', 1);

                        if (limit) {
                            limit = parseInt(limit, 10);
                        }

                        if (limit && limit <= numTriggers) {
                            return;
                        }

                        numTriggers++;
                        triggerEvent({event: 'WindowLeave'});
                    }, timerDelay);
                });

                TagManager.dom.addEventListener(parameters.document.documentElement, 'mouseenter', cancelTimer);

            });
        };
    };
})();