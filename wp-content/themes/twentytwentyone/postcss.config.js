module.exports = {
	plugins: [
		require('postcss-nested'),
		require('postcss-css-variables')({
			preserve: false,
			preserveAtRulesOrder: true
		}),
		require('postcss-calc')({
			precision: 0
		}),
		require('postcss-discard-duplicates'),
		require('postcss-merge-rules'),
		require('postcss-discard-empty')
	]
};
