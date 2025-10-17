( function ( wp ) {
        if ( ! wp || ! wp.plugins ) {
                return;
        }

        const { __ } = wp.i18n;
        const { registerPlugin } = wp.plugins;
        const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost || {};
        const { Button, Flex, FlexBlock, Notice, PanelBody, RangeControl, ToggleControl, ToolbarButton, ToolbarGroup } = wp.components;
        const { useDispatch, useSelect } = wp.data;
        const { Fragment, useCallback, useEffect, useMemo, useRef, useState } = wp.element;
        const { BlockPreview, useBlockDisplayInformation } = wp.blockEditor;

        const DEFAULT_BREAKPOINT = 'desktop';
        const CANVAS_META_KEY = ( window.wpLayoutCanvasSettings && window.wpLayoutCanvasSettings.metaKey ) || 'wp_layout_canvas';
        const GRID_DEFAULTS = ( window.wpLayoutCanvasSettings && window.wpLayoutCanvasSettings.gridDefaults ) || { snap: true, size: 10, visible: true };
        const BREAKPOINTS = ( window.wpLayoutCanvasSettings && window.wpLayoutCanvasSettings.breakpoints ) || [ 'desktop', 'tablet', 'mobile' ];
        const ONBOARDING_FLAG = window.wpLayoutCanvasSettings && window.wpLayoutCanvasSettings.onboardingFlag;

        const clamp = ( value, min, max ) => Math.min( Math.max( value, min ), max );

        const formatDimension = ( value, fallback ) => {
                if ( value === undefined || value === null || value === '' ) {
                        return fallback;
                }

                if ( typeof value === 'number' ) {
                        return value + 'px';
                }

                return value;
        };

        const getBreakpointCoordinates = ( layout, breakpoint ) => {
                if ( ! layout ) {
                        return { x: 0, y: 0 };
                }

                if ( breakpoint === DEFAULT_BREAKPOINT ) {
                        return {
                                x: layout.x || 0,
                                y: layout.y || 0,
                        };
                }

                const breakpoints = layout.breakpoints || {};
                const bp = breakpoints[ breakpoint ] || {};

                return {
                        x: bp.x !== undefined ? bp.x : layout.x || 0,
                        y: bp.y !== undefined ? bp.y : layout.y || 0,
                };
        };

        const sanitizeLayoutId = ( value ) => {
                if ( ! value && value !== 0 ) {
                        return '';
                }

                return String( value )
                        .toLowerCase()
                        .replace( /[^a-z0-9_-]/g, '-' )
                        .replace( /-+/g, '-' )
                        .replace( /^-+/, '' )
                        .replace( /-+$/, '' );
        };

        const createLayoutIdFromBlock = ( block ) => {
                const candidate = block && block.clientId ? 'layout-' + block.clientId : 'layout-' + Date.now().toString( 36 );
                const sanitized = sanitizeLayoutId( candidate );
                if ( sanitized ) {
                        return sanitized;
                }
                return 'layout-' + Math.random().toString( 36 ).slice( 2 );
        };

        const cloneLayout = ( layout ) => {
                if ( ! layout ) {
                        return {};
                }

                return JSON.parse( JSON.stringify( layout ) );
        };

        const applyCoordinatesToLayout = ( layout, breakpoint, coords ) => {
                const nextLayout = cloneLayout( layout );
                nextLayout.absolute = true;
                if ( breakpoint === DEFAULT_BREAKPOINT ) {
                        nextLayout.x = coords.x;
                        nextLayout.y = coords.y;
                        return nextLayout;
                }

                const breakpoints = nextLayout.breakpoints || {};
                const existing = breakpoints[ breakpoint ] || {};
                breakpoints[ breakpoint ] = Object.assign( {}, existing, {
                        x: coords.x,
                        y: coords.y,
                } );
                nextLayout.breakpoints = breakpoints;
                return nextLayout;
        };

        const LayoutCanvasItem = ( { block, layout, breakpoint, isSelected, onPointerDown, preview } ) => {
                const ref = useRef();
                const info = useBlockDisplayInformation ? useBlockDisplayInformation( block.clientId ) : null;
                const currentLayout = preview || layout || {};
                const coords = getBreakpointCoordinates( currentLayout, breakpoint );
                const style = {
                        position: 'absolute',
                        left: formatDimension( coords.x, '0px' ),
                        top: formatDimension( coords.y, '0px' ),
                        width: formatDimension( currentLayout.width, 'auto' ),
                        height: formatDimension( currentLayout.height, 'auto' ),
                        zIndex: currentLayout.zIndex || 1,
                };

                return wp.element.createElement(
                        'div',
                        {
                                className: 'wp-layout-designer__item' + ( isSelected ? ' is-selected' : '' ),
                                onPointerDown: ( event ) => onPointerDown( event, block, currentLayout ),
                                ref,
                                role: 'presentation',
                                tabIndex: -1,
                                style,
                                'data-client-id': block.clientId,
                        },
                        wp.element.createElement(
                                'div',
                                { className: 'wp-layout-designer__item-preview' },
                                BlockPreview ? wp.element.createElement( BlockPreview, {
                                        blocks: [ block ],
                                        viewportWidth: typeof currentLayout.width === 'number' ? currentLayout.width : undefined,
                                } ) : null,
                        ),
                        wp.element.createElement(
                                'div',
                                { className: 'wp-layout-designer__item-label' },
                                info && info.title ? info.title : block.name
                        )
                );
        };

        const LayoutCanvasView = ( { isActive, blocks, layouts, breakpoint, onPointerDown, dragPreview, grid, selected } ) => {
                const hostRef = useRef();

                useEffect( () => {
                        if ( ! isActive ) {
                                return;
                        }
                        document.body.classList.add( 'is-layout-designer-active' );
                        return () => {
                                document.body.classList.remove( 'is-layout-designer-active' );
                        };
                }, [ isActive ] );

                if ( ! isActive ) {
                        return null;
                }

                const canvasClass = [ 'wp-layout-designer__canvas' ];
                if ( ! grid.visible ) {
                        canvasClass.push( 'is-hidden-grid' );
                }

                const gridSize = clamp( grid.size || GRID_DEFAULTS.size, 4, 160 );

                return wp.element.createElement(
                        'div',
                        { className: 'wp-layout-designer-host', ref: hostRef },
                        wp.element.createElement(
                                'div',
                                {
                                        className: canvasClass.join( ' ' ),
                                        style: { '--wp-layout-canvas-grid': gridSize + 'px' },
                                },
                                blocks.map( ( block ) => {
                                        const layoutId = sanitizeLayoutId( block.attributes.layoutId ) || sanitizeLayoutId( block.clientId );
                                        const layout = ( layoutId && layouts[ layoutId ] ) || block.attributes.layoutCanvas || {};
                                        const preview = dragPreview && dragPreview.layoutId === layoutId ? dragPreview.layout : undefined;
                                        return wp.element.createElement( LayoutCanvasItem, {
                                                key: block.clientId,
                                                block,
                                                layout,
                                                breakpoint,
                                                onPointerDown,
                                                preview,
                                                isSelected: block.clientId === selected || ( dragPreview && dragPreview.layoutId === layoutId ),
                                        } );
                                } )
                        )
                );
        };

        const LayoutCanvasPlugin = () => {
                const [ isOpen, setIsOpen ] = useState( false );
                const [ breakpoint, setBreakpoint ] = useState( DEFAULT_BREAKPOINT );
                const [ dragPreview, setDragPreview ] = useState( null );

                const { meta, layoutMeta, blocks, selectedBlockClientId, previewDevice } = useSelect( ( select ) => {
                        const editorSelect = select( 'core/editor' );
                        const blockEditorSelect = select( 'core/block-editor' );
                        const editPostSelect = select( 'core/edit-post' );
                        const allMeta = editorSelect.getEditedPostAttribute( 'meta' ) || {};
                        const layoutValue = allMeta[ CANVAS_META_KEY ] || {};
                        return {
                                meta: allMeta,
                                layoutMeta: layoutValue,
                                blocks: blockEditorSelect.getBlocks(),
                                selectedBlockClientId: blockEditorSelect.getSelectedBlockClientId(),
                                previewDevice: editPostSelect && editPostSelect.__experimentalGetPreviewDeviceType ? editPostSelect.__experimentalGetPreviewDeviceType() : null,
                        };
                }, [] );

                const { editPost } = useDispatch( 'core/editor' );
                const { updateBlockAttributes, selectBlock } = useDispatch( 'core/block-editor' );
                const noticesDispatch = useDispatch( 'core/notices' );
                const editPostDispatch = useDispatch( 'core/edit-post' );

                const storedLayouts = useMemo( () => {
                        if ( layoutMeta && typeof layoutMeta === 'object' && layoutMeta.blocks ) {
                                return layoutMeta.blocks;
                        }
                        return {};
                }, [ layoutMeta ] );

                const gridSettings = useMemo( () => {
                        const stored = layoutMeta && layoutMeta.grid ? layoutMeta.grid : {};
                        return Object.assign( {}, GRID_DEFAULTS, stored );
                }, [ layoutMeta ] );

                const persistMeta = useCallback( ( nextValue ) => {
                        const metaValue = Object.assign( {}, meta );
                        metaValue[ CANVAS_META_KEY ] = Object.assign( {}, layoutMeta, nextValue );
                        editPost( { meta: metaValue } );
                }, [ editPost, meta, layoutMeta ] );

                useEffect( () => {
                        if ( previewDevice ) {
                                setBreakpoint( previewDevice.toLowerCase() );
                        }
                }, [ previewDevice ] );

                useEffect( () => {
                        if ( ! isOpen || ! ONBOARDING_FLAG || ! noticesDispatch ) {
                                return;
                        }
                        try {
                                if ( window.localStorage && ! window.localStorage.getItem( ONBOARDING_FLAG ) ) {
                                        noticesDispatch.createNotice( 'info', __( 'Drag blocks on the Layout Designer canvas to position them. Changes are saved automatically.', 'default' ), { isDismissible: true } );
                                        window.localStorage.setItem( ONBOARDING_FLAG, 'yes' );
                                }
                        } catch ( e ) {
                                // Ignore storage failures.
                        }
                }, [ isOpen, noticesDispatch ] );

                useEffect( () => {
                        if ( ! isOpen ) {
                                setDragPreview( null );
                        }
                }, [ isOpen ] );

                useEffect( () => {
                        if ( ! blocks || ! blocks.length ) {
                                return;
                        }
                        const existing = Object.keys( storedLayouts );
                        const layoutIdsInUse = new Set( blocks.map( ( block ) => sanitizeLayoutId( block.attributes.layoutId ) ).filter( Boolean ) );
                        const toRemove = existing.filter( ( id ) => ! layoutIdsInUse.has( id ) );
                        if ( toRemove.length ) {
                                const nextLayouts = Object.assign( {}, storedLayouts );
                                toRemove.forEach( ( id ) => delete nextLayouts[ id ] );
                                persistMeta( { blocks: nextLayouts, grid: gridSettings } );
                        }
                }, [ blocks, storedLayouts, persistMeta, gridSettings ] );

                const commitLayout = useCallback( ( clientId, nextLayout ) => {
                        if ( ! clientId ) {
                                return;
                        }

                        const block = blocks.find( ( item ) => item.clientId === clientId );
                        if ( ! block ) {
                                return;
                        }

                        const currentAttrId = block.attributes && block.attributes.layoutId ? sanitizeLayoutId( block.attributes.layoutId ) : '';
                        let layoutId = currentAttrId;
                        let shouldUpdateId = false;

                        if ( ! layoutId ) {
                                layoutId = createLayoutIdFromBlock( block );
                                shouldUpdateId = true;
                        }

                        const finalLayout = nextLayout ? Object.assign( { absolute: true }, nextLayout ) : null;
                        const attributesToUpdate = { layoutCanvas: finalLayout };

                        if ( shouldUpdateId || currentAttrId !== layoutId ) {
                                attributesToUpdate.layoutId = layoutId;
                        }

                        updateBlockAttributes( clientId, attributesToUpdate );

                        const nextLayouts = Object.assign( {}, storedLayouts );
                        if ( finalLayout ) {
                                nextLayouts[ layoutId ] = finalLayout;
                        } else {
                                delete nextLayouts[ layoutId ];
                        }

                        persistMeta( { blocks: nextLayouts, grid: gridSettings } );
                }, [ blocks, updateBlockAttributes, storedLayouts, persistMeta, gridSettings ] );

                const adjustZIndex = useCallback( ( clientId, delta ) => {
                        const block = blocks.find( ( item ) => item.clientId === clientId );
                        if ( ! block ) {
                                return;
                        }
                        const layoutId = sanitizeLayoutId( block.attributes.layoutId );
                        const layout = ( layoutId && storedLayouts[ layoutId ] ) || block.attributes.layoutCanvas || {};
                        const current = layout.zIndex || 1;
                        const nextLayout = Object.assign( {}, layout, {
                                zIndex: Math.max( 1, current + delta ),
                                absolute: layout.absolute !== false,
                        } );
                        commitLayout( clientId, nextLayout );
                }, [ blocks, storedLayouts, commitLayout ] );

                const resetLayout = useCallback( ( clientId ) => {
                        commitLayout( clientId, null );
                }, [ commitLayout ] );

                const handlePointerDown = useCallback( ( event, block, layout ) => {
                        if ( ! isOpen ) {
                                return;
                        }
                        event.preventDefault();
                        event.stopPropagation();
                        const canvas = event.currentTarget && event.currentTarget.closest( '.wp-layout-designer__canvas' );
                        if ( ! canvas ) {
                                return;
                        }
                        const rect = canvas.getBoundingClientRect();
                        const coords = getBreakpointCoordinates( layout, breakpoint );
                        const layoutId = sanitizeLayoutId( block.attributes.layoutId ) || sanitizeLayoutId( block.clientId );
                        selectBlock( block.clientId );
                        setDragPreview( {
                                clientId: block.clientId,
                                layoutId,
                                layout: layout,
                                initialLayout: layout,
                                offsetX: event.clientX - rect.left - coords.x,
                                offsetY: event.clientY - rect.top - coords.y,
                                container: rect,
                        } );
                }, [ isOpen, breakpoint, selectBlock ] );

                useEffect( () => {
                        if ( ! dragPreview ) {
                                return;
                        }

                        const handlePointerMove = ( event ) => {
                                event.preventDefault();
                                const container = dragPreview.container;
                                if ( ! container ) {
                                        return;
                                }
                                let x = event.clientX - container.left - dragPreview.offsetX;
                                let y = event.clientY - container.top - dragPreview.offsetY;
                                x = Math.max( 0, x );
                                y = Math.max( 0, y );
                                if ( gridSettings.snap ) {
                                        const size = clamp( gridSettings.size || GRID_DEFAULTS.size, 4, 160 );
                                        x = Math.round( x / size ) * size;
                                        y = Math.round( y / size ) * size;
                                }
                                const nextLayout = applyCoordinatesToLayout( dragPreview.initialLayout || {}, breakpoint, { x, y } );
                                setDragPreview( ( state ) => {
                                        if ( ! state ) {
                                                return state;
                                        }
                                        return Object.assign( {}, state, { layout: nextLayout } );
                                } );
                        };

                        const handlePointerUp = () => {
                                if ( dragPreview && dragPreview.layout ) {
                                        commitLayout( dragPreview.clientId, dragPreview.layout );
                                }
                                setDragPreview( null );
                        };

                        window.addEventListener( 'pointermove', handlePointerMove, { passive: false } );
                        window.addEventListener( 'pointerup', handlePointerUp, { passive: true } );

                        return () => {
                                window.removeEventListener( 'pointermove', handlePointerMove );
                                window.removeEventListener( 'pointerup', handlePointerUp );
                        };
                }, [ dragPreview, gridSettings, breakpoint, commitLayout ] );

                const updateGridSettings = useCallback( ( updates ) => {
                        const nextGrid = Object.assign( {}, gridSettings, updates );
                        persistMeta( { blocks: storedLayouts, grid: nextGrid } );
                }, [ gridSettings, persistMeta, storedLayouts ] );

                const toggleBreakpoint = useCallback( ( nextBreakpoint ) => {
                        setBreakpoint( nextBreakpoint );
                        if ( editPostDispatch && editPostDispatch.__experimentalSetPreviewDeviceType ) {
                                const label = nextBreakpoint.charAt( 0 ).toUpperCase() + nextBreakpoint.slice( 1 );
                                editPostDispatch.__experimentalSetPreviewDeviceType( label );
                        }
                }, [ editPostDispatch ] );

                const activeBlockId = selectedBlockClientId || ( dragPreview && dragPreview.clientId );
                const activeLayout = useMemo( () => {
                        if ( ! activeBlockId ) {
                                return null;
                        }
                        const block = blocks.find( ( item ) => item.clientId === activeBlockId );
                        if ( ! block ) {
                                return null;
                        }
                        const layoutId = sanitizeLayoutId( block.attributes.layoutId );
                        if ( layoutId && storedLayouts[ layoutId ] ) {
                                return storedLayouts[ layoutId ];
                        }
                        return block.attributes.layoutCanvas || null;
                }, [ activeBlockId, blocks, storedLayouts ] );

                return wp.element.createElement(
                        Fragment,
                        null,
                        PluginSidebarMoreMenuItem ? wp.element.createElement( PluginSidebarMoreMenuItem, {
                                target: 'layout-designer-sidebar',
                                icon: 'layout',
                                onClick: () => setIsOpen( ( value ) => ! value ),
                        }, __( 'Layout Designer', 'default' ) ) : null,
                        PluginSidebar ? wp.element.createElement( PluginSidebar, {
                                name: 'layout-designer-sidebar',
                                title: __( 'Layout Designer', 'default' ),
                                isOpen,
                                onClose: () => setIsOpen( false ),
                        },
                        wp.element.createElement( PanelBody, { title: __( 'Canvas Controls', 'default' ), initialOpen: true },
                                wp.element.createElement( ToggleControl, {
                                        label: __( 'Snap to grid', 'default' ),
                                        checked: !! gridSettings.snap,
                                        onChange: ( value ) => updateGridSettings( { snap: value } ),
                                } ),
                                wp.element.createElement( ToggleControl, {
                                        label: __( 'Show grid', 'default' ),
                                        checked: !! gridSettings.visible,
                                        onChange: ( value ) => updateGridSettings( { visible: value } ),
                                } ),
                                wp.element.createElement( RangeControl, {
                                        label: __( 'Grid size', 'default' ),
                                        value: gridSettings.size || GRID_DEFAULTS.size,
                                        min: 4,
                                        max: 160,
                                        onChange: ( value ) => updateGridSettings( { size: value } ),
                                } ),
                                wp.element.createElement( 'div', { className: 'wp-layout-designer__toolbar' },
                                        wp.element.createElement( ToolbarGroup, null,
                                                BREAKPOINTS.map( ( label ) => wp.element.createElement( ToolbarButton, {
                                                        key: label,
                                                        isPressed: breakpoint === label,
                                                        onClick: () => toggleBreakpoint( label ),
                                                }, label.charAt( 0 ).toUpperCase() + label.slice( 1 ) ) )
                                        ),
                                        wp.element.createElement( 'span', { className: 'wp-layout-designer__status' },
                                                __( 'Breakpoint', 'default' ), ': ', breakpoint
                                        )
                                ),
                                activeBlockId ? wp.element.createElement( Notice, { status: 'info', isDismissible: false },
                                        __( 'Use the stacking controls to adjust layer order. Undo/redo is supported through the standard editor shortcuts.', 'default' )
                                ) : null,
                        ),
                        activeBlockId ? wp.element.createElement( PanelBody, { title: __( 'Stacking & Reset', 'default' ), initialOpen: true },
                                wp.element.createElement( 'div', { className: 'wp-layout-designer__stack-controls' },
                                        wp.element.createElement( Button, {
                                                variant: 'secondary',
                                                onClick: () => adjustZIndex( activeBlockId, 1 ),
                                        }, __( 'Bring forward', 'default' ) ),
                                        wp.element.createElement( Button, {
                                                variant: 'secondary',
                                                onClick: () => adjustZIndex( activeBlockId, -1 ),
                                        }, __( 'Send backward', 'default' ) ),
                                        wp.element.createElement( Button, {
                                                variant: 'link',
                                                onClick: () => resetLayout( activeBlockId ),
                                        }, __( 'Reset layout', 'default' ) )
                                ),
                                activeLayout ? wp.element.createElement( Flex, { gap: 0 },
                                        wp.element.createElement( FlexBlock, null,
                                                wp.element.createElement( 'strong', null, __( 'X', 'default' ) ),
                                                wp.element.createElement( 'span', null, getBreakpointCoordinates( activeLayout, breakpoint ).x + 'px' )
                                        ),
                                        wp.element.createElement( FlexBlock, null,
                                                wp.element.createElement( 'strong', null, __( 'Y', 'default' ) ),
                                                wp.element.createElement( 'span', null, getBreakpointCoordinates( activeLayout, breakpoint ).y + 'px' )
                                        )
                                ) : null
                        ) : null
                        ) : null,
                        wp.element.createElement( LayoutCanvasView, {
                                isActive: isOpen,
                                blocks,
                                layouts: storedLayouts,
                                breakpoint,
                                onPointerDown: handlePointerDown,
                                dragPreview,
                                grid: gridSettings,
                                selected: selectedBlockClientId,
                        } )
                );
        };

        registerPlugin( 'wp-layout-designer', { render: LayoutCanvasPlugin } );
} )( window.wp || {} );
