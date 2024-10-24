(($, Drupal, once) => {
  'use strict';

  Drupal.behaviors.commerceInboxMessageToggleKeyboard = {
    attach: (context) => {
      once('commerceInboxMessageToggleKeyboard', '.inbox-message', context).forEach(
        (message) => {
          message.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
              if (message.classList.contains('opened')) {
                return;
              }
              message.classList.add('opened');
              if (message.classList.contains('unread')) {
                Drupal.ajax({
                  url: Drupal.url(
                    `admin/commerce/inbox-message/${message.dataset.messageId}/read`,
                  ),
                }).execute();
              }
            }
          });
        },
      );
    },
  };

  Drupal.behaviors.commerceInboxMessageNavKeyboard = {
    attach: (context) => {
      once('commerceInboxMessageNavKeyboard', '.inbox-message button, a', context).forEach(
        (message) => {
          message.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
              setTimeout(() => {
                const focusedElement = document.activeElement;
                const parentElement = focusedElement.parentElement;
                if (parentElement && (parentElement.classList.contains('inbox-message__status') || !parentElement.classList.contains('inbox-message__actions'))) {
                  const inboxMessage = e.target.closest('.inbox-message');
                  if (inboxMessage) {
                    e.stopPropagation();
                    inboxMessage.classList.remove('opened');
                  }
                }
              }, 0);
            }
          });
        },
      );
    },
  };
})(jQuery, Drupal, once);
