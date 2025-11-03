/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 1933:
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_RESULT__;/*global define:false */
/**
 * Copyright 2012-2017 Craig Campbell
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Mousetrap is a simple keyboard shortcut library for Javascript with
 * no external dependencies
 *
 * @version 1.6.5
 * @url craig.is/killing/mice
 */
(function(window, document, undefined) {

    // Check if mousetrap is used inside browser, if not, return
    if (!window) {
        return;
    }

    /**
     * mapping of special keycodes to their corresponding keys
     *
     * everything in this dictionary cannot use keypress events
     * so it has to be here to map to the correct keycodes for
     * keyup/keydown events
     *
     * @type {Object}
     */
    var _MAP = {
        8: 'backspace',
        9: 'tab',
        13: 'enter',
        16: 'shift',
        17: 'ctrl',
        18: 'alt',
        20: 'capslock',
        27: 'esc',
        32: 'space',
        33: 'pageup',
        34: 'pagedown',
        35: 'end',
        36: 'home',
        37: 'left',
        38: 'up',
        39: 'right',
        40: 'down',
        45: 'ins',
        46: 'del',
        91: 'meta',
        93: 'meta',
        224: 'meta'
    };

    /**
     * mapping for special characters so they can support
     *
     * this dictionary is only used incase you want to bind a
     * keyup or keydown event to one of these keys
     *
     * @type {Object}
     */
    var _KEYCODE_MAP = {
        106: '*',
        107: '+',
        109: '-',
        110: '.',
        111 : '/',
        186: ';',
        187: '=',
        188: ',',
        189: '-',
        190: '.',
        191: '/',
        192: '`',
        219: '[',
        220: '\\',
        221: ']',
        222: '\''
    };

    /**
     * this is a mapping of keys that require shift on a US keypad
     * back to the non shift equivelents
     *
     * this is so you can use keyup events with these keys
     *
     * note that this will only work reliably on US keyboards
     *
     * @type {Object}
     */
    var _SHIFT_MAP = {
        '~': '`',
        '!': '1',
        '@': '2',
        '#': '3',
        '$': '4',
        '%': '5',
        '^': '6',
        '&': '7',
        '*': '8',
        '(': '9',
        ')': '0',
        '_': '-',
        '+': '=',
        ':': ';',
        '\"': '\'',
        '<': ',',
        '>': '.',
        '?': '/',
        '|': '\\'
    };

    /**
     * this is a list of special strings you can use to map
     * to modifier keys when you specify your keyboard shortcuts
     *
     * @type {Object}
     */
    var _SPECIAL_ALIASES = {
        'option': 'alt',
        'command': 'meta',
        'return': 'enter',
        'escape': 'esc',
        'plus': '+',
        'mod': /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'meta' : 'ctrl'
    };

    /**
     * variable to store the flipped version of _MAP from above
     * needed to check if we should use keypress or not when no action
     * is specified
     *
     * @type {Object|undefined}
     */
    var _REVERSE_MAP;

    /**
     * loop through the f keys, f1 to f19 and add them to the map
     * programatically
     */
    for (var i = 1; i < 20; ++i) {
        _MAP[111 + i] = 'f' + i;
    }

    /**
     * loop through to map numbers on the numeric keypad
     */
    for (i = 0; i <= 9; ++i) {

        // This needs to use a string cause otherwise since 0 is falsey
        // mousetrap will never fire for numpad 0 pressed as part of a keydown
        // event.
        //
        // @see https://github.com/ccampbell/mousetrap/pull/258
        _MAP[i + 96] = i.toString();
    }

    /**
     * cross browser add event method
     *
     * @param {Element|HTMLDocument} object
     * @param {string} type
     * @param {Function} callback
     * @returns void
     */
    function _addEvent(object, type, callback) {
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
            return;
        }

        object.attachEvent('on' + type, callback);
    }

    /**
     * takes the event and returns the key character
     *
     * @param {Event} e
     * @return {string}
     */
    function _characterFromEvent(e) {

        // for keypress events we should return the character as is
        if (e.type == 'keypress') {
            var character = String.fromCharCode(e.which);

            // if the shift key is not pressed then it is safe to assume
            // that we want the character to be lowercase.  this means if
            // you accidentally have caps lock on then your key bindings
            // will continue to work
            //
            // the only side effect that might not be desired is if you
            // bind something like 'A' cause you want to trigger an
            // event when capital A is pressed caps lock will no longer
            // trigger the event.  shift+a will though.
            if (!e.shiftKey) {
                character = character.toLowerCase();
            }

            return character;
        }

        // for non keypress events the special maps are needed
        if (_MAP[e.which]) {
            return _MAP[e.which];
        }

        if (_KEYCODE_MAP[e.which]) {
            return _KEYCODE_MAP[e.which];
        }

        // if it is not in the special map

        // with keydown and keyup events the character seems to always
        // come in as an uppercase character whether you are pressing shift
        // or not.  we should make sure it is always lowercase for comparisons
        return String.fromCharCode(e.which).toLowerCase();
    }

    /**
     * checks if two arrays are equal
     *
     * @param {Array} modifiers1
     * @param {Array} modifiers2
     * @returns {boolean}
     */
    function _modifiersMatch(modifiers1, modifiers2) {
        return modifiers1.sort().join(',') === modifiers2.sort().join(',');
    }

    /**
     * takes a key event and figures out what the modifiers are
     *
     * @param {Event} e
     * @returns {Array}
     */
    function _eventModifiers(e) {
        var modifiers = [];

        if (e.shiftKey) {
            modifiers.push('shift');
        }

        if (e.altKey) {
            modifiers.push('alt');
        }

        if (e.ctrlKey) {
            modifiers.push('ctrl');
        }

        if (e.metaKey) {
            modifiers.push('meta');
        }

        return modifiers;
    }

    /**
     * prevents default for this event
     *
     * @param {Event} e
     * @returns void
     */
    function _preventDefault(e) {
        if (e.preventDefault) {
            e.preventDefault();
            return;
        }

        e.returnValue = false;
    }

    /**
     * stops propogation for this event
     *
     * @param {Event} e
     * @returns void
     */
    function _stopPropagation(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
            return;
        }

        e.cancelBubble = true;
    }

    /**
     * determines if the keycode specified is a modifier key or not
     *
     * @param {string} key
     * @returns {boolean}
     */
    function _isModifier(key) {
        return key == 'shift' || key == 'ctrl' || key == 'alt' || key == 'meta';
    }

    /**
     * reverses the map lookup so that we can look for specific keys
     * to see what can and can't use keypress
     *
     * @return {Object}
     */
    function _getReverseMap() {
        if (!_REVERSE_MAP) {
            _REVERSE_MAP = {};
            for (var key in _MAP) {

                // pull out the numeric keypad from here cause keypress should
                // be able to detect the keys from the character
                if (key > 95 && key < 112) {
                    continue;
                }

                if (_MAP.hasOwnProperty(key)) {
                    _REVERSE_MAP[_MAP[key]] = key;
                }
            }
        }
        return _REVERSE_MAP;
    }

    /**
     * picks the best action based on the key combination
     *
     * @param {string} key - character for key
     * @param {Array} modifiers
     * @param {string=} action passed in
     */
    function _pickBestAction(key, modifiers, action) {

        // if no action was picked in we should try to pick the one
        // that we think would work best for this key
        if (!action) {
            action = _getReverseMap()[key] ? 'keydown' : 'keypress';
        }

        // modifier keys don't work as expected with keypress,
        // switch to keydown
        if (action == 'keypress' && modifiers.length) {
            action = 'keydown';
        }

        return action;
    }

    /**
     * Converts from a string key combination to an array
     *
     * @param  {string} combination like "command+shift+l"
     * @return {Array}
     */
    function _keysFromString(combination) {
        if (combination === '+') {
            return ['+'];
        }

        combination = combination.replace(/\+{2}/g, '+plus');
        return combination.split('+');
    }

    /**
     * Gets info for a specific key combination
     *
     * @param  {string} combination key combination ("command+s" or "a" or "*")
     * @param  {string=} action
     * @returns {Object}
     */
    function _getKeyInfo(combination, action) {
        var keys;
        var key;
        var i;
        var modifiers = [];

        // take the keys from this pattern and figure out what the actual
        // pattern is all about
        keys = _keysFromString(combination);

        for (i = 0; i < keys.length; ++i) {
            key = keys[i];

            // normalize key names
            if (_SPECIAL_ALIASES[key]) {
                key = _SPECIAL_ALIASES[key];
            }

            // if this is not a keypress event then we should
            // be smart about using shift keys
            // this will only work for US keyboards however
            if (action && action != 'keypress' && _SHIFT_MAP[key]) {
                key = _SHIFT_MAP[key];
                modifiers.push('shift');
            }

            // if this key is a modifier then add it to the list of modifiers
            if (_isModifier(key)) {
                modifiers.push(key);
            }
        }

        // depending on what the key combination is
        // we will try to pick the best event for it
        action = _pickBestAction(key, modifiers, action);

        return {
            key: key,
            modifiers: modifiers,
            action: action
        };
    }

    function _belongsTo(element, ancestor) {
        if (element === null || element === document) {
            return false;
        }

        if (element === ancestor) {
            return true;
        }

        return _belongsTo(element.parentNode, ancestor);
    }

    function Mousetrap(targetElement) {
        var self = this;

        targetElement = targetElement || document;

        if (!(self instanceof Mousetrap)) {
            return new Mousetrap(targetElement);
        }

        /**
         * element to attach key events to
         *
         * @type {Element}
         */
        self.target = targetElement;

        /**
         * a list of all the callbacks setup via Mousetrap.bind()
         *
         * @type {Object}
         */
        self._callbacks = {};

        /**
         * direct map of string combinations to callbacks used for trigger()
         *
         * @type {Object}
         */
        self._directMap = {};

        /**
         * keeps track of what level each sequence is at since multiple
         * sequences can start out with the same sequence
         *
         * @type {Object}
         */
        var _sequenceLevels = {};

        /**
         * variable to store the setTimeout call
         *
         * @type {null|number}
         */
        var _resetTimer;

        /**
         * temporary state where we will ignore the next keyup
         *
         * @type {boolean|string}
         */
        var _ignoreNextKeyup = false;

        /**
         * temporary state where we will ignore the next keypress
         *
         * @type {boolean}
         */
        var _ignoreNextKeypress = false;

        /**
         * are we currently inside of a sequence?
         * type of action ("keyup" or "keydown" or "keypress") or false
         *
         * @type {boolean|string}
         */
        var _nextExpectedAction = false;

        /**
         * resets all sequence counters except for the ones passed in
         *
         * @param {Object} doNotReset
         * @returns void
         */
        function _resetSequences(doNotReset) {
            doNotReset = doNotReset || {};

            var activeSequences = false,
                key;

            for (key in _sequenceLevels) {
                if (doNotReset[key]) {
                    activeSequences = true;
                    continue;
                }
                _sequenceLevels[key] = 0;
            }

            if (!activeSequences) {
                _nextExpectedAction = false;
            }
        }

        /**
         * finds all callbacks that match based on the keycode, modifiers,
         * and action
         *
         * @param {string} character
         * @param {Array} modifiers
         * @param {Event|Object} e
         * @param {string=} sequenceName - name of the sequence we are looking for
         * @param {string=} combination
         * @param {number=} level
         * @returns {Array}
         */
        function _getMatches(character, modifiers, e, sequenceName, combination, level) {
            var i;
            var callback;
            var matches = [];
            var action = e.type;

            // if there are no events related to this keycode
            if (!self._callbacks[character]) {
                return [];
            }

            // if a modifier key is coming up on its own we should allow it
            if (action == 'keyup' && _isModifier(character)) {
                modifiers = [character];
            }

            // loop through all callbacks for the key that was pressed
            // and see if any of them match
            for (i = 0; i < self._callbacks[character].length; ++i) {
                callback = self._callbacks[character][i];

                // if a sequence name is not specified, but this is a sequence at
                // the wrong level then move onto the next match
                if (!sequenceName && callback.seq && _sequenceLevels[callback.seq] != callback.level) {
                    continue;
                }

                // if the action we are looking for doesn't match the action we got
                // then we should keep going
                if (action != callback.action) {
                    continue;
                }

                // if this is a keypress event and the meta key and control key
                // are not pressed that means that we need to only look at the
                // character, otherwise check the modifiers as well
                //
                // chrome will not fire a keypress if meta or control is down
                // safari will fire a keypress if meta or meta+shift is down
                // firefox will fire a keypress if meta or control is down
                if ((action == 'keypress' && !e.metaKey && !e.ctrlKey) || _modifiersMatch(modifiers, callback.modifiers)) {

                    // when you bind a combination or sequence a second time it
                    // should overwrite the first one.  if a sequenceName or
                    // combination is specified in this call it does just that
                    //
                    // @todo make deleting its own method?
                    var deleteCombo = !sequenceName && callback.combo == combination;
                    var deleteSequence = sequenceName && callback.seq == sequenceName && callback.level == level;
                    if (deleteCombo || deleteSequence) {
                        self._callbacks[character].splice(i, 1);
                    }

                    matches.push(callback);
                }
            }

            return matches;
        }

        /**
         * actually calls the callback function
         *
         * if your callback function returns false this will use the jquery
         * convention - prevent default and stop propogation on the event
         *
         * @param {Function} callback
         * @param {Event} e
         * @returns void
         */
        function _fireCallback(callback, e, combo, sequence) {

            // if this event should not happen stop here
            if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
                return;
            }

            if (callback(e, combo) === false) {
                _preventDefault(e);
                _stopPropagation(e);
            }
        }

        /**
         * handles a character key event
         *
         * @param {string} character
         * @param {Array} modifiers
         * @param {Event} e
         * @returns void
         */
        self._handleKey = function(character, modifiers, e) {
            var callbacks = _getMatches(character, modifiers, e);
            var i;
            var doNotReset = {};
            var maxLevel = 0;
            var processedSequenceCallback = false;

            // Calculate the maxLevel for sequences so we can only execute the longest callback sequence
            for (i = 0; i < callbacks.length; ++i) {
                if (callbacks[i].seq) {
                    maxLevel = Math.max(maxLevel, callbacks[i].level);
                }
            }

            // loop through matching callbacks for this key event
            for (i = 0; i < callbacks.length; ++i) {

                // fire for all sequence callbacks
                // this is because if for example you have multiple sequences
                // bound such as "g i" and "g t" they both need to fire the
                // callback for matching g cause otherwise you can only ever
                // match the first one
                if (callbacks[i].seq) {

                    // only fire callbacks for the maxLevel to prevent
                    // subsequences from also firing
                    //
                    // for example 'a option b' should not cause 'option b' to fire
                    // even though 'option b' is part of the other sequence
                    //
                    // any sequences that do not match here will be discarded
                    // below by the _resetSequences call
                    if (callbacks[i].level != maxLevel) {
                        continue;
                    }

                    processedSequenceCallback = true;

                    // keep a list of which sequences were matches for later
                    doNotReset[callbacks[i].seq] = 1;
                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo, callbacks[i].seq);
                    continue;
                }

                // if there were no sequence matches but we are still here
                // that means this is a regular match so we should fire that
                if (!processedSequenceCallback) {
                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo);
                }
            }

            // if the key you pressed matches the type of sequence without
            // being a modifier (ie "keyup" or "keypress") then we should
            // reset all sequences that were not matched by this event
            //
            // this is so, for example, if you have the sequence "h a t" and you
            // type "h e a r t" it does not match.  in this case the "e" will
            // cause the sequence to reset
            //
            // modifier keys are ignored because you can have a sequence
            // that contains modifiers such as "enter ctrl+space" and in most
            // cases the modifier key will be pressed before the next key
            //
            // also if you have a sequence such as "ctrl+b a" then pressing the
            // "b" key will trigger a "keypress" and a "keydown"
            //
            // the "keydown" is expected when there is a modifier, but the
            // "keypress" ends up matching the _nextExpectedAction since it occurs
            // after and that causes the sequence to reset
            //
            // we ignore keypresses in a sequence that directly follow a keydown
            // for the same character
            var ignoreThisKeypress = e.type == 'keypress' && _ignoreNextKeypress;
            if (e.type == _nextExpectedAction && !_isModifier(character) && !ignoreThisKeypress) {
                _resetSequences(doNotReset);
            }

            _ignoreNextKeypress = processedSequenceCallback && e.type == 'keydown';
        };

        /**
         * handles a keydown event
         *
         * @param {Event} e
         * @returns void
         */
        function _handleKeyEvent(e) {

            // normalize e.which for key events
            // @see http://stackoverflow.com/questions/4285627/javascript-keycode-vs-charcode-utter-confusion
            if (typeof e.which !== 'number') {
                e.which = e.keyCode;
            }

            var character = _characterFromEvent(e);

            // no character found then stop
            if (!character) {
                return;
            }

            // need to use === for the character check because the character can be 0
            if (e.type == 'keyup' && _ignoreNextKeyup === character) {
                _ignoreNextKeyup = false;
                return;
            }

            self.handleKey(character, _eventModifiers(e), e);
        }

        /**
         * called to set a 1 second timeout on the specified sequence
         *
         * this is so after each key press in the sequence you have 1 second
         * to press the next key before you have to start over
         *
         * @returns void
         */
        function _resetSequenceTimer() {
            clearTimeout(_resetTimer);
            _resetTimer = setTimeout(_resetSequences, 1000);
        }

        /**
         * binds a key sequence to an event
         *
         * @param {string} combo - combo specified in bind call
         * @param {Array} keys
         * @param {Function} callback
         * @param {string=} action
         * @returns void
         */
        function _bindSequence(combo, keys, callback, action) {

            // start off by adding a sequence level record for this combination
            // and setting the level to 0
            _sequenceLevels[combo] = 0;

            /**
             * callback to increase the sequence level for this sequence and reset
             * all other sequences that were active
             *
             * @param {string} nextAction
             * @returns {Function}
             */
            function _increaseSequence(nextAction) {
                return function() {
                    _nextExpectedAction = nextAction;
                    ++_sequenceLevels[combo];
                    _resetSequenceTimer();
                };
            }

            /**
             * wraps the specified callback inside of another function in order
             * to reset all sequence counters as soon as this sequence is done
             *
             * @param {Event} e
             * @returns void
             */
            function _callbackAndReset(e) {
                _fireCallback(callback, e, combo);

                // we should ignore the next key up if the action is key down
                // or keypress.  this is so if you finish a sequence and
                // release the key the final key will not trigger a keyup
                if (action !== 'keyup') {
                    _ignoreNextKeyup = _characterFromEvent(e);
                }

                // weird race condition if a sequence ends with the key
                // another sequence begins with
                setTimeout(_resetSequences, 10);
            }

            // loop through keys one at a time and bind the appropriate callback
            // function.  for any key leading up to the final one it should
            // increase the sequence. after the final, it should reset all sequences
            //
            // if an action is specified in the original bind call then that will
            // be used throughout.  otherwise we will pass the action that the
            // next key in the sequence should match.  this allows a sequence
            // to mix and match keypress and keydown events depending on which
            // ones are better suited to the key provided
            for (var i = 0; i < keys.length; ++i) {
                var isFinal = i + 1 === keys.length;
                var wrappedCallback = isFinal ? _callbackAndReset : _increaseSequence(action || _getKeyInfo(keys[i + 1]).action);
                _bindSingle(keys[i], wrappedCallback, action, combo, i);
            }
        }

        /**
         * binds a single keyboard combination
         *
         * @param {string} combination
         * @param {Function} callback
         * @param {string=} action
         * @param {string=} sequenceName - name of sequence if part of sequence
         * @param {number=} level - what part of the sequence the command is
         * @returns void
         */
        function _bindSingle(combination, callback, action, sequenceName, level) {

            // store a direct mapped reference for use with Mousetrap.trigger
            self._directMap[combination + ':' + action] = callback;

            // make sure multiple spaces in a row become a single space
            combination = combination.replace(/\s+/g, ' ');

            var sequence = combination.split(' ');
            var info;

            // if this pattern is a sequence of keys then run through this method
            // to reprocess each pattern one key at a time
            if (sequence.length > 1) {
                _bindSequence(combination, sequence, callback, action);
                return;
            }

            info = _getKeyInfo(combination, action);

            // make sure to initialize array if this is the first time
            // a callback is added for this key
            self._callbacks[info.key] = self._callbacks[info.key] || [];

            // remove an existing match if there is one
            _getMatches(info.key, info.modifiers, {type: info.action}, sequenceName, combination, level);

            // add this call back to the array
            // if it is a sequence put it at the beginning
            // if not put it at the end
            //
            // this is important because the way these are processed expects
            // the sequence ones to come first
            self._callbacks[info.key][sequenceName ? 'unshift' : 'push']({
                callback: callback,
                modifiers: info.modifiers,
                action: info.action,
                seq: sequenceName,
                level: level,
                combo: combination
            });
        }

        /**
         * binds multiple combinations to the same callback
         *
         * @param {Array} combinations
         * @param {Function} callback
         * @param {string|undefined} action
         * @returns void
         */
        self._bindMultiple = function(combinations, callback, action) {
            for (var i = 0; i < combinations.length; ++i) {
                _bindSingle(combinations[i], callback, action);
            }
        };

        // start!
        _addEvent(targetElement, 'keypress', _handleKeyEvent);
        _addEvent(targetElement, 'keydown', _handleKeyEvent);
        _addEvent(targetElement, 'keyup', _handleKeyEvent);
    }

    /**
     * binds an event to mousetrap
     *
     * can be a single key, a combination of keys separated with +,
     * an array of keys, or a sequence of keys separated by spaces
     *
     * be sure to list the modifier keys first to make sure that the
     * correct key ends up getting bound (the last key in the pattern)
     *
     * @param {string|Array} keys
     * @param {Function} callback
     * @param {string=} action - 'keypress', 'keydown', or 'keyup'
     * @returns void
     */
    Mousetrap.prototype.bind = function(keys, callback, action) {
        var self = this;
        keys = keys instanceof Array ? keys : [keys];
        self._bindMultiple.call(self, keys, callback, action);
        return self;
    };

    /**
     * unbinds an event to mousetrap
     *
     * the unbinding sets the callback function of the specified key combo
     * to an empty function and deletes the corresponding key in the
     * _directMap dict.
     *
     * TODO: actually remove this from the _callbacks dictionary instead
     * of binding an empty function
     *
     * the keycombo+action has to be exactly the same as
     * it was defined in the bind method
     *
     * @param {string|Array} keys
     * @param {string} action
     * @returns void
     */
    Mousetrap.prototype.unbind = function(keys, action) {
        var self = this;
        return self.bind.call(self, keys, function() {}, action);
    };

    /**
     * triggers an event that has already been bound
     *
     * @param {string} keys
     * @param {string=} action
     * @returns void
     */
    Mousetrap.prototype.trigger = function(keys, action) {
        var self = this;
        if (self._directMap[keys + ':' + action]) {
            self._directMap[keys + ':' + action]({}, keys);
        }
        return self;
    };

    /**
     * resets the library back to its initial state.  this is useful
     * if you want to clear out the current keyboard shortcuts and bind
     * new ones - for example if you switch to another page
     *
     * @returns void
     */
    Mousetrap.prototype.reset = function() {
        var self = this;
        self._callbacks = {};
        self._directMap = {};
        return self;
    };

    /**
     * should we stop this event before firing off callbacks
     *
     * @param {Event} e
     * @param {Element} element
     * @return {boolean}
     */
    Mousetrap.prototype.stopCallback = function(e, element) {
        var self = this;

        // if the element has the class "mousetrap" then no need to stop
        if ((' ' + element.className + ' ').indexOf(' mousetrap ') > -1) {
            return false;
        }

        if (_belongsTo(element, self.target)) {
            return false;
        }

        // Events originating from a shadow DOM are re-targetted and `e.target` is the shadow host,
        // not the initial event target in the shadow tree. Note that not all events cross the
        // shadow boundary.
        // For shadow trees with `mode: 'open'`, the initial event target is the first element in
        // the event’s composed path. For shadow trees with `mode: 'closed'`, the initial event
        // target cannot be obtained.
        if ('composedPath' in e && typeof e.composedPath === 'function') {
            // For open shadow trees, update `element` so that the following check works.
            var initialEventTarget = e.composedPath()[0];
            if (initialEventTarget !== e.target) {
                element = initialEventTarget;
            }
        }

        // stop for input, select, and textarea
        return element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA' || element.isContentEditable;
    };

    /**
     * exposes _handleKey publicly so it can be overwritten by extensions
     */
    Mousetrap.prototype.handleKey = function() {
        var self = this;
        return self._handleKey.apply(self, arguments);
    };

    /**
     * allow custom key mappings
     */
    Mousetrap.addKeycodes = function(object) {
        for (var key in object) {
            if (object.hasOwnProperty(key)) {
                _MAP[key] = object[key];
            }
        }
        _REVERSE_MAP = null;
    };

    /**
     * Init the global mousetrap functions
     *
     * This method is needed to allow the global mousetrap functions to work
     * now that mousetrap is a constructor function.
     */
    Mousetrap.init = function() {
        var documentMousetrap = Mousetrap(document);
        for (var method in documentMousetrap) {
            if (method.charAt(0) !== '_') {
                Mousetrap[method] = (function(method) {
                    return function() {
                        return documentMousetrap[method].apply(documentMousetrap, arguments);
                    };
                } (method));
            }
        }
    };

    Mousetrap.init();

    // expose mousetrap to the global object
    window.Mousetrap = Mousetrap;

    // expose as a common js module
    if ( true && module.exports) {
        module.exports = Mousetrap;
    }

    // expose mousetrap as an AMD module
    if (true) {
        !(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
            return Mousetrap;
        }).call(exports, __webpack_require__, exports, module),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
    }
}) (typeof window !== 'undefined' ? window : null, typeof  window !== 'undefined' ? document : null);


/***/ }),

/***/ 3758:
/***/ (function(module) {

/*!
 * clipboard.js v2.0.11
 * https://clipboardjs.com/
 *
 * Licensed MIT © Zeno Rocha
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(true)
		module.exports = factory();
	else {}
})(this, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 686:
/***/ (function(__unused_webpack_module, __nested_webpack_exports__, __nested_webpack_require_623__) {

"use strict";

// EXPORTS
__nested_webpack_require_623__.d(__nested_webpack_exports__, {
  "default": function() { return /* binding */ clipboard; }
});

// EXTERNAL MODULE: ./node_modules/tiny-emitter/index.js
var tiny_emitter = __nested_webpack_require_623__(279);
var tiny_emitter_default = /*#__PURE__*/__nested_webpack_require_623__.n(tiny_emitter);
// EXTERNAL MODULE: ./node_modules/good-listener/src/listen.js
var listen = __nested_webpack_require_623__(370);
var listen_default = /*#__PURE__*/__nested_webpack_require_623__.n(listen);
// EXTERNAL MODULE: ./node_modules/select/src/select.js
var src_select = __nested_webpack_require_623__(817);
var select_default = /*#__PURE__*/__nested_webpack_require_623__.n(src_select);
;// CONCATENATED MODULE: ./src/common/command.js
/**
 * Executes a given operation type.
 * @param {String} type
 * @return {Boolean}
 */
function command(type) {
  try {
    return document.execCommand(type);
  } catch (err) {
    return false;
  }
}
;// CONCATENATED MODULE: ./src/actions/cut.js


/**
 * Cut action wrapper.
 * @param {String|HTMLElement} target
 * @return {String}
 */

var ClipboardActionCut = function ClipboardActionCut(target) {
  var selectedText = select_default()(target);
  command('cut');
  return selectedText;
};

/* harmony default export */ var actions_cut = (ClipboardActionCut);
;// CONCATENATED MODULE: ./src/common/create-fake-element.js
/**
 * Creates a fake textarea element with a value.
 * @param {String} value
 * @return {HTMLElement}
 */
function createFakeElement(value) {
  var isRTL = document.documentElement.getAttribute('dir') === 'rtl';
  var fakeElement = document.createElement('textarea'); // Prevent zooming on iOS

  fakeElement.style.fontSize = '12pt'; // Reset box model

  fakeElement.style.border = '0';
  fakeElement.style.padding = '0';
  fakeElement.style.margin = '0'; // Move element out of screen horizontally

  fakeElement.style.position = 'absolute';
  fakeElement.style[isRTL ? 'right' : 'left'] = '-9999px'; // Move element to the same position vertically

  var yPosition = window.pageYOffset || document.documentElement.scrollTop;
  fakeElement.style.top = "".concat(yPosition, "px");
  fakeElement.setAttribute('readonly', '');
  fakeElement.value = value;
  return fakeElement;
}
;// CONCATENATED MODULE: ./src/actions/copy.js



/**
 * Create fake copy action wrapper using a fake element.
 * @param {String} target
 * @param {Object} options
 * @return {String}
 */

var fakeCopyAction = function fakeCopyAction(value, options) {
  var fakeElement = createFakeElement(value);
  options.container.appendChild(fakeElement);
  var selectedText = select_default()(fakeElement);
  command('copy');
  fakeElement.remove();
  return selectedText;
};
/**
 * Copy action wrapper.
 * @param {String|HTMLElement} target
 * @param {Object} options
 * @return {String}
 */


var ClipboardActionCopy = function ClipboardActionCopy(target) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
    container: document.body
  };
  var selectedText = '';

  if (typeof target === 'string') {
    selectedText = fakeCopyAction(target, options);
  } else if (target instanceof HTMLInputElement && !['text', 'search', 'url', 'tel', 'password'].includes(target === null || target === void 0 ? void 0 : target.type)) {
    // If input type doesn't support `setSelectionRange`. Simulate it. https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/setSelectionRange
    selectedText = fakeCopyAction(target.value, options);
  } else {
    selectedText = select_default()(target);
    command('copy');
  }

  return selectedText;
};

/* harmony default export */ var actions_copy = (ClipboardActionCopy);
;// CONCATENATED MODULE: ./src/actions/default.js
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }



/**
 * Inner function which performs selection from either `text` or `target`
 * properties and then executes copy or cut operations.
 * @param {Object} options
 */

var ClipboardActionDefault = function ClipboardActionDefault() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  // Defines base properties passed from constructor.
  var _options$action = options.action,
      action = _options$action === void 0 ? 'copy' : _options$action,
      container = options.container,
      target = options.target,
      text = options.text; // Sets the `action` to be performed which can be either 'copy' or 'cut'.

  if (action !== 'copy' && action !== 'cut') {
    throw new Error('Invalid "action" value, use either "copy" or "cut"');
  } // Sets the `target` property using an element that will be have its content copied.


  if (target !== undefined) {
    if (target && _typeof(target) === 'object' && target.nodeType === 1) {
      if (action === 'copy' && target.hasAttribute('disabled')) {
        throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');
      }

      if (action === 'cut' && (target.hasAttribute('readonly') || target.hasAttribute('disabled'))) {
        throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');
      }
    } else {
      throw new Error('Invalid "target" value, use a valid Element');
    }
  } // Define selection strategy based on `text` property.


  if (text) {
    return actions_copy(text, {
      container: container
    });
  } // Defines which selection strategy based on `target` property.


  if (target) {
    return action === 'cut' ? actions_cut(target) : actions_copy(target, {
      container: container
    });
  }
};

/* harmony default export */ var actions_default = (ClipboardActionDefault);
;// CONCATENATED MODULE: ./src/clipboard.js
function clipboard_typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { clipboard_typeof = function _typeof(obj) { return typeof obj; }; } else { clipboard_typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return clipboard_typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (clipboard_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }






/**
 * Helper function to retrieve attribute value.
 * @param {String} suffix
 * @param {Element} element
 */

function getAttributeValue(suffix, element) {
  var attribute = "data-clipboard-".concat(suffix);

  if (!element.hasAttribute(attribute)) {
    return;
  }

  return element.getAttribute(attribute);
}
/**
 * Base class which takes one or more elements, adds event listeners to them,
 * and instantiates a new `ClipboardAction` on each click.
 */


var Clipboard = /*#__PURE__*/function (_Emitter) {
  _inherits(Clipboard, _Emitter);

  var _super = _createSuper(Clipboard);

  /**
   * @param {String|HTMLElement|HTMLCollection|NodeList} trigger
   * @param {Object} options
   */
  function Clipboard(trigger, options) {
    var _this;

    _classCallCheck(this, Clipboard);

    _this = _super.call(this);

    _this.resolveOptions(options);

    _this.listenClick(trigger);

    return _this;
  }
  /**
   * Defines if attributes would be resolved using internal setter functions
   * or custom functions that were passed in the constructor.
   * @param {Object} options
   */


  _createClass(Clipboard, [{
    key: "resolveOptions",
    value: function resolveOptions() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.action = typeof options.action === 'function' ? options.action : this.defaultAction;
      this.target = typeof options.target === 'function' ? options.target : this.defaultTarget;
      this.text = typeof options.text === 'function' ? options.text : this.defaultText;
      this.container = clipboard_typeof(options.container) === 'object' ? options.container : document.body;
    }
    /**
     * Adds a click event listener to the passed trigger.
     * @param {String|HTMLElement|HTMLCollection|NodeList} trigger
     */

  }, {
    key: "listenClick",
    value: function listenClick(trigger) {
      var _this2 = this;

      this.listener = listen_default()(trigger, 'click', function (e) {
        return _this2.onClick(e);
      });
    }
    /**
     * Defines a new `ClipboardAction` on each click event.
     * @param {Event} e
     */

  }, {
    key: "onClick",
    value: function onClick(e) {
      var trigger = e.delegateTarget || e.currentTarget;
      var action = this.action(trigger) || 'copy';
      var text = actions_default({
        action: action,
        container: this.container,
        target: this.target(trigger),
        text: this.text(trigger)
      }); // Fires an event based on the copy operation result.

      this.emit(text ? 'success' : 'error', {
        action: action,
        text: text,
        trigger: trigger,
        clearSelection: function clearSelection() {
          if (trigger) {
            trigger.focus();
          }

          window.getSelection().removeAllRanges();
        }
      });
    }
    /**
     * Default `action` lookup function.
     * @param {Element} trigger
     */

  }, {
    key: "defaultAction",
    value: function defaultAction(trigger) {
      return getAttributeValue('action', trigger);
    }
    /**
     * Default `target` lookup function.
     * @param {Element} trigger
     */

  }, {
    key: "defaultTarget",
    value: function defaultTarget(trigger) {
      var selector = getAttributeValue('target', trigger);

      if (selector) {
        return document.querySelector(selector);
      }
    }
    /**
     * Allow fire programmatically a copy action
     * @param {String|HTMLElement} target
     * @param {Object} options
     * @returns Text copied.
     */

  }, {
    key: "defaultText",

    /**
     * Default `text` lookup function.
     * @param {Element} trigger
     */
    value: function defaultText(trigger) {
      return getAttributeValue('text', trigger);
    }
    /**
     * Destroy lifecycle.
     */

  }, {
    key: "destroy",
    value: function destroy() {
      this.listener.destroy();
    }
  }], [{
    key: "copy",
    value: function copy(target) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
        container: document.body
      };
      return actions_copy(target, options);
    }
    /**
     * Allow fire programmatically a cut action
     * @param {String|HTMLElement} target
     * @returns Text cutted.
     */

  }, {
    key: "cut",
    value: function cut(target) {
      return actions_cut(target);
    }
    /**
     * Returns the support of the given action, or all actions if no action is
     * given.
     * @param {String} [action]
     */

  }, {
    key: "isSupported",
    value: function isSupported() {
      var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : ['copy', 'cut'];
      var actions = typeof action === 'string' ? [action] : action;
      var support = !!document.queryCommandSupported;
      actions.forEach(function (action) {
        support = support && !!document.queryCommandSupported(action);
      });
      return support;
    }
  }]);

  return Clipboard;
}((tiny_emitter_default()));

/* harmony default export */ var clipboard = (Clipboard);

/***/ }),

/***/ 828:
/***/ (function(module) {

var DOCUMENT_NODE_TYPE = 9;

/**
 * A polyfill for Element.matches()
 */
if (typeof Element !== 'undefined' && !Element.prototype.matches) {
    var proto = Element.prototype;

    proto.matches = proto.matchesSelector ||
                    proto.mozMatchesSelector ||
                    proto.msMatchesSelector ||
                    proto.oMatchesSelector ||
                    proto.webkitMatchesSelector;
}

/**
 * Finds the closest parent that matches a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @return {Function}
 */
function closest (element, selector) {
    while (element && element.nodeType !== DOCUMENT_NODE_TYPE) {
        if (typeof element.matches === 'function' &&
            element.matches(selector)) {
          return element;
        }
        element = element.parentNode;
    }
}

module.exports = closest;


/***/ }),

/***/ 438:
/***/ (function(module, __unused_webpack_exports, __nested_webpack_require_15749__) {

var closest = __nested_webpack_require_15749__(828);

/**
 * Delegates event to a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function _delegate(element, selector, type, callback, useCapture) {
    var listenerFn = listener.apply(this, arguments);

    element.addEventListener(type, listenerFn, useCapture);

    return {
        destroy: function() {
            element.removeEventListener(type, listenerFn, useCapture);
        }
    }
}

/**
 * Delegates event to a selector.
 *
 * @param {Element|String|Array} [elements]
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function delegate(elements, selector, type, callback, useCapture) {
    // Handle the regular Element usage
    if (typeof elements.addEventListener === 'function') {
        return _delegate.apply(null, arguments);
    }

    // Handle Element-less usage, it defaults to global delegation
    if (typeof type === 'function') {
        // Use `document` as the first parameter, then apply arguments
        // This is a short way to .unshift `arguments` without running into deoptimizations
        return _delegate.bind(null, document).apply(null, arguments);
    }

    // Handle Selector-based usage
    if (typeof elements === 'string') {
        elements = document.querySelectorAll(elements);
    }

    // Handle Array-like based usage
    return Array.prototype.map.call(elements, function (element) {
        return _delegate(element, selector, type, callback, useCapture);
    });
}

/**
 * Finds closest match and invokes callback.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @return {Function}
 */
function listener(element, selector, type, callback) {
    return function(e) {
        e.delegateTarget = closest(e.target, selector);

        if (e.delegateTarget) {
            callback.call(element, e);
        }
    }
}

module.exports = delegate;


/***/ }),

/***/ 879:
/***/ (function(__unused_webpack_module, exports) {

/**
 * Check if argument is a HTML element.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.node = function(value) {
    return value !== undefined
        && value instanceof HTMLElement
        && value.nodeType === 1;
};

/**
 * Check if argument is a list of HTML elements.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.nodeList = function(value) {
    var type = Object.prototype.toString.call(value);

    return value !== undefined
        && (type === '[object NodeList]' || type === '[object HTMLCollection]')
        && ('length' in value)
        && (value.length === 0 || exports.node(value[0]));
};

/**
 * Check if argument is a string.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.string = function(value) {
    return typeof value === 'string'
        || value instanceof String;
};

/**
 * Check if argument is a function.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.fn = function(value) {
    var type = Object.prototype.toString.call(value);

    return type === '[object Function]';
};


/***/ }),

/***/ 370:
/***/ (function(module, __unused_webpack_exports, __nested_webpack_require_19113__) {

var is = __nested_webpack_require_19113__(879);
var delegate = __nested_webpack_require_19113__(438);

/**
 * Validates all params and calls the right
 * listener function based on its target type.
 *
 * @param {String|HTMLElement|HTMLCollection|NodeList} target
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listen(target, type, callback) {
    if (!target && !type && !callback) {
        throw new Error('Missing required arguments');
    }

    if (!is.string(type)) {
        throw new TypeError('Second argument must be a String');
    }

    if (!is.fn(callback)) {
        throw new TypeError('Third argument must be a Function');
    }

    if (is.node(target)) {
        return listenNode(target, type, callback);
    }
    else if (is.nodeList(target)) {
        return listenNodeList(target, type, callback);
    }
    else if (is.string(target)) {
        return listenSelector(target, type, callback);
    }
    else {
        throw new TypeError('First argument must be a String, HTMLElement, HTMLCollection, or NodeList');
    }
}

/**
 * Adds an event listener to a HTML element
 * and returns a remove listener function.
 *
 * @param {HTMLElement} node
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenNode(node, type, callback) {
    node.addEventListener(type, callback);

    return {
        destroy: function() {
            node.removeEventListener(type, callback);
        }
    }
}

/**
 * Add an event listener to a list of HTML elements
 * and returns a remove listener function.
 *
 * @param {NodeList|HTMLCollection} nodeList
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenNodeList(nodeList, type, callback) {
    Array.prototype.forEach.call(nodeList, function(node) {
        node.addEventListener(type, callback);
    });

    return {
        destroy: function() {
            Array.prototype.forEach.call(nodeList, function(node) {
                node.removeEventListener(type, callback);
            });
        }
    }
}

/**
 * Add an event listener to a selector
 * and returns a remove listener function.
 *
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenSelector(selector, type, callback) {
    return delegate(document.body, selector, type, callback);
}

module.exports = listen;


/***/ }),

/***/ 817:
/***/ (function(module) {

function select(element) {
    var selectedText;

    if (element.nodeName === 'SELECT') {
        element.focus();

        selectedText = element.value;
    }
    else if (element.nodeName === 'INPUT' || element.nodeName === 'TEXTAREA') {
        var isReadOnly = element.hasAttribute('readonly');

        if (!isReadOnly) {
            element.setAttribute('readonly', '');
        }

        element.select();
        element.setSelectionRange(0, element.value.length);

        if (!isReadOnly) {
            element.removeAttribute('readonly');
        }

        selectedText = element.value;
    }
    else {
        if (element.hasAttribute('contenteditable')) {
            element.focus();
        }

        var selection = window.getSelection();
        var range = document.createRange();

        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);

        selectedText = selection.toString();
    }

    return selectedText;
}

module.exports = select;


/***/ }),

/***/ 279:
/***/ (function(module) {

function E () {
  // Keep this empty so it's easier to inherit from
  // (via https://github.com/lipsmack from https://github.com/scottcorgan/tiny-emitter/issues/3)
}

E.prototype = {
  on: function (name, callback, ctx) {
    var e = this.e || (this.e = {});

    (e[name] || (e[name] = [])).push({
      fn: callback,
      ctx: ctx
    });

    return this;
  },

  once: function (name, callback, ctx) {
    var self = this;
    function listener () {
      self.off(name, listener);
      callback.apply(ctx, arguments);
    };

    listener._ = callback
    return this.on(name, listener, ctx);
  },

  emit: function (name) {
    var data = [].slice.call(arguments, 1);
    var evtArr = ((this.e || (this.e = {}))[name] || []).slice();
    var i = 0;
    var len = evtArr.length;

    for (i; i < len; i++) {
      evtArr[i].fn.apply(evtArr[i].ctx, data);
    }

    return this;
  },

  off: function (name, callback) {
    var e = this.e || (this.e = {});
    var evts = e[name];
    var liveEvents = [];

    if (evts && callback) {
      for (var i = 0, len = evts.length; i < len; i++) {
        if (evts[i].fn !== callback && evts[i].fn._ !== callback)
          liveEvents.push(evts[i]);
      }
    }

    // Remove event from queue to prevent memory leak
    // Suggested by https://github.com/lazd
    // Ref: https://github.com/scottcorgan/tiny-emitter/commit/c6ebfaa9bc973b33d110a84a307742b7cf94c953#commitcomment-5024910

    (liveEvents.length)
      ? e[name] = liveEvents
      : delete e[name];

    return this;
  }
};

module.exports = E;
module.exports.TinyEmitter = E;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __nested_webpack_require_24495__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __nested_webpack_require_24495__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__nested_webpack_require_24495__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__nested_webpack_require_24495__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__nested_webpack_require_24495__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__nested_webpack_require_24495__.o(definition, key) && !__nested_webpack_require_24495__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__nested_webpack_require_24495__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	// module exports must be returned from runtime so entry inlining is disabled
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	return __nested_webpack_require_24495__(686);
/******/ })()
.default;
});

/***/ }),

/***/ 5760:
/***/ (() => {

/**
 * adds a bindGlobal method to Mousetrap that allows you to
 * bind specific keyboard shortcuts that will still work
 * inside a text input field
 *
 * usage:
 * Mousetrap.bindGlobal('ctrl+s', _saveChanges);
 */
/* global Mousetrap:true */
(function(Mousetrap) {
    if (! Mousetrap) {
        return;
    }
    var _globalCallbacks = {};
    var _originalStopCallback = Mousetrap.prototype.stopCallback;

    Mousetrap.prototype.stopCallback = function(e, element, combo, sequence) {
        var self = this;

        if (self.paused) {
            return true;
        }

        if (_globalCallbacks[combo] || _globalCallbacks[sequence]) {
            return false;
        }

        return _originalStopCallback.call(self, e, element, combo);
    };

    Mousetrap.prototype.bindGlobal = function(keys, callback, action) {
        var self = this;
        self.bind(keys, callback, action);

        if (keys instanceof Array) {
            for (var i = 0; i < keys.length; i++) {
                _globalCallbacks[keys[i]] = true;
            }
            return;
        }

        _globalCallbacks[keys] = true;
    };

    Mousetrap.init();
}) (typeof Mousetrap !== "undefined" ? Mousetrap : undefined);


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalUseDialog: () => (/* reexport */ use_dialog_default),
  __experimentalUseDragging: () => (/* reexport */ useDragging),
  __experimentalUseDropZone: () => (/* reexport */ useDropZone),
  __experimentalUseFixedWindowList: () => (/* reexport */ useFixedWindowList),
  __experimentalUseFocusOutside: () => (/* reexport */ useFocusOutside),
  compose: () => (/* reexport */ compose_default),
  createHigherOrderComponent: () => (/* reexport */ createHigherOrderComponent),
  debounce: () => (/* reexport */ debounce),
  ifCondition: () => (/* reexport */ if_condition_default),
  observableMap: () => (/* reexport */ observableMap),
  pipe: () => (/* reexport */ pipe_default),
  pure: () => (/* reexport */ pure_default),
  throttle: () => (/* reexport */ throttle),
  useAsyncList: () => (/* reexport */ use_async_list_default),
  useConstrainedTabbing: () => (/* reexport */ use_constrained_tabbing_default),
  useCopyOnClick: () => (/* reexport */ useCopyOnClick),
  useCopyToClipboard: () => (/* reexport */ useCopyToClipboard),
  useDebounce: () => (/* reexport */ useDebounce),
  useDebouncedInput: () => (/* reexport */ useDebouncedInput),
  useDisabled: () => (/* reexport */ useDisabled),
  useEvent: () => (/* reexport */ useEvent),
  useFocusOnMount: () => (/* reexport */ useFocusOnMount),
  useFocusReturn: () => (/* reexport */ use_focus_return_default),
  useFocusableIframe: () => (/* reexport */ useFocusableIframe),
  useInstanceId: () => (/* reexport */ use_instance_id_default),
  useIsomorphicLayoutEffect: () => (/* reexport */ use_isomorphic_layout_effect_default),
  useKeyboardShortcut: () => (/* reexport */ use_keyboard_shortcut_default),
  useMediaQuery: () => (/* reexport */ useMediaQuery),
  useMergeRefs: () => (/* reexport */ useMergeRefs),
  useObservableValue: () => (/* reexport */ useObservableValue),
  usePrevious: () => (/* reexport */ usePrevious),
  useReducedMotion: () => (/* reexport */ use_reduced_motion_default),
  useRefEffect: () => (/* reexport */ useRefEffect),
  useResizeObserver: () => (/* reexport */ use_resize_observer_useResizeObserver),
  useStateWithHistory: () => (/* reexport */ useStateWithHistory),
  useThrottle: () => (/* reexport */ useThrottle),
  useViewportMatch: () => (/* reexport */ use_viewport_match_default),
  useWarnOnChange: () => (/* reexport */ use_warn_on_change_default),
  withGlobalEvents: () => (/* reexport */ withGlobalEvents),
  withInstanceId: () => (/* reexport */ with_instance_id_default),
  withSafeTimeout: () => (/* reexport */ with_safe_timeout_default),
  withState: () => (/* reexport */ withState)
});

;// ./node_modules/tslib/tslib.es6.mjs
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol, Iterator */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g = Object.create((typeof Iterator === "function" ? Iterator : Object).prototype);
  return g.next = verb(0), g["throw"] = verb(1), g["return"] = verb(2), typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = Object.create((typeof AsyncIterator === "function" ? AsyncIterator : Object).prototype), verb("next"), verb("throw"), verb("return", awaitReturn), i[Symbol.asyncIterator] = function () { return this; }, i;
  function awaitReturn(f) { return function (v) { return Promise.resolve(v).then(f, reject); }; }
  function verb(n, f) { if (g[n]) { i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; if (f) i[n] = f(i[n]); } }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

var ownKeys = function(o) {
  ownKeys = Object.getOwnPropertyNames || function (o) {
    var ar = [];
    for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
    return ar;
  };
  return ownKeys(o);
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose, inner;
    if (async) {
      if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
      dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
      if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
      dispose = value[Symbol.dispose];
      if (async) inner = dispose;
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    if (inner) dispose = function() { try { inner.call(this); } catch (e) { return Promise.reject(e); } };
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  var r, s = 0;
  function next() {
    while (r = env.stack.pop()) {
      try {
        if (!r.async && s === 1) return s = 0, env.stack.push(r), Promise.resolve().then(next);
        if (r.dispose) {
          var result = r.dispose.call(r.value);
          if (r.async) return s |= 2, Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
        }
        else s |= 1;
      }
      catch (e) {
        fail(e);
      }
    }
    if (s === 1) return env.hasError ? Promise.reject(env.error) : Promise.resolve();
    if (env.hasError) throw env.error;
  }
  return next();
}

function __rewriteRelativeImportExtension(path, preserveJsx) {
  if (typeof path === "string" && /^\.\.?\//.test(path)) {
      return path.replace(/\.(tsx)$|((?:\.d)?)((?:\.[^./]+?)?)\.([cm]?)ts$/i, function (m, tsx, d, ext, cm) {
          return tsx ? preserveJsx ? ".jsx" : ".js" : d && (!ext || !cm) ? m : (d + ext + "." + cm.toLowerCase() + "js");
      });
  }
  return path;
}

/* harmony default export */ const tslib_es6 = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __esDecorate,
  __runInitializers,
  __propKey,
  __setFunctionName,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
  __rewriteRelativeImportExtension,
});

;// ./node_modules/lower-case/dist.es2015/index.js
/**
 * Source: ftp://ftp.unicode.org/Public/UCD/latest/ucd/SpecialCasing.txt
 */
var SUPPORTED_LOCALE = {
    tr: {
        regexp: /\u0130|\u0049|\u0049\u0307/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    az: {
        regexp: /\u0130/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    lt: {
        regexp: /\u0049|\u004A|\u012E|\u00CC|\u00CD|\u0128/g,
        map: {
            I: "\u0069\u0307",
            J: "\u006A\u0307",
            Į: "\u012F\u0307",
            Ì: "\u0069\u0307\u0300",
            Í: "\u0069\u0307\u0301",
            Ĩ: "\u0069\u0307\u0303",
        },
    },
};
/**
 * Localized lower case.
 */
function localeLowerCase(str, locale) {
    var lang = SUPPORTED_LOCALE[locale.toLowerCase()];
    if (lang)
        return lowerCase(str.replace(lang.regexp, function (m) { return lang.map[m]; }));
    return lowerCase(str);
}
/**
 * Lower case as a function.
 */
function lowerCase(str) {
    return str.toLowerCase();
}

;// ./node_modules/no-case/dist.es2015/index.js

// Support camel case ("camelCase" -> "camel Case" and "CAMELCase" -> "CAMEL Case").
var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
// Remove all non-word characters.
var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
/**
 * Normalize the string into something other libraries can manipulate easier.
 */
function noCase(input, options) {
    if (options === void 0) { options = {}; }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    // Trim the delimiter from around the output string.
    while (result.charAt(start) === "\0")
        start++;
    while (result.charAt(end - 1) === "\0")
        end--;
    // Transform each token independently.
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
}
/**
 * Replace `re` in the input string with the replacement value.
 */
function replace(input, re, value) {
    if (re instanceof RegExp)
        return input.replace(re, value);
    return re.reduce(function (input, re) { return input.replace(re, value); }, input);
}

;// ./node_modules/pascal-case/dist.es2015/index.js


function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
        return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
}
function pascalCaseTransformMerge(input) {
    return input.charAt(0).toUpperCase() + input.slice(1).toLowerCase();
}
function pascalCase(input, options) {
    if (options === void 0) { options = {}; }
    return noCase(input, __assign({ delimiter: "", transform: pascalCaseTransform }, options));
}

;// ./node_modules/@wordpress/compose/build-module/utils/create-higher-order-component/index.js

function createHigherOrderComponent(mapComponent, modifierName) {
  return (Inner) => {
    const Outer = mapComponent(Inner);
    Outer.displayName = hocName(modifierName, Inner);
    return Outer;
  };
}
const hocName = (name, Inner) => {
  const inner = Inner.displayName || Inner.name || "Component";
  const outer = pascalCase(name ?? "");
  return `${outer}(${inner})`;
};


;// ./node_modules/@wordpress/compose/build-module/utils/debounce/index.js
const debounce = (func, wait, options) => {
  let lastArgs;
  let lastThis;
  let maxWait = 0;
  let result;
  let timerId;
  let lastCallTime;
  let lastInvokeTime = 0;
  let leading = false;
  let maxing = false;
  let trailing = true;
  if (options) {
    leading = !!options.leading;
    maxing = "maxWait" in options;
    if (options.maxWait !== void 0) {
      maxWait = Math.max(options.maxWait, wait);
    }
    trailing = "trailing" in options ? !!options.trailing : trailing;
  }
  function invokeFunc(time) {
    const args = lastArgs;
    const thisArg = lastThis;
    lastArgs = void 0;
    lastThis = void 0;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }
  function startTimer(pendingFunc, waitTime) {
    timerId = setTimeout(pendingFunc, waitTime);
  }
  function cancelTimer() {
    if (timerId !== void 0) {
      clearTimeout(timerId);
    }
  }
  function leadingEdge(time) {
    lastInvokeTime = time;
    startTimer(timerExpired, wait);
    return leading ? invokeFunc(time) : result;
  }
  function getTimeSinceLastCall(time) {
    return time - (lastCallTime || 0);
  }
  function remainingWait(time) {
    const timeSinceLastCall = getTimeSinceLastCall(time);
    const timeSinceLastInvoke = time - lastInvokeTime;
    const timeWaiting = wait - timeSinceLastCall;
    return maxing ? Math.min(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
  }
  function shouldInvoke(time) {
    const timeSinceLastCall = getTimeSinceLastCall(time);
    const timeSinceLastInvoke = time - lastInvokeTime;
    return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
  }
  function timerExpired() {
    const time = Date.now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    startTimer(timerExpired, remainingWait(time));
    return void 0;
  }
  function clearTimer() {
    timerId = void 0;
  }
  function trailingEdge(time) {
    clearTimer();
    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = void 0;
    return result;
  }
  function cancel() {
    cancelTimer();
    lastInvokeTime = 0;
    clearTimer();
    lastArgs = lastCallTime = lastThis = void 0;
  }
  function flush() {
    return pending() ? trailingEdge(Date.now()) : result;
  }
  function pending() {
    return timerId !== void 0;
  }
  function debounced(...args) {
    const time = Date.now();
    const isInvoking = shouldInvoke(time);
    lastArgs = args;
    lastThis = this;
    lastCallTime = time;
    if (isInvoking) {
      if (!pending()) {
        return leadingEdge(lastCallTime);
      }
      if (maxing) {
        startTimer(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (!pending()) {
      startTimer(timerExpired, wait);
    }
    return result;
  }
  debounced.cancel = cancel;
  debounced.flush = flush;
  debounced.pending = pending;
  return debounced;
};


;// ./node_modules/@wordpress/compose/build-module/utils/throttle/index.js

const throttle = (func, wait, options) => {
  let leading = true;
  let trailing = true;
  if (options) {
    leading = "leading" in options ? !!options.leading : leading;
    trailing = "trailing" in options ? !!options.trailing : trailing;
  }
  return debounce(func, wait, {
    leading,
    trailing,
    maxWait: wait
  });
};


;// ./node_modules/@wordpress/compose/build-module/utils/observable-map/index.js
function observableMap() {
  const map = /* @__PURE__ */ new Map();
  const listeners = /* @__PURE__ */ new Map();
  function callListeners(name) {
    const list = listeners.get(name);
    if (!list) {
      return;
    }
    for (const listener of list) {
      listener();
    }
  }
  return {
    get(name) {
      return map.get(name);
    },
    set(name, value) {
      map.set(name, value);
      callListeners(name);
    },
    delete(name) {
      map.delete(name);
      callListeners(name);
    },
    subscribe(name, listener) {
      let list = listeners.get(name);
      if (!list) {
        list = /* @__PURE__ */ new Set();
        listeners.set(name, list);
      }
      list.add(listener);
      return () => {
        list.delete(listener);
        if (list.size === 0) {
          listeners.delete(name);
        }
      };
    }
  };
}


;// ./node_modules/@wordpress/compose/build-module/higher-order/pipe.js
const basePipe = (reverse = false) => (...funcs) => (...args) => {
  const functions = funcs.flat();
  if (reverse) {
    functions.reverse();
  }
  return functions.reduce(
    (prev, func) => [func(...prev)],
    args
  )[0];
};
const pipe = basePipe();
var pipe_default = pipe;


;// ./node_modules/@wordpress/compose/build-module/higher-order/compose.js

const compose = basePipe(true);
var compose_default = compose;


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/compose/build-module/higher-order/if-condition/index.js


function ifCondition(predicate) {
  return createHigherOrderComponent(
    (WrappedComponent) => (props) => {
      if (!predicate(props)) {
        return null;
      }
      return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...props });
    },
    "ifCondition"
  );
}
var if_condition_default = ifCondition;


;// external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/compose/build-module/higher-order/pure/index.js




const pure = createHigherOrderComponent(function(WrappedComponent) {
  if (WrappedComponent.prototype instanceof external_wp_element_namespaceObject.Component) {
    return class extends WrappedComponent {
      shouldComponentUpdate(nextProps, nextState) {
        return !external_wp_isShallowEqual_default()(nextProps, this.props) || !external_wp_isShallowEqual_default()(nextState, this.state);
      }
    };
  }
  return class extends external_wp_element_namespaceObject.Component {
    shouldComponentUpdate(nextProps) {
      return !external_wp_isShallowEqual_default()(nextProps, this.props);
    }
    render() {
      return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...this.props });
    }
  };
}, "pure");
var pure_default = pure;


;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// ./node_modules/@wordpress/compose/build-module/higher-order/with-global-events/listener.js
class Listener {
  constructor() {
    this.listeners = {};
    this.handleEvent = this.handleEvent.bind(this);
  }
  add(eventType, instance) {
    if (!this.listeners[eventType]) {
      window.addEventListener(eventType, this.handleEvent);
      this.listeners[eventType] = [];
    }
    this.listeners[eventType].push(instance);
  }
  remove(eventType, instance) {
    if (!this.listeners[eventType]) {
      return;
    }
    this.listeners[eventType] = this.listeners[eventType].filter(
      (listener) => listener !== instance
    );
    if (!this.listeners[eventType].length) {
      window.removeEventListener(eventType, this.handleEvent);
      delete this.listeners[eventType];
    }
  }
  handleEvent(event) {
    this.listeners[event.type]?.forEach(
      (instance) => {
        instance.handleEvent(event);
      }
    );
  }
}
var listener_default = Listener;


;// ./node_modules/@wordpress/compose/build-module/higher-order/with-global-events/index.js





const listener = new listener_default();
function withGlobalEvents(eventTypesToHandlers) {
  external_wp_deprecated_default()("wp.compose.withGlobalEvents", {
    since: "5.7",
    alternative: "useEffect"
  });
  return createHigherOrderComponent((WrappedComponent) => {
    class Wrapper extends external_wp_element_namespaceObject.Component {
      constructor(props) {
        super(props);
        this.handleEvent = this.handleEvent.bind(this);
        this.handleRef = this.handleRef.bind(this);
      }
      componentDidMount() {
        Object.keys(eventTypesToHandlers).forEach((eventType) => {
          listener.add(eventType, this);
        });
      }
      componentWillUnmount() {
        Object.keys(eventTypesToHandlers).forEach((eventType) => {
          listener.remove(eventType, this);
        });
      }
      handleEvent(event) {
        const handler = eventTypesToHandlers[
          /** @type {keyof GlobalEventHandlersEventMap} */
          event.type
          /* eslint-enable jsdoc/no-undefined-types */
        ];
        if (typeof this.wrappedRef[handler] === "function") {
          this.wrappedRef[handler](event);
        }
      }
      handleRef(el) {
        this.wrappedRef = el;
        if (this.props.forwardedRef) {
          this.props.forwardedRef(el);
        }
      }
      render() {
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          WrappedComponent,
          {
            ...this.props.ownProps,
            ref: this.handleRef
          }
        );
      }
    }
    return (0,external_wp_element_namespaceObject.forwardRef)((props, ref) => {
      return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Wrapper, { ownProps: props, forwardedRef: ref });
    });
  }, "withGlobalEvents");
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-instance-id/index.js

const instanceMap = /* @__PURE__ */ new WeakMap();
function createId(object) {
  const instances = instanceMap.get(object) || 0;
  instanceMap.set(object, instances + 1);
  return instances;
}
function useInstanceId(object, prefix, preferredId) {
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (preferredId) {
      return preferredId;
    }
    const id = createId(object);
    return prefix ? `${prefix}-${id}` : id;
  }, [object, preferredId, prefix]);
}
var use_instance_id_default = useInstanceId;


;// ./node_modules/@wordpress/compose/build-module/higher-order/with-instance-id/index.js



const withInstanceId = createHigherOrderComponent(
  (WrappedComponent) => {
    return (props) => {
      const instanceId = use_instance_id_default(WrappedComponent);
      return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...props, instanceId });
    };
  },
  "instanceId"
);
var with_instance_id_default = withInstanceId;


;// ./node_modules/@wordpress/compose/build-module/higher-order/with-safe-timeout/index.js



const withSafeTimeout = createHigherOrderComponent(
  (OriginalComponent) => {
    return class WrappedComponent extends external_wp_element_namespaceObject.Component {
      timeouts;
      constructor(props) {
        super(props);
        this.timeouts = [];
        this.setTimeout = this.setTimeout.bind(this);
        this.clearTimeout = this.clearTimeout.bind(this);
      }
      componentWillUnmount() {
        this.timeouts.forEach(clearTimeout);
      }
      setTimeout(fn, delay) {
        const id = setTimeout(() => {
          fn();
          this.clearTimeout(id);
        }, delay);
        this.timeouts.push(id);
        return id;
      }
      clearTimeout(id) {
        clearTimeout(id);
        this.timeouts = this.timeouts.filter(
          (timeoutId) => timeoutId !== id
        );
      }
      render() {
        return (
          // @ts-ignore
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            OriginalComponent,
            {
              ...this.props,
              setTimeout: this.setTimeout,
              clearTimeout: this.clearTimeout
            }
          )
        );
      }
    };
  },
  "withSafeTimeout"
);
var with_safe_timeout_default = withSafeTimeout;


;// ./node_modules/@wordpress/compose/build-module/higher-order/with-state/index.js




function withState(initialState = {}) {
  external_wp_deprecated_default()("wp.compose.withState", {
    since: "5.8",
    alternative: "wp.element.useState"
  });
  return createHigherOrderComponent((OriginalComponent) => {
    return class WrappedComponent extends external_wp_element_namespaceObject.Component {
      constructor(props) {
        super(props);
        this.setState = this.setState.bind(this);
        this.state = initialState;
      }
      render() {
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          OriginalComponent,
          {
            ...this.props,
            ...this.state,
            setState: this.setState
          }
        );
      }
    };
  }, "withState");
}


;// external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// ./node_modules/@wordpress/compose/build-module/hooks/use-ref-effect/index.js

function useRefEffect(callback, dependencies) {
  const cleanupRef = (0,external_wp_element_namespaceObject.useRef)();
  return (0,external_wp_element_namespaceObject.useCallback)((node) => {
    if (node) {
      cleanupRef.current = callback(node);
    } else if (cleanupRef.current) {
      cleanupRef.current();
    }
  }, dependencies);
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-constrained-tabbing/index.js


function useConstrainedTabbing() {
  return useRefEffect((node) => {
    function onKeyDown(event) {
      const { key, shiftKey, target } = event;
      if (key !== "Tab") {
        return;
      }
      const action = shiftKey ? "findPrevious" : "findNext";
      const nextElement = external_wp_dom_namespaceObject.focus.tabbable[action](
        /** @type {HTMLElement} */
        target
      ) || null;
      if (
        /** @type {HTMLElement} */
        target.contains(nextElement)
      ) {
        event.preventDefault();
        nextElement?.focus();
        return;
      }
      if (node.contains(nextElement)) {
        return;
      }
      const domAction = shiftKey ? "append" : "prepend";
      const { ownerDocument } = node;
      const trap = ownerDocument.createElement("div");
      trap.tabIndex = -1;
      node[domAction](trap);
      trap.addEventListener("blur", () => node.removeChild(trap));
      trap.focus();
    }
    node.addEventListener("keydown", onKeyDown);
    return () => {
      node.removeEventListener("keydown", onKeyDown);
    };
  }, []);
}
var use_constrained_tabbing_default = useConstrainedTabbing;


// EXTERNAL MODULE: ./node_modules/clipboard/dist/clipboard.js
var dist_clipboard = __webpack_require__(3758);
var clipboard_default = /*#__PURE__*/__webpack_require__.n(dist_clipboard);
;// ./node_modules/@wordpress/compose/build-module/hooks/use-copy-on-click/index.js



function useCopyOnClick(ref, text, timeout = 4e3) {
  external_wp_deprecated_default()("wp.compose.useCopyOnClick", {
    since: "5.8",
    alternative: "wp.compose.useCopyToClipboard"
  });
  const clipboardRef = (0,external_wp_element_namespaceObject.useRef)();
  const [hasCopied, setHasCopied] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    let timeoutId;
    if (!ref.current) {
      return;
    }
    clipboardRef.current = new (clipboard_default())(ref.current, {
      text: () => typeof text === "function" ? text() : text
    });
    clipboardRef.current.on("success", ({ clearSelection, trigger }) => {
      clearSelection();
      if (trigger) {
        trigger.focus();
      }
      if (timeout) {
        setHasCopied(true);
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => setHasCopied(false), timeout);
      }
    });
    return () => {
      if (clipboardRef.current) {
        clipboardRef.current.destroy();
      }
      clearTimeout(timeoutId);
    };
  }, [text, timeout, setHasCopied]);
  return hasCopied;
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-copy-to-clipboard/index.js



function useUpdatedRef(value) {
  const ref = (0,external_wp_element_namespaceObject.useRef)(value);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    ref.current = value;
  }, [value]);
  return ref;
}
function useCopyToClipboard(text, onSuccess) {
  const textRef = useUpdatedRef(text);
  const onSuccessRef = useUpdatedRef(onSuccess);
  return useRefEffect((node) => {
    const clipboard = new (clipboard_default())(node, {
      text() {
        return typeof textRef.current === "function" ? textRef.current() : textRef.current || "";
      }
    });
    clipboard.on("success", ({ clearSelection }) => {
      clearSelection();
      if (onSuccessRef.current) {
        onSuccessRef.current();
      }
    });
    return () => {
      clipboard.destroy();
    };
  }, []);
}


;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// ./node_modules/@wordpress/compose/build-module/hooks/use-focus-on-mount/index.js



function useFocusOnMount(focusOnMount = "firstElement") {
  const focusOnMountRef = (0,external_wp_element_namespaceObject.useRef)(focusOnMount);
  const setFocus = (target) => {
    target.focus({
      // When focusing newly mounted dialogs,
      // the position of the popover is often not right on the first render
      // This prevents the layout shifts when focusing the dialogs.
      preventScroll: true
    });
  };
  const timerIdRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    focusOnMountRef.current = focusOnMount;
  }, [focusOnMount]);
  return useRefEffect((node) => {
    if (!node || focusOnMountRef.current === false) {
      return;
    }
    if (node.contains(node.ownerDocument?.activeElement ?? null)) {
      return;
    }
    if (focusOnMountRef.current !== "firstElement") {
      setFocus(node);
      return;
    }
    timerIdRef.current = setTimeout(() => {
      const firstTabbable = external_wp_dom_namespaceObject.focus.tabbable.find(node)[0];
      if (firstTabbable) {
        setFocus(firstTabbable);
      }
    }, 0);
    return () => {
      if (timerIdRef.current) {
        clearTimeout(timerIdRef.current);
      }
    };
  }, []);
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-focus-return/index.js

let origin = null;
function useFocusReturn(onFocusReturn) {
  const ref = (0,external_wp_element_namespaceObject.useRef)(null);
  const focusedBeforeMount = (0,external_wp_element_namespaceObject.useRef)(null);
  const onFocusReturnRef = (0,external_wp_element_namespaceObject.useRef)(onFocusReturn);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    onFocusReturnRef.current = onFocusReturn;
  }, [onFocusReturn]);
  return (0,external_wp_element_namespaceObject.useCallback)((node) => {
    if (node) {
      ref.current = node;
      if (focusedBeforeMount.current) {
        return;
      }
      const activeDocument = node.ownerDocument.activeElement instanceof window.HTMLIFrameElement ? node.ownerDocument.activeElement.contentDocument : node.ownerDocument;
      focusedBeforeMount.current = activeDocument?.activeElement ?? null;
    } else if (focusedBeforeMount.current) {
      const isFocused = ref.current?.contains(
        ref.current?.ownerDocument.activeElement
      );
      if (ref.current?.isConnected && !isFocused) {
        origin ??= focusedBeforeMount.current;
        return;
      }
      if (onFocusReturnRef.current) {
        onFocusReturnRef.current();
      } else {
        (!focusedBeforeMount.current.isConnected ? origin : focusedBeforeMount.current)?.focus();
      }
      origin = null;
    }
  }, []);
}
var use_focus_return_default = useFocusReturn;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-focus-outside/index.js

const INPUT_BUTTON_TYPES = ["button", "submit"];
function isFocusNormalizedButton(eventTarget) {
  if (!(eventTarget instanceof window.HTMLElement)) {
    return false;
  }
  switch (eventTarget.nodeName) {
    case "A":
    case "BUTTON":
      return true;
    case "INPUT":
      return INPUT_BUTTON_TYPES.includes(
        eventTarget.type
      );
  }
  return false;
}
function useFocusOutside(onFocusOutside) {
  const currentOnFocusOutsideRef = (0,external_wp_element_namespaceObject.useRef)(onFocusOutside);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentOnFocusOutsideRef.current = onFocusOutside;
  }, [onFocusOutside]);
  const preventBlurCheckRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const blurCheckTimeoutIdRef = (0,external_wp_element_namespaceObject.useRef)();
  const cancelBlurCheck = (0,external_wp_element_namespaceObject.useCallback)(() => {
    clearTimeout(blurCheckTimeoutIdRef.current);
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!onFocusOutside) {
      cancelBlurCheck();
    }
  }, [onFocusOutside, cancelBlurCheck]);
  const normalizeButtonFocus = (0,external_wp_element_namespaceObject.useCallback)((event) => {
    const { type, target } = event;
    const isInteractionEnd = ["mouseup", "touchend"].includes(type);
    if (isInteractionEnd) {
      preventBlurCheckRef.current = false;
    } else if (isFocusNormalizedButton(target)) {
      preventBlurCheckRef.current = true;
    }
  }, []);
  const queueBlurCheck = (0,external_wp_element_namespaceObject.useCallback)((event) => {
    event.persist();
    if (preventBlurCheckRef.current) {
      return;
    }
    const ignoreForRelatedTarget = event.target.getAttribute(
      "data-unstable-ignore-focus-outside-for-relatedtarget"
    );
    if (ignoreForRelatedTarget && event.relatedTarget?.closest(ignoreForRelatedTarget)) {
      return;
    }
    blurCheckTimeoutIdRef.current = setTimeout(() => {
      if (!document.hasFocus()) {
        event.preventDefault();
        return;
      }
      if ("function" === typeof currentOnFocusOutsideRef.current) {
        currentOnFocusOutsideRef.current(event);
      }
    }, 0);
  }, []);
  return {
    onFocus: cancelBlurCheck,
    onMouseDown: normalizeButtonFocus,
    onMouseUp: normalizeButtonFocus,
    onTouchStart: normalizeButtonFocus,
    onTouchEnd: normalizeButtonFocus,
    onBlur: queueBlurCheck
  };
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-merge-refs/index.js

function assignRef(ref, value) {
  if (typeof ref === "function") {
    ref(value);
  } else if (ref && ref.hasOwnProperty("current")) {
    ref.current = value;
  }
}
function useMergeRefs(refs) {
  const element = (0,external_wp_element_namespaceObject.useRef)();
  const isAttachedRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const didElementChangeRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const previousRefsRef = (0,external_wp_element_namespaceObject.useRef)([]);
  const currentRefsRef = (0,external_wp_element_namespaceObject.useRef)(refs);
  currentRefsRef.current = refs;
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (didElementChangeRef.current === false && isAttachedRef.current === true) {
      refs.forEach((ref, index) => {
        const previousRef = previousRefsRef.current[index];
        if (ref !== previousRef) {
          assignRef(previousRef, null);
          assignRef(ref, element.current);
        }
      });
    }
    previousRefsRef.current = refs;
  }, refs);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    didElementChangeRef.current = false;
  });
  return (0,external_wp_element_namespaceObject.useCallback)((value) => {
    assignRef(element, value);
    didElementChangeRef.current = true;
    isAttachedRef.current = value !== null;
    const refsToAssign = value ? currentRefsRef.current : previousRefsRef.current;
    for (const ref of refsToAssign) {
      assignRef(ref, value);
    }
  }, []);
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-dialog/index.js







function useDialog(options) {
  const currentOptions = (0,external_wp_element_namespaceObject.useRef)();
  const { constrainTabbing = options.focusOnMount !== false } = options;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentOptions.current = options;
  }, Object.values(options));
  const constrainedTabbingRef = use_constrained_tabbing_default();
  const focusOnMountRef = useFocusOnMount(options.focusOnMount);
  const focusReturnRef = use_focus_return_default();
  const focusOutsideProps = useFocusOutside((event) => {
    if (currentOptions.current?.__unstableOnClose) {
      currentOptions.current.__unstableOnClose("focus-outside", event);
    } else if (currentOptions.current?.onClose) {
      currentOptions.current.onClose();
    }
  });
  const closeOnEscapeRef = (0,external_wp_element_namespaceObject.useCallback)((node) => {
    if (!node) {
      return;
    }
    node.addEventListener("keydown", (event) => {
      if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented && currentOptions.current?.onClose) {
        event.preventDefault();
        currentOptions.current.onClose();
      }
    });
  }, []);
  return [
    useMergeRefs([
      constrainTabbing ? constrainedTabbingRef : null,
      options.focusOnMount !== false ? focusReturnRef : null,
      options.focusOnMount !== false ? focusOnMountRef : null,
      closeOnEscapeRef
    ]),
    {
      ...focusOutsideProps,
      tabIndex: -1
    }
  ];
}
var use_dialog_default = useDialog;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-disabled/index.js


function useDisabled({
  isDisabled: isDisabledProp = false
} = {}) {
  return useRefEffect(
    (node) => {
      if (isDisabledProp) {
        return;
      }
      const defaultView = node?.ownerDocument?.defaultView;
      if (!defaultView) {
        return;
      }
      const updates = [];
      const disable = () => {
        node.childNodes.forEach((child) => {
          if (!(child instanceof defaultView.HTMLElement)) {
            return;
          }
          if (!child.getAttribute("inert")) {
            child.setAttribute("inert", "true");
            updates.push(() => {
              child.removeAttribute("inert");
            });
          }
        });
      };
      const debouncedDisable = debounce(disable, 0, {
        leading: true
      });
      disable();
      const observer = new window.MutationObserver(debouncedDisable);
      observer.observe(node, {
        childList: true
      });
      return () => {
        if (observer) {
          observer.disconnect();
        }
        debouncedDisable.cancel();
        updates.forEach((update) => update());
      };
    },
    [isDisabledProp]
  );
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-event/index.js

function useEvent(callback) {
  const ref = (0,external_wp_element_namespaceObject.useRef)(() => {
    throw new Error(
      "Callbacks created with `useEvent` cannot be called during rendering."
    );
  });
  (0,external_wp_element_namespaceObject.useInsertionEffect)(() => {
    ref.current = callback;
  });
  return (0,external_wp_element_namespaceObject.useCallback)(
    (...args) => ref.current?.(...args),
    []
  );
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-isomorphic-layout-effect/index.js

const useIsomorphicLayoutEffect = typeof window !== "undefined" ? external_wp_element_namespaceObject.useLayoutEffect : external_wp_element_namespaceObject.useEffect;
var use_isomorphic_layout_effect_default = useIsomorphicLayoutEffect;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-dragging/index.js


function useDragging({ onDragStart, onDragMove, onDragEnd }) {
  const [isDragging, setIsDragging] = (0,external_wp_element_namespaceObject.useState)(false);
  const eventsRef = (0,external_wp_element_namespaceObject.useRef)({
    onDragStart,
    onDragMove,
    onDragEnd
  });
  use_isomorphic_layout_effect_default(() => {
    eventsRef.current.onDragStart = onDragStart;
    eventsRef.current.onDragMove = onDragMove;
    eventsRef.current.onDragEnd = onDragEnd;
  }, [onDragStart, onDragMove, onDragEnd]);
  const onMouseMove = (0,external_wp_element_namespaceObject.useCallback)(
    (event) => eventsRef.current.onDragMove && eventsRef.current.onDragMove(event),
    []
  );
  const endDrag = (0,external_wp_element_namespaceObject.useCallback)((event) => {
    if (eventsRef.current.onDragEnd) {
      eventsRef.current.onDragEnd(event);
    }
    document.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", endDrag);
    setIsDragging(false);
  }, []);
  const startDrag = (0,external_wp_element_namespaceObject.useCallback)((event) => {
    if (eventsRef.current.onDragStart) {
      eventsRef.current.onDragStart(event);
    }
    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup", endDrag);
    setIsDragging(true);
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    return () => {
      if (isDragging) {
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", endDrag);
      }
    };
  }, [isDragging]);
  return {
    startDrag,
    endDrag,
    isDragging
  };
}


// EXTERNAL MODULE: ./node_modules/mousetrap/mousetrap.js
var mousetrap_mousetrap = __webpack_require__(1933);
var mousetrap_default = /*#__PURE__*/__webpack_require__.n(mousetrap_mousetrap);
// EXTERNAL MODULE: ./node_modules/mousetrap/plugins/global-bind/mousetrap-global-bind.js
var mousetrap_global_bind = __webpack_require__(5760);
;// ./node_modules/@wordpress/compose/build-module/hooks/use-keyboard-shortcut/index.js




function useKeyboardShortcut(shortcuts, callback, {
  bindGlobal = false,
  eventName = "keydown",
  isDisabled = false,
  // This is important for performance considerations.
  target
} = {}) {
  const currentCallbackRef = (0,external_wp_element_namespaceObject.useRef)(callback);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentCallbackRef.current = callback;
  }, [callback]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isDisabled) {
      return;
    }
    const mousetrap = new (mousetrap_default())(
      target && target.current ? target.current : (
        // We were passing `document` here previously, so to successfully cast it to Element we must cast it first to `unknown`.
        // Not sure if this is a mistake but it was the behavior previous to the addition of types so we're just doing what's
        // necessary to maintain the existing behavior.
        /** @type {Element} */
        /** @type {unknown} */
        document
      )
    );
    const shortcutsArray = Array.isArray(shortcuts) ? shortcuts : [shortcuts];
    shortcutsArray.forEach((shortcut) => {
      const keys = shortcut.split("+");
      const modifiers = new Set(
        keys.filter((value) => value.length > 1)
      );
      const hasAlt = modifiers.has("alt");
      const hasShift = modifiers.has("shift");
      if ((0,external_wp_keycodes_namespaceObject.isAppleOS)() && (modifiers.size === 1 && hasAlt || modifiers.size === 2 && hasAlt && hasShift)) {
        throw new Error(
          `Cannot bind ${shortcut}. Alt and Shift+Alt modifiers are reserved for character input.`
        );
      }
      const bindFn = bindGlobal ? "bindGlobal" : "bind";
      mousetrap[bindFn](
        shortcut,
        (...args) => (
          /* eslint-enable jsdoc/valid-types */
          currentCallbackRef.current(...args)
        ),
        eventName
      );
    });
    return () => {
      mousetrap.reset();
    };
  }, [shortcuts, bindGlobal, eventName, target, isDisabled]);
}
var use_keyboard_shortcut_default = useKeyboardShortcut;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-media-query/index.js

const matchMediaCache = /* @__PURE__ */ new Map();
function getMediaQueryList(query) {
  if (!query) {
    return null;
  }
  let match = matchMediaCache.get(query);
  if (match) {
    return match;
  }
  if (typeof window !== "undefined" && typeof window.matchMedia === "function") {
    match = window.matchMedia(query);
    matchMediaCache.set(query, match);
    return match;
  }
  return null;
}
function useMediaQuery(query) {
  const source = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const mediaQueryList = getMediaQueryList(query);
    return {
      /** @type {(onStoreChange: () => void) => () => void} */
      subscribe(onStoreChange) {
        if (!mediaQueryList) {
          return () => {
          };
        }
        mediaQueryList.addEventListener?.("change", onStoreChange);
        return () => {
          mediaQueryList.removeEventListener?.(
            "change",
            onStoreChange
          );
        };
      },
      getValue() {
        return mediaQueryList?.matches ?? false;
      }
    };
  }, [query]);
  return (0,external_wp_element_namespaceObject.useSyncExternalStore)(
    source.subscribe,
    source.getValue,
    () => false
  );
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-previous/index.js

function usePrevious(value) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    ref.current = value;
  }, [value]);
  return ref.current;
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-reduced-motion/index.js

const useReducedMotion = () => useMediaQuery("(prefers-reduced-motion: reduce)");
var use_reduced_motion_default = useReducedMotion;


;// ./node_modules/@wordpress/undo-manager/build-module/index.js

function mergeHistoryChanges(changes1, changes2) {
  const newChanges = { ...changes1 };
  Object.entries(changes2).forEach(([key, value]) => {
    if (newChanges[key]) {
      newChanges[key] = { ...newChanges[key], to: value.to };
    } else {
      newChanges[key] = value;
    }
  });
  return newChanges;
}
const addHistoryChangesIntoRecord = (record, changes) => {
  const existingChangesIndex = record?.findIndex(
    ({ id: recordIdentifier }) => {
      return typeof recordIdentifier === "string" ? recordIdentifier === changes.id : external_wp_isShallowEqual_default()(recordIdentifier, changes.id);
    }
  );
  const nextRecord = [...record];
  if (existingChangesIndex !== -1) {
    nextRecord[existingChangesIndex] = {
      id: changes.id,
      changes: mergeHistoryChanges(
        nextRecord[existingChangesIndex].changes,
        changes.changes
      )
    };
  } else {
    nextRecord.push(changes);
  }
  return nextRecord;
};
function createUndoManager() {
  let history = [];
  let stagedRecord = [];
  let offset = 0;
  const dropPendingRedos = () => {
    history = history.slice(0, offset || void 0);
    offset = 0;
  };
  const appendStagedRecordToLatestHistoryRecord = () => {
    const index = history.length === 0 ? 0 : history.length - 1;
    let latestRecord = history[index] ?? [];
    stagedRecord.forEach((changes) => {
      latestRecord = addHistoryChangesIntoRecord(latestRecord, changes);
    });
    stagedRecord = [];
    history[index] = latestRecord;
  };
  const isRecordEmpty = (record) => {
    const filteredRecord = record.filter(({ changes }) => {
      return Object.values(changes).some(
        ({ from, to }) => typeof from !== "function" && typeof to !== "function" && !external_wp_isShallowEqual_default()(from, to)
      );
    });
    return !filteredRecord.length;
  };
  return {
    addRecord(record, isStaged = false) {
      const isEmpty = !record || isRecordEmpty(record);
      if (isStaged) {
        if (isEmpty) {
          return;
        }
        record.forEach((changes) => {
          stagedRecord = addHistoryChangesIntoRecord(
            stagedRecord,
            changes
          );
        });
      } else {
        dropPendingRedos();
        if (stagedRecord.length) {
          appendStagedRecordToLatestHistoryRecord();
        }
        if (isEmpty) {
          return;
        }
        history.push(record);
      }
    },
    undo() {
      if (stagedRecord.length) {
        dropPendingRedos();
        appendStagedRecordToLatestHistoryRecord();
      }
      const undoRecord = history[history.length - 1 + offset];
      if (!undoRecord) {
        return;
      }
      offset -= 1;
      return undoRecord;
    },
    redo() {
      const redoRecord = history[history.length + offset];
      if (!redoRecord) {
        return;
      }
      offset += 1;
      return redoRecord;
    },
    hasUndo() {
      return !!history[history.length - 1 + offset];
    },
    hasRedo() {
      return !!history[history.length + offset];
    }
  };
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-state-with-history/index.js


function undoRedoReducer(state, action) {
  switch (action.type) {
    case "UNDO": {
      const undoRecord = state.manager.undo();
      if (undoRecord) {
        return {
          ...state,
          value: undoRecord[0].changes.prop.from
        };
      }
      return state;
    }
    case "REDO": {
      const redoRecord = state.manager.redo();
      if (redoRecord) {
        return {
          ...state,
          value: redoRecord[0].changes.prop.to
        };
      }
      return state;
    }
    case "RECORD": {
      state.manager.addRecord(
        [
          {
            id: "object",
            changes: {
              prop: { from: state.value, to: action.value }
            }
          }
        ],
        action.isStaged
      );
      return {
        ...state,
        value: action.value
      };
    }
  }
  return state;
}
function initReducer(value) {
  return {
    manager: createUndoManager(),
    value
  };
}
function useStateWithHistory(initialValue) {
  const [state, dispatch] = (0,external_wp_element_namespaceObject.useReducer)(
    undoRedoReducer,
    initialValue,
    initReducer
  );
  return {
    value: state.value,
    setValue: (0,external_wp_element_namespaceObject.useCallback)((newValue, isStaged) => {
      dispatch({
        type: "RECORD",
        value: newValue,
        isStaged
      });
    }, []),
    hasUndo: state.manager.hasUndo(),
    hasRedo: state.manager.hasRedo(),
    undo: (0,external_wp_element_namespaceObject.useCallback)(() => {
      dispatch({ type: "UNDO" });
    }, []),
    redo: (0,external_wp_element_namespaceObject.useCallback)(() => {
      dispatch({ type: "REDO" });
    }, [])
  };
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-viewport-match/index.js


const BREAKPOINTS = {
  xhuge: 1920,
  huge: 1440,
  wide: 1280,
  xlarge: 1080,
  large: 960,
  medium: 782,
  small: 600,
  mobile: 480
};
const CONDITIONS = {
  ">=": "min-width",
  "<": "max-width"
};
const OPERATOR_EVALUATORS = {
  ">=": (breakpointValue, width) => width >= breakpointValue,
  "<": (breakpointValue, width) => width < breakpointValue
};
const ViewportMatchWidthContext = (0,external_wp_element_namespaceObject.createContext)(
  /** @type {null | number} */
  null
);
ViewportMatchWidthContext.displayName = "ViewportMatchWidthContext";
const useViewportMatch = (breakpoint, operator = ">=") => {
  const simulatedWidth = (0,external_wp_element_namespaceObject.useContext)(ViewportMatchWidthContext);
  const mediaQuery = !simulatedWidth && `(${CONDITIONS[operator]}: ${BREAKPOINTS[breakpoint]}px)`;
  const mediaQueryResult = useMediaQuery(mediaQuery || void 0);
  if (simulatedWidth) {
    return OPERATOR_EVALUATORS[operator](
      BREAKPOINTS[breakpoint],
      simulatedWidth
    );
  }
  return mediaQueryResult;
};
useViewportMatch.__experimentalWidthProvider = ViewportMatchWidthContext.Provider;
var use_viewport_match_default = useViewportMatch;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-resize-observer/use-resize-observer.js


function useResizeObserver(callback, resizeObserverOptions = {}) {
  const callbackEvent = useEvent(callback);
  const observedElementRef = (0,external_wp_element_namespaceObject.useRef)();
  const resizeObserverRef = (0,external_wp_element_namespaceObject.useRef)();
  return useEvent((element) => {
    if (element === observedElementRef.current) {
      return;
    }
    resizeObserverRef.current ??= new ResizeObserver(callbackEvent);
    const { current: resizeObserver } = resizeObserverRef;
    if (observedElementRef.current) {
      resizeObserver.unobserve(observedElementRef.current);
    }
    observedElementRef.current = element;
    if (element) {
      resizeObserver.observe(element, resizeObserverOptions);
    }
  });
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-resize-observer/legacy/index.js



const extractSize = (entry) => {
  let entrySize;
  if (!entry.contentBoxSize) {
    entrySize = [entry.contentRect.width, entry.contentRect.height];
  } else if (entry.contentBoxSize[0]) {
    const contentBoxSize = entry.contentBoxSize[0];
    entrySize = [contentBoxSize.inlineSize, contentBoxSize.blockSize];
  } else {
    const contentBoxSize = entry.contentBoxSize;
    entrySize = [contentBoxSize.inlineSize, contentBoxSize.blockSize];
  }
  const [width, height] = entrySize.map((d) => Math.round(d));
  return { width, height };
};
const RESIZE_ELEMENT_STYLES = {
  position: "absolute",
  top: 0,
  left: 0,
  right: 0,
  bottom: 0,
  pointerEvents: "none",
  opacity: 0,
  overflow: "hidden",
  zIndex: -1
};
function ResizeElement({ onResize }) {
  const resizeElementRef = useResizeObserver((entries) => {
    const newSize = extractSize(entries.at(-1));
    onResize(newSize);
  });
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "div",
    {
      ref: resizeElementRef,
      style: RESIZE_ELEMENT_STYLES,
      "aria-hidden": "true"
    }
  );
}
function sizeEquals(a, b) {
  return a.width === b.width && a.height === b.height;
}
const NULL_SIZE = { width: null, height: null };
function useLegacyResizeObserver() {
  const [size, setSize] = (0,external_wp_element_namespaceObject.useState)(NULL_SIZE);
  const previousSizeRef = (0,external_wp_element_namespaceObject.useRef)(NULL_SIZE);
  const handleResize = (0,external_wp_element_namespaceObject.useCallback)((newSize) => {
    if (!sizeEquals(previousSizeRef.current, newSize)) {
      previousSizeRef.current = newSize;
      setSize(newSize);
    }
  }, []);
  const resizeElement = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ResizeElement, { onResize: handleResize });
  return [resizeElement, size];
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-resize-observer/index.js


function use_resize_observer_useResizeObserver(callback, options = {}) {
  return callback ? useResizeObserver(callback, options) : useLegacyResizeObserver();
}


;// external ["wp","priorityQueue"]
const external_wp_priorityQueue_namespaceObject = window["wp"]["priorityQueue"];
;// ./node_modules/@wordpress/compose/build-module/hooks/use-async-list/index.js


function getFirstItemsPresentInState(list, state) {
  const firstItems = [];
  for (let i = 0; i < list.length; i++) {
    const item = list[i];
    if (!state.includes(item)) {
      break;
    }
    firstItems.push(item);
  }
  return firstItems;
}
function useAsyncList(list, config = { step: 1 }) {
  const { step = 1 } = config;
  const [current, setCurrent] = (0,external_wp_element_namespaceObject.useState)([]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    let firstItems = getFirstItemsPresentInState(list, current);
    if (firstItems.length < step) {
      firstItems = firstItems.concat(
        list.slice(firstItems.length, step)
      );
    }
    setCurrent(firstItems);
    const asyncQueue = (0,external_wp_priorityQueue_namespaceObject.createQueue)();
    for (let i = firstItems.length; i < list.length; i += step) {
      asyncQueue.add({}, () => {
        (0,external_wp_element_namespaceObject.flushSync)(() => {
          setCurrent((state) => [
            ...state,
            ...list.slice(i, i + step)
          ]);
        });
      });
    }
    return () => asyncQueue.reset();
  }, [list]);
  return current;
}
var use_async_list_default = useAsyncList;


;// ./node_modules/@wordpress/compose/build-module/hooks/use-warn-on-change/index.js

function useWarnOnChange(object, prefix = "Change detection") {
  const previousValues = usePrevious(object);
  Object.entries(previousValues ?? []).forEach(([key, value]) => {
    if (value !== object[
      /** @type {keyof typeof object} */
      key
    ]) {
      console.warn(
        `${prefix}: ${key} key changed:`,
        value,
        object[
          /** @type {keyof typeof object} */
          key
        ]
        /* eslint-enable jsdoc/check-types */
      );
    }
  });
}
var use_warn_on_change_default = useWarnOnChange;


;// external "React"
const external_React_namespaceObject = window["React"];
;// ./node_modules/use-memo-one/dist/use-memo-one.esm.js


function areInputsEqual(newInputs, lastInputs) {
  if (newInputs.length !== lastInputs.length) {
    return false;
  }

  for (var i = 0; i < newInputs.length; i++) {
    if (newInputs[i] !== lastInputs[i]) {
      return false;
    }
  }

  return true;
}

function useMemoOne(getResult, inputs) {
  var initial = (0,external_React_namespaceObject.useState)(function () {
    return {
      inputs: inputs,
      result: getResult()
    };
  })[0];
  var isFirstRun = (0,external_React_namespaceObject.useRef)(true);
  var committed = (0,external_React_namespaceObject.useRef)(initial);
  var useCache = isFirstRun.current || Boolean(inputs && committed.current.inputs && areInputsEqual(inputs, committed.current.inputs));
  var cache = useCache ? committed.current : {
    inputs: inputs,
    result: getResult()
  };
  (0,external_React_namespaceObject.useEffect)(function () {
    isFirstRun.current = false;
    committed.current = cache;
  }, [cache]);
  return cache.result;
}
function useCallbackOne(callback, inputs) {
  return useMemoOne(function () {
    return callback;
  }, inputs);
}
var useMemo = (/* unused pure expression or super */ null && (useMemoOne));
var useCallback = (/* unused pure expression or super */ null && (useCallbackOne));



;// ./node_modules/@wordpress/compose/build-module/hooks/use-debounce/index.js



function useDebounce(fn, wait, options) {
  const debounced = useMemoOne(
    () => debounce(fn, wait ?? 0, options),
    [fn, wait, options?.leading, options?.trailing, options?.maxWait]
  );
  (0,external_wp_element_namespaceObject.useEffect)(() => () => debounced.cancel(), [debounced]);
  return debounced;
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-debounced-input/index.js


function useDebouncedInput(defaultValue = "") {
  const [input, setInput] = (0,external_wp_element_namespaceObject.useState)(defaultValue);
  const [debouncedInput, setDebouncedState] = (0,external_wp_element_namespaceObject.useState)(defaultValue);
  const setDebouncedInput = useDebounce(setDebouncedState, 250);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setDebouncedInput(input);
  }, [input, setDebouncedInput]);
  return [input, setInput, debouncedInput];
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-throttle/index.js



function useThrottle(fn, wait, options) {
  const throttled = useMemoOne(
    () => throttle(fn, wait ?? 0, options),
    [fn, wait, options]
  );
  (0,external_wp_element_namespaceObject.useEffect)(() => () => throttled.cancel(), [throttled]);
  return throttled;
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-drop-zone/index.js


function useDropZone({
  dropZoneElement,
  isDisabled,
  onDrop: _onDrop,
  onDragStart: _onDragStart,
  onDragEnter: _onDragEnter,
  onDragLeave: _onDragLeave,
  onDragEnd: _onDragEnd,
  onDragOver: _onDragOver
}) {
  const onDropEvent = useEvent(_onDrop);
  const onDragStartEvent = useEvent(_onDragStart);
  const onDragEnterEvent = useEvent(_onDragEnter);
  const onDragLeaveEvent = useEvent(_onDragLeave);
  const onDragEndEvent = useEvent(_onDragEnd);
  const onDragOverEvent = useEvent(_onDragOver);
  return useRefEffect(
    (elem) => {
      if (isDisabled) {
        return;
      }
      const element = dropZoneElement ?? elem;
      let isDragging = false;
      const { ownerDocument } = element;
      function isElementInZone(targetToCheck) {
        const { defaultView } = ownerDocument;
        if (!targetToCheck || !defaultView || !(targetToCheck instanceof defaultView.HTMLElement) || !element.contains(targetToCheck)) {
          return false;
        }
        let elementToCheck = targetToCheck;
        do {
          if (elementToCheck.dataset.isDropZone) {
            return elementToCheck === element;
          }
        } while (elementToCheck = elementToCheck.parentElement);
        return false;
      }
      function maybeDragStart(event) {
        if (isDragging) {
          return;
        }
        isDragging = true;
        ownerDocument.addEventListener("dragend", maybeDragEnd);
        ownerDocument.addEventListener("mousemove", maybeDragEnd);
        if (_onDragStart) {
          onDragStartEvent(event);
        }
      }
      function onDragEnter(event) {
        event.preventDefault();
        if (element.contains(
          /** @type {Node} */
          event.relatedTarget
        )) {
          return;
        }
        if (_onDragEnter) {
          onDragEnterEvent(event);
        }
      }
      function onDragOver(event) {
        if (!event.defaultPrevented && _onDragOver) {
          onDragOverEvent(event);
        }
        event.preventDefault();
      }
      function onDragLeave(event) {
        if (isElementInZone(event.relatedTarget)) {
          return;
        }
        if (_onDragLeave) {
          onDragLeaveEvent(event);
        }
      }
      function onDrop(event) {
        if (event.defaultPrevented) {
          return;
        }
        event.preventDefault();
        event.dataTransfer && event.dataTransfer.files.length;
        if (_onDrop) {
          onDropEvent(event);
        }
        maybeDragEnd(event);
      }
      function maybeDragEnd(event) {
        if (!isDragging) {
          return;
        }
        isDragging = false;
        ownerDocument.removeEventListener("dragend", maybeDragEnd);
        ownerDocument.removeEventListener("mousemove", maybeDragEnd);
        if (_onDragEnd) {
          onDragEndEvent(event);
        }
      }
      element.setAttribute("data-is-drop-zone", "true");
      element.addEventListener("drop", onDrop);
      element.addEventListener("dragenter", onDragEnter);
      element.addEventListener("dragover", onDragOver);
      element.addEventListener("dragleave", onDragLeave);
      ownerDocument.addEventListener("dragenter", maybeDragStart);
      return () => {
        element.removeAttribute("data-is-drop-zone");
        element.removeEventListener("drop", onDrop);
        element.removeEventListener("dragenter", onDragEnter);
        element.removeEventListener("dragover", onDragOver);
        element.removeEventListener("dragleave", onDragLeave);
        ownerDocument.removeEventListener("dragend", maybeDragEnd);
        ownerDocument.removeEventListener("mousemove", maybeDragEnd);
        ownerDocument.removeEventListener(
          "dragenter",
          maybeDragStart
        );
      };
    },
    [isDisabled, dropZoneElement]
    // Refresh when the passed in dropZoneElement changes.
  );
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-focusable-iframe/index.js

function useFocusableIframe() {
  return useRefEffect((element) => {
    const { ownerDocument } = element;
    if (!ownerDocument) {
      return;
    }
    const { defaultView } = ownerDocument;
    if (!defaultView) {
      return;
    }
    function checkFocus() {
      if (ownerDocument && ownerDocument.activeElement === element) {
        element.focus();
      }
    }
    defaultView.addEventListener("blur", checkFocus);
    return () => {
      defaultView.removeEventListener("blur", checkFocus);
    };
  }, []);
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-fixed-window-list/index.js




const DEFAULT_INIT_WINDOW_SIZE = 30;
function useFixedWindowList(elementRef, itemHeight, totalItems, options) {
  const initWindowSize = options?.initWindowSize ?? DEFAULT_INIT_WINDOW_SIZE;
  const useWindowing = options?.useWindowing ?? true;
  const [fixedListWindow, setFixedListWindow] = (0,external_wp_element_namespaceObject.useState)({
    visibleItems: initWindowSize,
    start: 0,
    end: initWindowSize,
    itemInView: (index) => {
      return index >= 0 && index <= initWindowSize;
    }
  });
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!useWindowing) {
      return;
    }
    const scrollContainer = (0,external_wp_dom_namespaceObject.getScrollContainer)(elementRef.current);
    const measureWindow = (initRender) => {
      if (!scrollContainer) {
        return;
      }
      const visibleItems = Math.ceil(
        scrollContainer.clientHeight / itemHeight
      );
      const windowOverscan = initRender ? visibleItems : options?.windowOverscan ?? visibleItems;
      const firstViewableIndex = Math.floor(
        scrollContainer.scrollTop / itemHeight
      );
      const start = Math.max(0, firstViewableIndex - windowOverscan);
      const end = Math.min(
        totalItems - 1,
        firstViewableIndex + visibleItems + windowOverscan
      );
      setFixedListWindow((lastWindow) => {
        const nextWindow = {
          visibleItems,
          start,
          end,
          itemInView: (index) => {
            return start <= index && index <= end;
          }
        };
        if (lastWindow.start !== nextWindow.start || lastWindow.end !== nextWindow.end || lastWindow.visibleItems !== nextWindow.visibleItems) {
          return nextWindow;
        }
        return lastWindow;
      });
    };
    measureWindow(true);
    const debounceMeasureList = debounce(() => {
      measureWindow();
    }, 16);
    scrollContainer?.addEventListener("scroll", debounceMeasureList);
    scrollContainer?.ownerDocument?.defaultView?.addEventListener(
      "resize",
      debounceMeasureList
    );
    scrollContainer?.ownerDocument?.defaultView?.addEventListener(
      "resize",
      debounceMeasureList
    );
    return () => {
      scrollContainer?.removeEventListener(
        "scroll",
        debounceMeasureList
      );
      scrollContainer?.ownerDocument?.defaultView?.removeEventListener(
        "resize",
        debounceMeasureList
      );
    };
  }, [
    itemHeight,
    elementRef,
    totalItems,
    options?.expandedState,
    options?.windowOverscan,
    useWindowing
  ]);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!useWindowing) {
      return;
    }
    const scrollContainer = (0,external_wp_dom_namespaceObject.getScrollContainer)(elementRef.current);
    const handleKeyDown = (event) => {
      switch (event.keyCode) {
        case external_wp_keycodes_namespaceObject.HOME: {
          return scrollContainer?.scrollTo({ top: 0 });
        }
        case external_wp_keycodes_namespaceObject.END: {
          return scrollContainer?.scrollTo({
            top: totalItems * itemHeight
          });
        }
        case external_wp_keycodes_namespaceObject.PAGEUP: {
          return scrollContainer?.scrollTo({
            top: scrollContainer.scrollTop - fixedListWindow.visibleItems * itemHeight
          });
        }
        case external_wp_keycodes_namespaceObject.PAGEDOWN: {
          return scrollContainer?.scrollTo({
            top: scrollContainer.scrollTop + fixedListWindow.visibleItems * itemHeight
          });
        }
      }
    };
    scrollContainer?.ownerDocument?.defaultView?.addEventListener(
      "keydown",
      handleKeyDown
    );
    return () => {
      scrollContainer?.ownerDocument?.defaultView?.removeEventListener(
        "keydown",
        handleKeyDown
      );
    };
  }, [
    totalItems,
    itemHeight,
    elementRef,
    fixedListWindow.visibleItems,
    useWindowing,
    options?.expandedState
  ]);
  return [fixedListWindow, setFixedListWindow];
}


;// ./node_modules/@wordpress/compose/build-module/hooks/use-observable-value/index.js

function useObservableValue(map, name) {
  const [subscribe, getValue] = (0,external_wp_element_namespaceObject.useMemo)(
    () => [
      (listener) => map.subscribe(name, listener),
      () => map.get(name)
    ],
    [map, name]
  );
  return (0,external_wp_element_namespaceObject.useSyncExternalStore)(subscribe, getValue, getValue);
}


;// ./node_modules/@wordpress/compose/build-module/index.js












































})();

(window.wp = window.wp || {}).compose = __webpack_exports__;
/******/ })()
;