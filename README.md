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
| V1.0.3   | LESS Styling               | LESS Preprocessor, Variables, Mixins, Nesting, Luma Integration           | `feature/v1.0.3-less-styling`             |
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

**Version 2.0.0** (Current Branch: `feature/v2.0.0-db-models-schema`)

This version demonstrates:
- Frontend routing configuration (`routes.xml`)
- Controller action implementation (`Controller/Index/View.php`)
- Layout XML file structure (`banners_index_view.xml`)
- Template file organization with semantic HTML
- **LESS preprocessor** (`view/frontend/web/css/source/_module.less`)
- **Declarative Schema** (`etc/db_schema.xml`) for database table definition
- **Model Class** (`Model/Banner.php`) extending AbstractModel with getter/setter methods
- **Resource Model** (`Model/ResourceModel/Banner.php`) for database operations
- **Collection Class** (`Model/ResourceModel/Banner/Collection.php`) with filtering methods
- **ORM Pattern** implementation following Magento 2 best practices
- Database indexing on `is_active` and `sort_order` columns
- Timestamps (`created_at`, `updated_at`) with automatic updates
- Custom collection methods for filtering active banners

### Accessing the Module

Once installed, visit the banner display page at:
```
http://your-magento-site.local/banners/index/view
```

The page displays banners with LESS styling. Database table is ready for CRUD operations.

---

## Development & Demonstration Notes

To view a specific concept, switch to the corresponding branch:
```bash
git checkout feature/v2.0.0-db-models-schema
```

**Important**: This project uses Mark Shust's Docker setup. Run all commands from the workspace root:

```bash
# From /path/to/hyva-tutorials (workspace root, not src/)
bin/magento setup:upgrade
bin/magento setup:static-content:deploy -f
bin/magento cache:clean
bin/magento cache:flush
```

After switching branches, always clear cache and run setup:upgrade as configuration files (e.g., `di.xml`, `db_schema.xml`, `routes.xml`, layout XML) change between versions. **For V1.0.3+**, you must also deploy static content to compile LESS files into CSS.

---

## Module Structure (V2.0.0)

```
Vodacom/SiteBanners/
├── Controller/
│   └── Index/
│       └── View.php                          # Frontend controller action
├── etc/
│   ├── module.xml                            # Module declaration (v2.0.0)
│   ├── db_schema.xml                         # Database schema (NEW in V2.0.0)
│   └── frontend/
│       └── routes.xml                        # Frontend routing configuration
├── Model/
│   ├── Banner.php                            # Banner Model (NEW in V2.0.0)
│   └── ResourceModel/
│       ├── Banner.php                        # Banner Resource Model (NEW in V2.0.0)
│       └── Banner/
│           └── Collection.php                # Banner Collection (NEW in V2.0.0)
├── view/
│   └── frontend/
│       ├── layout/
│       │   ├── banners_index_view.xml        # Page layout configuration
│       │   └── default.xml                   # Global layout (includes CSS)
│       ├── templates/
│       │   └── view.phtml                    # Frontend template
│       └── web/
│           └── css/
│               └── source/
│                   └── _module.less          # LESS stylesheet
├── composer.json                              # Composer package definition
├── registration.php                           # Module registration
└── README.md                                  # This file
```

---

## Learning Objectives (V2.0.0)

By exploring this version, you will understand:

1. **Declarative Schema**: How to define database tables using `db_schema.xml`
2. **Database Table Structure**: Column types, constraints, indexes, and primary keys
3. **Model Layer**: Creating Model classes extending `AbstractModel`
4. **Resource Model**: Implementing Resource Models for database operations
5. **Collections**: Building Collection classes for fetching multiple records
6. **ORM Pattern**: Using Magento's ORM instead of raw SQL queries
7. **Getter/Setter Methods**: Type-safe data access methods with proper type hints
8. **Cache Tags**: Implementing cache tags for efficient cache invalidation
9. **Event Prefixes**: Setting up event prefixes for observers
10. **Collection Filtering**: Custom methods for filtering data (e.g., active banners)
11. **Database Indexes**: Optimizing queries with proper indexing strategy
12. **Timestamps**: Automatic `created_at` and `updated_at` timestamp management
13. **Setup:Upgrade**: How `setup:upgrade` creates tables from declarative schema

---

## Version History

### Version 2.0.0 (Current)
**Branch:** `feature/v2.0.0-db-models-schema`  
**Focus:** Database schema, Models, Resource Models, Collections  
**Status:** ✅ Completed

**What's New:**
- Created declarative schema `etc/db_schema.xml` with `vodacom_sitebanners_banner` table
- Implemented Banner Model extending AbstractModel with full getter/setter methods
- Created Banner Resource Model for database operations
- Built Banner Collection with custom filtering methods (`addActiveFilter`, `getActiveBanners`)
- Added database indexes on `is_active` and `sort_order` for query optimization
- Implemented automatic timestamp management (`created_at`, `updated_at`)
- Added cache tags and event prefixes for extensibility
- Updated module version to 2.0.0

**Files Changed:**
- `etc/db_schema.xml` - NEW: Database schema definition
- `Model/Banner.php` - NEW: Banner Model with getter/setter methods
- `Model/ResourceModel/Banner.php` - NEW: Banner Resource Model
- `Model/ResourceModel/Banner/Collection.php` - NEW: Banner Collection with filtering
- `etc/module.xml` - Updated version to 2.0.0
- `README.md` - Updated documentation with V2.0.0 details

**Key Concepts Demonstrated:**
- Declarative Schema: XML-based database definition
- Model-Resource-Collection Pattern: Magento's ORM architecture
- AbstractModel: Base class for entity models
- AbstractDb: Base class for resource models
- AbstractCollection: Base class for collections
- Type Safety: Strict type hints on all methods
- Database Optimization: Proper indexing strategy

**Database Schema:**
```sql
CREATE TABLE vodacom_sitebanners_banner (
    banner_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    is_active SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (is_active),
    INDEX (sort_order)
);
```

**Usage Example:**
```php
// Load banner by ID
$banner = $bannerFactory->create()->load($bannerId);

// Get active banners
$collection = $bannerCollectionFactory->create()
    ->getActiveBanners();

// Filter collection
$collection = $bannerCollectionFactory->create()
    ->addActiveFilter(true)
    ->addSortOrderFilter('ASC');
```

### Version 1.0.3
**Branch:** `feature/v1.0.3-less-styling`  
**Focus:** LESS preprocessor and Luma theme integration  
**Status:** ✅ Completed

**What's New:**
- Converted plain CSS to LESS preprocessor
- Created `view/frontend/web/css/source/_module.less` with full LESS features
- Removed plain CSS file (`banners.css`)
- Implemented LESS variables for all design tokens (colors, spacing, typography)
- Created reusable mixins for box-shadow and border-radius
- Used LESS nesting for better code organization
- Demonstrated LESS functions (lighten, darken) for color manipulation
- Added responsive design with LESS variable calculations
- Updated module version to 1.0.3

**Files Changed:**
- `view/frontend/web/css/source/_module.less` - NEW: LESS stylesheet with variables, mixins, nesting
- `view/frontend/web/css/banners.css` - REMOVED: Plain CSS file (replaced by LESS)
- `etc/module.xml` - Updated version to 1.0.3
- `README.md` - Updated documentation with V1.0.3 details

**Key Concepts Demonstrated:**
- LESS Variables: Centralized design tokens for maintainability
- LESS Mixins: Reusable style patterns (`.box-shadow()`, `.border-radius()`)
- LESS Nesting: Hierarchical style organization matching HTML structure
- LESS Functions: Color manipulation with `lighten()` and `darken()`
- LESS Operations: Mathematical calculations for responsive sizing
- Luma Theme Integration: Proper LESS file structure for Magento themes
- Static Content Deployment: LESS compilation process

**Breaking Changes:**
- Removed plain CSS file (V1.0.2 approach)
- Now requires static content deployment to compile LESS to CSS
- LESS source files in `view/frontend/web/css/source/` directory structure

**Migration from V1.0.2:**
1. Remove `view/frontend/web/css/banners.css`
2. Create `view/frontend/web/css/source/_module.less`
3. Run `bin/magento setup:static-content:deploy -f` to compile LESS
4. Clear cache with `bin/magento cache:flush`

### Version 1.0.2
**Branch:** `feature/view-with-css-style`  
**Focus:** Plain CSS styling for Luma theme compatibility  
**Status:** ✅ Completed

**What's New:**
- Removed all Tailwind utility classes from templates
- Implemented semantic CSS class naming convention
- Added plain CSS stylesheet at `view/frontend/web/css/banners.css`
- Added responsive design with mobile-first approach
- Updated layout XML to include CSS file

### Version 1.0.1
**Branch:** `feature/v1.0.1-routing-composition`  
**Focus:** Controller composition over inheritance  
**Status:** ✅ Completed

**What's New:**
- Replaced controller inheritance with composition pattern
- Implemented `HttpGetActionInterface` for type-safe routing

### Version 1.0.0
**Branch:** `feature/v1.0.0-routing-layout`  
**Focus:** Basic routing, layouts, blocks, templates  
**Status:** ✅ Completed

**What's New:**
- Initial module structure
- Frontend routing configuration
- Layout XML and template files
- Basic block implementation

---

## Next Steps

To explore more advanced concepts:
- **V2.0.0**: Learn about database schema, models, and collections
- **V3.0.0**: Create admin grids and forms with UI Components
- **V4.0.0**: Implement service contracts and REST APIs
- **V5.0.0**: Master dependency injection and ViewModels
- **V6.0.0**: Extend core functionality using plugins
