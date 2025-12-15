(function (Drupal, once) {
  Drupal.behaviors.staffMemberCards = {
    attach(context) {
      const elements = once('linkedStaffMemberCard', '.block-lb-staff-members .staff-member-item:has(.email a)', context);
      // Process each card.
      elements.forEach(processingCallback);
    }
  };

  // Adapted from https://css-tricks.com/block-links-the-search-for-a-perfect-solution/#aa-method-4-sprinkle-javascript-on-the-second-method
  function processingCallback(value, index) {
    const card = value;
    const mainLink = value.querySelector('.email a');
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
