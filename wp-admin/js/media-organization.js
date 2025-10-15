( function ( wp, $, window ) {
        'use strict';

        if ( ! wp || ! wp.media || ! wp.apiRequest ) {
                return;
        }

        const settings = window._wpMediaOrganizationSettings || null;

        if ( ! settings ) {
                return;
        }

        const state = {
                folders: [],
                tags: [],
                selections: {
                        folder: '',
                        tags: []
                },
                ready: false
        };

        const storageKey = 'wpMediaOrganizationFilters';

        try {
                const stored = window.sessionStorage.getItem( storageKey );

                if ( stored ) {
                        const parsed = JSON.parse( stored );

                        if ( parsed && 'object' === typeof parsed ) {
                                state.selections.folder = parsed.folder || '';
                                state.selections.tags = Array.isArray( parsed.tags ) ? parsed.tags : [];
                        }
                }
        } catch ( error ) {
                // Session storage is optional.
        }

        const request = function ( url, options ) {
                const requestOptions = options || {};

                requestOptions.url = url;
                requestOptions.headers = requestOptions.headers || {};
                requestOptions.headers['X-WP-Nonce'] = settings.nonce;

                return wp.apiRequest( requestOptions );
        };

        const persistSelections = function () {
                try {
                        window.sessionStorage.setItem( storageKey, JSON.stringify( state.selections ) );
                } catch ( error ) {
                        // Ignore persistence issues.
                }
        };

        const flattenFolders = function ( folders, depth, list ) {
                folders.forEach( function ( folder ) {
                        list.push( {
                                id: folder.id,
                                label: Array( depth + 1 ).join( '\u2014 ' ) + folder.name
                        } );

                        if ( Array.isArray( folder.children ) && folder.children.length ) {
                                flattenFolders( folder.children, depth + 1, list );
                        }
                } );
        };

        const renderFolderOptions = function ( selectEl ) {
                selectEl.innerHTML = '';

                const defaultOption = document.createElement( 'option' );
                defaultOption.value = '';
                defaultOption.textContent = settings.l10n.allFolders;
                selectEl.appendChild( defaultOption );

                const flattened = [];
                flattenFolders( state.folders, 0, flattened );

                flattened.forEach( function ( item ) {
                        const option = document.createElement( 'option' );
                        option.value = String( item.id );
                        option.textContent = item.label;
                        selectEl.appendChild( option );
                } );

                selectEl.value = state.selections.folder ? String( state.selections.folder ) : '';
        };

        const renderTagOptions = function ( selectEl ) {
                selectEl.innerHTML = '';

                const defaultOption = document.createElement( 'option' );
                defaultOption.value = '';
                defaultOption.textContent = settings.l10n.allTags;
                selectEl.appendChild( defaultOption );

                state.tags.forEach( function ( tag ) {
                        const option = document.createElement( 'option' );
                        option.value = String( tag.id );
                        option.textContent = tag.name;
                        selectEl.appendChild( option );
                } );

                Array.from( selectEl.options ).forEach( function ( option ) {
                        if ( option.value && state.selections.tags.includes( parseInt( option.value, 10 ) ) ) {
                                option.selected = true;
                        }
                } );
        };

        const getActiveFrame = function () {
                return wp.media && wp.media.frame ? wp.media.frame : null;
        };

        const updateFrameQuery = function () {
                const frame = getActiveFrame();

                if ( ! frame || ! frame.state || 'function' !== typeof frame.state ) {
                        return;
                }

                const stateObject = frame.state();

                if ( ! stateObject || 'function' !== typeof stateObject.get ) {
                        return;
                }

                const library = stateObject.get( 'library' );

                if ( ! library || ! library.props ) {
                        return;
                }

                if ( state.selections.folder ) {
                        library.props.set( 'media_folder', [ parseInt( state.selections.folder, 10 ) ] );
                } else {
                        library.props.unset( 'media_folder' );
                }

                if ( state.selections.tags.length ) {
                        library.props.set( 'media_tag', state.selections.tags.map( function ( value ) {
                                return parseInt( value, 10 );
                        } ) );
                } else {
                        library.props.unset( 'media_tag' );
                }

                if ( 'function' === typeof library.props.unset ) {
                        library.props.unset( 'paged' );
                }

                if ( 'function' === typeof library.fetch ) {
                        library.fetch( { reset: true } );
                }
        };

        const buildFilters = function ( toolbar ) {
                if ( toolbar.querySelector( '.media-organization-filters' ) ) {
                        return;
                }

                const container = document.createElement( 'div' );
                container.className = 'media-organization-filters';

                const folderLabel = document.createElement( 'label' );
                folderLabel.className = 'screen-reader-text';
                folderLabel.textContent = settings.l10n.folders;
                folderLabel.htmlFor = 'media-organization-folder-filter';
                container.appendChild( folderLabel );

                const folderSelect = document.createElement( 'select' );
                folderSelect.id = 'media-organization-folder-filter';
                folderSelect.className = 'media-organization-folder';
                renderFolderOptions( folderSelect );
                container.appendChild( folderSelect );

                const tagLabel = document.createElement( 'label' );
                tagLabel.className = 'screen-reader-text';
                tagLabel.textContent = settings.l10n.tags;
                tagLabel.htmlFor = 'media-organization-tag-filter';
                container.appendChild( tagLabel );

                const tagSelect = document.createElement( 'select' );
                tagSelect.id = 'media-organization-tag-filter';
                tagSelect.className = 'media-organization-tags';
                tagSelect.multiple = true;
                tagSelect.size = 4;
                renderTagOptions( tagSelect );
                container.appendChild( tagSelect );

                folderSelect.addEventListener( 'change', function () {
                        state.selections.folder = this.value;
                        persistSelections();
                        updateFrameQuery();
                } );

                tagSelect.addEventListener( 'change', function () {
                        const selected = Array.from( this.options )
                                .filter( function ( option ) {
                                        return option.selected && option.value;
                                } )
                                .map( function ( option ) {
                                        return parseInt( option.value, 10 );
                                } );

                        state.selections.tags = selected;
                        persistSelections();
                        updateFrameQuery();
                } );

                toolbar.appendChild( container );
        };

        const refreshFilters = function () {
                document.querySelectorAll( '.media-frame .media-toolbar-secondary' ).forEach( function ( toolbar ) {
                        buildFilters( toolbar );
                } );
        };

        const initialize = function () {
                Promise.all( [
                        request( settings.routes.folders ),
                        request( settings.routes.tags )
                ] ).then( function ( responses ) {
                        state.folders = Array.isArray( responses[0] ) ? responses[0] : [];
                        state.tags = Array.isArray( responses[1] ) ? responses[1] : [];
                        state.ready = true;
                        refreshFilters();
                        updateFrameQuery();
                } ).catch( function () {
                        state.ready = true;
                        refreshFilters();
                } );
        };

        const observer = new MutationObserver( function () {
                if ( state.ready ) {
                        refreshFilters();
                }
        } );

        observer.observe( document.body, { childList: true, subtree: true } );

        document.addEventListener( 'DOMContentLoaded', function () {
                if ( state.ready ) {
                        refreshFilters();
                }
        } );

        let trackedFrame = null;

        const monitorFrame = function () {
                const currentFrame = getActiveFrame();

                if ( currentFrame && currentFrame !== trackedFrame ) {
                        trackedFrame = currentFrame;
                        setTimeout( function () {
                                refreshFilters();
                                updateFrameQuery();
                        }, 50 );
                }

                window.requestAnimationFrame( monitorFrame );
        };

        window.requestAnimationFrame( monitorFrame );

        initialize();
} )( window.wp, window.jQuery, window );
