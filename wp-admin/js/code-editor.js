/**
 * @output wp-admin/js/code-editor.js
 */

/* eslint-env es2020 */

if ( 'undefined' === typeof window.wp ) {
	/**
	 * @namespace wp
	 */
	window.wp = {};
}
if ( 'undefined' === typeof window.wp.codeEditor ) {
	/**
	 * @namespace wp.codeEditor
	 */
	window.wp.codeEditor = {};
}

/**
 * @typedef {object} CodeMirrorState
 * @property {boolean} [completionActive] - Whether completion is active.
 * @property {boolean} [focused] - Whether the editor is focused.
 */

/**
 * @typedef {import('codemirror').EditorFromTextArea & {
 *   options: import('codemirror').EditorConfiguration,
 *   performLint?: () => void,
 *   showHint?: (options: import('codemirror').ShowHintOptions) => void,
 *   state: CodeMirrorState
 * }} CodeMirrorEditor
 */

/**
 * @typedef {object} LintAnnotation
 * @property {string} message - Message.
 * @property {'error'|'warning'} severity - Severity.
 * @property {import('codemirror').Position} from - From position.
 * @property {import('codemirror').Position} to - To position.
 */

/**
 * @typedef {object} CodeMirrorTokenState
 * @property {object} [htmlState] - HTML state.
 * @property {string} [htmlState.tagName] - Tag name.
 * @property {CodeMirrorTokenState} [curState] - Current state.
 */

/**
 * @typedef {import('codemirror').EditorConfiguration & {
 *   lint?: boolean | CombinedLintOptions,
 *   autoCloseBrackets?: boolean,
 *   matchBrackets?: boolean,
 *   continueComments?: boolean,
 *   styleActiveLine?: boolean
 * }} CodeMirrorSettings
 */

/**
 * @typedef {object} CSSLintRules
 * @property {boolean} [errors] - Errors.
 * @property {boolean} [box-model] - Box model rules.
 * @property {boolean} [display-property-grouping] - Display property grouping rules.
 * @property {boolean} [duplicate-properties] - Duplicate properties rules.
 * @property {boolean} [known-properties] - Known properties rules.
 * @property {boolean} [outline-none] - Outline none rules.
 */

/**
 * @typedef {object} JSHintRules
 * @property {number} [esversion] - ECMAScript version.
 * @property {boolean} [module] - Whether to use modules.
 * @property {boolean} [boss] - Whether to allow assignments in control expressions.
 * @property {boolean} [curly] - Whether to require curly braces.
 * @property {boolean} [eqeqeq] - Whether to require === and !==.
 * @property {boolean} [eqnull] - Whether to allow == null.
 * @property {boolean} [expr] - Whether to allow expressions.
 * @property {boolean} [immed] - Whether to require immediate function invocation.
 * @property {boolean} [noarg] - Whether to prohibit arguments.caller/callee.
 * @property {boolean} [nonbsp] - Whether to prohibit non-breaking spaces.
 * @property {string} [quotmark] - Quote mark preference.
 * @property {boolean} [undef] - Whether to prohibit undefined variables.
 * @property {boolean} [unused] - Whether to prohibit unused variables.
 * @property {boolean} [browser] - Whether to enable browser globals.
 * @property {Record<string, boolean>} [globals] - Global variables.
 */

/**
 * @typedef {object} HTMLHintRules
 * @property {boolean} [tagname-lowercase] - Tag name lowercase rules.
 * @property {boolean} [attr-lowercase] - Attribute lowercase rules.
 * @property {boolean} [attr-value-double-quotes] - Attribute value double quotes rules.
 * @property {boolean} [doctype-first] - Doctype first rules.
 * @property {boolean} [tag-pair] - Tag pair rules.
 * @property {boolean} [spec-char-escape] - Spec char escape rules.
 * @property {boolean} [id-unique] - ID unique rules.
 * @property {boolean} [src-not-empty] - Src not empty rules.
 * @property {boolean} [attr-no-duplication] - Attribute no duplication rules.
 * @property {boolean} [alt-require] - Alt require rules.
 * @property {string} [space-tab-mixed-disabled] - Space tab mixed disabled rules.
 * @property {boolean} [attr-unsafe-chars] - Attribute unsafe chars rules.
 * @property {JSHintRules} [jshint] - JSHint rules.
 * @property {CSSLintRules} [csslint] - CSSLint rules.
 */

/**
 * Settings for the code editor.
 *
 * @typedef {object} CodeEditorSettings
 *
 * @property {CodeMirrorSettings} [codemirror] - CodeMirror settings.
 * @property {CSSLintRules} [csslint] - CSSLint rules.
 * @property {JSHintRules} [jshint] - JSHint rules.
 * @property {HTMLHintRules} [htmlhint] - HTMLHint rules.
 *
 * @property {(codemirror: CodeMirrorEditor, event: KeyboardEvent|JQuery.KeyDownEvent) => void} [onTabNext] - Callback to handle tabbing to the next tabbable element.
 * @property {(codemirror: CodeMirrorEditor, event: KeyboardEvent|JQuery.KeyDownEvent) => void} [onTabPrevious] - Callback to handle tabbing to the previous tabbable element.
 * @property {(errorAnnotations: LintAnnotation[], annotations: LintAnnotation[], annotationsSorted: LintAnnotation[], cm: CodeMirrorEditor) => void} [onChangeLintingErrors] - Callback for when the linting errors have changed.
 * @property {(errorAnnotations: LintAnnotation[], editor: CodeMirrorEditor) => void} [onUpdateErrorNotice] - Callback for when error notice should be displayed.
 */

/**
 * @typedef {import('codemirror/addon/lint/lint').LintStateOptions<Record<string, unknown>> & JSHintRules & CSSLintRules & { rules?: HTMLHintRules }} CombinedLintOptions
 */

/**
 * @typedef {object} CodeEditorInstance
 * @property {CodeEditorSettings} settings - The code editor settings.
 * @property {CodeMirrorEditor} codemirror - The CodeMirror instance.
 * @property {() => void} updateErrorNotice - Force update the error notice.
 */

/**
 * @typedef {object} WpCodeEditor
 * @property {CodeEditorSettings} defaultSettings - Default settings.
 * @property {(textarea: string|JQuery|Element, settings?: CodeEditorSettings) => CodeEditorInstance} initialize - Initialize.
 */

/**
 * @param {JQueryStatic} $ - jQuery.
 * @param {Object & {
 *   codeEditor: WpCodeEditor,
 *   CodeMirror: typeof import('codemirror'),
 * }} wp - WordPress namespace.
 */
( function( $, wp ) {
	'use strict';

	/**
	 * Default settings for code editor.
	 *
	 * @since 4.9.0
	 * @type {CodeEditorSettings}
	 */
	wp.codeEditor.defaultSettings = {
		codemirror: {},
		csslint: {},
		htmlhint: {},
		jshint: {},
		onTabNext: function() {},
		onTabPrevious: function() {},
		onChangeLintingErrors: function() {},
		onUpdateErrorNotice: function() {},
	};

	/**
	 * Configure linting.
	 *
	 * @param {CodeEditorSettings} settings - Code editor settings.
	 *
	 * @return {LintingController} Linting controller.
	 */
	function configureLinting( settings ) { // eslint-disable-line complexity
		/** @type {LintAnnotation[]} */
		let currentErrorAnnotations = [];

		/** @type {LintAnnotation[]} */
		let previouslyShownErrorAnnotations = [];

		/**
		 * Call the onUpdateErrorNotice if there are new errors to show.
		 *
		 * @param {import('codemirror').Editor} editor - Editor.
		 * @return {void}
		 */
		function updateErrorNotice( editor ) {
			if ( settings.onUpdateErrorNotice && ! _.isEqual( currentErrorAnnotations, previouslyShownErrorAnnotations ) ) {
				settings.onUpdateErrorNotice( currentErrorAnnotations, /** @type {CodeMirrorEditor} */ ( editor ) );
				previouslyShownErrorAnnotations = currentErrorAnnotations;
			}
		}

		/**
		 * Get lint options.
		 *
		 * @return {CombinedLintOptions|false} Lint options.
		 */
		function getLintOptions() { // eslint-disable-line complexity
			/** @type {CombinedLintOptions | boolean} */
			let options = settings.codemirror?.lint ?? false;

			if ( ! options ) {
				return false;
			}

			if ( true === options ) {
				options = {};
			} else if ( _.isObject( options ) ) {
				options = $.extend( {}, options );
			}
			const linterOptions = /** @type {CombinedLintOptions} */ ( options );

			// Configure JSHint.
			if ( 'javascript' === settings.codemirror?.mode && settings.jshint ) {
				$.extend( linterOptions, settings.jshint );
			}

			// Configure CSSLint.
			if ( 'css' === settings.codemirror?.mode && settings.csslint ) {
				$.extend( linterOptions, settings.csslint );
			}

			// Configure HTMLHint.
			if ( 'htmlmixed' === settings.codemirror?.mode && settings.htmlhint ) {
				linterOptions.rules = $.extend( {}, settings.htmlhint );

				if ( settings.jshint && linterOptions.rules ) {
					linterOptions.rules.jshint = settings.jshint;
				}
				if ( settings.csslint && linterOptions.rules ) {
					linterOptions.rules.csslint = settings.csslint;
				}
			}

			// Wrap the onUpdateLinting CodeMirror event to route to onChangeLintingErrors and onUpdateErrorNotice.
			linterOptions.onUpdateLinting = (function( onUpdateLintingOverridden ) {
				/**
				 * @param {LintAnnotation[]} annotations - Annotations.
				 * @param {LintAnnotation[]} annotationsSorted - Sorted annotations.
				 * @param {CodeMirrorEditor} cm - Editor.
				 */
				return function( annotations, annotationsSorted, cm ) {
					const errorAnnotations = annotations.filter( function( annotation ) {
						return 'error' === annotation.severity;
					} );

					if ( onUpdateLintingOverridden ) {
						onUpdateLintingOverridden( annotations, annotationsSorted, cm );
					}

					// Skip if there are no changes to the errors.
					if ( _.isEqual( errorAnnotations, currentErrorAnnotations ) ) {
						return;
					}

					currentErrorAnnotations = errorAnnotations;

					if ( settings.onChangeLintingErrors ) {
						settings.onChangeLintingErrors( errorAnnotations, annotations, annotationsSorted, cm );
					}

					/*
					 * Update notifications when the editor is not focused to prevent error message
					 * from overwhelming the user during input, unless there are now no errors or there
					 * were previously errors shown. In these cases, update immediately so they can know
					 * that they fixed the errors.
					 */
					if ( ! cm.state.focused || 0 === currentErrorAnnotations.length || previouslyShownErrorAnnotations.length > 0 ) {
						updateErrorNotice( cm );
					}
				};
			})( linterOptions.onUpdateLinting );

			return linterOptions;
		}

		return {
			getLintOptions,
			/**
			 * @param {CodeMirrorEditor} editor - Editor instance.
			 * @return {void}
			 */
			init: function( editor ) {
				// Keep lint options populated.
				editor.on( 'optionChange', function( _cm, option ) {
					const gutterName = 'CodeMirror-lint-markers';
					if ( 'lint' !== ( /** @type {string} */ ( option ) ) ) {
						return;
					}
					const gutters = ( /** @type {string[]} */ ( editor.getOption( 'gutters' ) ) ) || [];
					const options = editor.getOption( 'lint' );
					if ( true === options ) {
						if ( ! _.contains( gutters, gutterName ) ) {
							editor.setOption( 'gutters', [ gutterName ].concat( gutters ) );
						}
						editor.setOption( 'lint', getLintOptions() ); // Expand to include linting options.
					} else if ( ! options ) {
						editor.setOption( 'gutters', _.without( gutters, gutterName ) );
					}

					// Force update on error notice to show or hide.
					if ( editor.getOption( 'lint' ) && editor.performLint ) {
						editor.performLint();
					} else {
						currentErrorAnnotations = [];
						updateErrorNotice( editor );
					}
				} );

				// Update error notice when leaving the editor.
				editor.on( 'blur', updateErrorNotice );

				// Work around hint selection with mouse causing focus to leave editor.
				editor.on( 'startCompletion', function() {
					editor.off( 'blur', updateErrorNotice );
				} );
				editor.on( 'endCompletion', function() {
					const editorRefocusWait = 500;
					editor.on( 'blur', updateErrorNotice );

					// Wait for editor to possibly get re-focused after selection.
					_.delay( function() {
						if ( ! editor.state.focused ) {
							updateErrorNotice( editor );
						}
					}, editorRefocusWait );
				} );

				/*
				 * Make sure setting validities are set if the user tries to click Publish
				 * while an autocomplete dropdown is still open. The Customizer will block
				 * saving when a setting has an error notifications on it. This is only
				 * necessary for mouse interactions because keyboards will have already
				 * blurred the field and cause onUpdateErrorNotice to have already been
				 * called.
				 */
				$( document.body ).on( 'mousedown', function( /** @type {JQuery.MouseDownEvent} */ event ) {
					if (
						editor.state.focused &&
						! editor.getWrapperElement().contains( event.target ) &&
						! event.target.classList.contains( 'CodeMirror-hint' )
					) {
						updateErrorNotice( editor );
					}
				} );
			},
			/**
			 * @param {CodeMirrorEditor} editor - Editor instance.
			 * @return {void}
			 */
			updateErrorNotice,
		};
	}

	/**
	 * Configure tabbing.
	 *
	 * @param {CodeMirrorEditor} codemirror - Editor.
	 * @param {CodeEditorSettings} settings - Code editor settings.
	 *
	 * @return {void}
	 */
	function configureTabbing( codemirror, settings ) {
		const $textarea = $( codemirror.getTextArea() );

		codemirror.on( 'blur', function() {
			$textarea.data( 'next-tab-blurs', false );
		});
		codemirror.on( 'keydown', function onKeydown( _editor, event ) {
			// Take note of the ESC keypress so that the next TAB can focus outside the editor.
			if ( 'Escape' === event.key ) {
				$textarea.data( 'next-tab-blurs', true );
				return;
			}

			// Short-circuit if tab key is not being pressed or the tab key press should move focus.
			if ( 'Tab' !== event.key || ! $textarea.data( 'next-tab-blurs' ) ) {
				return;
			}

			// Focus on previous or next focusable item.
			if ( event.shiftKey && settings.onTabPrevious ) {
				settings.onTabPrevious( codemirror, event );
			} else if ( ! event.shiftKey && settings.onTabNext ) {
				settings.onTabNext( codemirror, event );
			}

			// Reset tab state.
			$textarea.data( 'next-tab-blurs', false );

			// Prevent tab character from being added.
			event.preventDefault();
		});
	}

	/**
	 * @typedef {object} LintingController
	 * @property {() => CombinedLintOptions|false} getLintOptions - Get lint options.
	 * @property {(editor: CodeMirrorEditor) => void} init - Initialize.
	 * @property {(editor: import('codemirror').Editor) => void} updateErrorNotice - Update error notice.
	 */

	/**
	 * Initialize Code Editor (CodeMirror) for an existing textarea.
	 *
	 * @since 4.9.0
	 *
	 * @param {string|JQuery<HTMLElement>|HTMLElement} textarea - The HTML id, jQuery object, or DOM Element for the textarea that is used for the editor.
	 * @param {CodeEditorSettings}    [settings] - Settings to override defaults.
	 *
	 * @return {CodeEditorInstance} Instance.
	 */
	wp.codeEditor.initialize = function initialize( textarea, settings ) {
		let $textarea;
		if ( 'string' === typeof textarea ) {
			$textarea = $( '#' + textarea );
		} else {
			$textarea = $( textarea );
		}

		/** @type {CodeEditorSettings} */
		const instanceSettings = $.extend( true, {}, wp.codeEditor.defaultSettings, settings );

		const lintingController = configureLinting( instanceSettings );
		if ( instanceSettings.codemirror ) {
			instanceSettings.codemirror.lint = lintingController.getLintOptions();
		}

		const codemirror = /** @type {CodeMirrorEditor} */ ( wp.CodeMirror.fromTextArea( $textarea[0], instanceSettings.codemirror ) );

		lintingController.init( codemirror );

		/** @type {CodeEditorInstance} */
		const instance = {
			settings: instanceSettings,
			codemirror,
			updateErrorNotice: function() {
				lintingController.updateErrorNotice( codemirror );
			},
		};

		if ( codemirror.showHint ) {
			codemirror.on( 'inputRead', function( _editor, change ) {
				// Only trigger autocompletion for typed input or IME composition.
				if ( ! change.origin || ( '+input' !== change.origin && ! change.origin.startsWith( '*compose' ) ) ) {
					return;
				}

				// Only trigger autocompletion for single-character inputs.
				// The text property is an array of strings, one for each line.
				// We check that there is only one line and that line has only one character.
				if ( 1 !== change.text.length || 1 !== change.text[0].length ) {
					return;
				}

				const char = change.text[0];
				const isAlphaKey = /^[a-zA-Z]$/.test( char );
				if ( codemirror.state.completionActive && isAlphaKey ) {
					return;
				}

				// Prevent autocompletion in string literals or comments.
				const token = /** @type {import('codemirror').Token & { state: CodeMirrorTokenState }} */ ( codemirror.getTokenAt( codemirror.getCursor() ) );
				if ( 'string' === token.type || 'comment' === token.type ) {
					return;
				}

				const innerMode = wp.CodeMirror.innerMode( codemirror.getMode(), token.state ).mode.name;
				const doc = codemirror.getDoc();
				const lineBeforeCursor = doc.getLine( doc.getCursor().line ).slice( 0, doc.getCursor().ch );
				let shouldAutocomplete = false;
				if ( 'html' === innerMode || 'xml' === innerMode ) {
					shouldAutocomplete = (
						'<' === char ||
						( '/' === char && 'tag' === token.type ) ||
						( isAlphaKey && 'tag' === token.type ) ||
						( isAlphaKey && 'attribute' === token.type ) ||
						( '=' === char && !! (
							token.state.htmlState?.tagName ||
							token.state.curState?.htmlState?.tagName
						) )
					);
				} else if ( 'css' === innerMode ) {
					shouldAutocomplete =
						isAlphaKey ||
						':' === char ||
						( ' ' === char && /:\s+$/.test( lineBeforeCursor ) );
				} else if ( 'javascript' === innerMode ) {
					shouldAutocomplete = isAlphaKey || '.' === char;
				} else if ( 'clike' === innerMode && 'php' === codemirror.options.mode ) {
					shouldAutocomplete = isAlphaKey && ( 'keyword' === token.type || 'variable' === token.type );
				}
				if ( shouldAutocomplete ) {
					codemirror.showHint( { completeSingle: false } );
				}
			} );
		}

		// Facilitate tabbing out of the editor.
		configureTabbing( codemirror, instanceSettings );

		return instance;
	};

})( jQuery, window.wp );
