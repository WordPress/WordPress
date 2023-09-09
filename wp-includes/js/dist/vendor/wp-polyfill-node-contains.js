<<<<<<< HEAD

// Node.prototype.contains
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
(function() {

	function contains(node) {
		if (!(0 in arguments)) {
			throw new TypeError('1 argument is required');
		}

		do {
			if (this === node) {
				return true;
			}
<<<<<<< HEAD
		// eslint-disable-next-line no-cond-assign
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		} while (node = node && node.parentNode);

		return false;
	}

	// IE
<<<<<<< HEAD
	if ('HTMLElement' in self && 'contains' in HTMLElement.prototype) {
		try {
			delete HTMLElement.prototype.contains;
		// eslint-disable-next-line no-empty
		} catch (e) {}
	}

	if ('Node' in self) {
=======
	if ('HTMLElement' in this && 'contains' in HTMLElement.prototype) {
		try {
			delete HTMLElement.prototype.contains;
		} catch (e) {}
	}

	if ('Node' in this) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		Node.prototype.contains = contains;
	} else {
		document.contains = Element.prototype.contains = contains;
	}

}());
