const API = 'https://maps.googleapis.com';

const getScript = () => document.querySelector(`script[src^='${API}']`);

export default function loadMaps() {
  return new Promise((resolve, reject) => {
    if (global.google) {
      resolve(global.google.maps);
      return;
    }

    const script = getScript();

    script.addEventListener('load', () => {
      resolve(global.google.maps);
    });
  });
}
