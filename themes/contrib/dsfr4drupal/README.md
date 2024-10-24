# DSFR theme

## Introduction

This base theme bridges the gap between Drupal and the "Design Système de l'État" ([DSFR](https://www.systeme-de-design.gouv.fr/)).

Makes available all the components offered by the DSFR.

All that remains is to assemble the Twig components of this theme with your existing site building.

## Supported DSFR library version

Based on [DSFR](https://www.systeme-de-design.gouv.fr/) version **1.12.1**.

## How to use ?

The best way to take advantage of this theme is by creating a child theme: 

### Create a child theme
For the following instructions, replace `My Subtheme` by the name you want for your theme and `my_subtheme` by it's machine name.

1. In web/themes/custom, create a `my_subtheme` directory.
2. Within this directory, create a `my_subtheme.info.yml` with the sample content below.
3. Create a `my_subtheme.theme` file to hold your hooks.
4. Activate the theme on `/admin/appearance`

### my_subtheme.info.yml mandatory content
```
name: 'My Subtheme'
type: theme
base theme: dsfr4drupal
description: My DSFR Subtheme
package: Custom
core_version_requirement: ^10 || ^11
regions:
  header_tools_links: Header Tools Links
  header_tools_search: Header Tools Search
  header_navbar: Header Navbar
  breadcrumb: Breadcrumb
  content: Content
  footer_top: Footer Top
  footer_content: Footer Content
  footer_bottom: Footer Bottom
```
Off course, feel free to add libraries, or anything you need.

## License

**It is strictly forbidden to use the [DSFR](https://www.systeme-de-design.gouv.fr/)
outside of the French State's websites**
(including other public actors, such as territorial administrations).

[Scope of DSFR use](https://www.systeme-de-design.gouv.fr/utilisation-et-organisation/perimetre-d-application)
(available only in French)

## Recommandations

To be DSFR compliant, you can enable the "Inline form error" (inline_form_errors) Drupal core module.

## Supported modules

**Drupal 9, 10 and 11**

* [DSFR Editor](https://www.drupal.org/project/dsfr4drupal_editor)
* [DSFR Links](https://www.drupal.org/project/dsfr4drupal_links)

## Useful links

* [Documentation](https://www.systeme-de-design.gouv.fr/) (available only in French)
