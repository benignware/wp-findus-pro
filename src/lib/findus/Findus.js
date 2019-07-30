import Map from './Map';

function findus(element, options = {}) {
  return new Map(element, options);
}

global.findus = findus;

export default findus;
