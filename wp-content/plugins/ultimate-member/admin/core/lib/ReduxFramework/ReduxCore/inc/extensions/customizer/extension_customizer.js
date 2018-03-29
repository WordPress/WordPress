/* global redux, setting */

/*!
 SerializeJSON jQuery plugin.
 https://github.com/marioizquierdo/jquery.serializeJSON
 version 2.6.0 (Apr, 2015)

 Copyright (c) 2012, 2015 Mario Izquierdo
 Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 */
(function( $ ) {
    "use strict";

    // jQuery('form').serializeJSON()
    $.fn.serializeJSON = function( options ) {
        var serializedObject, formAsArray, keys, type, value, _ref, f, opts;
        f = $.serializeJSON;
        opts = f.setupOpts( options ); // calculate values for options {parseNumbers, parseBoolens, parseNulls}
        formAsArray = this.serializeArray(); // array of objects {name, value}
        f.readCheckboxUncheckedValues( formAsArray, this, opts ); // add {name, value} of unchecked checkboxes if needed

        serializedObject = {};
        $.each(
            formAsArray, function( i, input ) {
                keys = f.splitInputNameIntoKeysArray( input.name, opts );
                type = keys.pop(); // the last element is always the type ("string" by default)
                if ( type !== 'skip' ) { // easy way to skip a value
                    value = f.parseValue( input.value, type, opts ); // string, number, boolean or null
                    if ( opts.parseWithFunction && type === '_' ) value = opts.parseWithFunction( value, input.name ); // allow for custom parsing
                    f.deepSet( serializedObject, keys, value, opts );
                }
            }
        );
        return serializedObject;
    };

    // Use $.serializeJSON as namespace for the auxiliar functions
    // and to define defaults
    $.serializeJSON = {

        defaultOptions: {
            checkboxUncheckedValue: undefined, // to include that value for unchecked checkboxes (instead of ignoring them)

            parseNumbers: false, // convert values like "1", "-2.33" to 1, -2.33
            parseBooleans: false, // convert "true", "false" to true, false
            parseNulls: false, // convert "null" to null
            parseAll: false, // all of the above
            parseWithFunction: null, // to use custom parser, a function like: function(val){ return parsed_val; }

            customTypes: {}, // override defaultTypes
            defaultTypes: {
                string: function( str ) {
                    return String( str );
                },
                number: function( str ) {
                    return Number( str );
                },
                boolean: function( str ) {
                    return (["false", "null", "undefined", "", "0"].indexOf( str ) === -1);
                },
                null: function( str ) {
                    return (["false", "null", "undefined", "", "0"].indexOf( str ) !== -1) ? null : str;
                },
                array: function( str ) {
                    return JSON.parse( str );
                },
                object: function( str ) {
                    return JSON.parse( str );
                },
                auto: function( str ) {
                    return $.serializeJSON.parseValue(
                        str, null, {parseNumbers: true, parseBooleans: true, parseNulls: true}
                    );
                } // try again with something like "parseAll"
            },

            useIntKeysAsArrayIndex: false, // name="foo[2]" value="v" => {foo: [null, null, "v"]}, instead of {foo: ["2": "v"]}
        },

        // Merge option defaults into the options
        setupOpts: function( options ) {
            var opt, validOpts, defaultOptions, optWithDefault, parseAll, f;
            f = $.serializeJSON;

            if ( options === null || options === undefined ) options = {};       // options ||= {}
            defaultOptions = f.defaultOptions || {}; // defaultOptions

            // Make sure that the user didn't misspell an option
            validOpts = ['checkboxUncheckedValue', 'parseNumbers', 'parseBooleans', 'parseNulls', 'parseAll', 'parseWithFunction', 'customTypes', 'defaultTypes', 'useIntKeysAsArrayIndex']; // re-define because the user may override the defaultOptions
            for ( opt in options ) {
                if ( validOpts.indexOf( opt ) === -1 ) {
                    throw new Error( "serializeJSON ERROR: invalid option '" + opt + "'. Please use one of " + validOpts.join( ', ' ) );
                }
            }

            // Helper to get the default value for this option if none is specified by the user
            optWithDefault = function( key ) {
                return (options[key] !== false) && (options[key] !== '') && (options[key] || defaultOptions[key]);
            };

            // Return computed options (opts to be used in the rest of the script)
            parseAll = optWithDefault( 'parseAll' );
            return {
                checkboxUncheckedValue: optWithDefault( 'checkboxUncheckedValue' ),

                parseNumbers: parseAll || optWithDefault( 'parseNumbers' ),
                parseBooleans: parseAll || optWithDefault( 'parseBooleans' ),
                parseNulls: parseAll || optWithDefault( 'parseNulls' ),
                parseWithFunction: optWithDefault( 'parseWithFunction' ),

                typeFunctions: $.extend( {}, optWithDefault( 'defaultTypes' ), optWithDefault( 'customTypes' ) ),

                useIntKeysAsArrayIndex: optWithDefault( 'useIntKeysAsArrayIndex' ),
            };
        },

        // Given a string, apply the type or the relevant "parse" options, to return the parsed value
        parseValue: function( str, type, opts ) {
            var typeFunction, f;
            f = $.serializeJSON;

            // Parse with a type if available
            typeFunction = opts.typeFunctions && opts.typeFunctions[type];
            if ( typeFunction ) return typeFunction( str ); // use specific type

            // Otherwise, check if there is any auto-parse option enabled and use it.
            if ( opts.parseNumbers && f.isNumeric( str ) ) return Number( str ); // auto: number
            if ( opts.parseBooleans && (str === "true" || str === "false") ) return str === "true"; // auto: boolean
            if ( opts.parseNulls && str == "null" ) return null; // auto: null

            // If none applies, just return the str
            return str;
        },

        isObject: function( obj ) {
            return obj === Object( obj );
        }, // is this variable an object?
        isUndefined: function( obj ) {
            return obj === void 0;
        }, // safe check for undefined values
        isValidArrayIndex: function( val ) {
            return /^[0-9]+$/.test( String( val ) );
        }, // 1,2,3,4 ... are valid array indexes
        isNumeric: function( obj ) {
            return obj - parseFloat( obj ) >= 0;
        }, // taken from jQuery.isNumeric implementation. Not using jQuery.isNumeric to support old jQuery and Zepto versions

        optionKeys: function( obj ) {
            if ( Object.keys ) {
                return Object.keys( obj );
            } else {
                var keys = [];
                for ( var key in obj ) {
                    keys.push( key );
                }

                return keys;
            }
        }, // polyfill Object.keys to get option keys in IE<9

        // Split the input name in programatically readable keys.
        // The last element is always the type (default "_").
        // Examples:
        // "foo"              => ['foo', '_']
        // "foo:string"       => ['foo', 'string']
        // "foo:boolean"      => ['foo', 'boolean']
        // "[foo]"            => ['foo', '_']
        // "foo[inn][bar]"    => ['foo', 'inn', 'bar', '_']
        // "foo[inn[bar]]"    => ['foo', 'inn', 'bar', '_']
        // "foo[inn][arr][0]" => ['foo', 'inn', 'arr', '0', '_']
        // "arr[][val]"       => ['arr', '', 'val', '_']
        // "arr[][val]:null"  => ['arr', '', 'val', 'null']
        splitInputNameIntoKeysArray: function( name, opts ) {
            var keys, nameWithoutType, type, _ref, f;
            f = $.serializeJSON;
            _ref = f.extractTypeFromInputName( name, opts ), nameWithoutType = _ref[0], type = _ref[1];
            keys = nameWithoutType.split( '[' ); // split string into array
            keys = $.map(
                keys, function( key ) {
                    return key.replace( /]/g, '' );
                }
            ); // remove closing brackets
            if ( keys[0] === '' ) {
                keys.shift();
            } // ensure no opening bracket ("[foo][inn]" should be same as "foo[inn]")
            keys.push( type ); // add type at the end
            return keys;
        },

        // Returns [name-without-type, type] from name.
        // "foo"              =>  ["foo",      '_']
        // "foo:boolean"      =>  ["foo",      'boolean']
        // "foo[bar]:null"    =>  ["foo[bar]", 'null']
        extractTypeFromInputName: function( name, opts ) {
            var match, validTypes, f;
            if ( match = name.match( /(.*):([^:]+)$/ ) ) {
                f = $.serializeJSON;

                validTypes = f.optionKeys( opts ? opts.typeFunctions : f.defaultOptions.defaultTypes );
                validTypes.push( 'skip' ); // skip is a special type that makes it easy to remove
                if ( validTypes.indexOf( match[2] ) !== -1 ) {
                    return [match[1], match[2]];
                } else {
                    throw new Error( "serializeJSON ERROR: Invalid type " + match[2] + " found in input name '" + name + "', please use one of " + validTypes.join( ', ' ) )
                }
            } else {
                return [name, '_']; // no defined type, then use parse options
            }
        },

        // Set a value in an object or array, using multiple keys to set in a nested object or array:
        //
        // deepSet(obj, ['foo'], v)               // obj['foo'] = v
        // deepSet(obj, ['foo', 'inn'], v)        // obj['foo']['inn'] = v // Create the inner obj['foo'] object, if needed
        // deepSet(obj, ['foo', 'inn', '123'], v) // obj['foo']['arr']['123'] = v //
        //
        // deepSet(obj, ['0'], v)                                   // obj['0'] = v
        // deepSet(arr, ['0'], v, {useIntKeysAsArrayIndex: true})   // arr[0] = v
        // deepSet(arr, [''], v)                                    // arr.push(v)
        // deepSet(obj, ['arr', ''], v)                             // obj['arr'].push(v)
        //
        // arr = [];
        // deepSet(arr, ['', v]          // arr => [v]
        // deepSet(arr, ['', 'foo'], v)  // arr => [v, {foo: v}]
        // deepSet(arr, ['', 'bar'], v)  // arr => [v, {foo: v, bar: v}]
        // deepSet(arr, ['', 'bar'], v)  // arr => [v, {foo: v, bar: v}, {bar: v}]
        //
        deepSet: function( o, keys, value, opts ) {
            var key, nextKey, tail, lastIdx, lastVal, f;
            if ( opts == null ) opts = {};
            f = $.serializeJSON;
            if ( f.isUndefined( o ) ) {
                throw new Error( "ArgumentError: param 'o' expected to be an object or array, found undefined" );
            }
            if ( !keys || keys.length === 0 ) {
                throw new Error( "ArgumentError: param 'keys' expected to be an array with least one element" );
            }

            key = keys[0];

            // Only one key, then it's not a deepSet, just assign the value.
            if ( keys.length === 1 ) {
                if ( key === '' ) {
                    o.push( value ); // '' is used to push values into the array (assume o is an array)
                } else {
                    o[key] = value; // other keys can be used as object keys or array indexes
                }

                // With more keys is a deepSet. Apply recursively.
            } else {
                nextKey = keys[1];

                // '' is used to push values into the array,
                // with nextKey, set the value into the same object, in object[nextKey].
                // Covers the case of ['', 'foo'] and ['', 'var'] to push the object {foo, var}, and the case of nested arrays.
                if ( key === '' ) {
                    lastIdx = o.length - 1; // asume o is array
                    lastVal = o[lastIdx];
                    if ( f.isObject( lastVal ) && (f.isUndefined( lastVal[nextKey] ) || keys.length > 2) ) { // if nextKey is not present in the last object element, or there are more keys to deep set
                        key = lastIdx; // then set the new value in the same object element
                    } else {
                        key = lastIdx + 1; // otherwise, point to set the next index in the array
                    }
                }

                // '' is used to push values into the array "array[]"
                if ( nextKey === '' ) {
                    if ( f.isUndefined( o[key] ) || !$.isArray( o[key] ) ) {
                        o[key] = []; // define (or override) as array to push values
                    }
                } else {
                    if ( opts.useIntKeysAsArrayIndex && f.isValidArrayIndex( nextKey ) ) { // if 1, 2, 3 ... then use an array, where nextKey is the index
                        if ( f.isUndefined( o[key] ) || !$.isArray( o[key] ) ) {
                            o[key] = []; // define (or override) as array, to insert values using int keys as array indexes
                        }
                    } else { // for anything else, use an object, where nextKey is going to be the attribute name
                        if ( f.isUndefined( o[key] ) || !f.isObject( o[key] ) ) {
                            o[key] = {}; // define (or override) as object, to set nested properties
                        }
                    }
                }

                // Recursively set the inner object
                tail = keys.slice( 1 );
                f.deepSet( o[key], tail, value, opts );
            }
        },

        // Fill the formAsArray object with values for the unchecked checkbox inputs,
        // using the same format as the jquery.serializeArray function.
        // The value of the unchecked values is determined from the opts.checkboxUncheckedValue
        // and/or the data-unchecked-value attribute of the inputs.
        readCheckboxUncheckedValues: function( formAsArray, $form, opts ) {
            var selector, $uncheckedCheckboxes, $el, dataUncheckedValue, f;
            if ( opts == null ) opts = {};
            f = $.serializeJSON;

            selector = 'input[type=checkbox][name]:not(:checked):not([disabled])';
            $uncheckedCheckboxes = $form.find( selector ).add( $form.filter( selector ) );
            $uncheckedCheckboxes.each(
                function( i, el ) {
                    $el = $( el );
                    dataUncheckedValue = $el.attr( 'data-unchecked-value' );
                    if ( dataUncheckedValue ) { // data-unchecked-value has precedence over option opts.checkboxUncheckedValue
                        formAsArray.push( {name: el.name, value: dataUncheckedValue} );
                    } else {
                        if ( !f.isUndefined( opts.checkboxUncheckedValue ) ) {
                            formAsArray.push( {name: el.name, value: opts.checkboxUncheckedValue} );
                        }
                    }
                }
            );
        }

    };

}( window.jQuery || window.$ ));


(function( $ ) {  //This functions first parameter is named $
    'use strict';

    redux.customizer = redux.customizer || {};

    $( document ).ready(
        function() {
            redux.customizer.init();
        }
    );
    redux.customizer.init = function() {
        $( 'body' ).addClass( redux_customizer.body_class );
        $( '.accordion-section.redux-section, .accordion-section.redux-panel, .accordion-section-title' ).click(
            function() {
                $.redux.initFields();
            }
        );
    };
    redux.customizer.save = function( $obj, $container ) {
        var $parent = $obj.hasClass( 'redux-field' ) ? $obj : $obj.parents( '.redux-field-container:first' );
        redux.customizer.inputSave( $parent );
    };
    redux.customizer.inputSave = function( $parent ) {

        if ( !$parent.hasClass( 'redux-field-container' ) ) {
            $parent = $parent.parents( '[class^="redux-field-container"]' );
        }

        var $id = $parent.parent().find( '.redux-customizer-input' ).data( 'id' );

        if ( !$id ) {
            $parent = $parent.parents( '.redux-container-repeater:first' );
            var $id = $parent.parent().find( '.redux-customizer-input' ).data( 'id' );
        }

        //var $nData = $parent.serializeJSON();
        var $nData = $parent.find(':input').serializeJSON();

        $.each(
            $nData, function( $k, $v ) {
                $nData = $v;
            }
        );

        var $key = $parent.parent().find( '.redux-customizer-input' ).data( 'key' );
        if ( $nData[$key] ) {
            $nData = $nData[$key];
        }

        var $control = wp.customize.control( $id );

        $control.setting.set( $nData );

    }
})( jQuery );