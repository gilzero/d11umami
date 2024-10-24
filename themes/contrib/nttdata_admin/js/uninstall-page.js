(function ($, Drupal, once) {
Drupal.behaviors.toggleDescription = {
    attach: function (context, settings) {
        // Create the button element.
        let $button = $('<button>', {
            class: 'link tableresponsive-toggle',
            text: Drupal.t('Show details')
        });

        let toggleBtn = $('.description-toggle-button');
        // Append the button to the appropriate container.
        if (toggleBtn.length == 0) {
            $('.table-filter').after($button.addClass('description-toggle-button'));
            toggleBtn = $('.description-toggle-button');
        }

        // Find the modules' description in the table.
        let $modulesDescription = $('.system-modules-uninstall .responsive-enabled .description');
        // Toggle the visibility of the description table when the button is clicked.
        $(once('toggleDescription', toggleBtn)).on('click', function (e) {
            e.preventDefault();
            $modulesDescription.toggle();
            if ($modulesDescription.is(":visible")) {
                toggleBtn.text(Drupal.t('Hide details'));
            } else{
                toggleBtn.text(Drupal.t('Show details'));
            }
        });

        // Function to handle window resize events.
        function handleResize() {
            // Add toggle functionality only when window width is below a certain number (e.g., 768px).
            if ($(window).width() < 768) {
                // Create the button element.
                $modulesDescription.hide();
                toggleBtn.text(Drupal.t('Show details'));
                toggleBtn.show();
            } else {
                $modulesDescription.show();
                toggleBtn.hide();
            }
        }

        // Call handleResize initially.
        handleResize();
        // Attach resize event handler to window.
        $(window).on( "resize", handleResize);
    }
};
})(jQuery, Drupal, once);