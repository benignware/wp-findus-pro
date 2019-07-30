import Geocoder from './Geocoder';
import filterPrefixedOptions from './filterPrefixedOptions';
import loadMaps from './loadMaps';

/**
 * FindUs Implementation
 */
function Findus(elem, options) {
  let googlemaps = null;

  /*if (!google || !googlemaps) {
    console.error("jquery-findus needs Google Maps API");
    return;
  }*/

  var
    defaults = {
      address: "",
      autoShow: true,
      bindResize: true,
      content: "",
      info: {
        // InfoWindow options
      },
      map: {
        // Map options
        zoom: 14,
        // mapTypeId: googlemaps.MapTypeId.ROADMAP,
        disableDefaultUI: false,
        draggable: false,
        zoomControl: false,
        scrollwheel: false,
        disableDoubleClickZoom: true
      },
      marker: {
        // Marker options
        // animation: googlemaps.Animation.DROP
      },
      minWidth: 0,
      minHeight: 460
    },
    opts = {
      marker: {},
      map: {},
      info: {}
    },
    instance = this,
    // $elem = $(elem),
    content = elem.innerHTML.replace(/^\s+|\s+$/g, ''),
    center,
    centerTimeoutId,
    map,
    marker,
    infoWindow,
    infoWindowTimeoutId,
    geocodeResult = null;

  function resizeHandler(e) {
    instance.resize();
  }

  function centerChanged(event) {
    center = map.getCenter();
  }

  function markerClickHandler(event) {
    if ((opts.content || opts.address) && infoWindow) {
      var infoWindowOpened = (infoWindow.getMap());
      if (infoWindowOpened) {
        infoWindow.close();
      } else {
        infoWindow.open(map, marker);
      }
    }
  }

  function mapClickHandler() {
    if (infoWindow) {
      infoWindow.close();
    }
  }

  // Update marker
  function updateMarker() {

    var markerOptions = {
      ...opts.marker,
      map: map,
      position: center
    };

    var markerPosition = marker && marker.getPosition() || null;

    if (marker) {
      // Update marker
      marker.setOptions(markerOptions);
    } else {
      // Init marker
      marker = new googlemaps.Marker(markerOptions);
      // Init marker listeners
      googlemaps.event.addListener(marker, 'click', markerClickHandler);
    }

    // Trigger update on infoWindow
    updateInfoWindow();

    // Make sure that infoWindow is defined
    if (!infoWindow) {
      return;
    }

    // Open infowindow
    clearTimeout(infoWindowTimeoutId);
    if (opts.info && opts.autoShow && !infoWindow.getMap() && (!markerPosition || markerPosition.lat() !== center.lat() && markerPosition.lng() !== center.lng())) {
      infoWindowTimeoutId = setTimeout(function() {
        infoWindow.open(map, marker);
        // FIXME: Google Maps icon needs max-width
        // $(elem).find('img[src*="gstatic.com/"], img[src*="googleapis.com/"]').css('max-width', 'none');

        [
          ...document.querySelectorAll(`img[src*="gstatic.com/"]`),
          ...document.querySelectorAll(`img[src*="googleapis.com/"`)
        ].forEach((icon) => icon.style.maxWidth = 'none');

        marker.setAnimation(null);
      }, marker.getAnimation() ? 700 : 350);
    }
  }

  // Update infowindow
  function updateInfoWindow() {
    var infoOpts;
    if (opts.info) {
      infoOpts = {
        ...opts.info,
        content: opts.content || (
          opts.address || geocodeResult && geocodeResult.formatted_address
        ).split(/,|\n|<\s*br\s*\/?\s*>/).join("<br/>")
      };
      if (infoWindow) {
        // Update InfoWindow
        infoWindow.setOptions(infoOpts);
      } else {
        // Init InfoWindow
        infoWindow = new googlemaps.InfoWindow(infoOpts);
      }
    } else {
      if (infoWindow) {
        // Close InfoWindow
        infowindow.close();
      }
    }
  }

  // Update map
  function updateMap() {
    if (!center) {
      return;
    }

    // Get map options
    var mapOptions = {
      ...defaults.map,
      ...opts.map,
      center: center
    };

    if (map) {
      // Update map
      map.setOptions(mapOptions);
    } else {

      // Apply container size before map is initialized
      resizeContainer();

      // Init map
      map = new googlemaps.Map(elem, {
        ...mapOptions,
        center: {
          lat: mapOptions.center.lat(),
          lng: mapOptions.center.lng(),
        }
      });
      console.log('map created', mapOptions);
      // Init map listeners
      googlemaps.event.addListener(map, "click", mapClickHandler);
    }
    // Update marker after tiles have been loaded
    googlemaps.event.addListenerOnce(map, 'tilesloaded', function() {
      console.log('update marker');
      updateMarker();
    });

  }

  /**
   * Updates the component
   * @param {Object} options
   */
  this.update = function(options = {}) {

    const ownProps = [ 'map', 'marker', 'info', 'address', 'latitude', 'longitude', 'content' ];

    opts = {
      ...opts,
      ...options,
      map: {
        ...opts.map,
        ...options.map,
        ...Object.assign({}, ...Object.entries(options)
          .filter(([ key ]) => !ownProps.includes(key))
          .map(([ key, value ]) => ({
            [key]: value
          }))
        ),
      },
      marker: {
        ...opts.marker,
        ...options.marker
      },
      info: {
        ...opts.info,
        ...options.info
      }
    };

    console.log('UPDATE...', options);

    // Make sure that api has been loaded
    if (!googlemaps.Geocoder || !googlemaps.LatLng) {
      return;
    }

    const geocoder = new Geocoder();

    if (opts.latitude && opts.longitude) {
      // By coordinates
      console.log('get by coordinates...');
      center = new googlemaps.LatLng(opts.latitude, opts.longitude);
      if (!opts.content && !opts.address) {
        // Reverse geocode location
        geocoder.geocode( {
          location: {
            latitude: opts.latitude,
            longitude: opts.longitude
          }
        }, function(results, status) {
          geocodeResult = results[0];
          if (status === googlemaps.GeocoderStatus.OK) {
            center = geocodeResult.geometry.location || center;
            updateMap();
          } else {
            console.warn("Geocoder returned with error: ", status);
          }
        });
      } else {
        // No Geocoding required, immediately update map
        updateMap();
      }

    } else if (opts.content || opts.address) {
      // Geocode by address or content
      console.log('geocde by address', opts.address || opts.content);
      geocoder.geocode({
        address: opts.address || opts.content
      }, function(results, status) {

        console.log('results', results);
        geocodeResult = results[0];
        if (status === googlemaps.GeocoderStatus.OK) {

          console.log('status ok');
          center = geocodeResult.geometry.location;

          console.log('center', center);
          updateMap();
        } else {
          console.warn("Geocoder returned with error: ", status);
        }
      });
    }

    // Resize
    this.resize();
  };

  this.destroy = function() {

  }

  // Deprecated, use update(options) instead
  this.setOptions = function(options) {
    this.update(options);
  };

  this.getOptions = function() {
    return { ...opts };
  };

  // Resize map container
  function resizeContainer() {
    // Set size
    var minHeight = typeof opts.minHeight === 'function' ? opts.minHeight.call(this, options) : opts.minHeight;
    var maxHeight = typeof opts.maxHeight === 'function' ? opts.maxHeight.call(this, options) : opts.maxHeight;

    elem.style.minHeight = opts.minHeight || '';
    elem.style.maxHeight = opts.maxHeight || '';
    elem.style.color = 'black';

    console.log('resize container...');
  }

  /**
   * Update geometry
   */
  this.resize = function() {

    if (opts.latitude && opts.longitude || opts.address || opts.content) {
      // Only resize container if options have been specified
      resizeContainer();
    }

    console.log('resize...', map);

    if (map) {
      // Adjust center
      googlemaps.event.clearListeners(map, 'center_changed');

      console.log('adjust center');

      window.clearTimeout(centerTimeoutId);
      centerTimeoutId = window.setTimeout(function() {
        console.log('resize set center...', center);
        map.setCenter(center);
        googlemaps.event.addListener(map, 'center_changed', centerChanged);
      }, 100);

      // Resize map
      googlemaps.event.trigger(map, 'resize');
    }

  };

 // Clear elem
 elem.innerHTML = '';

 // Get options including data-attribtues

 opts = {
   ...defaults,
   content,
   ...options,
   // ...filterPrefixedOptions($elem.data(), ["map", "marker", "info"])
 };

 loadMaps().then(() => {
   console.log('maps loaded');
   googlemaps = global.google.maps;

   this.update(opts);

   // Initial resize
   this.resize();
 });

  // Init resize handler
  // $(window).off('resize', resizeHandler);

  window.removeEventListener('resize', resizeHandler);

  if (opts.bindResize) {
    // $(window).on('resize', resizeHandler);
    window.addEventListener('resize', resizeHandler);
  }
}

global.Findus = Findus;

export default Findus;
