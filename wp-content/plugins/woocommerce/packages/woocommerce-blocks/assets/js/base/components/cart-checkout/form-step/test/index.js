/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import FormStep from '..';

describe( 'FormStep', () => {
	test( 'should render a div if no title or legend is provided', () => {
		const component = TestRenderer.create(
			<FormStep>Dolor sit amet</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should apply id and className props', () => {
		const component = TestRenderer.create(
			<FormStep id="my-id" className="my-classname">
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render a fieldset if a legend is provided', () => {
		const component = TestRenderer.create(
			<FormStep legend="Lorem Ipsum 2">Dolor sit amet</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render a fieldset with heading if a title is provided', () => {
		const component = TestRenderer.create(
			<FormStep title="Lorem Ipsum">Dolor sit amet</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'fieldset legend should default to legend prop when title and legend are defined', () => {
		const component = TestRenderer.create(
			<FormStep title="Lorem Ipsum" legend="Lorem Ipsum 2">
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should remove step number CSS class if prop is false', () => {
		const component = TestRenderer.create(
			<FormStep title="Lorem Ipsum" showStepNumber={ false }>
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render step heading content', () => {
		const component = TestRenderer.create(
			<FormStep
				title="Lorem Ipsum"
				stepHeadingContent={ () => (
					<span>Some context to render next to the heading</span>
				) }
			>
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render step description', () => {
		const component = TestRenderer.create(
			<FormStep title="Lorem Ipsum" description="This is the description">
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should set disabled prop to the fieldset element when disabled is true', () => {
		const component = TestRenderer.create(
			<FormStep title="Lorem Ipsum" disabled={ true }>
				Dolor sit amet
			</FormStep>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
