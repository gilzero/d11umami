/**
 * @file
 * Initiates map(s) for the Styled Google Map module.
 *
 * A single or multiple Styled Google Maps will be initiated.
 * Drupal behaviors are used to make sure ajax called map(s) are correctly
 *     loaded.
 */

(function (Drupal) {

  /**
   * Calculates the route and displays it on the map.
   */
  function calculateAndDisplayRoute(directionsService, directionsDisplay, map_id, destination) {
    var selectedMode = document.getElementById('getting_directions_mode-' + map_id).value;
    var address = document.getElementById('getting_directions_address-' + map_id).value;
    if (document.getElementById('getting_directions_location-' + map_id).checked) {
      if (typeof window.drupalSettings.user_current_location === 'object' && typeof window.drupalSettings.user_current_location.getPosition === 'function') {
        address = window.drupalSettings.user_current_location.getPosition();
      }
    }
    directionsService.route({
      origin: address,
      destination: {lat: destination.lat, lng: destination.lon},
      travelMode: google.maps.TravelMode[selectedMode]
    }, function(response, status) {
      if (status === 'OK') {
        directionsDisplay.setDirections(response);
      } else {
        const messages = new Drupal.Message();
        var message = Drupal.t('Directions request failed due to @status', {'@status': status});
        if (typeof Drupal.Message !== 'undefined') {
          messages.add(message, {type: 'error'});
        } else {
          window.alert(message);
        }
      }
    });

  }

  /**
   * Behavior to display the map.
   *
   * @type {{attach: Drupal.behaviors.styled_google_maps.attach}}
   */
  Drupal.behaviors.styled_google_maps = {
    attach: function (context, settings) {
      var maps = settings.styled_google_map;
      var markers = [];
      // There can be multiple maps on a page, let's iterate over them.
      for (var i in maps) {
        var current_map = settings.maps['id' + maps[i]];
        var map_id = current_map.id;
        var map_container = document.getElementById(map_id);
        // Continue in case we find the map object on the current page.
        if (map_container) {
          // Declare the map locations, style, bounds, settings and types.
          var map_locations = current_map.locations;
          var map_settings = current_map.settings;
          var bounds = new google.maps.LatLngBounds();
          var map_types = {
            'ROADMAP': google.maps.MapTypeId.ROADMAP,
            'SATELLITE': google.maps.MapTypeId.SATELLITE,
            'HYBRID': google.maps.MapTypeId.HYBRID,
            'TERRAIN': google.maps.MapTypeId.TERRAIN
          };
          var map_style = (map_settings.style.style !== '' ? map_settings.style.style : '[]');

          // Set the map inline style width and height.
          map_container.style.width = current_map.settings.width;
          map_container.style.height = current_map.settings.height;

          // Disable scrolling on screens smaller than 480 pixels.
          // TODO: This is deprecated and should be removed due to gesture handling.
          map_settings.draggable = document.documentElement.clientWidth > 480 ? map_settings.draggable : map_settings.mobile_draggable;

          // Declare and initialize the base settings of the Google Map.
          var init_map = {
            gestureHandling: map_settings.gestureHandling,
            zoom: parseInt(map_settings.zoom.default),
            mapTypeId: map_types[map_settings.style.maptype],
            disableDefaultUI: !map_settings.ui,
            maxZoom: parseInt(map_settings.zoom.max),
            minZoom: parseInt(map_settings.zoom.min),
            styles: JSON.parse(map_style),
            mapTypeControl: map_settings.maptypecontrol,
            scaleControl: map_settings.scalecontrol,
            rotateControl: map_settings.rotatecontrol,
            streetViewControl: map_settings.streetviewcontrol,
            zoomControl: map_settings.zoomcontrol,
            fullscreenControl: map_settings.fullscreen,
            draggable: map_settings.draggable
          };
          var map = new google.maps.Map(map_container, init_map);

          var heatmap = {};

          // Additional functionality to enable a heatmap.
          if (typeof map_settings.heat_map != 'undefined' && map_settings.heat_map.heatmap_enabled) {
            var heatmap_data = [];
            for (var k = 0; k < map_settings.heat_map.data.length; k++) {
              var heatmap_item = {
                location: new google.maps.LatLng(map_settings.heat_map.data[k].lat, map_settings.heat_map.data[k].lon),
                weight: map_settings.heat_map.data[k].weight,
              };
              heatmap_data.push(heatmap_item);
            }
            heatmap = new google.maps.visualization.HeatmapLayer({
              data: heatmap_data,
              gradient: map_settings.heat_map.gradient,
              opacity: map_settings.heat_map.opacity,
              maxIntensity: map_settings.heat_map.maxIntensity,
              dissipating: map_settings.heat_map.dissipating,
              radius: map_settings.heat_map.radius
            });
            heatmap.setMap(map);
          }
          else {
            // Declare the infoBubble (popup) settings.
            var infoBubble = new InfoBubble({
              shadowStyle: parseInt(map_settings.popup.shadow_style),
              padding: parseInt(map_settings.popup.padding),
              borderRadius: parseInt(map_settings.popup.border_radius),
              borderWidth: parseInt(map_settings.popup.border_width),
              borderColor: map_settings.popup.border_color,
              backgroundColor: map_settings.popup.background_color,
              minWidth: map_settings.popup.min_width,
              maxWidth: map_settings.popup.max_width,
              maxHeight: map_settings.popup.min_height,
              minHeight: map_settings.popup.max_height,
              arrowStyle: parseInt(map_settings.popup.arrow_style),
              arrowSize: parseInt(map_settings.popup.arrow_size),
              arrowPosition: parseInt(map_settings.popup.arrow_position),
              disableAutoPan: parseInt(map_settings.popup.disable_autopan),
              disableAnimation: parseInt(map_settings.popup.disable_animation),
              hideCloseButton: parseInt(map_settings.popup.hide_close_button),
              backgroundClassName: map_settings.popup.classes.background
            });

            // Set extra custom classes for easy styling.
            if (typeof map_settings.popup.close_button_source != 'undefined' && map_settings.popup.close_button_source) {
              infoBubble.close_.src = map_settings.popup.close_button_source;
            }
            infoBubble.contentContainer_.className = map_settings.popup.classes.container;
            infoBubble.arrow_.className = map_settings.popup.classes.arrow;
            infoBubble.arrowOuter_.className = map_settings.popup.classes.arrow_outer;
            infoBubble.arrowInner_.className = map_settings.popup.classes.arrow_inner;

            // Loop each location (pin) our map contains.
            for (var j in map_locations) {
              // Set the image icon of our pin as well as the image for its active state.
              var icon = map_locations[j].pin;
              if (
                typeof map_settings.style.pin_width !== 'undefined' && map_settings.style.pin_width !== '' &&
                typeof map_settings.style.pin_height !== 'undefined' && map_settings.style.pin_height !== ''
              ) {
                icon = {
                  url: map_locations[j].pin,
                  scaledSize: new google.maps.Size(map_settings.style.pin_width, map_settings.style.pin_height)
                }
              }
              if (typeof map_locations[j].active_pin !== 'undefined') {
                var active_icon = map_locations[j].active_pin;
                if (map_settings.style.pin_width !== '' && map_settings.style.pin_height !== '') {
                  active_icon = {
                    url: map_locations[j].active_pin,
                    scaledSize: new google.maps.Size(map_settings.style.pin_width, map_settings.style.pin_height)
                  }
                }
              }
              else {
                active_icon = icon;
              }
              // Create the Marker (pin) object with all its information and add it to the Markers array.
              var marker = new google.maps.Marker({
                position: new google.maps.LatLng(map_locations[j].lat, map_locations[j].lon),
                map: map,
                html: map_locations[j].popup,
                label: map_locations[j].marker_label,
                icon: icon,
                original_icon: icon,
                active_icon: active_icon,
                category: map_locations[j].category
              });
              markers.push(marker);

              // Handle all popup functionality.
              if (map_locations[j].popup) {
                // Set the default open/close state of the popup.
                if (typeof map_settings.popup.default_state != 'undefined' && map_settings.popup.default_state == 1 && j == 0) {
                  infoBubble.setContent(marker.html);
                  infoBubble.bubble_.className = 'sgmpopup sgmpopup-' + marker.category;
                  marker.setZIndex(1);
                  marker.setIcon(marker.active_icon);
                  infoBubble.open(map, marker);
                }
                var open_event = map_settings.popup.open_event;
                // Event handler when clicking a marker (pin).
                google.maps.event.addListener(marker, open_event, (function (map) {
                  return function () {
                    if (infoBubble.isOpen() && map_settings.popup.second_click == 1 && this.getZIndex()) {
                      infoBubble.close(map, this);
                      this.setIcon(this.original_icon);
                      this.setZIndex(0);
                    }
                    else {
                      infoBubble.setContent(this.html);
                      for (var i = 0; i < markers.length; i++) {
                        markers[i].setIcon(markers[i].original_icon);
                        infoBubble.bubble_.className = 'sgmpopup sgmpopup-' + markers[i].category;
                        markers[i].setZIndex(0);
                      }
                      this.setIcon(this.active_icon);
                      this.setZIndex(1);
                      infoBubble.open(map, this);
                    }
                  };

                }(map)));
              }
              bounds.extend(marker.getPosition());
            }

            var markerCluster = {};

            // Additional functionality to enable clustering of pins.
            if (typeof map_settings.cluster != 'undefined' && map_settings.cluster.cluster_enabled) {
              var clusterStyles = [
                {
                  textColor: map_settings.cluster.text_color,
                  url: map_settings.cluster.pin_image,
                  height: parseInt(map_settings.cluster.height),
                  width: parseInt(map_settings.cluster.width),
                  textSize: parseInt(map_settings.cluster.text_size)
                }
              ];
              var mcOptions = {
                gridSize: 60,
                zoomOnClick: map_settings.cluster.zoomOnClick,
                maxZoom: map_settings.zoom.max - 1,
                styles: clusterStyles,
                minimumClusterSize: parseInt(map_settings.cluster.min_size),
              };
              // Handle clustering of the map.
              if (typeof map_settings.cluster != 'undefined' &&  map_settings.cluster.cluster_enabled) {
                markerCluster = new MarkerClusterer(map, markers, mcOptions);
                markerCluster.setMaxZoom(mcOptions.maxZoom);
              }
            }

            var markerSpiderfier = {};

            // Additional functionality to spiderify multiple close pins.
            if (typeof map_settings.spider != 'undefined' && map_settings.spider.spider_enabled) {
              var spidericonSize = new google.maps.Size(map_settings.spider.width, map_settings.spider.height);
              var spiderConfig = {
                markersWontMove: map_settings.spider.markers_wont_move,
                markersWontHide: map_settings.spider.markers_wont_hide,
                basicFormatEvents: map_settings.spider.basic_format_events,
                keepSpiderfied: map_settings.spider.keep_spiderfied,
                nearbyDistance: map_settings.spider.nearby_distance,
                circleSpiralSwitchover: map_settings.spider.circle_spiral_switchover,
                legWeight: map_settings.spider.leg_weight,
                spiralFootSeparation: map_settings.spider.spiralFootSeparation,
                circleFootSeparation: map_settings.spider.circleFootSeparation,
                icon: {
                  url: map_settings.spider.pin_image,
                  size: spidericonSize,
                  scaledSize: spidericonSize
                }
              };
              // Init OverlappingMarkerSpiderfier with map.
              markerSpiderfier = new OverlappingMarkerSpiderfier(map, spiderConfig);
              for (var m in markers ) {
                // Add the Marker to OverlappingMarkerSpiderfier.
                markerSpiderfier.addMarker(markers[m]);
              }
              // Set original pins when spiderify.
              markerSpiderfier.addListener('spiderfy', function (markers) {
                for (var i = 0; i < markers.length; i++) {
                  markers[i].setIcon(markers[i].original_icon);
                  markers[i].setZIndex(1);
                }
              });
              // Set pin icon when unspiderfy.
              markerSpiderfier.addListener('unspiderfy', function (markers) {
                for (var i = 0; i < markers.length; i++) {
                  markers[i].setIcon(spiderConfig.icon);
                  infoBubble.close();
                }
              });
              google.maps.event.addListener(map, 'idle', function (marker) {
                // Change spiderable markers to plus sign markers
                // and subsequently any other zoom/idle.
                var spidered = markerSpiderfier.markersNearAnyOtherMarker();
                for (var i = 0; i < spidered.length; i++) {
                  // Set spidered icon when inside cluster.
                  spidered[i].setIcon(spiderConfig.icon);
                  spidered[i].setZIndex(0);
                }
              });
              // triggering drag event so that the spider pin icons get enabled again
              google.maps.event.addListener(map, 'dragend', function() {
                google.maps.event.trigger(map, 'click');
              });
            }

          }

          // Add controls that are configured for views.
          var map_view = map_container.closest('.view');
          if (map_view) {
            // Try to find the map controls.
            var custom_controls = map_view.getElementsByClassName('google-map-control');
            if (custom_controls) {
              // Add controls to the positions that are provided.
              for (var index = 0; index < custom_controls.length; index++) {
                var position = custom_controls[index].getAttribute('data-position');
                var id = custom_controls[index].getAttribute('id');
                map.controls[google.maps.ControlPosition[position]].push(document.getElementById(id));
              }
            }
          }

          // Handle centering of the map.
          if (map_settings.map_center && map_settings.map_center.center_coordinates) {
            if (!isNaN(parseInt(map_settings.map_center.center_coordinates.lat)) && !isNaN(parseInt(map_settings.map_center.center_coordinates.lon))) {
              var map_center = new google.maps.LatLng(map_settings.map_center.center_coordinates.lat, map_settings.map_center.center_coordinates.lon);
              bounds.extend(map_center);
              map.setCenter(map_center);
            }
          }
          else {
            map.setCenter(bounds.getCenter());
          }

          // Process settings of the directions plugin.
          if (typeof map_settings.directions != 'undefined' && map_settings.directions.enabled) {
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsService = new google.maps.DirectionsService;
            directionsDisplay.setMap(map);
            if (map_settings.directions.steps) {
              var route_details_id = 'steps-' + maps[i];
              directionsDisplay.setPanel(document.getElementById(route_details_id));
            }
            var button_id = 'find-directions-' + maps[i];

            var current_location = 'getting_directions_location-' + maps[i];
            if (document.location.protocol === 'https:') {
              document.addEventListener('geolocation_error', function(error){
                document.getElementById('directions' + maps[i]).getElementsByClassName('error-message').item(0).textContent = error.code;
              });
              document.getElementById(current_location).addEventListener('change', function () {
                var address = 'getting_directions_address-' + maps[i];
                if (this.checked) {
                  settings.user_current_location = settings.user_current_location || false;
                  var user_location = new GeolocationMarker(map);
                  if (user_location) {
                    settings.user_current_location = user_location;
                    document.getElementById(address).style.display = 'none';
                  }
                } else {
                  document.getElementById(address).style.display = 'block';
                }
              });
            } else {
              document.getElementById(current_location).parentNode.style.display = 'none';
            }
            document.getElementById(button_id).addEventListener('click', function() {
              calculateAndDisplayRoute(directionsService, directionsDisplay, maps[i], map_locations[0]);
            });
          }

          // This is needed to set the zoom after fitbounds.
          google.maps.event.addListener(map, 'zoom_changed', function () {
            var zoomChangeBoundsListener =
              google.maps.event.addListener(map, 'bounds_changed', function (event) {
                var current_zoom = this.getZoom();
                if (current_zoom > parseInt(map_settings.zoom.default) && map.initialZoom == true) {
                  // Change max/min zoom here.
                  this.setZoom(parseInt(map_settings.zoom.default) - 1);
                }
                map.initialZoom = false;
                google.maps.event.removeListener(zoomChangeBoundsListener);
              });
          });

          map.initialZoom = true;
          map.fitBounds(bounds);
          settings.initialized_styles_google_maps = settings.initialized_styles_google_maps || [];
          settings.initialized_styles_google_maps[maps[i]] = {map: map, markers: markers, cluster: markerCluster, spider: markerSpiderfier, heatmap: heatmap};
        }
      }
      // Prevents piling up generated map ids.
      settings.styled_google_map = [];
    }
  };

})(Drupal);
