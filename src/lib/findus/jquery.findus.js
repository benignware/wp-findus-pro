const { Findus } = global;

// Add Plugin to registry
$.fn.findus = function() {
  var
    args = [].slice.call(arguments);
  return this.each(function() {
    return (function(instance) {
      var
        result;
      // Update or init plugin
      $(this).data('findus', instance = instance ? typeof args[0] === 'object' && instance.update(args[0]) && instance || instance : new FindUs(this, args[0]));
      result = typeof args[0] === 'string' && typeof instance[args[0]] === 'function' ? instance[args[0]].apply(instance, args.slice(1)) : result;
      // Return undefined or chaining element
      return typeof result !== 'undefined' ? result : this;
    }).call(this, $(this).data('findus'));
  });
};
