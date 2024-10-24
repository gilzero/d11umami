<?php

/**
 * @file
 * Solo Theme.
 *
 * Filename:     solo.theme
 * Website:      http://www.flashwebcenter.com
 * Description:  template
 * Author:       Alaa Haddad http://www.alaahaddad.com.
 */

/**
 * The theme settings.
 */

$form['solo_settings']['settings_blueprint'] = [
  '#type' => 'details',
  '#title' => t("<strong class='purple'>Blueprint for The Theme Regions</strong> - Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description' => t("

<strong>This is a graphical representation illustrating the positioning of each region on the page.</strong>
<div class='blueprint page-wrapper'>
        <div class='highlighted'><h2>Highlighted</h2><p>Spotlight section for featured content or important announcements.</p></div>
        <div class='primary-sidebar-menu'><h2>Primary Sidebar Menu</h2><p>Navigation menu with links to main sections of the site.</p></div>
        <div class='fixed-search-block'><h2>Fixed Search Block</h2><p>Search bar, always accessible for quick content searching.</p></div>
        <div class='popup-login-block'><h2>Popup Login Block</h2><p>User login area, appearing as a modal or popup element.</p></div>
        <div class='primary-menu'><h2>Primary Menu</h2><p>Main navigation menu, typically at the top of the page.</p></div>
        <div class='header'><h2>Header</h2><p>Topmost section, often contains logo and primary navigation.</p></div>
        <div class='welcome-text'><h2>Welcome Text</h2><p>Introduction or welcome message for visitors to the site.</p></div>
        <div class='top-regions'>
            <div class='top-one'><h3>Top One</h3><p>First top region, possibly for news or updates.</p></div>
            <div class='top-two'><h3>Top Two</h3><p>Second top region, suitable for highlights or features.</p></div>
            <div class='top-three'><h3>Top Three</h3><p>Third top region, can be used for additional information.</p></div>
        </div>
        <div class='system-messages'><h2>System Messages</h2><p>Area for displaying system notifications or user feedback.</p></div>

        <div class='page-title-solo'><h2>Page Title</h2><p>Descriptive title of the current page or section.</p></div>

        <div class='breadcrumb'><h2>Breadcrumb</h2><p>Navigation aid showing the user's location in the site hierarchy.</p></div>
        <div class='main-regions'>
            <div class='sidebar-first'><h3>Sidebar First</h3><p>Left sidebar, good for secondary navigation or widgets.</p></div>
            <div class='content'><h3>Content</h3><p>Main content area, the primary focus of the page.</p></div>
            <div class='sidebar-second'><h3>Sidebar Second</h3><p>Right sidebar, ideal for advertisements or additional links.</p></div>
        </div>
        <div class='bottom-regions'>
            <div class='bottom-one'><h3>Bottom One</h3><p>Lower section, possibly for less prominent links or info.</p></div>
            <div class='bottom-two'><h3>Bottom Two</h3><p>Another lower section, for diverse auxiliary content.</p></div>
            <div class='bottom-three'><h3>Bottom Three</h3><p>Additional bottom section for various uses.</p></div>
            <div class='bottom-four'><h3>Bottom Four</h3><p>Final bottom section, can be used for extra details.</p></div>
        </div>
        <div class='footer-regions'>
            <div class='footer-one'><h3>Footer One</h3><p>First part of the footer, often for essential links.</p></div>
            <div class='footer-two'><h3>Footer Two</h3><p>Second part of the footer, for additional resources.</p></div>
            <div class='footer-three'><h3>Footer Three</h3><p>Third part of the footer, typically for contact info.</p></div>
        </div>
        <div class='footer-menu'><h2>Footer Menu</h2><p>A menu in the footer for secondary navigation purposes.</p></div>
        <div class='copyright'><h2>Copyright</h2><p>Copyright and legal information, typically found at the very bottom.</p></div>
    </div>


"),
];
