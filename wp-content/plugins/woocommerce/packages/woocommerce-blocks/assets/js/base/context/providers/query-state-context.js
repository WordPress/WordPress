/**
 * External dependencies
 */
import { createContext, useContext } from '@wordpress/element';

/**
 * Query state context is the index for used for a query state store. By
 * exposing this via context, it allows for all children blocks to be
 * synchronized to the same query state defined by the parent in the tree.
 *
 * Defaults to 'page' for general global query state shared among all blocks
 * in a view.
 *
 * @member  {Object}  QueryStateContext A react context object
 */
const QueryStateContext = createContext( 'page' );

export const useQueryStateContext = () => useContext( QueryStateContext );
export const QueryStateContextProvider = QueryStateContext.Provider;
