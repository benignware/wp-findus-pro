import RequestQueue from './RequestQueue';
import GeocoderRequest from './GeocoderRequest';
import getGeocodeableString from './getGeocodeableString';

function Geocoder() {
  const geocoder = this;
  const requestQueue = new RequestQueue();
  const responseCache = {};

  this.geocode = function(options, callback) {
    options = options || {};
    if (typeof arguments[0] === "string" && typeof arguments[1] === "function") {
      // Geocode location from string
      options.address = arguments[0];
    } else if (!isNaN(parseFloat(arguments[0])) && !isNaN(parseFloat(arguments[1]))) {
      // Reverse geocode address from location
      options.location = {
        latitude: arguments[0],
        longitude: arguments[1]
      };
      callback = arguments[2];
    }

    // Optimize address string
    if (options.address) {
      options.address = getGeocodeableString(options.address);
    }

    if (options.location && options.location.latitude && options.location.longitude) {
      options.location = new google.maps.LatLng(options.location.latitude, options.location.longitude);
    }

    requestQueue.add(new GeocoderRequest(options, function(results, status) {
      if (results && results.length || !options.address) {
        // Return successful result
        callback(results, status);
        return;
      }
      // If no exact match, geocode parts of the string
      var array = options.address.split(/(?:\n|,|<br\/?>)+/);
      var matchingResults = {};
      array.forEach(function(string) {
        // Geocode chunk
        requestQueue.add(new GeocoderRequest({
          address: string
        }, function(results, status) {
          // Capture full matches
          matchingResults[string] = status === google.maps.GeocoderStatus.OK && !results[0].partial_match && results[0] || false;
          if (Object.keys(matchingResults).length === array.length) {
            // Concatenate matching strings
            string = array.filter(function(string) {
              return matchingResults[string] !== false;
            }).join(", ");
            // Finally geocode successful matches
            geocoder.geocode({
              address: string
            }, function(results, status) {
              // Override response cache with concatenated string result
              responseCache[JSON.stringify(options)] = {
                results: results,
                status: status
              };
              callback(results, status);
            });
          }
        }));
      });
    }));
  };
};

export default Geocoder;
