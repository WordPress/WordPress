/*
 * easy-autocomplete
 * jQuery plugin for autocompletion
 * 
 * @author Łukasz Pawełczak (http://github.com/pawelczak)
 * @version 1.4.0
 * Copyright  License: 
 */

/*
 * EasyAutocomplete - Configuration 
 */

"use strict";

(function ($) {
    var EasyAutocomplete = (function (scope) {

        scope.Configuration = function Configuration(options) {
            var defaults = {
                data: 'list-required',
                url: 'list-required',
                dataType: 'json',

                listLocation: function (data) {
                    return data;
                },

                xmlElementName: '',

                getValue: function (element) {
                    return element;
                },

                autocompleteOff: true,

                placeholder: false,

                ajaxCallback: function () {
                },

                matchResponseProperty: false,

                list: {
                    sort: {
                        enabled: false,
                        method: function (a, b) {
                            a = defaults.getValue(a);
                            b = defaults.getValue(b);
                            if (a < b) {
                                return -1;
                            }
                            if (a > b) {
                                return 1;
                            }
                            return 0;
                        }
                    },

                    maxNumberOfElements: 6,

                    hideOnEmptyPhrase: true,

                    match: {
                        enabled: false,
                        caseSensitive: false,
                        method: function (element, phrase) {

                            return element.search(phrase) > -1;
                        }
                    },

                    showAnimation: {
                        type: 'normal', //normal|slide|fade
                        time: 400,
                        callback: function () {
                        }
                    },

                    hideAnimation: {
                        type: 'normal',
                        time: 400,
                        callback: function () {
                        }
                    },

                    /* Events */
                    onClickEvent: function () {
                    },
                    onSelectItemEvent: function () {
                    },
                    onLoadEvent: function () {
                    },
                    onChooseEvent: function () {
                    },
                    onKeyEnterEvent: function () {
                    },
                    onMouseOverEvent: function () {
                    },
                    onMouseOutEvent: function () {
                    },
                    onShowListEvent: function () {
                    },
                    onHideListEvent: function () {
                    }
                },

                highlightPhrase: true,

                theme: '',

                cssClasses: '',

                minCharNumber: 0,

                requestDelay: 0,

                adjustWidth: true,

                ajaxSettings: {},

                preparePostData: function (data, inputPhrase) {
                    return data;
                },

                loggerEnabled: true,

                template: '',

                categoriesAssigned: false,

                categories: [{
                        maxNumberOfElements: 4
                    }]

            };

            var externalObjects = ['ajaxSettings', 'template'];

            this.get = function (propertyName) {
                return defaults[propertyName];
            };

            this.equals = function (name, value) {
                if (isAssigned(name)) {
                    if (defaults[name] === value) {
                        return true;
                    }
                }

                return false;
            };

            this.checkDataUrlProperties = function () {
                return !(defaults.url === 'list-required' && defaults.data === 'list-required');

            };
            this.checkRequiredProperties = function () {
                for (var propertyName in defaults) {
                    if (defaults[propertyName] === 'required') {
                        logger.error('Option ' + propertyName + ' must be defined');
                        return false;
                    }
                }
                return true;
            };

            this.printPropertiesThatDoesntExist = function (consol, optionsToCheck) {
                printPropertiesThatDoesntExist(consol, optionsToCheck);
            };


            prepareDefaults();

            mergeOptions();

            if (defaults.loggerEnabled === true) {
                printPropertiesThatDoesntExist(console, options);
            }

            addAjaxSettings();

            processAfterMerge();
            function prepareDefaults() {

                if (options.dataType === 'xml') {

                    if (!options.getValue) {

                        options.getValue = function (element) {
                            return $(element).text();
                        };
                    }


                    if (!options.list) {

                        options.list = {};
                    }

                    if (!options.list.sort) {
                        options.list.sort = {};
                    }


                    options.list.sort.method = function (a, b) {
                        a = options.getValue(a);
                        b = options.getValue(b);
                        if (a < b) {
                            return -1;
                        }
                        if (a > b) {
                            return 1;
                        }
                        return 0;
                    };

                    if (!options.list.match) {
                        options.list.match = {};
                    }

                    options.list.match.method = function (element, phrase) {

                        return element.search(phrase) > -1;
                    };

                }
                if (options.categories !== undefined && options.categories instanceof Array) {

                    var categories = [];

                    for (var i = 0, length = options.categories.length; i < length; i += 1) {

                        var category = options.categories[i];

                        for (var property in defaults.categories[0]) {

                            if (category[property] === undefined) {
                                category[property] = defaults.categories[0][property];
                            }
                        }

                        categories.push(category);
                    }

                    options.categories = categories;
                }
            }

            function mergeOptions() {

                defaults = mergeObjects(defaults, options);

                function mergeObjects(source, target) {
                    var mergedObject = source || {};

                    for (var propertyName in source) {
                        if (target[propertyName] !== undefined && target[propertyName] !== null) {

                            if (typeof target[propertyName] !== 'object' ||
                                    target[propertyName] instanceof Array) {
                                mergedObject[propertyName] = target[propertyName];
                            } else {
                                mergeObjects(source[propertyName], target[propertyName]);
                            }
                        }
                    }

                    /* If data is an object */
                    if (target.data !== undefined && target.data !== null && typeof target.data === 'object') {
                        mergedObject.data = target.data;
                    }

                    return mergedObject;
                }
            }

            function processAfterMerge() {

                if (defaults.url !== 'list-required' && typeof defaults.url !== 'function') {
                    var defaultUrl = defaults.url;
                    defaults.url = function () {
                        return defaultUrl;
                    };
                }

                if (defaults.ajaxSettings.url !== undefined && typeof defaults.ajaxSettings.url !== 'function') {
                    var defaultUrl = defaults.ajaxSettings.url;
                    defaults.ajaxSettings.url = function () {
                        return defaultUrl;
                    };
                }

                if (typeof defaults.listLocation === 'string') {
                    var defaultlistLocation = defaults.listLocation;

                    if (defaults.dataType.toUpperCase() === 'XML') {
                        defaults.listLocation = function (data) {
                            return $(data).find(defaultlistLocation);
                        };
                    } else {
                        defaults.listLocation = function (data) {
                            return data[defaultlistLocation];
                        };
                    }
                }

                if (typeof defaults.getValue === 'string') {
                    var defaultsGetValue = defaults.getValue;
                    defaults.getValue = function (element) {
                        return element[defaultsGetValue];
                    };
                }

                if (options.categories !== undefined) {
                    defaults.categoriesAssigned = true;
                }

            }

            function addAjaxSettings() {

                if (options.ajaxSettings !== undefined && typeof options.ajaxSettings === 'object') {
                    defaults.ajaxSettings = options.ajaxSettings;
                } else {
                    defaults.ajaxSettings = {};
                }

            }

            function isAssigned(name) {
                return defaults[name] !== undefined && defaults[name] !== null;
            }
            function printPropertiesThatDoesntExist(consol, optionsToCheck) {

                checkPropertiesIfExist(defaults, optionsToCheck);

                function checkPropertiesIfExist(source, target) {
                    for (var property in target) {
                        if (source[property] === undefined) {
                            consol.log('Property \'' + property + '\' does not exist in EasyAutocomplete options API.');
                        }

                        if (typeof source[property] === 'object' && $.inArray(property, externalObjects) === -1) {
                            checkPropertiesIfExist(source[property], target[property]);
                        }
                    }
                }
            }
        };

        return scope;

    })(EasyAutocomplete || {});

    /*
     * EasyAutocomplete - Logger 
     */
    var EasyAutocomplete = (function (scope) {

        scope.Logger = function Logger() {

            this.error = function (message) {
                console.log('ERROR: ' + message);
            };

            this.warning = function (message) {
                console.log('WARNING: ' + message);
            };
        };

        return scope;

    })(EasyAutocomplete || {});


    /*
     * EasyAutocomplete - Constants
     */
    var EasyAutocomplete = (function (scope) {

        scope.Constants = function Constants() {

            var constants = {
                CONTAINER_CLASS: 'easy-autocomplete-container',
                CONTAINER_ID: 'eac-container-',
                WRAPPER_CSS_CLASS: 'easy-autocomplete'
            };

            this.getValue = function (propertyName) {
                return constants[propertyName];
            };

        };

        return scope;

    })(EasyAutocomplete || {});

    /*
     * EasyAutocomplete - ListBuilderService 
     *
     * @author Łukasz Pawełczak 
     *
     */
    var EasyAutocomplete = (function (scope) {

        scope.ListBuilderService = function ListBuilderService(configuration, proccessResponseData) {


            this.init = function (data) {
                var listBuilder = [],
                        builder = {};

                builder.data = configuration.get('listLocation')(data);
                builder.getValue = configuration.get('getValue');
                builder.maxListSize = configuration.get('list').maxNumberOfElements;


                listBuilder.push(builder);

                return listBuilder;
            };

            this.updateCategories = function (listBuilder, data) {

                if (configuration.get('categoriesAssigned')) {

                    listBuilder = [];

                    for (var i = 0; i < configuration.get("categories").length; i += 1) {

                        var builder = convertToListBuilder(configuration.get('categories')[i], data);

                        listBuilder.push(builder);
                    }

                }

                return listBuilder;
            };

            this.convertXml = function (listBuilder) {
                if (configuration.get('dataType').toUpperCase() === 'XML') {

                    for (var i = 0; i < listBuilder.length; i += 1) {
                        listBuilder[i].data = convertXmlToList(listBuilder[i]);
                    }
                }

                return listBuilder;
            };

            this.processData = function (listBuilder, inputPhrase) {

                for (var i = 0, length = listBuilder.length; i < length; i += 1) {
                    listBuilder[i].data = proccessResponseData(configuration, listBuilder[i], inputPhrase);
                }

                return listBuilder;
            };

            this.checkIfDataExists = function (listBuilders) {

                for (var i = 0, length = listBuilders.length; i < length; i += 1) {

                    if (listBuilders[i].data !== undefined && listBuilders[i].data instanceof Array) {
                        if (listBuilders[i].data.length > 0) {
                            return true;
                        }
                    }
                }

                return false;
            };


            function convertToListBuilder(category, data) {

                var builder = {};

                if (configuration.get('dataType').toUpperCase() === 'XML') {

                    builder = convertXmlToListBuilder();
                } else {

                    builder = convertDataToListBuilder();
                }


                if (category.header !== undefined) {
                    builder.header = category.header;
                }

                if (category.maxNumberOfElements !== undefined) {
                    builder.maxNumberOfElements = category.maxNumberOfElements;
                }

                if (configuration.get('list').maxNumberOfElements !== undefined) {

                    builder.maxListSize = configuration.get('list').maxNumberOfElements;
                }

                if (category.getValue !== undefined) {

                    if (typeof category.getValue === 'string') {
                        var defaultsGetValue = category.getValue;
                        builder.getValue = function (element) {
                            return element[defaultsGetValue];
                        };
                    } else if (typeof category.getValue === 'function') {
                        builder.getValue = category.getValue;
                    }

                } else {
                    builder.getValue = configuration.get('getValue');
                }


                return builder;


                function convertXmlToListBuilder() {

                    var builder = {},
                            listLocation;

                    if (category.xmlElementName !== undefined) {
                        builder.xmlElementName = category.xmlElementName;
                    }

                    if (category.listLocation !== undefined) {

                        listLocation = category.listLocation;
                    } else if (configuration.get('listLocation') !== undefined) {

                        listLocation = configuration.get('listLocation');
                    }

                    if (listLocation !== undefined) {
                        if (typeof listLocation === 'string') {
                            builder.data = $(data).find(listLocation);
                        } else if (typeof listLocation === 'function') {

                            builder.data = listLocation(data);
                        }
                    } else {

                        builder.data = data;
                    }

                    return builder;
                }


                function convertDataToListBuilder() {

                    var builder = {};

                    if (category.listLocation !== undefined) {

                        if (typeof category.listLocation === 'string') {
                            builder.data = data[category.listLocation];
                        } else if (typeof category.listLocation === 'function') {
                            builder.data = category.listLocation(data);
                        }
                    } else {
                        builder.data = data;
                    }

                    return builder;
                }
            }

            function convertXmlToList(builder) {
                var simpleList = [];

                if (builder.xmlElementName === undefined) {
                    builder.xmlElementName = configuration.get('xmlElementName');
                }


                $(builder.data).find(builder.xmlElementName).each(function () {
                    simpleList.push(this);
                });

                return simpleList;
            }

        };

        return scope;

    })(EasyAutocomplete || {});


    /*
     * EasyAutocomplete - Data proccess module
     *
     * Process list to display:
     * - sort 
     * - decrease number to specific number
     * - show only matching list
     *
     */
    var EasyAutocomplete = (function (scope) {

        scope.proccess = function DataProcessor(config, listBuilder, phrase) {

            scope.proccess.match = match;

            var list = listBuilder.data,
                    inputPhrase = phrase; // TODO REFACTOR

            list = findMatch(list, inputPhrase);
            list = reduceElementsInList(list);
            list = sort(list);

            return list;

            function findMatch(list, phrase) {
                var preparedList = [],
                        value = '';

                if (config.get('list').match.enabled) {

                    for (var i = 0, length = list.length; i < length; i += 1) {

                        value = config.get('getValue')(list[i]);

                        if (match(value, phrase)) {
                            preparedList.push(list[i]);
                        }

                    }

                } else {
                    preparedList = list;
                }

                return preparedList;
            }

            function match(value, phrase) {

                if (!config.get('list').match.caseSensitive) {

                    if (typeof value === 'string') {
                        value = value.toLowerCase();
                    }

                    phrase = phrase.toLowerCase();
                }

                return (config.get('list').match.method(value, phrase));
            }

            function reduceElementsInList(list) {
                if (listBuilder.maxNumberOfElements !== undefined && list.length > listBuilder.maxNumberOfElements) {
                    list = list.slice(0, listBuilder.maxNumberOfElements);
                }

                return list;
            }

            function sort(list) {
                if (config.get('list').sort.enabled) {
                    list.sort(config.get('list').sort.method);
                }

                return list;
            }

        };


        return scope;


    })(EasyAutocomplete || {});


    /*
     * EasyAutocomplete - Template 
     *
     * 
     *
     */
    var EasyAutocomplete = (function (scope) {

        scope.Template = function Template(options) {

            var genericTemplates = {
                basic: {
                    type: 'basic',
                    method: function (element) {
                        return element;
                    },
                    cssClass: ''
                },
                description: {
                    type: 'description',
                    fields: {
                        description: 'description'
                    },
                    method: function (element) {
                        return element + ' - description';
                    },
                    cssClass: 'eac-description'
                },
                iconLeft: {
                    type: 'iconLeft',
                    fields: {
                        icon: ''
                    },
                    method: function (element) {
                        return element;
                    },
                    cssClass: 'eac-icon-left'
                },
                iconRight: {
                    type: 'iconRight',
                    fields: {
                        iconSrc: ''
                    },
                    method: function (element) {
                        return element;
                    },
                    cssClass: 'eac-icon-right'
                },
                links: {
                    type: 'links',
                    fields: {
                        link: ''
                    },
                    method: function (element) {
                        return element;
                    },
                    cssClass: ''
                },
                custom: {
                    type: 'custom',
                    method: function () {
                    },
                    cssClass: ''
                }
            },
                    /*
                     * Converts method with {{text}} to function
                     */
                    convertTemplateToMethod = function (template) {


                        var _fields = template.fields,
                                buildMethod;

                        if (template.type === 'description') {

                            buildMethod = genericTemplates.description.method;

                            if (typeof _fields.description === 'string') {
                                buildMethod = function (elementValue, element) {
                                    return elementValue + ' - <span>' + element[_fields.description] + '</span>';
                                };
                            } else if (typeof _fields.description === 'function') {
                                buildMethod = function (elementValue, element) {
                                    return elementValue + ' - <span>' + _fields.description(element) + '</span>';
                                };
                            }

                            return buildMethod;
                        }

                        if (template.type === 'iconRight') {

                            if (typeof _fields.iconSrc === 'string') {
                                buildMethod = function (elementValue, element) {
                                    return elementValue + '<img class=\'eac-icon\' src=\'' + element[_fields.iconSrc] + '\' />';
                                };
                            } else if (typeof _fields.iconSrc === 'function') {
                                buildMethod = function (elementValue, element) {
                                    return elementValue + '<img class=\'eac-icon\' src=\'' + _fields.iconSrc(element) + '\' />';
                                };
                            }

                            return buildMethod;
                        }


                        if (template.type === 'iconLeft') {

                            if (typeof _fields.iconSrc === 'string') {
                                buildMethod = function (elementValue, element) {
                                    return '<img class=\'eac-icon\' src=\'' + element[_fields.iconSrc] + '\' />' + elementValue;
                                };
                            } else if (typeof _fields.iconSrc === 'function') {
                                buildMethod = function (elementValue, element) {
                                    return '<img class=\'eac-icon\' src=\'' + _fields.iconSrc(element) + '\' />' + elementValue;
                                };
                            }

                            return buildMethod;
                        }

                        if (template.type === 'links') {

                            if (typeof _fields.link === 'string') {
                                buildMethod = function (elementValue, element) {
                                    return '<a href=\'' + element[_fields.link] + '\' >' + elementValue + '</a>';
                                };
                            } else if (typeof _fields.link === 'function') {
                                buildMethod = function (elementValue, element) {
                                    return '<a href=\'' + _fields.link(element) + '\' >' + elementValue + '</a>';
                                };
                            }

                            return buildMethod;
                        }


                        if (template.type === 'custom') {

                            return template.method;
                        }

                        return genericTemplates.basic.method;

                    },
                    prepareBuildMethod = function (options) {
                        if (!options || !options.type) {

                            return genericTemplates.basic.method;
                        }

                        if (options.type && genericTemplates[options.type]) {

                            return convertTemplateToMethod(options);
                        } else {

                            return genericTemplates.basic.method;
                        }

                    },
                    templateClass = function (options) {
                        var emptyStringFunction = function () {
                            return '';
                        };

                        if (!options || !options.type) {

                            return emptyStringFunction;
                        }

                        if (options.type && genericTemplates[options.type]) {
                            return (function () {
                                var _cssClass = genericTemplates[options.type].cssClass;
                                return function () {
                                    return _cssClass;
                                };
                            })();
                        } else {
                            return emptyStringFunction;
                        }
                    };

            this.getTemplateClass = templateClass(options);

            this.build = prepareBuildMethod(options);

        };

        return scope;

    })(EasyAutocomplete || {});

    /*
     * EasyAutocomplete - jQuery plugin for autocompletion
     *
     */
    var EasyAutocomplete = (function (scope) {

        scope.main = function Core($input, options) {

            var module = {
                name: 'EasyAutocomplete',
                shortcut: 'eac'
            };

            var consts = new scope.Constants(),
                    config = new scope.Configuration(options),
                    logger = new scope.Logger(),
                    template = new scope.Template(options.template),
                    listBuilderService = new scope.ListBuilderService(config, scope.proccess),
                    checkParam = config.equals,
                    $field = $input,
                    $container = '',
                    elementsList = [],
                    selectedElement = -1,
                    requestDelayTimeoutId;

            scope.consts = consts;

            this.getConstants = function () {
                return consts;
            };

            this.getConfiguration = function () {
                return config;
            };

            this.getContainer = function () {
                return $container;
            };

            this.getSelectedItemIndex = function () {
                return selectedElement;
            };

            this.getItems = function () {
                return elementsList;
            };

            this.getItemData = function (index) {

                if (elementsList.length < index || elementsList[index] === undefined) {
                    return -1;
                } else {
                    return elementsList[index];
                }
            };

            this.getSelectedItemData = function () {
                return this.getItemData(selectedElement);
            };

            this.build = function () {
                prepareField();
            };

            this.init = function () {
                init();
            };
            function init() {

                if ($field.length === 0) {
                    logger.error('Input field doesn\'t exist.');
                    return;
                }

                if (!config.checkDataUrlProperties()) {
                    logger.error('One of options variables \'data\' or \'url\' must be defined.');
                    return;
                }

                if (!config.checkRequiredProperties()) {
                    logger.error('Will not work without mentioned properties.');
                    return;
                }


                prepareField();
                bindEvents();

            }
            function prepareField() {


                if ($field.parent().hasClass(consts.getValue('WRAPPER_CSS_CLASS'))) {
                    removeContainer();
                    removeWrapper();
                }

                createWrapper();
                createContainer();

                $container = $('#' + getContainerId());
                if (config.get('placeholder')) {
                    $field.attr('placeholder', config.get('placeholder'));
                }


                function createWrapper() {
                    var $wrapper = $('<div>'),
                            classes = consts.getValue('WRAPPER_CSS_CLASS');


                    if (config.get('theme') && config.get('theme') !== '') {
                        classes += ' eac-' + config.get('theme');
                    }

                    if (config.get('cssClasses') && config.get('cssClasses') !== '') {
                        classes += ' ' + config.get('cssClasses');
                    }

                    if (template.getTemplateClass() !== '') {
                        classes += ' ' + template.getTemplateClass();
                    }


                    $wrapper
                            .addClass(classes);
                    $field.wrap($wrapper);


                    if (config.get('adjustWidth') === true) {
                        adjustWrapperWidth();
                    }


                }

                function adjustWrapperWidth() {
                    var fieldWidth = $field.outerWidth();

                    $field.parent().css('width', fieldWidth);
                }

                function removeWrapper() {
                    $field.unwrap();
                }

                function createContainer() {
                    var $elements_container = $('<div>').addClass(consts.getValue('CONTAINER_CLASS'));

                    $elements_container
                            .attr('id', getContainerId())
                            .prepend($('<ul>'));


                    (function () {

                        $elements_container
                                /* List show animation */
                                .on('show.eac', function () {
                                    if (!$field.is(':focus')) {
                                        return
                                    }

                                    switch (config.get('list').showAnimation.type) {

                                        case 'slide':
                                            var animationTime = config.get('list').showAnimation.time,
                                                    callback = config.get('list').showAnimation.callback;

                                            $elements_container.find('ul').slideDown(animationTime, callback);
                                            break;

                                        case 'fade':
                                            var animationTime = config.get('list').showAnimation.time,
                                                    callback = config.get('list').showAnimation.callback;

                                            $elements_container.find('ul').fadeIn(animationTime), callback;
                                            break;

                                        default:
                                            $elements_container.find('ul').show();
                                            break;
                                    }

                                    config.get('list').onShowListEvent();

                                })
                                /* List hide animation */
                                .on('hide.eac', function () {

                                    switch (config.get('list').hideAnimation.type) {

                                        case 'slide':
                                            var animationTime = config.get('list').hideAnimation.time,
                                                    callback = config.get('list').hideAnimation.callback;

                                            $elements_container.find('ul').slideUp(animationTime, callback);
                                            break;

                                        case 'fade':
                                            var animationTime = config.get('list').hideAnimation.time,
                                                    callback = config.get('list').hideAnimation.callback;

                                            $elements_container.find('ul').fadeOut(animationTime, callback);
                                            break;

                                        default:
                                            $elements_container.find('ul').hide();
                                            break;
                                    }

                                    config.get('list').onHideListEvent();

                                })
                                .on('selectElement.eac', function () {
                                    $elements_container.find('ul li').removeClass('selected');
                                    $elements_container.find('ul li').eq(selectedElement).addClass('selected');

                                    config.get('list').onSelectItemEvent();
                                })
                                .on('loadElements.eac', function (event, listBuilders, phrase) {


                                    var $item = '',
                                            $listContainer = $elements_container.find('ul');

                                    $listContainer
                                            .empty()
                                            .detach();

                                    elementsList = [];
                                    var counter = 0;
                                    for (var builderIndex = 0, listBuildersLength = listBuilders.length; builderIndex < listBuildersLength; builderIndex += 1) {

                                        var listData = listBuilders[builderIndex].data;

                                        if (listData.length === 0) {
                                            continue;
                                        }

                                        if (listBuilders[builderIndex].header !== undefined && listBuilders[builderIndex].header.length > 0) {
                                            $listContainer.append('<div class=\'eac-category\' >' + listBuilders[builderIndex].header + '</div>');
                                        }

                                        for (var i = 0, listDataLength = listData.length; i < listDataLength && counter < listBuilders[builderIndex].maxListSize; i += 1) {
                                            $item = $('<li><div class=\'eac-item\'></div></li>');


                                            (function () {
                                                var j = i,
                                                        itemCounter = counter,
                                                        elementsValue = listBuilders[builderIndex].getValue(listData[j]);

                                                $item.find(' > div')
                                                        .on('click', function () {

                                                            $field.val(elementsValue).trigger('change');

                                                            selectedElement = itemCounter;
                                                            selectElement(itemCounter);

                                                            config.get('list').onClickEvent();
                                                            config.get('list').onChooseEvent();
                                                        })
                                                        .mouseover(function () {

                                                            selectedElement = itemCounter;
                                                            selectElement(itemCounter);

                                                            config.get('list').onMouseOverEvent();
                                                        })
                                                        .mouseout(function () {
                                                            config.get('list').onMouseOutEvent();
                                                        })
                                                        .html(template.build(highlight(elementsValue, phrase), listData[j]));
                                            })();

                                            $listContainer.append($item);
                                            elementsList.push(listData[i]);
                                            counter += 1;
                                        }
                                    }

                                    $elements_container.append($listContainer);

                                    config.get('list').onLoadEvent();
                                });

                    })();

                    $field.after($elements_container);
                }

                function removeContainer() {
                    $field.next('.' + consts.getValue('CONTAINER_CLASS')).remove();
                }

                function highlight(string, phrase) {

                    if (config.get('highlightPhrase') && phrase !== '') {
                        return highlightPhrase(string, phrase);
                    } else {
                        return string;
                    }

                }

                function escapeRegExp(str) {
                    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
                }

                function highlightPhrase(string, phrase) {
                    var escapedPhrase = escapeRegExp(phrase);
                    return (string + '').replace(new RegExp('(' + escapedPhrase + ')', 'gi'), '<b>$1</b>');
                }


            }
            function getContainerId() {

                var elementId = $field.attr('id');

                elementId = consts.getValue('CONTAINER_ID') + elementId;

                return elementId;
            }
            function bindEvents() {

                bindAllEvents();


                function bindAllEvents() {
                    if (checkParam('autocompleteOff', true)) {
                        removeAutocomplete();
                    }

                    bindFocusOut();
                    bindKeyup();
                    bindKeydown();
                    bindKeypress();
                    bindFocus();
                    bindBlur();
                }

                function bindFocusOut() {
                    $field.focusout(function () {

                        var fieldValue = $field.val(),
                                phrase;

                        if (!config.get('list').match.caseSensitive) {
                            fieldValue = fieldValue.toLowerCase();
                        }

                        for (var i = 0, length = elementsList.length; i < length; i += 1) {

                            phrase = config.get('getValue')(elementsList[i]);
                            if (!config.get('list').match.caseSensitive) {
                                phrase = phrase.toLowerCase();
                            }

                            if (phrase === fieldValue) {
                                selectedElement = i;
                                selectElement(selectedElement);
                                return;
                            }
                        }
                    });
                }

                function bindKeyup() {
                    $field
                            .off('keyup')
                            .keyup(function (event) {

                                switch (event.keyCode) {

                                    case 27:

                                        hideContainer();
                                        loseFieldFocus();
                                        break;

                                    case 38:

                                        event.preventDefault();

                                        if (elementsList.length > 0 && selectedElement > 0) {

                                            selectedElement -= 1;

                                            $field.val(config.get('getValue')(elementsList[selectedElement]));

                                            selectElement(selectedElement);

                                        }
                                        break;

                                    case 40:

                                        event.preventDefault();

                                        if (elementsList.length > 0 && selectedElement < elementsList.length - 1) {

                                            selectedElement += 1;

                                            $field.val(config.get('getValue')(elementsList[selectedElement]));

                                            selectElement(selectedElement);

                                        }

                                        break;

                                    default:


                                        if (event.keyCode > 40 || event.keyCode === 8 || event.keyCode === 0) {

                                            var inputPhrase = $field.val();

                                            if (!(config.get('list').hideOnEmptyPhrase === true && event.keyCode === 8 && inputPhrase === '')) {

                                                if (config.get('requestDelay') > 0) {
                                                    if (requestDelayTimeoutId !== undefined) {
                                                        clearTimeout(requestDelayTimeoutId);
                                                    }

                                                    requestDelayTimeoutId = setTimeout(function () {
                                                        loadData(inputPhrase);
                                                    }, config.get('requestDelay'));
                                                } else {
                                                    loadData(inputPhrase);
                                                }

                                            } else {
                                                hideContainer();
                                            }

                                        }


                                        break;
                                }


                                function loadData(inputPhrase) {


                                    if (inputPhrase.length < config.get('minCharNumber')) {
                                        return;
                                    }


                                    if (config.get('data') !== 'list-required') {

                                        var data = config.get('data');

                                        var listBuilders = listBuilderService.init(data);

                                        listBuilders = listBuilderService.updateCategories(listBuilders, data);

                                        listBuilders = listBuilderService.processData(listBuilders, inputPhrase);

                                        loadElements(listBuilders, inputPhrase);

                                        if ($field.parent().find('li').length > 0) {
                                            showContainer();
                                        } else {
                                            hideContainer();
                                        }

                                    }

                                    var settings = createAjaxSettings();

                                    if (settings.url === undefined || settings.url === '') {
                                        settings.url = config.get('url');
                                    }

                                    if (settings.dataType === undefined || settings.dataType === '') {
                                        settings.dataType = config.get('dataType');
                                    }


                                    if (settings.url !== undefined && settings.url !== 'list-required') {

                                        settings.url = settings.url(inputPhrase);

                                        settings.data = config.get('preparePostData')(settings.data, inputPhrase);

                                        $.ajax(settings)
                                                .done(function (data) {

                                                    var listBuilders = listBuilderService.init(data);

                                                    listBuilders = listBuilderService.updateCategories(listBuilders, data);

                                                    listBuilders = listBuilderService.convertXml(listBuilders);
                                                    if (checkInputPhraseMatchResponse(inputPhrase, data)) {

                                                        listBuilders = listBuilderService.processData(listBuilders, inputPhrase);

                                                        loadElements(listBuilders, inputPhrase);

                                                    }

                                                    if (listBuilderService.checkIfDataExists(listBuilders) && $field.parent().find('li').length > 0) {
                                                        showContainer();
                                                    } else {
                                                        hideContainer();
                                                    }

                                                    config.get('ajaxCallback')();

                                                })
                                                .fail(function () {
                                                    logger.warning('Fail to load response data');
                                                })
                                                .always(function () {

                                                });
                                    }


                                    function createAjaxSettings() {

                                        var settings = {},
                                                ajaxSettings = config.get('ajaxSettings') || {};

                                        for (var set in ajaxSettings) {
                                            settings[set] = ajaxSettings[set];
                                        }

                                        return settings;
                                    }

                                    function checkInputPhraseMatchResponse(inputPhrase, data) {

                                        if (config.get('matchResponseProperty') !== false) {
                                            if (typeof config.get('matchResponseProperty') === 'string') {
                                                return (data[config.get('matchResponseProperty')] === inputPhrase);
                                            }

                                            if (typeof config.get('matchResponseProperty') === 'function') {
                                                return (config.get('matchResponseProperty')(data) === inputPhrase);
                                            }

                                            return true;
                                        } else {
                                            return true;
                                        }

                                    }

                                }


                            });
                }

                function bindKeydown() {
                    $field
                            .on('keydown', function (evt) {
                                evt = evt || window.event;
                                var keyCode = evt.keyCode;
                                if (keyCode === 38) {
                                    suppressKeypress = true;
                                    return false;
                                }
                            })
                            .keydown(function (event) {

                                if (event.keyCode === 13 && selectedElement > -1) {

                                    $field.val(config.get('getValue')(elementsList[selectedElement]));

                                    config.get('list').onKeyEnterEvent();
                                    config.get('list').onChooseEvent();

                                    selectedElement = -1;
                                    hideContainer();

                                    event.preventDefault();
                                }
                            });
                }

                function bindKeypress() {
                    $field
                            .off('keypress');
                }

                function bindFocus() {
                    $field.focus(function () {

                        if ($field.val() !== '' && elementsList.length > 0) {

                            selectedElement = -1;
                            showContainer();
                        }

                    });
                }

                function bindBlur() {
                    $field.blur(function () {
                        setTimeout(function () {

                            selectedElement = -1;
                            hideContainer();
                        }, 250);
                    });
                }

                function removeAutocomplete() {
                    $field.attr('autocomplete', 'off');
                }

            }

            function showContainer() {
                $container.trigger('show.eac');
            }

            function hideContainer() {
                $container.trigger('hide.eac');
            }

            function selectElement(index) {

                $container.trigger('selectElement.eac', index);
            }

            function loadElements(list, phrase) {
                $container.trigger('loadElements.eac', [list, phrase]);
            }

            function loseFieldFocus() {
                $field.trigger('blur');
            }


        };
        scope.eacHandles = [];

        scope.getHandle = function (id) {
            return scope.eacHandles[id];
        };

        scope.inputHasId = function (input) {

            if ($(input).attr('id') !== undefined && $(input).attr('id').length > 0) {
                return true;
            } else {
                return false;
            }

        };

        scope.assignRandomId = function (input) {

            var fieldId = '';

            do {
                fieldId = 'eac-' + Math.floor(Math.random() * 10000);
            } while ($('#' + fieldId).length !== 0);

            elementId = scope.consts.getValue('CONTAINER_ID') + fieldId;

            $(input).attr('id', fieldId);

        };

        scope.setHandle = function (handle, id) {
            scope.eacHandles[id] = handle;
        };


        return scope;

    })(EasyAutocomplete || {});



    $.fn.easyAutocomplete = function (options) {

        return this.each(function () {
            var $this = $(this),
                    eacHandle = new EasyAutocomplete.main($this, options);

            if (!EasyAutocomplete.inputHasId($this)) {
                EasyAutocomplete.assignRandomId($this);
            }

            eacHandle.init();

            EasyAutocomplete.setHandle(eacHandle, $this.attr('id'));

        });
    };

    $.fn.getSelectedItemIndex = function () {

        var inputId = $(this).attr('id');

        if (inputId !== undefined) {
            return EasyAutocomplete.getHandle(inputId).getSelectedItemIndex();
        }

        return -1;
    };

    $.fn.getItems = function () {

        var inputId = $(this).attr('id');

        if (inputId !== undefined) {
            return EasyAutocomplete.getHandle(inputId).getItems();
        }

        return -1;
    };

    $.fn.getItemData = function (index) {

        var inputId = $(this).attr('id');

        if (inputId !== undefined && index > -1) {
            return EasyAutocomplete.getHandle(inputId).getItemData(index);
        }

        return -1;
    };

    $.fn.getSelectedItemData = function () {

        var inputId = $(this).attr('id');

        if (inputId !== undefined) {
            return EasyAutocomplete.getHandle(inputId).getSelectedItemData();
        }

        return -1;
    };

})(jQuery);
