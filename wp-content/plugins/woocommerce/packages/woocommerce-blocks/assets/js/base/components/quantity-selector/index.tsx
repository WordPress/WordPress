/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';
import classNames from 'classnames';
import { useCallback, useLayoutEffect } from '@wordpress/element';
import { DOWN, UP } from '@wordpress/keycodes';
import { useDebouncedCallback } from 'use-debounce';

/**
 * Internal dependencies
 */
import './style.scss';

export interface QuantitySelectorProps {
	/**
	 * Component wrapper classname
	 *
	 * @default 'wc-block-components-quantity-selector'
	 */
	className?: string;
	/**
	 * Current quantity
	 */
	quantity?: number;
	/**
	 * Minimum quantity
	 */
	minimum?: number;
	/**
	 * Maximum quantity
	 */
	maximum: number;
	/**
	 * Input step attribute.
	 */
	step?: number;
	/**
	 * Event handler triggered when the quantity is changed
	 */
	onChange: ( newQuantity: number ) => void;
	/**
	 * Name of the item the quantity selector refers to
	 *
	 * Used for a11y purposes
	 */
	itemName?: string;
	/**
	 * Whether the component should be interactable or not
	 */
	disabled: boolean;
}

const QuantitySelector = ( {
	className,
	quantity = 1,
	minimum = 1,
	maximum,
	onChange = () => void 0,
	step = 1,
	itemName = '',
	disabled,
}: QuantitySelectorProps ): JSX.Element => {
	const classes = classNames(
		'wc-block-components-quantity-selector',
		className
	);

	const hasMaximum = typeof maximum !== 'undefined';
	const canDecrease = quantity - step >= minimum;
	const canIncrease = ! hasMaximum || quantity + step <= maximum;

	/**
	 * The goal of this function is to normalize what was inserted,
	 * but after the customer has stopped typing.
	 */
	const normalizeQuantity = useCallback(
		( initialValue: number ) => {
			// We copy the starting value.
			let value = initialValue;

			// We check if we have a maximum value, and select the lowest between what was inserted and the maximum.
			if ( hasMaximum ) {
				value = Math.min(
					value,
					// the maximum possible value in step increments.
					Math.floor( maximum / step ) * step
				);
			}

			// Select the biggest between what's inserted, the the minimum value in steps.
			value = Math.max( value, Math.ceil( minimum / step ) * step );

			// We round off the value to our steps.
			value = Math.floor( value / step ) * step;

			// Only commit if the value has changed
			if ( value !== initialValue ) {
				onChange( value );
			}
		},
		[ hasMaximum, maximum, minimum, onChange, step ]
	);

	/*
	 * It's important to wait before normalizing or we end up with
	 * a frustrating experience, for example, if the minimum is 2 and
	 * the customer is trying to type "10", premature normalizing would
	 * always kick in at "1" and turn that into 2.
	 */
	const debouncedNormalizeQuantity = useDebouncedCallback(
		normalizeQuantity,
		// This value is deliberately smaller than what's in useStoreCartItemQuantity so we don't end up with two requests.
		300
	);

	/**
	 * Normalize qty on mount before render.
	 */
	useLayoutEffect( () => {
		normalizeQuantity( quantity );
	}, [ quantity, normalizeQuantity ] );

	/**
	 * Handles keyboard up and down keys to change quantity value.
	 *
	 * @param {Object} event event data.
	 */
	const quantityInputOnKeyDown = useCallback(
		( event ) => {
			const isArrowDown =
				typeof event.key !== undefined
					? event.key === 'ArrowDown'
					: event.keyCode === DOWN;
			const isArrowUp =
				typeof event.key !== undefined
					? event.key === 'ArrowUp'
					: event.keyCode === UP;

			if ( isArrowDown && canDecrease ) {
				event.preventDefault();
				onChange( quantity - step );
			}

			if ( isArrowUp && canIncrease ) {
				event.preventDefault();
				onChange( quantity + step );
			}
		},
		[ quantity, onChange, canIncrease, canDecrease, step ]
	);

	return (
		<div className={ classes }>
			<input
				className="wc-block-components-quantity-selector__input"
				disabled={ disabled }
				type="number"
				step={ step }
				min={ minimum }
				max={ maximum }
				value={ quantity }
				onKeyDown={ quantityInputOnKeyDown }
				onChange={ ( event ) => {
					// Inputs values are strings, we parse them here.
					let value = parseInt( event.target.value, 10 );
					// parseInt would throw NaN for anything not a number,
					// so we revert value to the quantity value.
					value = isNaN( value ) ? quantity : value;

					if ( value !== quantity ) {
						// we commit this value immediately.
						onChange( value );
						// but once the customer has stopped typing, we make sure his value is respecting the bounds (maximum value, minimum value, step value), and commit the normalized value.
						debouncedNormalizeQuantity( value );
					}
				} }
				aria-label={ sprintf(
					/* translators: %s refers to the item name in the cart. */
					__(
						'Quantity of %s in your cart.',
						'woo-gutenberg-products-block'
					),
					itemName
				) }
			/>
			<button
				aria-label={ sprintf(
					/* translators: %s refers to the item name in the cart. */
					__(
						'Reduce quantity of %s',
						'woo-gutenberg-products-block'
					),
					itemName
				) }
				className="wc-block-components-quantity-selector__button wc-block-components-quantity-selector__button--minus"
				disabled={ disabled || ! canDecrease }
				onClick={ () => {
					const newQuantity = quantity - step;
					onChange( newQuantity );
					speak(
						sprintf(
							/* translators: %s refers to the item's new quantity in the cart. */
							__(
								'Quantity reduced to %s.',
								'woo-gutenberg-products-block'
							),
							newQuantity
						)
					);
					normalizeQuantity( newQuantity );
				} }
			>
				&#65293;
			</button>
			<button
				aria-label={ sprintf(
					/* translators: %s refers to the item's name in the cart. */
					__(
						'Increase quantity of %s',
						'woo-gutenberg-products-block'
					),
					itemName
				) }
				disabled={ disabled || ! canIncrease }
				className="wc-block-components-quantity-selector__button wc-block-components-quantity-selector__button--plus"
				onClick={ () => {
					const newQuantity = quantity + step;
					onChange( newQuantity );
					speak(
						sprintf(
							/* translators: %s refers to the item's new quantity in the cart. */
							__(
								'Quantity increased to %s.',
								'woo-gutenberg-products-block'
							),
							newQuantity
						)
					);
					normalizeQuantity( newQuantity );
				} }
			>
				&#65291;
			</button>
		</div>
	);
};

export default QuantitySelector;
