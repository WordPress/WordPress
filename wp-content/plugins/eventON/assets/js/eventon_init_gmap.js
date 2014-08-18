/*
	INITIATE google gmaps on web page
	version: 0.2
*/

	
var geocoder;
var test =3;

function getGeocoder(){
	return geocoder;
}

function initialize(map_canvas_id, address,mapformat, zoom_level, location_type, scrollwheel) {
	var map;
	geocoder = new google.maps.Geocoder();
			
	var latlng = new google.maps.LatLng(-34.397, 150.644);	

	if(scrollwheel==false ){
		var myOptions = {			
			center: latlng,	
			mapTypeId:mapformat,	
			zoom: zoom_level,	
			scrollwheel: false,
		}
	}else{
		var myOptions = {	
			center: latlng,	
			mapTypeId:mapformat,	
			zoom: zoom_level }
	}
	
	
	var map_canvas = document.getElementById(map_canvas_id);
	map = new google.maps.Map(map_canvas, myOptions);
	
	
	// address from latlng
	if(location_type=='latlng'){
		var latlngStr = address.split(",",2);
		var lat = parseFloat(latlngStr[0]);
		var lng = parseFloat(latlngStr[1]);
		var latlng = new google.maps.LatLng(lat, lng);
		
		geocoder.geocode({'latLng': latlng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				var marker = new google.maps.Marker({
					map: map,
					position: latlng
				});
				//map.setCenter(results[0].geometry.location);
				map.setCenter(marker.getPosition());
				//console.log(results[0].geometry.location);
			} else {
				document.getElementById(map_canvas_id).style.display='none';
			}
		});
		
	}else if(address==''){
		console.log('t');
	}else{
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				
				//console.log('map '+results[0].geometry.location);
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
				
				
			} else {
				document.getElementById(map_canvas_id).style.display='none';				
			}
		});
	}
	
	
}


