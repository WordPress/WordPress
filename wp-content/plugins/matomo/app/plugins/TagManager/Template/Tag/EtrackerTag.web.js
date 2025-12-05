(function () {
    var hasBeenLoaded = false;
    return function (parameters, TagManager) {
        this.fire = function () {
        var etrackerConfig = parameters.get('etrackerConfig', {});
        var trackingType = parameters.get('trackingType');
        var etrackerID = etrackerConfig.etrackerID;
        var etrackerBlockCookies = etrackerConfig.etrackerBlockCookies;
        
        if (trackingType === 'pageview') {
            // Pageview parameters for overwrite
            for (var i in etrackerConfig) {
                if (TagManager.utils.hasProperty(etrackerConfig, i)) {
                    if(i.startsWith('et_')){
                        window[i] = etrackerConfig[i];
                    }
                }
            }
            // Custom Dimensions
            if (etrackerConfig.customDimensions
                && TagManager.utils.isArray(etrackerConfig.customDimensions)
                && etrackerConfig.customDimensions.length) {
                var dimIndex;
                for (dimIndex = 0; dimIndex < etrackerConfig.customDimensions.length; dimIndex++) {
                    var dimension = etrackerConfig.customDimensions[dimIndex];
                    if (dimension && TagManager.utils.isObject(dimension) && dimension.index && dimension.value) {
                        window[dimension.index] = dimension.value;
                    }
                }
            }

            if (!hasBeenLoaded && etrackerID) {
                hasBeenLoaded = true;
                // Pageview script
                var s = document.getElementsByTagName('script')[0];
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.setAttribute('data-secure-code', etrackerID);
                script.setAttribute('id', '_etLoader');
                script.setAttribute('charset', 'UTF-8');
                if (etrackerConfig.etrackerBlockCookies) {
                    script.setAttribute('data-block-cookies', 'true');
                }
                if (etrackerConfig.etrackerDNT) {
                    script.setAttribute('data-respect-dnt', 'true');
                }
                script.src = '//code.etracker.com/code/e.js';
                script.setAttribute('async', '');
                s.parentNode.insertBefore(script, s);
            }
        }
        //wrapper function    
        if (trackingType === 'wrapper' && typeof(_etracker) === "object") {
            var ewrapper = new Object();
            ewrapper.et_et = etrackerID;
            ewrapper.et_pagename = parameters.get('etrackerWrapperPagename');
            if(parameters.get('etrackerWrapperArea')){
               ewrapper.et_areas = parameters.get('etrackerWrapperArea');
            }
            if(parameters.get('etrackerWrapperTarget')){
               ewrapper.et_target = parameters.get('etrackerWrapperTarget');
            }
            if(parameters.get('etrackerWrapperTval')){
               ewrapper.et_tval = parameters.get('etrackerWrapperTval');
            }
            if(parameters.get('etrackerWrapperTonr')){
               ewrapper.et_tonr = parameters.get('etrackerWrapperTonr');
            }
            if(parameters.get('etrackerWrapperTsale')){
               ewrapper.et_tsale = parameters.get('etrackerWrapperTsale');
            }
            if(parameters.get('etrackerWrapperCust')){
               ewrapper.et_cust = parameters.get('etrackerWrapperCust');
            }
            if(parameters.get('etrackerWrapperBasket')){
               ewrapper.et_basket = parameters.get('etrackerWrapperBasket');
            }
            // Custom Dimensions
            if (etrackerConfig.customDimensions
                && TagManager.utils.isArray(etrackerConfig.customDimensions)
                && etrackerConfig.customDimensions.length) {
                var dimIndex;
                for (dimIndex = 0; dimIndex < etrackerConfig.customDimensions.length; dimIndex++) {
                    var dimension = etrackerConfig.customDimensions[dimIndex];
                    if (dimension && TagManager.utils.isObject(dimension) && dimension.index && dimension.value) {
                        ewrapper[dimension.index] = dimension.value;
                    }
                }
            }
            et_eC_Wrapper(ewrapper);
        }
        // event tracking function
        if (trackingType === 'event' && typeof(_etracker) === "object") {
            _etracker.sendEvent(new et_UserDefinedEvent(parameters.get('etrackerEventObject'), parameters.get('etrackerEventCategory'), parameters.get('etrackerEventAction'), parameters.get('etrackerEventType')));
        }
        // transaction tracking function
        if (trackingType === 'transaction') {
            if(parameters.get('etrackerTransactionDebugMode')){
                etCommerce.debugMode = true;
            }
            var etorder = {orderNumber:parameters.get('etrackerTransactionID'),status:parameters.get('etrackerTransactionType'),orderPrice:parameters.get('etrackerTransactionValue').toString(),basket:parameters.get('etrackerTransactionBasket'),currency:parameters.get('etrackerTransactionCurrency'),customerGroup:parameters.get('etrackerTransactionCustomerGroup'),deliveryConditions:parameters.get('etrackerTransactionDeliveryConditions'),paymentConditions:parameters.get('etrackerTransactionPaymentConditions')};
            etCommerce.sendEvent('order', etorder);
        }
        // ecommerce - add to cart tracking function
        if (trackingType === 'addtocart') {
            etCommerce.sendEvent('insertToBasket', parameters.get('etrackerAddToCartProduct'), Number(parameters.get('etrackerAddToCartNumber')));
        }
        // form - form tracking
        if (trackingType === 'form' && typeof(_etracker) === "object") {
            if(parameters.get('etrackerFormData')){
                etForm.sendEvent(parameters.get('etrackerFormType'), parameters.get('etrackerFormName'), parameters.get('etrackerFormData')) ;
            }
            else{ etForm.sendEvent(parameters.get('etrackerFormType'), parameters.get('etrackerFormName'));}
        }
        };
        };
    }
)();
