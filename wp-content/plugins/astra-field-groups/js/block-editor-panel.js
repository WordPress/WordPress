( function( wp, data ) {
    if ( ! data || ! data.groups || ! data.groups.length ) {
        return;
    }

    const { createElement: el, useState } = wp.element;
    const { __ } = wp.i18n;
    const { registerPlugin } = wp.plugins;
    const { PluginDocumentSettingPanel } = wp.editPost;
    const { TextControl, TextareaControl, ToggleControl, Button, Notice, Spinner } = wp.components;
    const apiFetch = wp.apiFetch;

    function FieldControl( { field, value, onChange } ) {
        const baseProps = {
            label: field.label,
            onChange,
            value: value ?? '',
        };

        switch ( field.type ) {
            case 'textarea':
                return el( TextareaControl, {
                    ...baseProps,
                    rows: 4,
                } );
            case 'number':
                return el( TextControl, {
                    ...baseProps,
                    type: 'number',
                } );
            case 'boolean':
                return el( ToggleControl, {
                    label: field.label,
                    checked: !! value,
                    onChange: ( checked ) => onChange( checked ),
                } );
            case 'date':
                return el( TextControl, {
                    ...baseProps,
                    type: 'date',
                } );
            case 'text':
            default:
                return el( TextControl, baseProps );
        }
    }

    function FieldGroupPanel( { group, initial, postId, restNamespace, restNonce, canEdit } ) {
        const [ values, setValues ] = useState( initial || {} );
        const [ saving, setSaving ] = useState( false );
        const [ notice, setNotice ] = useState( null );
        const [ dirty, setDirty ] = useState( false );

        const save = () => {
            if ( ! canEdit ) {
                return;
            }
            setSaving( true );
            setNotice( null );
            apiFetch( {
                path: `${ restNamespace }/field-groups/${ group.id }/values/${ postId }`,
                method: 'POST',
                headers: {
                    'X-WP-Nonce': restNonce,
                },
                data: values,
            } ).then( ( response ) => {
                setValues( response );
                setDirty( false );
                setNotice( { status: 'success', message: __( 'Field group saved.', 'astra-field-groups' ) } );
            } ).catch( () => {
                setNotice( { status: 'error', message: __( 'Unable to save field group.', 'astra-field-groups' ) } );
            } ).finally( () => {
                setSaving( false );
            } );
        };

        const updateValue = ( key, value ) => {
            setValues( { ...values, [ key ]: value } );
            setDirty( true );
        };

        const fields = group.fields || [];

        if ( ! fields.length ) {
            return null;
        }

        return el( PluginDocumentSettingPanel, {
            name: `astra-field-group-${ group.id }`,
            title: group.title,
            className: 'astra-field-group-panel',
        },
            notice && el( Notice, {
                status: notice.status,
                onRemove: () => setNotice( null ),
                isDismissible: true,
            }, notice.message ),
            fields.map( ( field ) => el( FieldControl, {
                key: field.key,
                field,
                value: values[ field.key ],
                onChange: ( next ) => updateValue( field.key, next ),
            } ) ),
            canEdit && el( Button, {
                variant: 'primary',
                onClick: save,
                disabled: saving || ! dirty,
            }, saving ? el( 'span', { className: 'astra-field-group-saving' }, el( Spinner ), ' ', __( 'Saving…', 'astra-field-groups' ) ) : __( 'Save Fields', 'astra-field-groups' ) )
        );
    }

    function Panels() {
        return el( wp.element.Fragment, null,
            data.groups.map( ( group ) => el( FieldGroupPanel, {
                key: group.id,
                group,
                initial: data.values && data.values[ group.id ] ? data.values[ group.id ] : {},
                postId: data.postId,
                restNamespace: data.restNamespace || '/astra/v1',
                restNonce: data.restNonce,
                canEdit: data.canEdit,
            } ) )
        );
    }

    registerPlugin( 'astra-field-group-panel', {
        render: Panels,
        icon: 'feedback',
    } );
}( window.wp, window.AstraFieldGroupsEditorData ) );
