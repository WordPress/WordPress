(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {

            TagManager.dom.onReady(function () {

                function isClickNode(nodeName)
                {
                    return nodeName === 'A' || nodeName === 'AREA';
                }

                TagManager.dom.onClick(function (event, clickButton) {
                    clickCallback(event, triggerEvent, clickButton);
                });

                function clickCallback(event, triggerEvent, clickButton) {
                    if (!event.target) {
                        return;
                    }

                    var target = event.target;                    
                    if (target.shadowRoot) {
                        var composedPath = event.composedPath();
                        if (composedPath.length) {
                            target = composedPath[0];   //In shadow DOM select the first event path as the target
                        }
                    }
                    
                    var nodeName = target.nodeName;

                    while (!isClickNode(nodeName) && target && target.parentNode) {
                        target = target.parentNode;
                        nodeName = target.nodeName;
                    }

                    if (target && isClickNode(nodeName)) {
                        triggerEvent({
                            event: 'mtm.AllLinksClick',
                            'mtm.clickElement': target,
                            'mtm.clickElementId': TagManager.dom.getElementAttribute(target, 'id'),
                            'mtm.clickElementClasses': TagManager.dom.getElementClassNames(target),
                            'mtm.clickText': TagManager.dom.getElementText(target),
                            'mtm.clickNodeName': nodeName,
                            'mtm.clickElementUrl': TagManager.dom.getElementAttribute(target, 'href'),
                            'mtm.clickButton': clickButton
                        });
                    }
                }

            });
        };
    };
})();