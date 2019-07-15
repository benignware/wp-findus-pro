const responseCache = {};

function GeocoderRequest(options, callback) {
  let geocoderImpl = null;

  this.options = options || {};
  this.callback = callback;
  this.tries = 0;

  this.send = function(callback) {
    var
      geocoderRequest = this,
      responseCacheId = JSON.stringify(options),
      responseCacheResult = responseCache[responseCacheId];
    // Try to get the result from cache
    if (responseCacheResult) {
      // Function callback
      callback(responseCacheResult.results, responseCacheResult.status);
      // Object callback
      geocoderRequest.callback(responseCacheResult.results, responseCacheResult.status);
      return;
    }
    // Get geocoder instance
    geocoderImpl = geocoderImpl || new google.maps.Geocoder();
    // Actually send geocode request
    geocoderRequest.tries++;
    geocoderImpl.geocode( options, function(results, status) {
      if (geocoderRequest.tries < 2 && status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
        // Retry once more after delay
        window.setTimeout(function() {
          geocoderRequest.send(callback);
        }, GeocoderRequest.DELAY);
        return;
      }
      if (status !== google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
        // Save the result to cache
        responseCache[responseCacheId] = {
          results: results,
          status: status
        };
      }
      // Function callback
      callback(results, status);
      // Object callback
      geocoderRequest.callback(results, status);
    });
  };
}

GeocoderRequest.DELAY = 500;

export default GeocoderRequest;
