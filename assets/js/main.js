import { parseEmojis } from './emojis';
import { parseHeaders } from './headers-links';
import { imageZoom } from './image-zoom';
import { highlightCode } from './highlight-code';

document.addEventListener('DOMContentLoaded', function () {
  console.log('DOMContentLoaded');
  parseHeaders();
  parseEmojis();
  imageZoom();
  highlightCode();
});