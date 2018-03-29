/*! $.noUiSlider
 @version 5.0.0
 @author Leon Gersen https://twitter.com/LeonGersen
 @license WTFPL http://www.wtfpl.net/about/
 @documentation http://refreshless.com/nouislider/
*/

// ==ClosureCompiler==
// @externs_url http://refreshless.com/externs/jquery-1.8.js
// @compilation_level ADVANCED_OPTIMIZATIONS
// @warning_level VERBOSE
// ==/ClosureCompiler==

/*jshint laxcomma: true */
/*jshint smarttabs: true */
/*jshint sub: true */

/*jslint browser: true */
/*jslint continue: true */
/*jslint plusplus: true */
/*jslint white: true */
/*jslint sub: true */

(function( $ ){

	'use strict';

	if ( $['zepto'] && !$.fn.removeData ) {
		throw new ReferenceError('Zepto is loaded without the data module.');
	}

	$.fn['noUiSlider'] = function( options, rebuild ){

		var
		// Cache the document and body selectors;
		 doc = $(document)
		,body = $('body')

		// Namespace for binding and unbinding slider events;
		,namespace = '.nui'

		// Copy of the current value function;
		,$VAL = $.fn.val

		// Re-usable list of classes;
		,clsList = [
		/*  0 */  'noUi-base'
		/*  1 */ ,'noUi-origin'
		/*  2 */ ,'noUi-handle'
		/*  3 */ ,'noUi-input'
		/*  4 */ ,'noUi-active'
		/*  5 */ ,'noUi-state-tap'
		/*  6 */ ,'noUi-target'
		/*  7 */ ,'-lower'
		/*  8 */ ,'-upper'
		/*  9 */ ,'noUi-connect'
		/* 10 */ ,'noUi-horizontal'
		/* 11 */ ,'noUi-vertical'
		/* 12 */ ,'noUi-background'
		/* 13 */ ,'noUi-stacking'
		/* 14 */ ,'noUi-block'
		/* 15 */ ,'noUi-state-blocked'
		/* 16 */ ,'noUi-ltr'
		/* 17 */ ,'noUi-rtl'
		/* 18 */ ,'noUi-dragable'
		/* 19 */ ,'noUi-extended'
		/* 20 */ ,'noUi-state-drag'
		]

		// Determine the events to bind. IE11 implements pointerEvents without
		// a prefix, which breaks compatibility with the IE10 implementation.
		,actions = window.navigator['pointerEnabled'] ? {
			 start: 'pointerdown'
			,move: 'pointermove'
			,end: 'pointerup'
		} : window.navigator['msPointerEnabled'] ? {
			 start: 'MSPointerDown'
			,move: 'MSPointerMove'
			,end: 'MSPointerUp'
		} : {
			 start: 'mousedown touchstart'
			,move: 'mousemove touchmove'
			,end: 'mouseup touchend'
		};


// Percentage calculation

	// (percentage) How many percent is this value of this range?
		function fromPercentage ( range, value ) {
			return (value * 100) / ( range[1] - range[0] );
		}

	// (percentage) Where is this value on this range?
		function toPercentage ( range, value ) {
			return fromPercentage( range, range[0] < 0 ?
				value + Math.abs(range[0]) :
					value - range[0] );
		}

	// (value) How much is this percentage on this range?
		function isPercentage ( range, value ) {
			return ((value * ( range[1] - range[0] )) / 100) + range[0];
		}


// Type tests

	// Test in an object is an instance of jQuery or Zepto.
		function isInstance ( a ) {
			return a instanceof $ || ( $['zepto'] && $['zepto']['isZ'](a) );
		}

	// Checks whether a value is numerical.
		function isNumeric ( a ) {
			return !isNaN( parseFloat( a ) ) && isFinite( a );
		}


// General helper functions

	// Test an array of objects, and calls them if they are a function.
		function call ( functions, scope ) {

			// Allow the passing of an unwrapped function.
			// Leaves other code a more comprehensible.
			if( !$.isArray( functions ) ){
				functions = [ functions ];
			}

			$.each( functions, function(){
				if (typeof this === 'function') {
					this.call(scope);
				}
			});
		}

	// Returns a proxy to set a target using the public value method.
		function setN ( target, number ) {

			return function(){

				// Determine the correct position to set,
				// leave the other one unchanged.
				var val = [null, null];
				val[ number ] = $(this).val();

				// Trigger the 'set' callback
				target.val(val, true);
			};
		}

	// Round a value to the closest 'to'.
		function closest ( value, to ){
			return Math.round(value / to) * to;
		}

	// Format output value to specified standards.
		function format ( value, options ) {

			// Round the value to the resolution that was set
			// with the serialization options.
			value = value.toFixed( options['decimals'] );

			// Rounding away decimals might cause a value of -0
			// when using very small ranges. Remove those cases.
			if ( parseFloat(value) === 0 ) {
				value = value.replace('-0', '0');
			}

			// Apply the proper decimal mark to the value.
			return value.replace( '.', options['serialization']['mark'] );
		}

	// Determine the handle closest to an event.
		function closestHandle ( handles, location, style ) {

			if ( handles.length === 1 ) {
				return handles[0];
			}

			var total = handles[0].offset()[style] +
						handles[1].offset()[style];

			return handles[ location < total / 2 ? 0 : 1 ];
		}

	// Round away small numbers in floating point implementation.
		function digits ( value, round ) {
			return parseFloat(value.toFixed(round));
		}

// Event abstraction

	// Provide a clean event with standardized offset values.
		function fixEvent ( e ) {

			// Prevent scrolling and panning on touch events, while
			// attempting to slide. The tap event also depends on this.
			e.preventDefault();

			// Filter the event to register the type, which can be
			// touch, mouse or pointer. Offset changes need to be
			// made on an event specific basis.
			var  touch = e.type.indexOf('touch') === 0
				,mouse = e.type.indexOf('mouse') === 0
				,pointer = e.type.indexOf('pointer') === 0
				,x,y, event = e;

			// IE10 implemented pointer events with a prefix;
			if ( e.type.indexOf('MSPointer') === 0 ) {
				pointer = true;
			}

			// Get the originalEvent, if the event has been wrapped
			// by jQuery. Zepto doesn't wrap the event.
			if ( e.originalEvent ) {
				e = e.originalEvent;
			}

			if ( touch ) {
				// noUiSlider supports one movement at a time,
				// so we can select the first 'changedTouch'.
				x = e.changedTouches[0].pageX;
				y = e.changedTouches[0].pageY;
			}
			if ( mouse || pointer ) {

				// Polyfill the pageXOffset and pageYOffset
				// variables for IE7 and IE8;
				if( !pointer && window.pageXOffset === undefined ){
					window.pageXOffset = document.documentElement.scrollLeft;
					window.pageYOffset = document.documentElement.scrollTop;
				}

				x = e.clientX + window.pageXOffset;
				y = e.clientY + window.pageYOffset;
			}

			return $.extend( event, {
				 'pointX': x
				,'pointY': y
				,cursor: mouse
			});
		}

	// Handler for attaching events trough a proxy
		function attach ( events, element, callback, pass ) {

			var target = pass.target;

			// Add the noUiSlider namespace to all events.
			events = events.replace( /\s/g, namespace + ' ' ) + namespace;

			// Bind a closure on the target.
			return element.on( events, function( e ){

				// jQuery and Zepto handle unset attributes differently.
				var disabled = target.attr('disabled');
					disabled = !( disabled === undefined || disabled === null );

				// Test if there is anything that should prevent an event
				// from being handled, such as a disabled state or an active
				// 'tap' transition.
				if( target.hasClass('noUi-state-tap') || disabled ) {
					return false;
				}

				// Call the event handler with three arguments:
				// - The event;
				// - An object with data for the event;
				// - The slider options;
				// Having the slider options as a function parameter prevents
				// getting it in every function, which muddies things up.
				callback (
					 fixEvent( e )
					,pass
					,target.data('base').data('options')
				);
			});
		}


// Serialization and value storage

	// Store a value on all serialization targets, or get the current value.
		function serialize ( a ) {

			/*jshint validthis: true */

			// Re-scope target for availability within .each;
			var target = this.target;

			// Get the value for this handle
			if ( a === undefined ) {
				return this.element.data('value');
			}

			// Write the value to all serialization objects
			// or store a new value on the handle
			if ( a === true ) {
				a = this.element.data('value');
			} else {
				this.element.data('value', a);
			}

			// Prevent a serialization call if the value wasn't initialized.
			if ( a === undefined ) {
				return;
			}

			// If the provided element was a function,
			// call it with the slider as scope. Otherwise,
			// simply call the function on the object.
			$.each( this.elements, function() {
				if ( typeof this === 'function' ) {
					this.call(target, a);
				} else {
					this[0][this[1]](a);
				}
			});
		}

	// Map serialization to [ element, method ]. Attach events where required.
		function storeElement ( handle, item, number ) {

			// Add a change event to the supplied jQuery objects,
			// which triggers the value-setting function on the target.
			if ( isInstance( item ) ) {

				var elements = [], target = handle.data('target');

				// Link the field to the other handle if the
				// slider is inverted.
				if ( handle.data('options').direction ) {
					number = number ? 0 : 1;
				}

				// Loop all items so the change event is properly bound,
				// and the items can individually be added to the array.
				item.each(function(){

					// Bind the change event.
					$(this).on('change' + namespace, setN( target, number ));

					// Store the element with the proper handler.
					elements.push([ $(this), 'val' ]);
				});

				return elements;
			}

			// Append a new input to the noUiSlider base.
			// Prevent the change event from flowing upward.
			if ( typeof item === 'string' ) {

				item = [ $('<input type="hidden" name="'+ item +'">')
					.appendTo(handle)
					.addClass(clsList[3])
					.change(function ( e ) {
						e.stopPropagation();
					}), 'val'];
			}

			return [item];
		}

	// Access point and abstraction for serialization.
		function store ( handle, i, serialization ) {

			var elements = [];

			// Loops all items in the provided serialization setting,
			// add the proper events to them or create new input fields,
			// and add them as data to the handle so they can be kept
			// in sync with the slider value.
			$.each( serialization['to'][i], function( index ){
				elements = elements.concat(
					storeElement( handle, serialization['to'][i][index], i )
				);
			});

			return {
				 element: handle
				,elements: elements
				,target: handle.data('target')
				,'val': serialize
			};
		}


// Handle placement

	// Fire callback on unsuccessful handle movement.
		function block ( base, stateless ) {

			var target = base.data('target');

			if ( !target.hasClass(clsList[14]) ){

				// The visual effects should not always be applied.
				if ( !stateless ) {
					target.addClass(clsList[15]);
					setTimeout(function(){
						target.removeClass(clsList[15]);
					}, 450);
				}

				target.addClass(clsList[14]);
				call( base.data('options').block, target );
			}
		}

	// Change inline style and apply proper classes.
		function placeHandle ( handle, to ) {

			var settings = handle.data('options');

			to = digits(to, 7);

			// If the slider can move, remove the class
			// indicating the block state.
			handle.data('target').removeClass(clsList[14]);

			// Set handle to new location
			handle.css( settings['style'], to + '%' ).data('pct', to);

			// Force proper handle stacking
			if ( handle.is(':first-child') ) {
				handle.toggleClass(clsList[13], to > 50 );
			}

			if ( settings['direction'] ) {
				to = 100 - to;
			}

			// Write the value to the serialization object.
			handle.data('store').val(
				format ( isPercentage( settings['range'], to ), settings )
			);
		}

	// Test suggested values and apply margin, step.
		function setHandle ( handle, to ) {

			var base = handle.data('base'), settings = base.data('options'),
				handles = base.data('handles'), lower = 0, upper = 100;

			// Catch invalid user input
			if ( !isNumeric( to ) ){
				return false;
			}

			// Handle the step option.
			if ( settings['step'] ){
				to = closest( to, settings['step'] );
			}

			if ( handles.length > 1 ){
				if ( handle[0] !== handles[0][0] ) {
					lower = digits(handles[0].data('pct')+settings['margin'],7);
				} else {
					upper = digits(handles[1].data('pct')-settings['margin'],7);
				}
			}

			// Limit position to boundaries. When the handles aren't set yet,
			// they return -1 as a percentage value.
			to = Math.min( Math.max( to, lower ), upper < 0 ? 100 : upper );

			// Stop handling this call if the handle can't move past another.
			// Return an array containing the hit limit, so the caller can
			// provide feedback. ( block callback ).
			if ( to === handle.data('pct') ) {
				return [!lower ? false : lower, upper === 100 ? false : upper];
			}

			placeHandle ( handle, to );
			return true;
		}

	// Handles movement by tapping
		function jump ( base, handle, to, callbacks ) {

			// Flag the slider as it is now in a transitional state.
			// Transition takes 300 ms, so re-enable the slider afterwards.
			base.addClass(clsList[5]);
			setTimeout(function(){
				base.removeClass(clsList[5]);
			}, 300);

			// Move the handle to the new position.
			setHandle( handle, to );

			// Trigger the 'slide' and 'set' callbacks,
			// pass the target so that it is 'this'.
			call( callbacks, base.data('target') );

			base.data('target').change();
		}


// Event handlers

	// Handle movement on document for handle and range drag.
		function move ( event, Dt, Op ) {

			// Map event movement to a slider percentage.
			var handles = Dt.handles, limits,
				proposal = event[ Dt.point ] - Dt.start[ Dt.point ];

			proposal = ( proposal * 100 ) / Dt.size;

			if ( handles.length === 1 ) {

				// Run handle placement, receive true for success or an
				// array with potential limits.
				limits = setHandle( handles[0], Dt.positions[0] + proposal );

				if ( limits !== true ) {

					if ( $.inArray ( handles[0].data('pct'), limits ) >= 0 ){
						block ( Dt.base, !Op['margin'] );
					}
					return;
				}

			} else {

				// Dragging the range could be implemented by forcing the
				// 'move' event on both handles, but this solution proved
				// lagging on slower devices, resulting in range errors. The
				// slightly ugly solution below is considerably faster, and
				// it can't move the handle out of sync. Bypass the standard
				// setting method, as other checks are needed.

				var l1, u1, l2, u2;

				// Round the proposal to the step setting.
				if ( Op['step'] ) {
					proposal = closest( proposal, Op['step'] );
				}

				// Determine the new position, store it twice. Once for
				// limiting, once for checking whether placement should occur.
				l1 = l2 = Dt.positions[0] + proposal;
				u1 = u2 = Dt.positions[1] + proposal;

				// Round the values within a sensible range.
				if ( l1 < 0 ) {
					u1 += -1 * l1;
					l1 = 0;
				} else if ( u1 > 100 ) {
					l1 -= ( u1 - 100 );
					u1 = 100;
				}

				// Don't perform placement if no handles are to be changed.
				// Check if the lowest value is set to zero.
				if ( l2 < 0 && !l1 && !handles[0].data('pct') ) {
					return;
				}
				// The highest value is limited to 100%.
				if ( u1 === 100 && u2 > 100 && handles[1].data('pct') === 100 ){
					return;
				}

				placeHandle ( handles[0], l1 );
				placeHandle ( handles[1], u1 );
			}

			// Trigger the 'slide' event, if the handle was moved.
			call( Op['slide'], Dt.target );
		}

	// Unbind move events on document, call callbacks.
		function end ( event, Dt, Op ) {

			// The handle is no longer active, so remove the class.
			if ( Dt.handles.length === 1 ) {
				Dt.handles[0].data('grab').removeClass(clsList[4]);
			}

			// Remove cursor styles and text-selection events bound to the body.
			if ( event.cursor ) {
				body.css('cursor', '').off( namespace );
			}

			// Unbind the move and end events, which are added on 'start'.
			doc.off( namespace );

			// Trigger the change event.
			Dt.target.removeClass( clsList[14] +' '+ clsList[20]).change();

			// Trigger the 'end' callback.
			call( Op['set'], Dt.target );
		}

	// Bind move events on document.
		function start ( event, Dt, Op ) {

			// Mark the handle as 'active' so it can be styled.
			if( Dt.handles.length === 1 ) {
				Dt.handles[0].data('grab').addClass(clsList[4]);
			}

			// A drag should never propagate up to the 'tap' event.
			event.stopPropagation();

			// Attach the move event.
			attach ( actions.move, doc, move, {
				 start: event
				,base: Dt.base
				,target: Dt.target
				,handles: Dt.handles
				,positions: [ Dt.handles[0].data('pct')
					   ,Dt.handles[ Dt.handles.length - 1 ].data('pct') ]
				,point: Op['orientation'] ? 'pointY' : 'pointX'
				,size: Op['orientation'] ? Dt.base.height() : Dt.base.width()
			});

			// Unbind all movement when the drag ends.
			attach ( actions.end, doc, end, {
				 target: Dt.target
				,handles: Dt.handles
			});

			// Text selection isn't an issue on touch devices,
			// so adding additional callbacks isn't required.
			if ( event.cursor ) {

				// Prevent the 'I' cursor and extend the range-drag cursor.
				body.css('cursor', $(event.target).css('cursor'));

				// Mark the target with a dragging state.
				if ( Dt.handles.length > 1 ) {
					Dt.target.addClass(clsList[20]);
				}

				// Prevent text selection when dragging the handles.
				body.on('selectstart' + namespace, function( ){
					return false;
				});
			}
		}

	// Move closest handle to tapped location.
		function tap ( event, Dt, Op ) {

			var base = Dt.base, handle, to, point, size;

			// The tap event shouldn't propagate up to trigger 'edge'.
			event.stopPropagation();

			// Determine the direction of the slider.
			if ( Op['orientation'] ) {
				point = event['pointY'];
				size = base.height();
			} else {
				point = event['pointX'];
				size = base.width();
			}

			// Find the closest handle and calculate the tapped point.
			handle = closestHandle( base.data('handles'), point, Op['style'] );
			to = (( point - base.offset()[ Op['style'] ] ) * 100 ) / size;

			// The set handle to the new position.
			jump( base, handle, to, [ Op['slide'], Op['set'] ]);
		}

	// Move handle to edges when target gets tapped.
		function edge ( event, Dt, Op ) {

			var handles = Dt.base.data('handles'), to, i;

			i = Op['orientation'] ? event['pointY'] : event['pointX'];
			i = i < Dt.base.offset()[Op['style']];

			to = i ? 0 : 100;
			i = i ? 0 : handles.length - 1;

			jump ( Dt.base, handles[i], to, [ Op['slide'], Op['set'] ]);
		}

// API

	// Validate and standardize input.
		function test ( input, sliders ){

	/*	Every input option is tested and parsed. This'll prevent
		endless validation in internal methods. These tests are
		structured with an item for every option available. An
		option can be marked as required by setting the 'r' flag.
		The testing function is provided with three arguments:
			- The provided value for the option;
			- A reference to the options object;
			- The name for the option;

		The testing function returns false when an error is detected,
		or true when everything is OK. It can also modify the option
		object, to make sure all values can be correctly looped elsewhere. */

			function values ( a ) {

				if ( a.length !== 2 ){
					return false;
				}

				// Convert the array to floats
				a = [ parseFloat(a[0]), parseFloat(a[1]) ];

				// Test if all values are numerical
				if( !isNumeric(a[0]) || !isNumeric(a[1]) ){
					return false;
				}

				// The lowest value must really be the lowest value.
				if( a[1] < a[0] ){
					return false;
				}

				return a;
			}

			var serialization = {
				 resolution: function(q,o){

					// Parse the syntactic sugar that is the serialization
					// resolution option to a usable integer.
					// Checking for a string '1', since the resolution needs
					// to be cast to a string to split in on the period.
					switch( q ){
						case 1:
						case 0.1:
						case 0.01:
						case 0.001:
						case 0.0001:
						case 0.00001:
							q = q.toString().split('.');
							o['decimals'] = q[0] === '1' ? 0 : q[1].length;
							break;
						case undefined:
							o['decimals'] = 2;
							break;
						default:
							return false;
					}

					return true;
				}
				,mark: function(q,o,w){

					if ( !q ) {
						o[w]['mark'] = '.';
						return true;
					}

					switch( q ){
						case '.':
						case ',':
							return true;
						default:
							return false;
					}
				}
				,to: function(q,o,w){

					// Checks whether a variable is a candidate to be a
					// valid serialization target.
					function ser(r){
						return isInstance ( r ) ||
							typeof r === 'string' ||
							typeof r === 'function' ||
							r === false ||
							( isInstance ( r[0] ) &&
							  typeof r[0][r[1]] === 'function' );
					}

					// Flatten the serialization array into a reliable
					// set of elements, which can be tested and looped.
					function filter ( value ) {

						var items = [[],[]];

						// If a single value is provided it can be pushed
						// immediately.
						if ( ser(value) ) {
							items[0].push(value);
						} else {

							// Otherwise, determine whether this is an
							// array of single elements or sets.
							$.each(value, function(i, val) {

								// Don't handle an overflow of elements.
								if( i > 1 ){
									return;
								}

								// Decide if this is a group or not
								if( ser(val) ){
									items[i].push(val);
								} else {
									items[i] = items[i].concat(val);
								}
							});
						}

						return items;
					}

					if ( !q ) {
						o[w]['to'] = [[],[]];
					} else {

						var i, j;

						// Flatten the serialization array
						q = filter ( q );

						// Reverse the API for RTL sliders.
						if ( o['direction'] && q[1].length ) {
							q.reverse();
						}

						// Test all elements in the flattened array.
						for ( i = 0; i < o['handles']; i++ ) {
							for ( j = 0; j < q[i].length; j++ ) {

								// Return false on invalid input
								if( !ser(q[i][j]) ){
									return false;
								}

								// Remove 'false' elements, since those
								// won't be handled anyway.
								if( !q[i][j] ){
									q[i].splice(j, 1);
								}
							}
						}

						// Write the new values back
						o[w]['to'] = q;
					}

					return true;
				}
			}, tests = {
				/*	Handles.
				 *	Has default, can be 1 or 2.
				 */
				 'handles': {
					 'r': true
					,'t': function(q){
						q = parseInt(q, 10);
						return ( q === 1 || q === 2 );
					}
				}
				/*	Range.
				 *	Must be an array of two numerical floats,
				 *	which can't be identical.
				 */
				,'range': {
					 'r': true
					,'t': function(q,o,w){

						o[w] = values(q);

						// The values can't be identical.
						return o[w] && o[w][0] !== o[w][1];
					}
				 }
				/*	Start.
				 *	Must be an array of two numerical floats when handles = 2;
				 *	Uses 'range' test.
				 *	When handles = 1, a single float is also allowed.
				 */
				,'start': {
					 'r': true
					,'t': function(q,o,w){
						if( o['handles'] === 1 ){
							if( $.isArray(q) ){
								q = q[0];
							}
							q = parseFloat(q);
							o.start = [q];
							return isNumeric(q);
						}

						o[w] = values(q);
						return !!o[w];
					}
				}
				/*	Connect.
				 *	Must be true or false when handles = 2;
				 *	Can use 'lower' and 'upper' when handles = 1.
				 */
				,'connect': {
					 'r': true
					,'t': function(q,o,w){

						if ( q === 'lower' ) {
							o[w] = 1;
						} else if ( q === 'upper' ) {
							o[w] = 2;
						} else if ( q === true ) {
							o[w] = 3;
						} else if ( q === false ) {
							o[w] = 0;
						} else {
							return false;
						}

						return true;
					}
				}
				/*	Connect.
				 *	Will default to horizontal, not required.
				 */
				,'orientation': {
					 't': function(q,o,w){
						switch (q){
							case 'horizontal':
								o[w] = 0;
								break;
							case 'vertical':
								o[w] = 1;
								break;
							default: return false;
						}
						return true;
					}
				}
				/*	Margin.
				 *	Must be a float, has a default value.
				 */
				,'margin': {
					 'r': true
					,'t': function(q,o,w){
						q = parseFloat(q);
						o[w] = fromPercentage(o['range'], q);
						return isNumeric(q);
					}
				}
				/*	Direction.
				 *	Required, can be 'ltr' or 'rtl'.
				 */
				,'direction': {
					 'r': true
					,'t': function(q,o,w){

						switch ( q ) {
							case 'ltr': o[w] = 0;
								break;
							case 'rtl': o[w] = 1;
								// Invert connection for RTL sliders;
								o['connect'] = [0,2,1,3][o['connect']];
								break;
							default:
								return false;
						}

						return true;
					}
				}
				/*	Behaviour.
				 *	Required, defines responses to tapping and
				 *	dragging elements.
				 */
				,'behaviour': {
					 'r': true
					,'t': function(q,o,w){

						o[w] = {
							 'tap': q !== (q = q.replace('tap', ''))
							,'extend': q !== (q = q.replace('extend', ''))
							,'drag': q !== (q = q.replace('drag', ''))
							,'fixed': q !== (q = q.replace('fixed', ''))
						};

						return !q.replace('none','').replace(/\-/g,'');
					}
				}
				/*	Serialization.
				 *	Required, but has default. Must be an array
				 *	when using two handles, can be a single value when using
				 *	one handle. 'mark' can be period (.) or comma (,).
				 */
				,'serialization': {
					 'r': true
					,'t': function(q,o,w){

						return serialization.to( q['to'], o, w ) &&
							   serialization.resolution( q['resolution'], o ) &&
							   serialization.mark( q['mark'], o, w );
					}
				}
				/*	Slide.
				 *	Not required. Must be a function.
				 */
				,'slide': {
					 't': function(q){
						return $.isFunction(q);
					}
				}
				/*	Set.
				 *	Not required. Must be a function.
				 *	Tested using the 'slide' test.
				 */
				,'set': {
					 't': function(q){
						return $.isFunction(q);
					}
				}
				/*	Block.
				 *	Not required. Must be a function.
				 *	Tested using the 'slide' test.
				 */
				,'block': {
					 't': function(q){
						return $.isFunction(q);
					}
				}
				/*	Step.
				 *	Not required.
				 */
				,'step': {
					 't': function(q,o,w){
						q = parseFloat(q);
						o[w] = fromPercentage ( o['range'], q );
						return isNumeric(q);
					}
				}
			};

			$.each( tests, function( name, test ){

				/*jslint devel: true */

				var value = input[name], isSet = value !== undefined;

				// If the value is required but not set, fail.
				if( ( test['r'] && !isSet ) ||
				// If the test returns false, fail.
					( isSet && !test['t']( value, input, name ) ) ){

					// For debugging purposes it might be very useful to know
					// what option caused the trouble. Since throwing an error
					// will prevent further script execution, log the error
					// first. Test for console, as it might not be available.
					if( console && console.log && console.group ){
						console.group( 'Invalid noUiSlider initialisation:' );
						console.log( 'Option:\t', name );
						console.log( 'Value:\t', value );
						console.log( 'Slider(s):\t', sliders );
						console.groupEnd();
					}

					throw new RangeError('noUiSlider');
				}
			});
		}

	// Parse options, add classes, attach events, create HTML.
		function create ( options ) {

			/*jshint validthis: true */

			// Store the original set of options on all targets,
			// so they can be re-used and re-tested later.
			// Make sure to break the relation with the options,
			// which will be changed by the 'test' function.
			this.data('options', $.extend(true, {}, options));

			// Set defaults where applicable;
			options = $.extend({
				 'handles': 2
				,'margin': 0
				,'connect': false
				,'direction': 'ltr'
				,'behaviour': 'tap'
				,'orientation': 'horizontal'
			}, options);

			// Make sure the test for serialization runs.
			options['serialization'] = options['serialization'] || {};

			// Run all options through a testing mechanism to ensure correct
			// input. The test function will throw errors, so there is
			// no need to capture the result of this call. It should be noted
			// that options might get modified to be handled properly. E.g.
			// wrapping integers in arrays.
			test( options, this );

			// Pre-define the styles.
			options['style'] = options['orientation'] ? 'top' : 'left';

			return this.each(function(){

				var target = $(this), i, dragable, handles = [], handle,
					base = $('<div/>').appendTo(target);

				// Throw an error if the slider was already initialized.
                                if ( target.data('base') ) {
					throw new Error('Slider was already initialized.');
				}

				// Apply classes and data to the target.
				target.data('base', base).addClass([
					clsList[6]
				   ,clsList[16 + options['direction']]
				   ,clsList[10 + options['orientation']] ].join(' '));

				for (i = 0; i < options['handles']; i++ ) {

					handle = $('<div><div/></div>').appendTo(base);

					// Add all default and option-specific classes to the
					// origins and handles.
					handle.addClass( clsList[1] );

					handle.children().addClass([
						clsList[2]
					   ,clsList[2] + clsList[ 7 + options['direction'] +
						( options['direction'] ? -1 * i : i ) ]].join(' ') );

					// Make sure every handle has access to all variables.
					handle.data({
						 'base': base
						,'target': target
						,'options': options
						,'grab': handle.children()
						,'pct': -1
					}).attr('data-style', options['style']);

					// Every handle has a storage point, which takes care
					// of triggering the proper serialization callbacks.
					handle.data({
						'store': store(handle, i, options['serialization'])
					});

					// Store handles on the base
					handles.push(handle);
				}

				// Apply the required connection classes to the elements
				// that need them. Some classes are made up for several
				// segments listed in the class list, to allow easy
				// renaming and provide a minor compression benefit.
				switch ( options['connect'] ) {
					case 1:	target.addClass( clsList[9] );
							handles[0].addClass( clsList[12] );
							break;
					case 3: handles[1].addClass( clsList[12] );
							/* falls through */
					case 2: handles[0].addClass( clsList[9] );
							/* falls through */
					case 0: target.addClass(clsList[12]);
							break;
				}

				// Merge base classes with default,
				// and store relevant data on the base element.
				base.addClass( clsList[0] ).data({
					 'target': target
					,'options': options
					,'handles': handles
				});

				// Use the public value method to set the start values.
				target.val( options['start'] );

				// Attach the standard drag event to the handles.
				if ( !options['behaviour']['fixed'] ) {
					for ( i = 0; i < handles.length; i++ ) {

						// These events are only bound to the visual handle
						// element, not the 'real' origin element.
						attach ( actions.start, handles[i].children(), start, {
							 base: base
							,target: target
							,handles: [ handles[i] ]
						});
					}
				}

				// Attach the tap event to the slider base.
				if ( options['behaviour']['tap'] ) {
					attach ( actions.start, base, tap, {
						 base: base
						,target: target
					});
				}

				// Extend tapping behaviour to target
				if ( options['behaviour']['extend'] ) {

					target.addClass( clsList[19] );

					if ( options['behaviour']['tap'] ) {
						attach ( actions.start, target, edge, {
							 base: base
							,target: target
						});
					}
				}

				// Make the range dragable.
				if ( options['behaviour']['drag'] ){

					dragable = base.find('.'+clsList[9]).addClass(clsList[18]);

					// When the range is fixed, the entire range can
					// be dragged by the handles. The handle in the first
					// origin will propagate the start event upward,
					// but it needs to be bound manually on the other.
					if ( options['behaviour']['fixed'] ) {
						dragable = dragable
							.add( base.children().not(dragable).data('grab') );
					}

					attach ( actions.start, dragable, start, {
						 base: base
						,target: target
						,handles: handles
					});
				}
			});
		}

	// Return value for the slider, relative to 'range'.
		function getValue ( ) {

			/*jshint validthis: true */

			var base = $(this).data('base'), answer = [];

			// Loop the handles, and get the value from the input
			// for every handle on its' own.
			$.each( base.data('handles'), function(){
				answer.push( $(this).data('store').val() );
			});

			// If the slider has just one handle, return a single value.
			// Otherwise, return an array, which is in reverse order
			// if the slider is used RTL.
			if ( answer.length === 1 ) {
				return answer[0];
			}

			if ( base.data('options').direction ) {
				return answer.reverse();
			}

			return answer;
		}

	// Set value for the slider, relative to 'range'.
		function setValue ( args, set ) {

			/*jshint validthis: true */

			// If the value is to be set to a number, which is valid
			// when using a one-handle slider, wrap it in an array.
			if( !$.isArray(args) ){
				args = [args];
			}

			// Setting is handled properly for each slider in the data set.
			return this.each(function(){

				var b = $(this).data('base'), to, i,
					handles = Array.prototype.slice.call(b.data('handles'),0),
					settings = b.data('options');

				// If there are multiple handles to be set run the setting
				// mechanism twice for the first handle, to make sure it
				// can be bounced of the second one properly.
				if ( handles.length > 1) {
					handles[2] = handles[0];
				}

				// The RTL settings is implemented by reversing the front-end,
				// internal mechanisms are the same.
				if ( settings['direction'] ) {
					args.reverse();
				}

				for ( i = 0; i < handles.length; i++ ){

					// Calculate a new position for the handle.
					to = args[ i%2 ];

					// The set request might want to ignore this handle.
					// Test for 'undefined' too, as a two-handle slider
					// can still be set with an integer.
					if( to === null || to === undefined ) {
						continue;
					}

					// Add support for the comma (,) as a decimal symbol.
					// Replace it by a period so it is handled properly by
					// parseFloat. Omitting this would result in a removal
					// of decimals. This way, the developer can also
					// input a comma separated string.
					if( $.type(to) === 'string' ) {
						to = to.replace(',', '.');
					}

					// Calculate the new handle position
					to = toPercentage( settings['range'], parseFloat( to ) );

					// Invert the value if this is an right-to-left slider.
					if ( settings['direction'] ) {
						to = 100 - to;
					}

					// If the value of the input doesn't match the slider,
					// reset it. Sometimes the input is changed to a value the
					// slider has rejected. This can occur when using 'select'
					// or 'input[type="number"]' elements. In this case, set
					// the value back to the input.
					if ( setHandle( handles[i], to ) !== true ){
						handles[i].data('store').val( true );
					}

					// Optionally trigger the 'set' event.
					if( set === true ) {
						call( settings['set'], $(this) );
					}
				}
			});
		}

	// Unbind all attached events, remove classed and HTML.
		function destroy ( target ) {

			// Start the list of elements to be unbound with the target.
			var elements = [[target,'']];

			// Get the fields bound to both handles.
			$.each(target.data('base').data('handles'), function(){
				elements = elements.concat( $(this).data('store').elements );
			});

			// Remove all events added by noUiSlider.
			$.each(elements, function(){
				if( this.length > 1 ){
					this[0].off( namespace );
				}
			});

			// Remove all classes from the target.
			target.removeClass(clsList.join(' '));

			// Empty the target and remove all data.
			target.empty().removeData('base options');
		}

	// Merge options with current initialization, destroy slider
	// and reinitialize.
		function build ( options ) {

			/*jshint validthis: true */

			return this.each(function(){

				// When uninitialised, jQuery will return '',
				// Zepto returns undefined. Both are falsy.
				var values = $(this).val() || false,
					current = $(this).data('options'),
				// Extend the current setup with the new options.
					setup = $.extend( {}, current, options );

				// If there was a slider initialised, remove it first.
				if ( values !== false ) {
					destroy( $(this) );
				}

				// Make the destroy method publicly accessible.
				if( !options ) {
					return;
				}

				// Create a new slider
				$(this)['noUiSlider']( setup );

				// Set the slider values back. If the start options changed,
				// it gets precedence.
				if ( values !== false && setup.start === current.start ) {
					$(this).val( values );
				}
			});
		}

	// Overwrite the native jQuery value function
	// with a simple handler. noUiSlider will use the internal
	// value method, anything else will use the standard method.
		$.fn.val = function(){

			// If the function is called without arguments,
			// act as a 'getter'. Call the getValue function
			// in the same scope as this call.
			if ( this.hasClass( clsList[6] ) ){
				return arguments.length ?
					setValue.apply( this, arguments ) :
					getValue.apply( this );
			}

			// If this isn't noUiSlider, continue with jQuery's
			// original method.
			return $VAL.apply( this, arguments );
		};

		return ( rebuild ? build : create ).call( this, options );
	};

}( window['jQuery'] || window['Zepto'] ));
