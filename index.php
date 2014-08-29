<html>
<head>
<title>Jabodetabek Property Search </title>
<link href="assets/css/bootstrap.css" rel="stylesheet">
<style>
  body {
	padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
  }
  #map_canvas img {
  	max-width: none;
	}

   #map_canvas label {
    width: auto; display:inline;
</style>
<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
<script type="text/javascript" src="assets/js/markerclusterer_packed.js"></script>
<script src="assets/js/jquery.js"></script>
<!--

<script type="text/javascript" src="script/autocomplete.js"></script>
<script type="text/javascript" src="script/map-marker.js"></script>
<script type="text/javascript" src="script/kml-map.js"></script>
 load googlemaps api dulu 
-->
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="http://geoxml3.googlecode.com/svn/branches/polys/geoxml3.js"></script>
<script type="text/javascript">
var peta;
var peta2;
var gambar_tanda;
gambar_tanda = 'assets/img/marker.png';
var x = new Array();
var y = new Array();
  var nama = new Array();
function peta_awal(){
	// posisi default peta saat diload
	
	var lokasibaru = new google.maps.LatLng(-7.090911,107.668887);
	var petaoption = {
		zoom: 8,
		center: lokasibaru,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	peta = new google.maps.Map(document.getElementById("map_canvas"),petaoption);
	var geoXml = new geoXML3.parser({map:peta,zoom:true,singleInfoWindow:true});
	geoXml.parse('assets/jawa_barat.kml');
			
	// memanggil function ambilpeta() untuk menampilkan koordinat
	ambilpeta();
}

function ambilpeta(){
    url = "json.php";
    $.ajax({
        url: url,
        dataType: 'json',
        cache: false,
        success: function(msg) {
	    var markers = [];
            for(i=0;i<msg.warteg.cabang.length;i++){
				x[i] = msg.warteg.cabang[i].x;
				y[i] = msg.warteg.cabang[i].y;
				nama[i] = msg.warteg.cabang[i].nama_cabang;
                var point = new google.maps.LatLng(parseFloat(msg.warteg.cabang[i].x),parseFloat(msg.warteg.cabang[i].y));
                  tanda = new google.maps.Marker({
							position: point,
							map: peta,
							icon: gambar_tanda,
							clickable: true
							});
				var infowindow = new google.maps.InfoWindow({
				content: 'Tanda :' + nama[i] + '<br>Latitude: ' + x[i] +
				'<br>Longitude: ' + y[i]
				});
				google.maps.event.addListener(tanda,'click',function(event){
					//kasihtanda(event.latLng);
					//infowindow.setContent();
					infowindow.open(peta,tanda);
				});
					markers.push(tanda);
			}
			
			var markerCluster = new MarkerClusterer(peta, markers);
			
        }
    });
}

// ketika button caripeta ditekan, maka script ini akan berjalan
$(document).ready(function() {
    $("#caripeta").click(function(){
        var kab 	= $("#kab").val();
	$.ajax({
        url: "json.php",
        data: "kab="+kab,
        dataType: 'json',
        cache: false,
        success: function(msg){
			var awal2 = new google.maps.LatLng(-7.090911,107.668887);
			var petaoption2 = {
				zoom: 8,
				center: awal2,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var peta2 = new google.maps.Map(document.getElementById("map_canvas"),petaoption2);
					
             var markers = [];
            for(i=0;i<msg.warteg.cabang.length;i++){
				x[i] = msg.warteg.cabang[i].x;
				y[i] = msg.warteg.cabang[i].y;
				nama[i] = msg.warteg.cabang[i].nama_cabang;
                var point2 = new google.maps.LatLng(parseFloat(msg.warteg.cabang[i].x),parseFloat(msg.warteg.cabang[i].y));
                  tanda = new google.maps.Marker({
							position: point2,
							map: peta2,
							icon: gambar_tanda,
							animation:google.maps.Animation.BOUNCE,
							clickable: true
				});
				var infowindow2 = new google.maps.InfoWindow({
				content: 'Tanda :' + nama[i] + 
				'<br>Latitude: ' + x[i] +
				'<br>Longitude: ' + y[i]
				});
				google.maps.event.addListener(tanda,'mouseover',function(event){
					infowindow2.open(peta2,tanda);
			});
				markers.push(tanda);
			}
			var markerCluster = new MarkerClusterer(peta2, markers);
						
			//delay action
			google.maps.event.addListener(peta2,'center_changed',function() {
			  window.setTimeout(function() {
				peta2.panTo(tanda.getPosition());
			  },3000);
			});
        }
    });
    });
});
</script> 
</head>
<body onload="peta_awal()">
<div class="container">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				</a>
			<a class="brand" href="#">Jabodetabek Property Search</a>
			<div class="btn-group pull-right"></div>
			</div>
		</div>
	</div>
<form id="formpeta" class="form-inline">
<select name="kabkota" id="kab">
<option value="">- Pilih Kabupaten -</option>
<?php
require ('config.php');
$sql = mysql_query("SELECT * FROM `kabkota`");
while ($kab = mysql_fetch_array($sql)) {
	echo '<option value="'.$kab['idkabkota'].'">'.$kab['nama_kabkota'].'</option>';
}
?>
</select>
<input type="button" id="caripeta" class="btn" value="Tampilkan">
 <table id="alamat">
        <tr>
            <td>Alamat :</td><td><input type="text" id="route" class="form"></td>
			<td>|| Kode Pos :</td><td><input type="text" id="postal_code"></td>
        </tr>
        <tr>
			<td>Kota :</td><td><input type="text" id="locality" class="form"></td>
            <td>|| Provinsi :</td><td><input type="text" id="provinsi" class="form"></td>
         </tr>
    </table>
</form>

<div id="map_canvas" style="height:500px"></div>
	
<hr>
	  <footer>
        <p>&copy; Trial Map v2 2014</p>
      </footer>
</div>
</body>
</html>
