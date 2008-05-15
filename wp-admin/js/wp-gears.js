
wpGears = {

	init : function() {
		if ( 'undefined' != typeof google && google.gears ) {
			try { 
				localServer = google.gears.factory.create("beta.localserver");
				this.createStore();
			} catch(e) { // silence if canceled
				this.message();
			}
	  	}
	},

	createStore : function() {
		if ( 'undefined' == typeof google || ! google.gears ) return;

		store = localServer.createManagedStore(this.storeName());
		store.manifestUrl = "gears-manifest.php";
		store.checkForUpdate();
		this.message();
	},

	removeStore : function() {
		if ( 'undefined' == typeof google || ! google.gears ) return;

		localServer.removeManagedStore(this.storeName());
		this.message();
	},

	storeName : function() {
      var name = window.location.protocol + window.location.host;

      name = name.replace(/[\/\\:*"?<>|;,]+/g, '_'); // gears beta doesn't allow certain chars in the store name
	  name = 'wp_' + name.substring(0, 60); // max length of name is 64 chars

      return name;
    },

    message : function() {
		var t = this, msg1 = t.I('gears-msg1'), msg2 = t.I('gears-msg2'), msg3 = t.I('gears-msg3'), num = t.I('gears-upd-number'), wait = t.I('gears-wait');

		if ( ! msg1 ) return;

        if ( 'undefined' != typeof store ) {
			msg1.style.display = msg2.style.display = 'none';
			msg3.style.display = 'block';

			store.oncomplete = function(){wait.innerHTML = (' ' + wpGearsL10n.updateCompleted);};
			store.onerror = function(){wait.innerHTML = (' ' + wpGearsL10n.error + ' ' + store.lastErrorMessage);};
			store.onprogress = function(e){if(num) num.innerHTML = (' ' + e.filesComplete + ' / ' + e.filesTotal);};
        } else if ( 'undefined' != typeof google && google.gears ) {
			msg1.style.display = 'none';
			msg2.style.display = 'block';
		}
	},
	
	I : function(id) {
		return document.getElementById(id);
	}
}

addLoadEvent( function(){wpGears.init()} );

function gearsInit() {
	if ( 'undefined' != typeof google && google.gears ) return;

	var gf = false;
	if ( 'undefined' != typeof GearsFactory ) { // Firefox
		gf = new GearsFactory();
	} else { // IE
		try {
			gf = new ActiveXObject('Gears.Factory');
		} catch (e) {}
	}

	if ( ! gf ) return;
	if ( 'undefined' == typeof google ) google = {};
	if ( ! google.gears ) google.gears = { factory : gf };
}

gearsInit();
