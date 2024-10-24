/**
 * @file
 * Overriding core's message functions to add DSFR style.
 */

((Drupal) => {
  /**
   * Overrides message theme function.
   *
   * @param {object} message
   *   The message object.
   * @param {string} message.text
   *   The message text.
   * @param {object} options
   *   The message context.
   * @param {string} options.type
   *   The message type.
   * @param {string} options.id
   *   ID of the message, for reference.
   *
   * @return {HTMLElement}
   *   A DOM Node.
   */
  Drupal.theme.message = ({ text }, { type, id }) => {
    const messageWrapper = document.createElement('div');

    messageWrapper.setAttribute(
      'class',
      `fr-alert fr-alert--${type === 'status' ? 'success' : type}`,
    );
    messageWrapper.setAttribute(
      'role',
      type === 'error' || type === 'warning' ? 'alert' : 'status',
    );
    messageWrapper.setAttribute('aria-labelledby', `${id}-title`);
    messageWrapper.setAttribute('data-drupal-message-id', id);
    messageWrapper.setAttribute('data-drupal-message-type', type);

    messageWrapper.innerHTML = `
      ${text}
      <button class="fr-btn--close fr-btn" title="${Drupal.t('Hide message')}">
        ${Drupal.t('Hide message')}
      </button>
  `;

    messageWrapper.querySelector(".fr-btn--close").addEventListener("click", (event) => {
      messageWrapper.remove();
    });

    return messageWrapper;
  };
})(Drupal);
