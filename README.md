# ProductCatalog

This is a custom Drupal module to show-case the display and configuration of a product Catalog.

## Instalation
1. Add the module toe modules folder on your Drupal installation.
2. Enable the module (it will automatically create the majority o the required configurations).
3. As an admin go to /admin/config/product_catalog/image_search/settings and configure the JoJ Image search API key and host.

## Usage
* This module installs a new content type (products), to store products with the fields, product name, description, price and image URL (field automatically populated by the Image Search service).
* The module has a data import form located in /admin/config/content/product-import to import the content to the products' content type.
* The module also creates a new view "Product Catalog" to render the list of all the products (including the price and images) on a single page. The Product Catalog page is located in /product-catalog.
* This also implement a favorite system on the product catalog view. The feature displays a fav start the user can interact with and make the product as favorite. This information is store in the DB and displayed every time the user visits the product catalog page.

