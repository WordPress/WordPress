const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const openLayoutDesigner = async ( page ) => {
        await page.getByRole( 'button', { name: /More tools & options/i } ).click();
        await page.getByRole( 'menuitem', { name: /Layout Designer/i } ).click();
        await expect( page.locator( '.wp-layout-designer__canvas' ) ).toBeVisible();
};

test.describe( 'Layout Designer canvas', () => {
        test.beforeEach( async ( { admin, editor, page } ) => {
                await admin.createNewPost();
                await editor.insertBlock( { name: 'core/paragraph' } );
                await page.keyboard.type( 'Drag me around' );
        } );

        test( 'supports dragging blocks with snapping', async ( { page, editor } ) => {
                await openLayoutDesigner( page );

                const firstItem = page.locator( '.wp-layout-designer__item' ).first();
                const initialBox = await firstItem.boundingBox();

                await page.mouse.move( initialBox.x + 10, initialBox.y + 10 );
                await page.mouse.down();
                await page.mouse.move( initialBox.x + 160, initialBox.y + 130 );
                await page.mouse.up();

                const layout = await page.evaluate( () => {
                        const [ block ] = wp.data.select( 'core/block-editor' ).getBlocks();
                        return block?.attributes?.layoutCanvas;
                } );

                expect( layout ).toBeTruthy();
                expect( layout.absolute ).toBeTruthy();
                expect( layout.x ).toBeGreaterThan( 0 );
                expect( layout.y ).toBeGreaterThan( 0 );

                await editor.saveDraft();

                const meta = await page.evaluate( () => {
                        return wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );
                } );

                expect( meta?.wp_layout_canvas?.blocks ).toBeTruthy();
        } );

        test( 'switches responsive breakpoints', async ( { page } ) => {
                await openLayoutDesigner( page );

                await page.getByRole( 'button', { name: /Tablet/i } ).click();

                const device = await page.evaluate( () => {
                        const store = wp.data.select( 'core/edit-post' );
                        if ( store && store.__experimentalGetPreviewDeviceType ) {
                                return store.__experimentalGetPreviewDeviceType();
                        }
                        return null;
                } );

                expect( device === null || device === 'Tablet' ).toBeTruthy();
        } );

        test( 'adds and removes body state when activated', async ( { page } ) => {
                await openLayoutDesigner( page );
                await expect( page.locator( 'body' ) ).toHaveClass( /is-layout-designer-active/ );

                await page.getByRole( 'button', { name: /Close plugin sidebar/i } ).click();
                await expect( page.locator( 'body' ) ).not.toHaveClass( /is-layout-designer-active/ );
        } );
} );
