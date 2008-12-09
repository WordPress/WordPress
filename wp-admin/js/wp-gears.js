
wpGears = {

	createStore : function() {
		if ( 'undefined' == typeof google || ! google.gears ) return;

		if ( 'undefined' == typeof localServer )
			localServer = google.gears.factory.create("beta.localserver");

		store = localServer.createManagedStore(this.storeName());
		store.manifestUrl = "gears-manifest.php";
		store.checkForUpdate();
		this.message(3);
	},

	getPermission : function() {
		var perm = true;

		if ( 'undefined' != typeof google && google.gears ) {
			if ( ! google.gears.factory.hasPermission )
				perm = google.gears.factory.getPermission( 'WordPress', 'images/logo.gif' );

			if ( perm )
				try { this.createStore(); } catch(e) { this.message(); } // silence if canceled
			else
				this.message(4);
		}
	},

	storeName : function() {
		var name = window.location.protocol + window.location.host;

		name = name.replace(/[\/\\:*"?<>|;,]+/g, '_'); // gears beta doesn't allow certain chars in the store name
		name = 'wp_' + name.substring(0, 60); // max length of name is 64 chars

		return name;
	},

	message : function(show) {
		var t = this, msg1 = t.I('gears-msg1'), msg2 = t.I('gears-msg2'), msg3 = t.I('gears-msg3'), msg4 = t.I('gears-msg4'), num = t.I('gears-upd-number'), wait = t.I('gears-wait');

		if ( ! msg1 ) return;

		if ( 'undefined' != typeof google && google.gears ) {
			if ( show && show == 4 ) {
				msg1.style.display = msg2.style.display = msg3.style.display = 'none';
				msg4.style.display = 'block';
			} else if ( google.gears.factory.hasPermission ) {
				msg1.style.display = msg2.style.display = msg4.style.display = 'none';
				msg3.style.display = 'block';

				if ( 'undefined' == typeof store )
					t.createStore();

				store.oncomplete = function(){wait.innerHTML = (' ' + wpGearsL10n.updateCompleted);};
				store.onerror = function(){wait.innerHTML = (' ' + wpGearsL10n.error + ' ' + store.lastErrorMessage);};
				store.onprogress = function(e){if(num) num.innerHTML = (' ' + e.filesComplete + ' / ' + e.filesTotal);};
			} else {
				msg1.style.display = msg3.style.display = msg4.style.display = 'none';
				msg2.style.display = 'block';
			}
		}
	},

	I : function(id) {
		return document.getElementById(id);
	}
};

(function() {
	if ( 'undefined' != typeof google && google.gears ) return;

	var gf = false;
	if ( 'undefined' != typeof GearsFactory ) {
		gf = new GearsFactory();
	} else {
		try {
			gf = new ActiveXObject('Gears.Factory');
			if ( factory.getBuildInfo().indexOf('ie_mobile') != -1 )
				gf.privateSetGlobalObject(this);
		} catch (e) {
			if ( ( 'undefined' != typeof navigator.mimeTypes ) && navigator.mimeTypes['application/x-googlegears'] ) {
				gf = document.createElement("object");
				gf.style.display = "none";
				gf.width = 0;
				gf.height = 0;
				gf.type = "application/x-googlegears";
				document.documentElement.appendChild(gf);
			}
		}
	}

	if ( ! gf ) return;
	if ( 'undefined' == typeof google ) google = {};
	if ( ! google.gears ) google.gears = { factory : gf };
})();
