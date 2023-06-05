/**
 * External dependencies
 */
import { createContext, useContext, useCallback } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

interface EditorContextType {
	// Indicates whether in the editor context.
	isEditor: boolean;

	// The post ID being edited.
	currentPostId: number;

	// The current view name, if using a view switcher.
	currentView: string;

	// Object containing preview data for the editor.
	previewData: Record< string, unknown >;

	// Get data by name.
	getPreviewData: ( name: string ) => Record< string, unknown >;

	// Indicates whether in the preview context.
	isPreview?: boolean;
}

const EditorContext = createContext( {
	isEditor: false,
	currentPostId: 0,
	currentView: '',
	previewData: {},
	getPreviewData: () => ( {} ),
} as EditorContextType );

export const useEditorContext = (): EditorContextType => {
	return useContext( EditorContext );
};

export const EditorProvider = ( {
	children,
	currentPostId = 0,
	previewData = {},
	currentView = '',
	isPreview = false,
}: {
	children: React.ReactChildren;
	currentPostId?: number | undefined;
	previewData?: Record< string, unknown > | undefined;
	currentView?: string | undefined;
	isPreview?: boolean | undefined;
} ) => {
	const editingPostId = useSelect(
		( select ): number =>
			currentPostId
				? currentPostId
				: select( 'core/editor' ).getCurrentPostId(),
		[ currentPostId ]
	);

	const getPreviewData = useCallback(
		( name: string ): Record< string, unknown > => {
			if ( previewData && name in previewData ) {
				return previewData[ name ] as Record< string, unknown >;
			}
			return {};
		},
		[ previewData ]
	);

	const editorData: EditorContextType = {
		isEditor: true,
		currentPostId: editingPostId,
		currentView,
		previewData,
		getPreviewData,
		isPreview,
	};

	return (
		<EditorContext.Provider value={ editorData }>
			{ children }
		</EditorContext.Provider>
	);
};
