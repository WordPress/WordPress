(function () {
    return function (parameters, TagManager) {

        var self = this;
        var numTriggers = 0;

        this.setUp = function (triggerEvent) {
            var scrollType = parameters.get('scrollType');
            var pixels = parameters.get('pixels', 1000);
            var percentage = parameters.get('percentage', 50);

            if (!scrollType) {
                return;
            }
            this.scrollIndex = TagManager.window.onScroll(function (event) {
                var hasReachedScrollPosition = false;
                var dom = TagManager.dom;

                var lastScrollTop = parseInt(dom.getScrollTop(), 10) + TagManager.window.getViewportHeight();
                var lastScrollLeft = parseInt(dom.getScrollLeft(), 10) + TagManager.window.getViewportWidth();
                var docHeight = dom.getDocumentHeight();
                var docWidth = dom.getDocumentWidth();
                var scrollPercentageVertical = (lastScrollTop / docHeight) * 100;
                var scrollPercentageHorizontal = (lastScrollLeft / docWidth) * 100;
                var eventType = event && event.type ? event.type : '';

                switch (scrollType) {
                    case 'verticalpixel':
                        if (lastScrollTop > pixels) {
                            hasReachedScrollPosition = true;
                        }
                        break;
                    case 'horizontalpixel':
                        if (lastScrollLeft > pixels) {
                            hasReachedScrollPosition = true;
                        }
                        break;
                    case 'verticalpercentage':
                        if (scrollPercentageVertical >= percentage) {
                            hasReachedScrollPosition = true;
                        }
                        break;
                    case 'horizontalpercentage':
                        if (scrollPercentageHorizontal >= percentage) {
                            hasReachedScrollPosition = true;
                        }
                        break;
                }

                if (hasReachedScrollPosition) {
                    if (!numTriggers) {
                        // ensure it won't be executed twice even if there is some race condition

                        numTriggers++;
                        triggerEvent({
                            event: 'mtm.ScrollReach',
                            'mtm.scrollSource': eventType,
                            'mtm.scrollLeftPx': lastScrollLeft,
                            'mtm.scrollTopPx': lastScrollTop,
                            'mtm.scrollVerticalPercentage': Math.round(scrollPercentageVertical * 100) / 100,
                            'mtm.scrollHorizontalPercentage': Math.round(scrollPercentageHorizontal * 100) / 100,
                            'mtm.scrollDocumentHeightPx': docHeight,
                            'mtm.scrollDocumentWidthPx': docWidth,
                        });
                    }

                    if (self.scrollIndex !== null) {
                        TagManager.window.offScroll(self.scrollIndex);
                        self.scrollIndex = null;
                    }
                }
            });
        };
    };
})();