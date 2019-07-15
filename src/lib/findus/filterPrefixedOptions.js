  /**
   * Converts data-options to camel-case while respecting object-prefixes
   */
export default function filterPrefixedOptions(options, prefixes) {
  var key, i, prefix, name;
  for (key in options) {
    for (i = 0; i < prefixes.length; i++) {
      prefix = prefixes[i];
      if (key.substring(0, prefix.length) === prefix && key.length > prefix.length) {
        name = key.substring(prefix.length, prefix.length + 1).toLowerCase() + key.substring(prefix.length + 1);
        options[prefix] = options[prefix] || {};
        options[prefix][name] = options[key];
        delete options[key];
      }
    }
  }
  return options;
}
