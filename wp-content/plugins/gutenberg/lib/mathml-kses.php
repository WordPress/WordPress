<?php
/**
 * Add MathML elements and attributes to wp_kses_allowed_html.
 *
 * @package gutenberg
 */

/**
 * Add MathML elements to the allowed tags array.
 *
 * This enables MathML content (e.g. converted from LaTeX, but also directly
 * imported or pasted) to pass through WordPress's content sanitization.
 *
 * @param array $allowedtags The allowed tags.
 *
 * @return array The allowed tags with MathML elements added.
 */
function gutenberg_kses_allow_mathml( $allowedtags ) {
	// https://www.w3.org/TR/mathml-core/#global-attributes
	// Except common attributes added by _wp_add_global_attributes.
	$math_global_attributes = array(
		'displaystyle'   => true,
		'scriptlevel'    => true,
		'mathbackground' => true,
		'mathcolor'      => true,
		'mathsize'       => true,
		// Common attributes also defined by _wp_add_global_attributes.
		// We do not want to add all those global attributes though.
		'class'          => true,
		'data-*'         => true,
		'dir'            => true,
		'id'             => true,
		'style'          => true,
	);

	$math_overunder_attributes = array(
		'accentunder' => true,
		'accent'      => true,
	);

	return array_merge(
		$allowedtags,
		array(
			// https://www.w3.org/TR/mathml-core/#the-top-level-math-element
			'math'          => array_merge(
				$math_global_attributes,
				array(
					'display' => true,
				)
			),

			// https://www.w3.org/TR/mathml-core/#token-elements
			// https://www.w3.org/TR/mathml-core/#text-mtext
			'mtext'         => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#the-mi-element
			'mi'            => array_merge(
				$math_global_attributes,
				array(
					'mathvariant' => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#number-mn
			'mn'            => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#operator-fence-separator-or-accent-mo
			'mo'            => array_merge(
				$math_global_attributes,
				array(
					'form'          => true,
					'fence'         => true,
					'separator'     => true,
					'lspace'        => true,
					'rspace'        => true,
					'stretchy'      => true,
					'symmetric'     => true,
					'maxsize'       => true,
					'minsize'       => true,
					'largeop'       => true,
					'movablelimits' => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#space-mspace
			'mspace'        => array_merge(
				$math_global_attributes,
				array(
					'width'  => true,
					'height' => true,
					'depth'  => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#string-literal-ms
			'ms'            => $math_global_attributes,

			// https://www.w3.org/TR/mathml-core/#general-layout-schemata
			// https://www.w3.org/TR/mathml-core/#horizontally-group-sub-expressions-mrow
			'mrow'          => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#fractions-mfrac
			'mfrac'         => array_merge(
				$math_global_attributes,
				array(
					'linethickness' => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#radicals-msqrt-mroot
			'msqrt'         => $math_global_attributes,
			'mroot'         => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#style-change-mstyle
			'mstyle'        => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#error-message-merror
			'merror'        => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#adjust-space-around-content-mpadded
			'mpadded'       => array_merge(
				$math_global_attributes,
				array(
					'width'   => true,
					'height'  => true,
					'depth'   => true,
					'lspace'  => true,
					'voffset' => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#making-sub-expressions-invisible-mphantom
			'mphantom'      => $math_global_attributes,

			// https://www.w3.org/TR/mathml-core/#script-and-limit-schemata
			// https://www.w3.org/TR/mathml-core/#subscripts-and-superscripts-msub-msup-msubsup
			'msub'          => $math_global_attributes,
			'msup'          => $math_global_attributes,
			'msubsup'       => $math_global_attributes,
			// https://www.w3.org/TR/mathml-core/#underscripts-and-overscripts-munder-mover-munderover
			'munder'        => array_merge( $math_global_attributes, $math_overunder_attributes ),
			'mover'         => array_merge( $math_global_attributes, $math_overunder_attributes ),
			'munderover'    => array_merge( $math_global_attributes, $math_overunder_attributes ),
			// https://www.w3.org/TR/mathml-core/#prescripts-and-tensor-indices-mmultiscripts
			'mmultiscripts' => $math_global_attributes,
			'mprescripts'   => $math_global_attributes,

			// https://www.w3.org/TR/mathml-core/#tabular-math
			// https://www.w3.org/TR/mathml-core/#table-or-matrix-mtable
			'mtable'        => array_merge(
				$math_global_attributes,
				array(
					// Non-standard, used by temml/katex.
					// https://developer.mozilla.org/en-US/docs/Web/MathML/Reference/Element/mtable
					'columnalign'   => true,
					'rowspacing'    => true,
					'columnspacing' => true,
					'align'         => true,
					'rowalign'      => true,
					'columnlines'   => true,
					'rowlines'      => true,
					'frame'         => true,
					'framespacing'  => true,
					'width'         => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#row-in-table-or-matrix-mtr
			'mtr'           => array_merge(
				$math_global_attributes,
				array(
					// Non-standard, used by temml/katex.
					// https://developer.mozilla.org/en-US/docs/Web/MathML/Reference/Element/mtr
					'columnalign' => true,
					'rowalign'    => true,
				)
			),
			// https://www.w3.org/TR/mathml-core/#entry-in-table-or-matrix-mtd
			'mtd'           => array_merge(
				$math_global_attributes,
				array(
					'columnspan'  => true,
					'rowspan'     => true,
					// Non-standard, used by temml/katex.
					// https://developer.mozilla.org/en-US/docs/Web/MathML/Reference/Element/mtd
					'columnalign' => true,
					'rowalign'    => true,
				)
			),

			// https://www.w3.org/TR/mathml-core/#semantics-and-presentation
			'semantics'     => $math_global_attributes,
			'annotation'    => array_merge(
				$math_global_attributes,
				array(
					'encoding' => true,
				)
			),

			// Non-standard but widely supported, used by temml/katex.
			'menclose'      => array_merge(
				$math_global_attributes,
				array(
					'notation' => true,
				)
			),
		)
	);
}
add_filter( 'wp_kses_allowed_html', 'gutenberg_kses_allow_mathml' );
