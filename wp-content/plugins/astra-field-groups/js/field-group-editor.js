( function( wp ) {
    const { createElement: el, render, useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const {
        Card,
        CardBody,
        CardHeader,
        CheckboxControl,
        PanelBody,
        SelectControl,
        TextControl,
        ToggleControl,
        Button,
    } = wp.components;

    const FIELD_TYPES = [
        { value: 'text', label: __( 'Text', 'astra-field-groups' ) },
        { value: 'textarea', label: __( 'Textarea', 'astra-field-groups' ) },
        { value: 'number', label: __( 'Number', 'astra-field-groups' ) },
        { value: 'boolean', label: __( 'Toggle', 'astra-field-groups' ) },
        { value: 'date', label: __( 'Date', 'astra-field-groups' ) },
    ];

    function FieldEditor( { field, index, onChange, onRemove, postTypes, onMove, isLast } ) {
        const selectedTypes = field.post_types || [];

        return el(
            Card,
            { key: field.key || index, className: 'astra-field-group-card' },
            el( CardHeader, null,
                el( 'div', { className: 'astra-field-group-card__title' },
                    field.label || __( 'Untitled field', 'astra-field-groups' )
                ),
                el( 'div', { className: 'astra-field-group-card__actions' },
                    el( Button, {
                        icon: 'arrow-up-alt2',
                        label: __( 'Move up', 'astra-field-groups' ),
                        onClick: () => onMove( index, -1 ),
                        disabled: index === 0,
                        isSmall: true,
                        variant: 'secondary',
                    }, __( 'Up', 'astra-field-groups' ) ),
                    el( Button, {
                        icon: 'arrow-down-alt2',
                        label: __( 'Move down', 'astra-field-groups' ),
                        onClick: () => onMove( index, 1 ),
                        disabled: isLast,
                        isSmall: true,
                        variant: 'secondary',
                    }, __( 'Down', 'astra-field-groups' ) ),
                    el( Button, {
                        icon: 'trash',
                        label: __( 'Remove field', 'astra-field-groups' ),
                        onClick: () => onRemove( index ),
                        variant: 'secondary',
                        isDestructive: true,
                        isSmall: true,
                    }, __( 'Remove', 'astra-field-groups' ) )
                )
            ),
            el( CardBody, null,
                el( TextControl, {
                    label: __( 'Field Label', 'astra-field-groups' ),
                    value: field.label,
                    onChange: ( value ) => onChange( index, { ...field, label: value } ),
                } ),
                el( TextControl, {
                    label: __( 'Field Key', 'astra-field-groups' ),
                    help: __( 'Lowercase identifier used to save the value.', 'astra-field-groups' ),
                    value: field.key,
                    onChange: ( value ) => {
                        const sanitized = value.replace( /[^a-z0-9_]/gi, '_' ).toLowerCase();
                        onChange( index, { ...field, key: sanitized } );
                    },
                } ),
                el( SelectControl, {
                    label: __( 'Field Type', 'astra-field-groups' ),
                    value: field.type,
                    options: FIELD_TYPES,
                    onChange: ( value ) => onChange( index, { ...field, type: value } ),
                } ),
                el( ToggleControl, {
                    label: __( 'Required', 'astra-field-groups' ),
                    checked: !! field.required,
                    onChange: ( value ) => onChange( index, { ...field, required: value } ),
                } ),
                el( PanelBody, {
                    title: __( 'Display on Post Types', 'astra-field-groups' ),
                    initialOpen: true,
                },
                    el( 'div', { className: 'astra-field-group-card__post-types' },
                        postTypes.map( ( type ) => el( CheckboxControl, {
                            key: type.name,
                            label: type.label,
                            checked: selectedTypes.includes( type.name ),
                            onChange: ( checked ) => {
                                const next = checked
                                    ? Array.from( new Set( [ ...selectedTypes, type.name ] ) )
                                    : selectedTypes.filter( ( item ) => item !== type.name );
                                onChange( index, { ...field, post_types: next } );
                            },
                        } ) )
                    ),
                    ! postTypes.length && el( 'p', null, __( 'No editable post types available.', 'astra-field-groups' ) )
                )
            )
        );
    }

    function App( { schema, postTypes } ) {
        const [ fields, setFields ] = useState( schema.length ? schema : [] );

        useEffect( () => {
            const hidden = document.getElementById( 'astra-field-group-schema' );
            if ( hidden ) {
                hidden.value = JSON.stringify( fields );
            }
        }, [ fields ] );

        const updateField = ( index, value ) => {
            const next = [ ...fields ];
            next[ index ] = value;
            setFields( next );
        };

        const addField = () => {
            setFields( [
                ...fields,
                {
                    key: '',
                    label: '',
                    type: 'text',
                    required: false,
                    post_types: [],
                },
            ] );
        };

        const removeField = ( index ) => {
            setFields( fields.filter( ( _item, current ) => current !== index ) );
        };

        const moveField = ( index, direction ) => {
            const target = index + direction;
            if ( target < 0 || target >= fields.length ) {
                return;
            }
            const next = [ ...fields ];
            const [ moved ] = next.splice( index, 1 );
            next.splice( target, 0, moved );
            setFields( next );
        };

        return el( 'div', { className: 'astra-field-group-editor-app' },
            fields.map( ( field, index ) => el( FieldEditor, {
                field,
                index,
                key: `${ field.key || 'field' }-${ index }`,
                onChange: updateField,
                onRemove: removeField,
                postTypes,
                onMove: moveField,
                isLast: index === fields.length - 1,
            } ) ),
            el( Button, { variant: 'primary', onClick: addField }, __( 'Add Field', 'astra-field-groups' ) ),
            ! fields.length && el( 'p', { className: 'description' }, __( 'No fields defined. Add a field to begin configuring this group.', 'astra-field-groups' ) )
        );
    }

    document.addEventListener( 'DOMContentLoaded', () => {
        const container = document.getElementById( 'astra-field-group-editor' );
        if ( ! container ) {
            return;
        }
        const schema = JSON.parse( container.dataset.schema || '[]' );
        const postTypes = JSON.parse( container.dataset.postTypes || '[]' );
        render( el( App, { schema, postTypes } ), container );
    } );
}( window.wp ));
