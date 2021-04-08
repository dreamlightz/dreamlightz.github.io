<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" />
<link rel="stylesheet" href="src/L.Control.Locate.css" />
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://gfr.erc.or.th/js/leaflet.canvas.markers.js"></script>
<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" data-auto-replace-svg="nest"></script>
<script src="src/L.Control.Locate.js" charset="utf-8"></script>
<!-- <script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js'></script> -->
<!-- <script src='togeoJson.js'></script> -->
<!-- <script src='togeojson-master/lib/kml.js'></script> -->
<div id="map" style="width:100%; height:100%"></div>
<style>
    .mapLocate{
        font-size: 20px;
        display: flex !important;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
</style>
<script>
    //Fake variable
    var kmlLayers = [];
    var lyrName = 'test';
    var allowDel = true;
    var allowCheck = true;

    var ercMapOpt = {
        center: [13.736717, 100.523186],
        zoom: 6,
        minZoom: 0,
        maxZoom: 19, // 19 for gg, 18 for arc
        inertia: false,
        attributionControl: true,
        zoomControl: false,
        // zoomSnap: 0.25,
        // zoomDelta: 0.25,
        renderer: L.canvas(),
    }
    var ercMap = L.map('map', ercMapOpt),
        hybrid= L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {maxZoom: 19, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']}),
        terrain= L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {maxZoom: 19, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']}),
        aerial= L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {maxZoom: 19, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']}),
        road= L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {maxZoom: 19, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']});
    ercMap.addLayer(road);
    // var map = L.mapbox.map('map', 'mapbox.streets').setView([38, -102.0], 5);
    // L.Control.MapLocate = L.Control.extend(
    // {
    //     options:{ position: 'bottomright' },
    //     onAdd: function (map) {
    //         var controlDiv = L.DomUtil.create('div', 'leaflet-bar');
    //         L.DomEvent
    //             .addListener(controlDiv, 'click', L.DomEvent.stopPropagation)
    //             .addListener(controlDiv, 'click', L.DomEvent.preventDefault)
    //             .addListener(controlDiv, 'click', function () {
    //                 ercMap.locate({setView: true, maxZoom: 16});
    //             })
    //         var controlUI = L.DomUtil.create('a', 'mapLocate fas fa-location-arrow ', controlDiv);
    //         controlUI.title = 'ไปยังพิกัดของคุณ';
    //         controlUI.href = '#';
    //         return controlDiv;
    //     }
    // });
    // L.control.zoom({position: 'bottomright'}).addTo(ercMap);
    // L.control.scale({imperial: false}).addTo(ercMap);
    // new L.Control.MapLocate({position: 'bottomright'}).addTo(ercMap);
    var lc = L.control.locate({
        position: 'bottomright',
        showCompass: true,
        drawCircle: true,
        icon: 'mapLocate fa fa-map-marker-alt',
        iconLoading: 'fa fa-spinner fa-spin',
        showPopup: true,
        strings: {
            title: "Show me where I am, yo!"
        }
    }).addTo(ercMap);










    
    $(document).ready(function(){
        $.ajax({
            type: "POST" ,
            url: "doc1.kml" ,
            dataType: "html" ,
            success: function(data) {
                let _styleJson = {};
                let _styleMap = {};
                let _geoJson = {"type": "FeatureCollection", "features": []};
                function cutStrByTagName(str='', tagName='', nStart=0, nStop=0){
                    return (str.includes(`<${tagName}>`) !== false ? str.substring(str.lastIndexOf(`<${tagName}>`) + `<${tagName}>`.length + nStart, str.lastIndexOf(`</${tagName}>`) - nStop) : 'false');
                }
                $(data).find('Style').each(function( index, value ) {
                    let str = $(value).html();
                    let lineStyle = cutStrByTagName(str, 'LineStyle');
                    let polyStyle = cutStrByTagName(str, 'PolyStyle');
                    let markerStyle = cutStrByTagName(str, 'IconStyle');
                    if($(value).attr('id').match(/(Line|Poly)/)){ //Check style for line and poly
                        _styleJson[$(value).attr('id')] = {
                            fillColor:      (cutStrByTagName(polyStyle, 'color', 2, 0) !== 'false' ? `#${cutStrByTagName(polyStyle, 'color', 2, 0)}` : '#3388ff'),
                            fillOpacity:    (cutStrByTagName(polyStyle, 'color', 0 , 6) !== 'false' ? parseInt(cutStrByTagName(polyStyle, 'color', 0 , 6), 16)/255 : 1),
                            color:          (cutStrByTagName(lineStyle, 'color', 2, 0) !== 'false' ? `#${cutStrByTagName(lineStyle, 'color', 2, 0)}` : '#3388ff'),
                            weight:         (cutStrByTagName(lineStyle, 'width') !== 'false' ? cutStrByTagName(lineStyle, 'width') : 1),
                            opacity:        (cutStrByTagName(lineStyle, 'color', 0 , 6) !== 'false' ? parseInt(cutStrByTagName(lineStyle, 'color', 0 , 6), 16)/255 : 1),
                            // fill:           (fillOpacity <= 0 ? false : true),
                            fill:           true,
                            stroke:         true
                        };
                    } else {
                        _styleJson[$(value).attr('id')] = {
                            imgUrl: (cutStrByTagName(markerStyle, 'href') !== 'false' ? cutStrByTagName(markerStyle, 'href') : 'false'),
                        };
                    }
                });
                $(data).find('coordinates').each(function( index, value ) {
                    let _coordinates = [], arrCoord = $(value).text().split(',0');
                    arrCoord.forEach(function(_value){
                        let arrValue = _value.replaceAll(/\s/g, ''); //remove all space
                        if(arrValue.length > 0){ _coordinates.push(arrValue.split(',').map(parseFloat).reduce(function(x,y){return [x,y];})); } //Split to array and parse string in arr to float
                    });
                    let type = '', coordinates = 0;
                    switch ($(value).parents('Placemark').find('LineString, Polygon, Point')[0].nodeName) {
                        case 'LINESTRING': type = 'LineString'; coordinates = _coordinates; break;
                        case 'POLYGON': type = 'Polygon'; coordinates = [_coordinates]; break;
                        case 'POINT': type = 'Point'; coordinates = _coordinates[0]; break;
                        default: console.log(`type :: Found other type >>> ${$(value).parent()[0].nodeName}`); break;
                    }
                    _geoJson.features.push({
                        'type': 'Feature',
                        'properties': {
                            'name' : ($(value).parents('Placemark').find('name').text() || 'ไม่พบข้อมูล'),
                            'styleurl': ($(data).find(`stylemap${$(value).parents('Placemark').find('styleurl').text()}`).length > 0 ? $($($(data).find(`stylemap${$(value).parents('Placemark').find('styleurl').text()}`)).find('Pair')[0]).find('styleurl').text() : $(value).parents('Placemark').find('styleurl').text()),
                            'description' : (Object($(value).parents('Placemark').find('description table')[0]).outerHTML || 'ไม่ระบุ').replaceAll('Null', 'ไม่ระบุ'),
                        },
                        'geometry': {
                            'type': type,
                            'coordinates': coordinates,
                        }
                    });
                });
                kmlLayers[lyrName] = L.geoJSON(_geoJson, {
                    name: lyrName,
                    del: allowDel,
                    checked: allowCheck,
                    popupLabelValue: {'name': 'ชื่อ', val: 'name'},
                    pointToLayer: function(feature,latlng){
                        let _imgUrl = (_styleJson[feature.properties.styleurl.replaceAll('#', '')].imgUrl !== 'false' ? _styleJson[feature.properties.styleurl.replaceAll('#', '')].imgUrl : 'img/marker-icon-2x.png');
                        // let _width=50, _height=50, _calSize=1;
                        // let _marker;
                        // let img = new Image();
                        // img.onload = function(){
                        //     _width = this.width;
                        //     _height = this.height;
                        //     for(let _i=1; _i < this.width; _i++){
                        //         if((this.width/_i) <= 25){
                        //             _calSize = _i;
                        //             _i = this.width;
                        //         }
                        //     }
                        //     // console.log(this);
                        //     // console.log( (this.width)/_calSize + ' '+ (this.height)/_calSize );
                        //     _marker = L.canvasMarker(latlng, {radius: 20, img: {url: _imgUrl, size: [_width/_calSize, _height/_calSize]}});
                        //     console.log(_marker);
                        // };
                        // img.src = _imgUrl;
                        // return _marker;
                        return L.canvasMarker(latlng, {radius: 20, img: {url: _imgUrl, size: [15, 15]}});
                    },
                    onEachFeature: function (geojsonFeat, layer) {
                        if(geojsonFeat.geometry.type === 'LineString' || geojsonFeat.geometry.type === 'Polygon' ) {
                            if(_styleJson[geojsonFeat.properties.styleurl.replaceAll('#', '')]){
                                if(geojsonFeat.geometry.type === 'LineString'){
                                    _styleJson[geojsonFeat.properties.styleurl.replaceAll('#', '')].fill = false;
                                }
                                layer.setStyle(_styleJson[geojsonFeat.properties.styleurl.replaceAll('#', '')]);
                            }
                        } else { //Marker
                            layer.bringToFront();
                        }
                        layer.bindPopup(geojsonFeat.properties.name + '<br>' + geojsonFeat.properties.styleurl1 + '<br>' + geojsonFeat.properties.styleurl + '<br>' + geojsonFeat.properties.description, {minWidth: 300});
                        layer.options.name = lyrName;
                        layer.options.del = allowDel;
                        layer.options.checked = allowCheck;
                        layer.options.popupLabelValue = {'name': 'ชื่อ', val: 'name'};
                        // erc.ShowGenMapAttr(geojsonFeat, layer);
                    }
                }).addTo(ercMap);
            }
        });
    });
</script>