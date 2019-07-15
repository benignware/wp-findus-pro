import decodeEntities from './decodeEntities';
import {
  PATTERN_ITEM_INT_PHONE,
  PATTERN_ITEM_NO_LETTER,
  PATTERN_ITEM_EMAIL,
  PATTERN_ITEM_URL,
  PATTERN_EMAIL,
  PATTERN_INT_PHONE,
  PATTERN_URL
} from './constants';

const stringCache = {};

 function getGeocodeableString(string) {
  return stringCache[string] || (function(string) {
    // Look for an address tag
    string = string.match(/<address/) && $("<div>" + string + "</div>").find('address').html().trim() || string;
    // Decode entities
    string = decodeEntities(string);
    // Strip html tags and line breaks
    string = string.split(/<(?:.|\n|<br\s*\/?\s*>)*?>|\n+/gm)
      // Perform regex on chunks
      .map(function(string) {
        string = string.replace(PATTERN_ITEM_INT_PHONE, '');
        string = string.replace(PATTERN_ITEM_NO_LETTER, '');
        string = string.replace(PATTERN_ITEM_EMAIL, '');
        string = string.replace(PATTERN_ITEM_URL, '');
        string = string.replace(PATTERN_INT_PHONE, '');
        string = string.replace(PATTERN_EMAIL, '');
        string = string.replace(PATTERN_URL, '');
        return string;
      })
      // Trim paragraphs
      .map(function(string) {
        return string.trim();
      })
      // Remove empty paragraphs
      .filter(function(string) {
        return string;
      }).join(", ");
    return string;
  })(string);
};

export default getGeocodeableString;
