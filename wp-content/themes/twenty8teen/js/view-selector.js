/**
 * Toggle the table view, and save the state in session storage.
  * @package Twenty8teen
 */
( function() {
	function storageAvailable(type) {
    try {
      var storage = window[type],
        x = '__storage_test__';
      storage.setItem(x, x);
      storage.removeItem(x);
      return true;
    }
    catch(e) {
      return false;
    }
	}

	const target = document.querySelector('main');
	if (! target) {return;}
	var localSave = storageAvailable('sessionStorage'),
		key = 'twenty8teen-view-selector',
		view = target.classList.contains('table-view'),
		stored;
	if ( localSave ) {
    stored = sessionStorage.getItem(key);
		if ( stored !== null ) {  // If it has been saved, use it.
			stored = stored !== 'false';
			if( stored !== view ) {
				target.classList.toggle('table-view');
			}
		}
	}
	document.querySelector('.view-switch').addEventListener('click', function(){
		target.classList.toggle('table-view');
		if ( localSave ) {
			var state = target.classList.contains('table-view');
			sessionStorage.setItem(key, state);
			if ( state === view ) {  // Don't save if same as original.
				sessionStorage.removeItem(key);
			}
		}
	});
} )();
