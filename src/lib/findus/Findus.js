import Geocoder from './Geocoder';
import filterPrefixedOptions from './filterPrefixedOptions';


const $ = global.jQuery;
/**
 * FindUs Implementation
 */
function Findus(elem, options) {
  const { google } = global;

  if (!google || !google.maps) {
    console.error("jquery-findus needs Google Maps API");
    return;
  }

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
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        draggable: false,
        zoomControl: false,
        scrollwheel: false,
        disableDoubleClickZoom: true
      },
      marker: {
        // Marker options
        //animation: google.maps.Animation.DROP
      },
      minWidth: 0,
      minHeight: 460
    },
    opts = {},
    instance = this,
    $elem = $(elem),
    content = $elem.html().replace(/^\s+|\s+$/g, ''),
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

    var
      markerOptions = $.extend(true, {}, opts.marker, {
        map: map,
        position: center
      }),
      markerPosition = marker && marker.getPosition() || null;

    if (marker) {
      // Update marker
      marker.setOptions(markerOptions);
    } else {
      // Init marker
      marker = new google.maps.Marker(markerOptions);
      // Init marker listeners
      google.maps.event.addListener(marker, 'click', markerClickHandler);
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
        $(elem).find('img[src*="gstatic.com/"], img[src*="googleapis.com/"]').css('max-width', 'none');
        marker.setAnimation(null);
      }, marker.getAnimation() ? 700 : 350);
    }
  }

  // Update infowindow
  function updateInfoWindow() {
    var infoOpts;
    if (opts.info) {
      infoOpts = $.extend(true, {}, opts.info, {
        content: opts.content || (opts.address || geocodeResult && geocodeResult.formatted_address).split(/,|\n|<\s*br\s*\/?\s*>/).join("<br/>")
      });
      if (infoWindow) {
        // Update InfoWindow
        infoWindow.setOptions(infoOpts);
      } else {
        // Init InfoWindow
        infoWindow = new google.maps.InfoWindow(infoOpts);
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
    var mapOptions = $.extend(true, {}, opts.map || defaults.map, {
      center: center
    });
    if (map) {
      // Update map
      map.setOptions(mapOptions);
    } else {
      // Apply container size before map is initialized
      resizeContainer();
      // Init map
      map = new google.maps.Map(elem, mapOptions);
      // Init map listeners
      google.maps.event.addListener(map, "click", mapClickHandler);
    }
    // Update marker after tiles have been loaded
    google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
      updateMarker();
    });

  }

  /**
   * Updates the component
   * @param {Object} options
   */
  this.update = function(options) {

    $.extend(opts, options);

    // Make sure that api has been loaded
    if (!google.maps.Geocoder || !google.maps.LatLng) {
      return;
    }

    const geocoder = new Geocoder();

    if (opts.latitude && opts.longitude) {
      // By coordinates
      center = new google.maps.LatLng(opts.latitude, opts.longitude);
      if (!opts.content && !opts.address) {
        // Reverse geocode location
        geocoder.geocode( {
          location: {
            latitude: opts.latitude,
            longitude: opts.longitude
          }
        }, function(results, status) {
          geocodeResult = results[0];
          if (status === google.maps.GeocoderStatus.OK) {
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
      geocoder.geocode({
        address: opts.address || opts.content
      }, function(results, status) {
        geocodeResult = results[0];
        if (status === google.maps.GeocoderStatus.OK) {
          center = geocodeResult.geometry.location;
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
    return $.extend({}, opts);
  };

  // Resize map container
  function resizeContainer() {
    // Set size
    var minHeight = typeof opts.minHeight === 'function' ? opts.minHeight.call(this, options) : opts.minHeight;
    var maxHeight = typeof opts.maxHeight === 'function' ? opts.maxHeight.call(this, options) : opts.maxHeight;
    $(elem).css('min-height', opts.minHeight || "");
    $(elem).css('max-height', opts.maxHeight || "");
    // Set text color
    $(elem).css('color', "black");
  }

  /**
   * Update geometry
   */
  this.resize = function() {

    if (opts.latitude && opts.longitude || opts.address || opts.content) {
      // Only resize container if options have been specified
      resizeContainer();
    }

    if (map) {
      // Adjust center
      google.maps.event.clearListeners(map, 'center_changed');
      window.clearTimeout(centerTimeoutId);
      centerTimeoutId = window.setTimeout(function() {
        map.setCenter(center);
        google.maps.event.addListener(map, 'center_changed', centerChanged);
      }, 0);

      // Resize map
      google.maps.event.trigger(map, 'resize');
    }

  };

 // Clear elem
 $elem.html('');

 // Get options including data-attribtues

 opts = {
   ...defaults,
   content: content,
   ...options,
   ...filterPrefixedOptions($elem.data(), ["map", "marker", "info"])
 };

 this.update(opts);

 // Initial resize
 this.resize();

  // Init resize handler
  $(window).off('resize', resizeHandler);

  if (opts.bindResize) {
    $(window).on('resize', resizeHandler);
  }
}

global.Findus = Findus;

export default Findus;
