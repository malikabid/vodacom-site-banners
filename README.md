# Vodacom_SiteBanners

## Introduction

The **Vodacom_SiteBanners** module is a custom Magento 2 extension designed to demonstrate core architectural concepts, as covered in a foundational training slide deck.

This module enables administrators to create, manage, and display promotional banners on the storefront. Development is structured into six distinct versions, each introducing and isolating a specific Magento 2 feature or pattern.

---

## Module Version Roadmap

Development follows a progressive, branch-based approach to isolate key concepts. Each version corresponds to a major section of the Magento 2 Architecture training.

| Version  | Focus Area (Slides)         | Architectural Concept Demonstrated                                         | Git Branch Name                           |
|----------|----------------------------|---------------------------------------------------------------------------|-------------------------------------------|
| V1.0.0   | Routing, Layout, Blocks    | Request Flow (URL → Controller → Block)                                   | `feature/v1.0.0-routing-layout`           |
| V1.0.1   | Routing Composition        | Controller Composition Layout Handles, Template Organization              | `feature/v1.0.1-routing-composition`      |
| V1.0.2   | Static Assets & CSS        | Frontend Assets, CSS Styling, requirejs-config.js                         | `feature/view-with-css-style`             |
| V2.0.0   | Database & Models          | Declarative Schema, Models, Resource Models, Collections                  | `feature/v2.0.0-db-models-schema`         |
| V3.0.0   | Admin UI                   | Admin Menu, ACL, UI Components (Grid & Form)                              | `feature/v3.0.0-admin-uicomponents`       |
| V4.0.0   | Service Contracts & API    | Repository/Data Interfaces, Web API (`webapi.xml`)                        | `feature/v4.0.0-service-contract-api`     |
| V5.0.0   | Dependency Injection       | Constructor Injection, Factories, ViewModel Pattern                       | `feature/v5.0.0-di-factories-viewmodel`   |
| V6.0.0   | Extensibility (Plugins)    | Before, Around, After Plugins (Interceptors) on core Magento classes      | `feature/v6.0.0-extensibility-plugins`    |

---

## Installation & Setup

1. Place the `Vodacom` directory into your Magento installation's `app/code/` directory.
2. Enable the module:
    ```bash
    bin/magento module:enable Vodacom_SiteBanners
    ```
3. Run setup upgrade to install database schemas and dependencies:
    ```bash
    bin/magento setup:upgrade
    ```
4. Clear cache:
    ```bash
    bin/magento cache:clean
    ```

---

## Current Version Features

**Version 1.0.2** (Current Branch: `feature/view-with-css-style`)

This version demonstrates:
- Frontend routing configuration (`routes.xml`)
- Controller action implementation (`Controller/Index/View.php`)
- Layout XML file structure (`banners_index_view.xml`)
- Template file organization with semantic HTML
- **Static CSS assets** (`view/frontend/web/css/banners.css`)
- **Asset loading** via layout XML
- Professional styling with plain CSS
- Responsive design patterns

### Accessing the Module

Once installed, visit the banner display page at:
```
http://your-magento-site.local/banners/index/view
```

The page now features professionally styled banners with CSS styling.

---

## Development & Demonstration Notes

To view a specific concept, switch to the corresponding branch:
```bash
git checkout feature/view-with-css-style
```

**Important**: This project uses Mark Shust's Docker setup. Run all commands from the workspace root:

```bash
# From /path/to/hyva-tutorials (workspace root, not src/)
bin/magento setup:upgrade
bin/magento cache:clean
bin/magento cache:flush
```

After switching branches, always clear cache and run setup:upgrade as configuration files (e.g., `di.xml`, `db_schema.xml`, `routes.xml`, layout XML) change between versions.

---

## Module Structure (V1.0.2)

```
Vodacom/SiteBanners/
├── Controller/
│   └── Index/
│       └── View.php                          # Frontend controller action
├── etc/
│   ├── module.xml                            # Module declaration
│   └── frontend/
│       └── routes.xml                        # Frontend routing configuration
├── view/
│   └── frontend/
│       ├── layout/
│       │   └── banners_index_view.xml        # Layout configuration (includes CSS)
│       ├── templates/
│       │   └── view.phtml                    # Frontend template
│       └── web/
│           └── css/
│               └── banners.css               # Custom CSS styles
├── composer.json                              # Composer package definition
├── registration.php                           # Module registration
└── README.md                                  # This file
```

---

## Learning Objectives (V1.0.2)

By exploring this version, you will understand:

1. **Routing**: How URLs are mapped to controllers via `routes.xml`
2. **Controllers**: How to create frontend controller actions
3. **Layout System**: How layout XML files define page structure and load assets
4. **Templates**: How to create and organize `.phtml` template files with semantic HTML
5. **Static Assets**: How to organize and load CSS files in `view/frontend/web/`
6. **Asset Management**: How Magento processes and serves static assets
7. **CSS Styling**: Professional CSS techniques without preprocessors
8. **Request Flow**: The complete flow from URL to styled rendered output

---

## Next Steps

To explore more advanced concepts:
- **V2.0.0**: Learn about database schema, models, and collections
- **V3.0.0**: Create admin grids and forms with UI Components
- **V4.0.0**: Implement service contracts and REST APIs
- **V5.0.0**: Master dependency injection and ViewModels
- **V6.0.0**: Extend core functionality using plugins
