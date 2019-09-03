
const utils = require( '@wordpress/e2e-test-utils' );

describe( 'Hello World', () => {
	it( 'Should load properly', async () => {

		await utils.visitAdminPage( '/' );



		// const nodes = await page.$x(
		// 	'//h2[contains(text(), "Welcome to WordPress!")]'
		// );
		// expect( nodes.length ).not.toEqual( 0 );
	} );
} );
