function RequestQueue() {
  this.queue = [];
  this.isRunning = false;
  this.add = function(geocoderRequest) {
    this.queue.push(geocoderRequest);
    if (!this.isRunning) {
      this.next();
    }
  };
  this.next = function() {
    var
      requestQueue = this,
      geocoderRequest = this.queue.shift();
    if (geocoderRequest) {
      this.isRunning = true;
      geocoderRequest.send(function() {
        requestQueue.next();
      });
    } else {
      this.isRunning = false;
    }
  };
}

export default RequestQueue;
