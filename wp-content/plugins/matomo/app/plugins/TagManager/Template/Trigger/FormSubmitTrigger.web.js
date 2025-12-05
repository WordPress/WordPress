(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {
            TagManager.dom.onReady(function () {
                TagManager.dom.addEventListener(parameters.document.body, "submit", function (event) {
                    if (!event.target) {
                        return;
                    }
                    var target = event.target;
                    if (target.nodeName === 'FORM') {
                        var dom = TagManager.dom;
                        var formAction = dom.getElementAttribute(target, 'action');
                        if (!formAction) {
                            formAction = parameters.window.location.href;
                        }

                        triggerEvent({
                            event: 'mtm.FormSubmit',
                            'mtm.formElement': target,
                            'mtm.formElementId': dom.getElementAttribute(target, 'id'),
                            'mtm.formElementName': dom.getElementAttribute(target, 'name'),
                            'mtm.formElementClasses': dom.getElementClassNames(target),
                            'mtm.formElementAction': formAction
                        });
                    }
                }, true);
            });
        };
    };
})();