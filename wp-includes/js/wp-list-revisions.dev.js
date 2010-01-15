(function(w) {
	var init = function() {
		var pr = document.getElementById('post-revisions'),
		inputs = pr ? pr.getElementsByTagName('input') : [];
		pr.onclick = function() {
			var i, checkCount = 0, side;
			for ( i = 0; i < inputs.length; i++ ) {
				checkCount += inputs[i].checked ? 1 : 0;
				side = inputs[i].getAttribute('name');
				if ( ! inputs[i].checked &&
				( 'left' == side && 1 > checkCount || 'right' == side && 1 < checkCount && ( ! inputs[i-1] || ! inputs[i-1].checked ) ) &&
				! ( inputs[i+1] && inputs[i+1].checked && 'right' == inputs[i+1].getAttribute('name') ) )
					inputs[i].style.visibility = 'hidden';
				else if ( 'left' == side || 'right' == side )
					inputs[i].style.visibility = 'visible';
			}
		}
		pr.onclick();
	}
	if ( w && w.addEventListener )
		w.addEventListener('load', init, false);
	else if ( w && w.attachEvent )
		w.attachEvent('onload', init);
})(window);
