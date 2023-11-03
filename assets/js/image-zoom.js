let currentContainer;

export function imageZoom() {
  const images = document.querySelectorAll('figure:not(.banner) > img, picture > img');

  images.forEach((image) => {
    // Add title to figure
    image.parentElement.title = 'Cliquez pour zoomer';

    image.addEventListener('click', function (event) {
      // Prevent default click action
      event.preventDefault();

      if (currentContainer) {
        currentContainer.remove();
      }

      // Create container element
      const container = document.createElement('div');

      // Add container class
      container.className = 'zoom-container';

      // Copy image
      const content = event.target.parentElement.cloneNode(true);
      content.title = 'Cliquez pour fermer';
      container.appendChild(content);

      // Add click event to overlay
      container.addEventListener('click', function (event) {
        // Prevent default click action
        event.preventDefault();

        // Element is a figcaption
        if (event.target.tagName === 'FIGCAPTION') {
          return;
        }

        // Remove overlay and delete it
        container.remove();
      });

      // Set current overlay
      currentContainer = container;

      // Add overlay to body
      document.body.appendChild(container);
    });
  });
}