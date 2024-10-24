# Solo - W3CSS Theme

Solo is the third generation of the W3CSS Theme and has been thoroughly
revamped. This version does not rely on jQuery, making the inclusion of
the w3.css library optional.

Should you wish to construct a more intricate website, consider utilizing
the [Paragraphs Bundles](https://www.drupal.org/project/paragraphs_bundles)
module. This module equips you with robust capabilities to create complex
components such as 3D cards, slideshows, 3D carousel, tabs, cards,
accordions, and more.

## Features

### 24 Regions

The theme offers 24 distinct regions, each with its own unique settings,
allowing for a high degree of customization and control over the look and
feel of your website. The regions in this theme are designed to accommodate
a wide variety of content types and layouts.

### Intelligent Design

If a region is not being used, its settings will be hidden. This helps to
keep the theme's interface clean and uncluttered, making it easier for you
to find and adjust the settings you need.

### Responsive Design

Solo theme is fully responsive, ensuring your website will look and function
perfectly on all devices, including desktops, tablets, and smartphones.

### Five Breakpoints

The Solo theme provides 5 breakpoints for the site and 5 for the menu,
enabling you to display the most optimal layout to the user.

### Three-Level Main Menu and Megamenu

Includes a three-level main menu, enabling you to create a complex navigation
structure with ease. Hoverable or clickable.

### Sidebar Main Menu

In addition to the three-level main menu, the theme also includes a sidebar
main menu, providing an additional navigation option.

### Customized Colors

Customize 15 color fields per region to align the colors with your brand
identity.

### Customized Layout

Customize your website's layout with seven styles for two-column layouts,
three styles for three-column layouts, and four styles for four-column layouts.

### Conclusion

Solo is perfect for those who want a high degree of control over their
website's design, but also value simplicity and ease of use. Whether you are
creating a simple blog or a complex corporate website, Solo theme will provide
you with the tools you need to create an effective online presence.

## Solo Regions

The Solo theme encompasses two types of regions: **Single and Grouped regions**.

### [1] Single Regions

The theme consists of the following single regions:

1. Popup Login Block
2. Fixed Search Block
3. Header
4. Primary Sidebar Menu
5. Primary Menu
6. Welcome Text
7. Highlighted
8. System Messages
9. Page Title
10. Breadcrumb
11. Footer Menu Container

All single regions are visible on all site pages, except 'Page Title' (not
visible on the home page) and 'Welcome Text' (visible on the home page only).

### [2] Grouped Regions

The theme also includes the following grouped regions:

| TOP                  | MAIN              | BOTTOM                 | FOOTER              |
|----------------------|-------------------|------------------------|---------------------|
| 1. Top First Container | 1. Left Sidebar | 1. Bottom First Container | 1. Footer First Container |
| 2. Top Second Container| 2. Content      | 2. Bottom Second Container | 2. Footer Second Container |
| 3. Top Third Container | 3. Right Sidebar| 3. Bottom Third Container | 3. Footer Third Container |
|                        |                 | 4. Bottom Fourth Container |                     |

# Theme Architecture

The `div` element with the class `page-wrapper` wraps around 2
4 distinct regions.
Both the `page-wrapper` and each of these regions can have up to 15 different
color inputs applied to them. The color inputs can be categorized as follows:

## Color Categories

- **General**
  - Background color
  - Text color
  - Border color
  - HTML heading color (h1, h2, h3)
  - Background color for Form Input Field

- **Links**
  - Text link color
  - Text link hover color

- **Menus**
  - Text menu link color
  - Text menu link hover color
  - Background menu link color
  - Background menu link hover color

- **Buttons**
  - Text color for buttons
  - Text hover color for buttons
  - Background button color
  - Background button hover color

**Note:** The construction of the 15 colors is as follows: First, apply global
colors to the page wrapper. Then, for each region, apply specific colors. Note
that the colors of the regions will override the global colors.

# Theme Settings

## Blueprint

The blueprint for the Solo theme regions is a structured layout that defines
the various sections of a web page. Here's a brief description of each region:

- `highlighted`: This block is for highlighted or important content.
- `popup_login_block`: This is a block that pops up for user login.
- `fixed_search_block`: This block is for the search bar, fixed in place.
- `header`: The topmost part of the webpage, usually with the logo and
navigation.
- `primary_sidebar_menu`: The main sidebar menu, on the left or right of the
page.
- `primary_menu`: The main navigation menu, usually in the header.
- `welcome_text`: A text block welcoming the user to the website.
- `top_first`, `top_second`, `top_third`: Container blocks at the top of the
page.
- `system_messages`: This block displays system messages to the user.
- `page_title`: Displays the title of the current page.
- `breadcrumb`: Navigation links showing the user's path to the current page.
- `sidebar_first` (Left Sidebar), `sidebar_second` (Right Sidebar).
- `content`: The main content area of the webpage.
- `bottom_first`, `bottom_second`, `bottom_third`, `bottom_fourth`: Bottom
blocks.
- `footer_first`, `footer_second`, `footer_third`: Footer container blocks.
- `footer_menu`: The navigation menu in the footer.

Each of these regions can contain various types of content, such as text,
images, links, and more, depending on the needs of the website.

This blueprint serves as a starting point and guide for developers, designers,
and other site builders involved in the development process.

## Global Site Settings

- Solo theme provides features to adjust website width and breakpoints.
- It allows altering spaces between regions and modifying layout settings.
- Solo theme enables font size modifications and custom data formats.
- Ability to import Google fonts and add custom CSS to the header.
- Modify login, register, and password pages; change header and menu order.
- Offers animation features and layout changes for multiple value fields.
- Apply reading mode to content types, setting max width for content regions.

## Libraries and Fonts Settings

- Feature to upload and use the w3.css library locally.
- Modify the font family of the entire website with Google fonts.
- Alter font family of h1, h2, h3 with Google fonts selections.
- Offers 25 special Google fonts for application to HTML tags.

## Page Wrapper Settings

- Input field for predefined CSS classes from Solo or third-party libraries.
- 15 color input fields for global use across the site.

## Predefined Color Scheme Settings

- Dropdown select list with 50 predefined Color Scheme Themes for the site.

## Social Media Links Settings

- Options for social media icon sizes (Small, Medium, Large).
- Input color field to change social media icon colors.
- Checkbox to show or hide social icons.
- Eight social text fields and one RSS field.

# Single Regions Settings

## Highlighted Settings

- Change visibility options for the highlighted block.
- Text input for predefined CSS classes.
- 15 color input fields for this region.

## Popup Login Block Settings

- Input field for predefined CSS classes.
- Checkbox for popup block login feature.
- Text field to customize login wording.
- 15 color input fields for this region.

## Fixed Search Block Settings

- Input field for predefined CSS classes.
- 15 color input fields for this region.

# Theme Settings Documentation

## Header Settings

- Text input for predefined CSS classes from Solo or third-party libraries.
- Checkbox to center Site's Name, Slogan, and Logo.
- Checkbox to adjust positioning of Search Icon, User Menu, Sidebar Menu, and
  Sidebar Hamburger to the left.
- Dropdown for 24 predefined CSS text animations for the Site name.
- 15 color input fields for this region.

## Primary Sidebar Menu Settings

- Text input for predefined CSS classes.
- Checkbox to apply borders to menu items.
- 15 color input fields for this region.

## Primary Menu Settings

- Text input for predefined CSS classes.
- Checkbox for hover-to-show main menu dropdowns.
- Checkbox to apply borders to menu items.
- Checkbox for even space distribution between menu items.
- 15 color input fields for this region.
- Checkbox for Mega Menu transformation with additional fields:
  - Mega Menu Layout options (2-4 columns with various percentages).
  - Mega Menu Header checkbox for second level headers.

## Welcome Settings

- Text input for predefined CSS classes.
- 15 color input fields for this region.

## System Message Settings

- Text input for predefined CSS classes.
- 15 color input fields for this region.

## Page Title Settings

- Text input for predefined CSS classes.
- Dropdown for 24 predefined CSS text animations for the page title.
- 15 color input fields for this region.

## Breadcrumb Settings

- Text input for predefined CSS classes.
- Checkbox to hide page title from the breadcrumb.
- 15 color input fields for this region.

## Footer Menu Settings

- Text input for predefined CSS classes.
- Dropdown for 24 predefined CSS text animations for the page title.
- 15 color input fields for this region.

## Copyright and Credit Settings

- Text input for predefined CSS classes.
- 15 color input fields for this region.
- Text field for dynamic copyright year.
- Checkbox to show or hide credit.

## Grouped Regions

Regions grouped together are enclosed within a div. While the region wrapper
has its own settings, individual regions maintain their own settings.

### The (TOP, MAIN, BOTTOM, FOOTER) Region Wrapper Settings

- Two, three, and four columns settings with various percentage distributions.
- Change visibility options (Visible on all pages, Home Page Only, All Pages
Except Home Page).
- Checkbox for animation on individual region's border (Not for Main).
- Checkbox to apply borders to each individual region.
- Radio field for round corners on each individual region.
- Text input for predefined CSS classes.
- 15 color input fields for this region.

### Individual Region Settings

- Text input for predefined CSS classes.
- 15 color input fields for this region.
