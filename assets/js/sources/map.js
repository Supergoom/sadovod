jQuery(function($) { 
	if(typeof startCoords !== 'undefined' && startCoords.length){
		ymaps.ready(init);
	}
	function init(){
		var yaPlacemark,
			yaMap = new ymaps.Map("map", {
			center: (startCoords),
			zoom: 7,
			controls: []
		}, {
			suppressMapOpenBlock: true,
			suppressObsoleteBrowserNotifier: true
		});
		
		if(startCoords){
			yaPlacemark = createPlacemark(startCoords);
			yaMap.geoObjects.add(yaPlacemark);
		}
		
		if(window.innerWidth < 768){
			yaMap.behaviors.disable('scrollZoom');
			yaMap.controls.add('zoomControl');
		}
			
		function createPlacemark(coords) {
			return new ymaps.Placemark(coords, {}, {
				iconLayout: 'default#image',
				iconImageHref: '/wp-content/themes/sadovod/assets/img/point.png',					
				iconImageSize: [30, 41],
				iconImageOffset: [-15, -48],
				draggable: true
			});
		}
	}
});