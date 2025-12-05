(function () {
    return function (parameters, TagManager) {
        var self = this;
        var numTriggers = 0;
        var defaultEvent = 'mtm.Timer';

        function getEventName() {
            var eventName = parameters.get('eventName', defaultEvent);
            if (!eventName) {
                eventName = defaultEvent;
            }
            return eventName;
        }

        function getTriggerInterval() {
            // we set a default value of every 3 seconds
            var triggerInterval = parameters.get('triggerInterval', 3000);
            if (!triggerInterval || triggerInterval < 50) {
                triggerInterval = 50; // interval should be at least 50ms
            }
            return triggerInterval;
        }

        this.setUp = function (triggerEvent) {
            setTimeout(function () {
                var limit = parameters.get('triggerLimit', 1);

                if (limit) {
                    limit = parseInt(limit, 10);
                }

                if (limit && limit <= numTriggers) {
                    return;
                }

                numTriggers++;
                triggerEvent({event: getEventName()});
                self.setUp(triggerEvent);
            }, getTriggerInterval());
        };
    };
})();