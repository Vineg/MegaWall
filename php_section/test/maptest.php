
<?php
$uid = User::get_id ();
if(!$uid){
	loc("/login_vk");
	exit;
}
if ($_POST [lat] && $_POST [long]) {
	$lat = my_s ( $_POST [lat] );
	$long = my_s ( $_POST [long] );
	if (! my_qn ( "select * from user_coordinates where user_id=$uid" )) {
		my_in ( "user_coordinates:user_id=$uid" );
	}
	my_up ("user_coordinates:latitude=$lat,longitude=$long:user_id=$uid");
	exit ();
}

$q = my_q("select * from user_coordinates where true");
for($i=0; $i<my_n($q); $i++){
	$lati = my_r($q,$i, "latitude");
	$longi = my_r($q,$i, "longitude");
	$coordar[] = "[$lati, $longi]";
}
$points = "[".join(",", $coordar)."]"
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Примеры. Добавление меток на карту.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!--
        Подключаем API карт 2.x
        Параметры:
          - load=package.full - полная сборка;
	      - lang=ru-RU - язык русский.
    -->
<script
	src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"
	type="text/javascript"></script>
<script type="text/javascript" src="/files/jscripts/jq.js"></script>
<script type="text/javascript" src="/files/jscripts/jqs.js"></script>

<script type="text/javascript">
        // Как только будет загружен API и готов DOM, выполняем инициализацию
        ymaps.ready(init);

		$.stream.setup({enableXDR: true});
        
        
        $(function() {
                $.stream("http://localhost:8080/SimpleStreamServ/chat", {
                        type: "http",
                        dataType: "json",
                        context: $("#content")[0],
                        open: function(event, stream) {
                                //$("#editor .message").removeAttr("disabled").focus();
                                //stream.send({username: chat.username, message: "Hello"});
                        },
                        message: function(event) {
                            alert(event.data.message);
                               /* if (chat.lastUsername !== event.data.username) {
                                        $("<p />").addClass("user").text(chat.lastUsername = event.data.username).appendTo(this);
                                }
                                
                                $("<p />").addClass("message").text(event.data.message).appendTo(this);
                                this.scrollTop = this.scrollHeight; */
                        },
                        error: function() {
                               // $("#editor .message").attr("disabled", "disabled");
                        },
                        close: function() {
                              //  $("#editor .message").attr("disabled", "disabled");
                        }
                });
                
        });

        function sendCoord(){
        		navigator.geolocation.getCurrentPosition(foundLocation);
        		function foundLocation(position)
                {
                    //alert(position.coords.accuracy);
                  var lat = position.coords.latitude;
                  var long = position.coords.longitude;
                  send([lat, long]);
                }
        }

        function send(data){
        		$.stream().send({username: "Vineg", message: ""+data});
            }

        
        function init () {
       	 navigator.geolocation.getCurrentPosition(foundLocation, noLocation);
		
         function foundLocation(position)
         {
             setInterval(sendCoord, 10000);
             //alert(position.coords.accuracy);
           var lat = position.coords.latitude;
           var long = position.coords.longitude;
           var myPlacemark = new ymaps.Placemark([lat, long]);
           //myMap.geoObjects.add(myPlacemark);

/*           var properties = {
       	        balloonContent: 'Hello Yandex!',
       	        hintContent: "Круг",
       	        iconContent: "1",
       	        color: "red"
       	    },
       	    options = { balloonCloseButton: true,  color: "red" },
       	    circle = new ymaps.Circle([[lat, long], 20, "red"], properties, options);

       	myMap.geoObjects.add(circle);*/
           var myGeoObject = new ymaps.GeoObject({
               geometry: {
                   type: "Point",
                   coordinates: [lat, long],
               },
               properties: {
                   iconContent: "1",
                   hintContent: "Метка",
                   balloonContentHeader: "Hello Yandex!"
               }
           }, {
               draggable: true,
               balloonCloseButton: false,
               preset: "twirl#blueDotIcon",
               iconImageSize: [10,10],
               iconContentSize: 500
           });
      // Добавляем геообъект на карту
      myMap.geoObjects.add(myGeoObject);
      
           myMap.setCenter([lat, long]);
           //alert(position);
           
           $.post("/test/maptest.php", {lat:lat,long:long});
         }
         function noLocation()
         {
          	 alert('Could not find location');
         }
         
           var myMap = new ymaps.Map("map", {
                    center: [59.939, 30.315],
                    zoom: 14
                });
           myMap.controls.add('zoomControl');
            /*,
                // Первый способ задания метки
                myPlacemark = new ymaps.Placemark([55.8, 37.6]),
                // Второй способ
       
                myGeoObject = new ymaps.GeoObject({
                    // Геометрия.
                    geometry: {
                        // Тип геометрии - точка
                        type: "Point",
                        // Координаты точки.
                        
                        coordinates: [55.8, 37.8]
                    }
                });

            // Добавляем метки на карту
            myMap.geoObjects
                .add(myPlacemark)
                .add(myGeoObject);
         */
         var points = <?php //cho $points;?>;
         addPoints(points);
         function addPoints(coords){
  			for(var i in coords){
  	  			var val= coords[i];
  				 var lat = val[0];
  		          var long = val[1];
    		        //alert(val);
  		           var myPlacemark = new ymaps.Placemark([lat, long]);
  		           myMap.geoObjects.add(myPlacemark);
  				}
              }
        }


    </script>
</head>

<body>
	<h2>Добавление меток на карту</h2>

	<div id="map" style="width: 600px; height: 300px"></div>
</body>

</html>
