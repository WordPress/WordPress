(function () {
    return function (parameters, TagManager) {

        function isMatchingEvent(value) {
            var eventName = parameters.get('eventName');
            return eventName && TagManager.utils.isObject(value) && 'event' in value && value.event === eventName;
        }

        // we are catching all events that have been triggered before the container has been set up
        // ie events that are directly triggered before the tag manager has been loaded etc
        var missedEvents = [];
        var index = parameters.container.dataLayer.on(function (value) {
            if (isMatchingEvent(value)) {
                missedEvents.push(value.event);
            }
        });

        this.setUp = function (triggerEvent) {
            // no longer listen to previous events, we will now trigger events directly when they occur
            parameters.container.dataLayer.off(index);

            // replay missed events
            for (var i = 0; i < missedEvents.length; i++) {
                triggerEvent({event: 'mtm.CustomEvent', 'mtm.customEventMatch': missedEvents[i]});
            }

            parameters.container.dataLayer.on(function (value) {
                if (isMatchingEvent(value)) {
                    triggerEvent({event: 'mtm.CustomEvent', 'mtm.customEventMatch': value.event});
                }
            });

        };
    };
})();