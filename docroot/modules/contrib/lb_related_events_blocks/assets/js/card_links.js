(function (Drupal, once) {
  Drupal.behaviors.eventCards = {
    attach(context) {
      const elements = once('linkedRelatedEventCard', '.node--type-lb-event.node--view-mode-related-content', context);
      // Process each card.
      elements.forEach(processingCallback);
    }
  };

  // Adapted from https://css-tricks.com/block-links-the-search-for-a-perfect-solution/#aa-method-4-sprinkle-javascript-on-the-second-method
  function processingCallback(value, index) {
    const card = value;
    const mainLink = value.querySelector('h3 a');

    card.addEventListener("click", handleClick);

    function handleClick(event) {
      const isTextSelected = window.getSelection().toString();
      const isContextualMenu = event.target.parentElement.classList.contains('contextual');
      if (!isTextSelected && !isContextualMenu) {
        mainLink.click();
      }
    }
  }
}(Drupal, once));
