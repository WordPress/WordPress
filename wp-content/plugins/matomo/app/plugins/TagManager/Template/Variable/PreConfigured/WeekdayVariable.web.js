(function () {
    return function (parameters, TagManager) {

        this.get = function () {
            var weekday = new Date().getDay();
            var weekdays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

            return weekdays[weekday];
        };
    };
})();