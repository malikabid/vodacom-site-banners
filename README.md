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
| V1.0.2   | Static Assets & CSS        | Frontend Assets, CSS Styling, requirejs-config.js                         | `feature/v1.0.2-view-with-css-style`      |
| V1.0.3   | LESS Styling               | LESS Preprocessor, Variables, Mixins, Nesting, Luma Integration           | `feature/v1.0.3-less-styling`             |
| V2.0.0   | Database & Models          | Declarative Schema, Models, Resource Models, Collections                  | `feature/v2.0.0-db-models-schema`         |
| V2.0.1   | Schema Patches             | Schema Patches, Database Alterations, Migration Strategy                  | `feature/v2.0.1-schema-patches`           |
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

**Version 3.0.3** (Current Branch: `feature/v3.0.3-crud-mass-actions`) ✅ **Completed**

This version demonstrates **CRUD Operations and Mass Actions**:

### CRUD Controllers
- **Save controller** (`Controller/Adminhtml/Banner/Save.php`)
  - Handles both create and update operations
  - Distinguishes between new and existing records via `banner_id` in POST data
  - Uses DataPersistor for form state preservation on errors
  - Redirects to grid or form (Save and Continue)
  - Proper error handling and success messages
- **Delete controller** (`Controller/Adminhtml/Banner/Delete.php`)
  - Deletes single banner with validation
  - Confirmation dialog via DeleteButton
  - Error handling for non-existent records
- **InlineEdit controller** (`Controller/Adminhtml/Banner/InlineEdit.php`)
  - AJAX-based editing directly in grid
  - JSON response format
  - Handles multiple field updates simultaneously
  - Validates banner existence before update

### Mass Actions Controllers
- **MassDelete** (`Controller/Adminhtml/Banner/MassDelete.php`)
  - Deletes multiple selected banners
  - Uses Magento\Ui\Component\MassAction\Filter
  - Shows count of deleted records
- **MassEnable** (`Controller/Adminhtml/Banner/MassEnable.php`)
  - Sets `is_active=true` for selected banners
  - Batch operation with success count
- **MassDisable** (`Controller/Adminhtml/Banner/MassDisable.php`)
  - Sets `is_active=false` for selected banners
  - Batch operation with success count

### Grid Enhancements
- **Inline editing configuration** in grid XML
  - Title column - text editor with required validation
  - Active column - Yes/No dropdown
  - Sort Order column - numeric input with validation
  - Click any field to edit, press Enter to save
- **Mass actions dropdown** in grid toolbar
  - Delete with confirmation dialog
  - Enable/Disable for bulk status changes
  - Uses checkbox selection

### Bug Fixes
- Fixed DeleteButton parameter (`banner_id` → `id`)
- Fixed Save controller to use POST `banner_id` instead of URL `id`
- Fixed mass actions to pass boolean types (not integers) to `setIsActive()`
- Proper handling of AUTO_INCREMENT for new records

**Previous versions included:**
- Frontend routing, controllers, layouts, templates (V1.0.0-V1.0.1)
- LESS styling with variables and mixins (V1.0.3)
- Declarative Schema with Models, Resource Models, Collections (V2.0.0)
- Schema Patches for database alterations (V2.0.1)
- Data Patches with 5 sample banners (V2.0.2)
- Admin Menu, ACL Foundation (V3.0.0)
- Grid UI Component (V3.0.1)
- Form UI Component (V3.0.2)

### Accessing the Module

**Frontend Display:**
Visit the banner display page at:
```
http://your-magento-site.local/banners/index/view
```

**Admin Panel (V3.0.0+):**
1. Log into Magento Admin
2. Navigate to **Vodacom > Site Banners** (custom top-level menu)
3. You'll see a placeholder page confirming admin foundation is working
4. In V3.0.1-V3.0.3, this will become a full CRUD interface with grid and forms

---

## Development & Demonstration Notes

To view a specific concept, switch to the corresponding branch:
```bash
git checkout feature/v2.0.1-schema-patches
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

## Module Structure (V3.0.2)

```
Vodacom/SiteBanners/
├── Controller/
│   ├── Index/
│   │   └── View.php                          # Frontend controller action
│   └── Adminhtml/
│       └── Banner/
│           ├── Index.php                     # Admin grid controller (V3.0.0)
│           ├── Edit.php                      # Edit form controller (NEW in V3.0.2)
│           └── NewAction.php                 # New banner controller (NEW in V3.0.2)
├── Block/
│   └── Adminhtml/
│       └── Banner/
│           └── Edit/
│               ├── GenericButton.php         # Base class for buttons (NEW in V3.0.2)
│               ├── BackButton.php            # Back button (NEW in V3.0.2)
│               ├── DeleteButton.php          # Delete button (NEW in V3.0.2)
│               ├── SaveButton.php            # Save button (NEW in V3.0.2)
│               └── SaveAndContinueButton.php # Save & Continue button (NEW in V3.0.2)
├── Ui/
│   └── Component/
│       └── Listing/
│           └── Column/
│               └── BannerActions.php         # Actions column for grid (V3.0.1)
├── etc/
│   ├── module.xml                            # Module declaration (v3.0.2)
│   ├── acl.xml                               # ACL permissions (V3.0.0)
│   ├── db_schema.xml                         # Database schema
│   ├── di.xml                                # Dependency injection (NEW in V3.0.1)
│   ├── frontend/
│   │   └── routes.xml                        # Frontend routing configuration
│   └── adminhtml/
│       ├── routes.xml                        # Admin routing (V3.0.0)
│       └── menu.xml                          # Admin menu (V3.0.0)
├── Model/
│   ├── Banner.php                            # Banner Model with date scheduling
│   ├── Banner/
│   │   └── DataProvider.php                  # Form data provider (NEW in V3.0.2)
│   └── ResourceModel/
│       ├── Banner.php                        # Banner Resource Model
│       └── Banner/
│           └── Collection.php                # Banner Collection with date filtering
├── Setup/
│   └── Patch/
│       ├── Schema/
│       │   └── AddActiveDatesToBannerTable.php  # Schema Patch (V2.0.1)
│       └── Data/
│           └── AddSampleBanners.php          # Data Patch (NEW in V2.0.2)
├── view/
│   ├── frontend/
│   │   ├── layout/
│   │   │   ├── banners_index_view.xml        # Page layout configuration
│   │   │   └── default.xml                   # Global layout (includes CSS)
│   │   ├── templates/
│   │   │   └── view.phtml                    # Frontend template
│   │   └── web/
│   │       └── css/
│   │           └── source/
│   │               └── _module.less          # LESS stylesheet
│   └── adminhtml/
│       ├── layout/
│       │   ├── vodacom_sitebanners_banner_index.xml  # Grid layout (V3.0.0, updated V3.0.1)
│       │   └── vodacom_sitebanners_banner_edit.xml   # Form layout (NEW in V3.0.2)
│       ├── templates/
│       │   └── banner/
│       │       └── index.phtml               # Admin template (V3.0.0, replaced in V3.0.1)
│       └── ui_component/
│           ├── vodacom_sitebanners_banner_listing.xml  # Grid UI Component (V3.0.1)
│           └── vodacom_sitebanners_banner_form.xml     # Form UI Component (NEW in V3.0.2)
├── composer.json                              # Composer package definition
├── registration.php                           # Module registration
└── README.md                                  # This file
```

---

## Learning Objectives (V3.0.2)

By exploring this version, you will understand:

1. **Form UI Components**: XML-based form configuration
2. **DataProvider Pattern**: Loading and preparing form data
3. **Button Provider Interface**: Creating reusable form buttons
4. **Generic Button Pattern**: Base class for button inheritance
5. **Form Field Types**: Input, textarea, checkbox, date fields
6. **Field Validation**: Required fields, numeric validation, date validation
7. **Data Persistence**: Using DataPersistor for form state
8. **Form Controllers**: Edit and NewAction patterns
9. **Forward Result**: Forwarding requests between controllers
10. **Form Layout Integration**: Connecting UI components to layouts

**Previous Version Learning:**
- Grid UI Components, Virtual Types (V3.0.1)
- Admin Routing, ACL, Menu Configuration (V3.0.0)
- Data/Schema Patches (V2.0.1-V2.0.2)
- Models, Resource Models, Collections (V2.0.0)
- LESS Styling (V1.0.3)
- Frontend Routing & Controllers (V1.0.0-V1.0.1)

---

## Version History

### Version 3.0.2 (Current)
**Branch:** `feature/v3.0.2-form-ui`  
**Focus:** Form UI Component  
**Status:** ✅ Completed

**What's New:**
- Created Form UI Component XML (`view/adminhtml/ui_component/vodacom_sitebanners_banner_form.xml`)
- Implemented DataProvider class for form data (`Model/Banner/DataProvider.php`)
- Created GenericButton base class for button reusability (`Block/Adminhtml/Banner/Edit/GenericButton.php`)
- Implemented button classes:
  - BackButton - Navigate back to grid
  - DeleteButton - Delete banner with confirmation dialog
  - SaveButton - Save banner
  - SaveAndContinueButton - Save and continue editing
- Created Edit controller for displaying form (`Controller/Adminhtml/Banner/Edit.php`)
- Created NewAction controller for new banners (`Controller/Adminhtml/Banner/NewAction.php`)
- Added form layout XML (`view/adminhtml/layout/vodacom_sitebanners_banner_edit.xml`)
- Configured all form fields with proper validation
- Updated module version to 3.0.2

**Files Changed:**
- `view/adminhtml/ui_component/vodacom_sitebanners_banner_form.xml` - NEW: Form UI Component
- `Model/Banner/DataProvider.php` - NEW: Form data provider
- `Block/Adminhtml/Banner/Edit/GenericButton.php` - NEW: Base button class
- `Block/Adminhtml/Banner/Edit/BackButton.php` - NEW: Back button
- `Block/Adminhtml/Banner/Edit/DeleteButton.php` - NEW: Delete button
- `Block/Adminhtml/Banner/Edit/SaveButton.php` - NEW: Save button
- `Block/Adminhtml/Banner/Edit/SaveAndContinueButton.php` - NEW: Save & Continue button
- `Controller/Adminhtml/Banner/Edit.php` - NEW: Edit controller
- `Controller/Adminhtml/Banner/NewAction.php` - NEW: New action controller
- `view/adminhtml/layout/vodacom_sitebanners_banner_edit.xml` - NEW: Form layout
- `etc/module.xml` - Updated version to 3.0.2
- `README.md` - Updated documentation with V3.0.2 details

**Key Concepts Demonstrated:**
- **Form UI Components**: XML-based form configuration
- **DataProvider Pattern**: Loading form data with DataPersistorInterface
- **ButtonProviderInterface**: Creating form action buttons
- **Generic Button Pattern**: Base class for button inheritance
- **Form Field Configuration**: Input, textarea, checkbox, date fields
- **Field Validation**: Required fields, numeric, and date validation
- **Data Persistence**: Using DataPersistor for form state management
- **Controller Patterns**: Edit action and forward pattern (NewAction)
- **Form Buttons**: Back, Delete (with confirmation), Save, Save & Continue
- **Layout Integration**: uiComponent in layout XML

**Testing (V3.0.2 Completed Features):**
1. ✅ Navigate to **Vodacom > Site Banners** in admin
2. ✅ Click "Add New Banner" button - opens empty form
3. ✅ Click "Edit" link on any banner - loads data into all 7 fields
4. ✅ All form fields display correctly:
   - banner_id (hidden)
   - is_active (toggle switch, defaults to enabled)
   - title (text input, required)
   - content (textarea)
   - sort_order (numeric input)
   - active_from (date picker)
   - active_to (date picker)
5. ✅ Test Back button - returns to grid
6. ✅ Delete button only shows when editing (not on new)
7. ✅ All 4 buttons render correctly
8. ✅ Field validation configured (title required, sort_order numeric, dates valid)
9. ❌ Save/SaveAndContinue buttons present but will 404 (intentionally not implemented)
10. ❌ Delete button shows but will 404 (intentionally not implemented)

**Intentionally NOT Implemented in V3.0.2:**
- Save Controller (V3.0.3)
- Delete Controller (V3.0.3)
- Mass Actions (V3.0.3)
- Inline Edit (V3.0.3)

**Next Steps (V3.0.3):**
- Implement Save controller (create/update operations)
- Implement Delete controller
- Add mass actions (mass delete, mass enable/disable)
- Add inline edit functionality in grid
- Complete end-to-end CRUD workflow testing

### Version 3.0.1
**Branch:** `feature/v3.0.1-grid-ui`  
**Focus:** Grid UI Component  
**Status:** ✅ Completed

**What's New:**
- Created Grid UI Component XML (`view/adminhtml/ui_component/vodacom_sitebanners_banner_listing.xml`)
- Configured data provider using virtual types in di.xml
- Implemented grid collection with SearchResult interface
- Created BannerActions column class for Edit/Delete links
- Updated admin layout to use UI Component instead of template
- Added all banner columns: ID, Title, Content, Active, Active From/To, Sort Order, Created
- Implemented filters for all columns (text, date range, yes/no select)
- Added sorting capability on all columns
- Integrated with V2.0.2 sample data (displays 5 banners)
- Added Magento_Ui module dependency
- Updated module version to 3.0.1

**Files Changed:**
- `view/adminhtml/ui_component/vodacom_sitebanners_banner_listing.xml` - NEW: Grid UI Component
- `etc/di.xml` - NEW: Data provider configuration with virtual types
- `Ui/Component/Listing/Column/BannerActions.php` - NEW: Actions column class
- `view/adminhtml/layout/vodacom_sitebanners_banner_index.xml` - Updated to use uiComponent
- `etc/module.xml` - Updated version to 3.0.1, added Magento_Ui dependency
- `README.md` - Updated documentation with V3.0.1 details

**Key Concepts Demonstrated:**
- **UI Components Architecture**: XML-based grid configuration
- **Virtual Types**: Creating data providers without concrete classes
- **Data Provider Pattern**: Connecting UI components to data sources
- **Grid Collection**: Using SearchResult interface for grid data
- **Column Components**: Different column types (text, date, select, actions)
- **Filter Configuration**: Text, date range, and dropdown filters
- **Actions Column**: Dynamic Edit/Delete links with confirmation dialogs
- **Layout Integration**: Replacing template blocks with UI components

**Testing:**
1. Navigate to **Vodacom > Site Banners** in admin
2. Should see grid with 5 sample banners from V2.0.2
3. Test filters (Active, date ranges, search)
4. Test sorting on all columns
5. Verify Edit and Delete links appear (will 404 until V3.0.2/V3.0.3)

**Next Steps:**
- V3.0.2: Add Form UI Component for create/edit functionality
- V3.0.3: Add CRUD controllers and mass actions

### Version 3.0.0
**Branch:** `feature/v3.0.0-admin-menu-acl`  
**Focus:** Admin Menu & ACL Foundation  
**Status:** ✅ Completed

**What's New:**
- Created admin routing configuration (`etc/adminhtml/routes.xml`)
- Implemented ACL resources with 3-level permission hierarchy (`etc/acl.xml`):
  - `Vodacom_SiteBanners::banners` - View banners (parent permission)
  - `Vodacom_SiteBanners::banner_save` - Save/edit banners
  - `Vodacom_SiteBanners::banner_delete` - Delete banners
- Created custom "Vodacom" top-level admin menu section (`etc/adminhtml/menu.xml`)
- Added "Site Banners" submenu under Vodacom section
- Created Index controller with authorization (`Controller/Adminhtml/Banner/Index.php`)
- Implemented ADMIN_RESOURCE constant for permission enforcement
- Created admin layout XML (`view/adminhtml/layout/vodacom_sitebanners_banner_index.xml`)
- Added placeholder admin template (`view/adminhtml/templates/banner/index.phtml`)
- Added Magento_Backend module dependency
- Updated module version to 3.0.0

**Files Changed:**
- `etc/adminhtml/routes.xml` - NEW: Admin routing configuration
- `etc/acl.xml` - NEW: ACL permission hierarchy
- `etc/adminhtml/menu.xml` - NEW: Admin menu entry
- `Controller/Adminhtml/Banner/Index.php` - NEW: Admin controller
- `view/adminhtml/layout/vodacom_sitebanners_banner_index.xml` - NEW: Admin layout
- `view/adminhtml/templates/banner/index.phtml` - NEW: Admin template (placeholder)
- `etc/module.xml` - Updated version to 3.0.0, added Magento_Backend dependency
- `README.md` - Updated documentation with V3.0.0 details

**Key Concepts Demonstrated:**
- **Admin Routing**: Separate routing for admin area (`adminhtml/routes.xml`)
- **ACL Resources**: Permission hierarchy for role-based access control
- **Admin Menu Configuration**: Adding menu items to existing admin sections
- **Authorization**: Using ADMIN_RESOURCE constant to enforce permissions
- **Controller Authorization**: Automatic permission checking via `_isAllowed()` method
- **Admin Page Factory**: Creating admin result pages
- **Admin Layout Handles**: Naming convention for admin layouts
- **Module Dependencies**: Declaring dependency on Magento_Backend

**Testing Admin Access:**
1. Log into Magento Admin Panel
2. Navigate to **Vodacom > Site Banners** (custom top-level menu)
3. Should see placeholder page with V3.0.0 confirmation message
4. Verify "Vodacom" appears as a top-level menu item in admin navigation
4. Verify permission enforcement:
   - Admin user needs `Vodacom_SiteBanners::banners` permission
   - Check in System > Permissions > User Roles

**Next Steps:**
- V3.0.1: Add Grid UI Component to display sample banners from V2.0.2
- V3.0.2: Add Form UI Component for create/edit functionality
- V3.0.3: Add CRUD controllers and mass actions

### Version 2.0.2
**Branch:** `feature/v2.0.2-data-patches`  
**Focus:** Data Patches for database seeding  
**Status:** ✅ Completed

**What's New:**
- Created Data Patch: `Setup/Patch/Data/AddSampleBanners.php`
- Implemented DataPatchInterface with proper structure
- Added dependency on AddActiveDatesToBannerTable schema patch
- Installed 5 diverse sample banners demonstrating:
  - Always-active banner (no date restrictions)
  - Past date range (expired holiday sale)
  - Future date range (upcoming spring promotion)
  - Inactive banner (manual control)
  - Expired banner (past dates)
- Used Factory + Resource Model pattern for data insertion
- Implemented comprehensive error handling with logging
- Updated module version to 2.0.2

**Files Changed:**
- `Setup/Patch/Data/AddSampleBanners.php` - NEW: Data patch for sample banners
- `etc/module.xml` - Updated version to 2.0.2
- `README.md` - Updated documentation with V2.0.2 details

**Key Concepts Demonstrated:**
- **DataPatchInterface Implementation**: Proper structure for data patches
- **Patch Dependencies**: Data patches depending on schema patches via `getDependencies()`
- **Factory Pattern**: Creating model instances using `BannerFactory`
- **Resource Model Pattern**: Saving entities via `BannerResource->save()` (not `$model->save()`)
- **Error Handling**: Try-catch blocks with logging for production safety
- **Sample Data Strategy**: Diverse use cases in a single patch
- **NULL Handling**: Optional datetime fields for flexible scheduling
- **Patch Tracking**: Automatic tracking in `patch_list` table

**Sample Banner Details:**
1. **Welcome Banner** (sort_order: 10)
   - Always active, no date restrictions
   - Demonstrates basic active banner

2. **Holiday Sale 2024** (sort_order: 20)
   - Active with past date range (Dec 2024)
   - Demonstrates expired scheduled content

3. **Spring Promotion 2026** (sort_order: 30)
   - Active with future date range (March 2026)
   - Demonstrates upcoming scheduled content

4. **Flash Sale - Inactive** (sort_order: 40)
   - Manually deactivated (is_active = 0)
   - Demonstrates manual control override

5. **Expired Limited Time Offer** (sort_order: 50)
   - Active but with expired dates (Jan 2024)
   - Demonstrates active flag vs date scheduling

**Usage:**
Sample banners are automatically installed during `setup:upgrade`. 
No manual data entry needed for testing or demonstration.

**Database Verification:**
```bash
# Access database
docker exec -it hyva-tutorials-db-1 mariadb -u magento -pmagento magento

# Check sample banners
SELECT banner_id, title, is_active, active_from, active_to, sort_order 
FROM vodacom_sitebanners_banner ORDER BY sort_order;

# Verify patch tracking
SELECT * FROM patch_list WHERE patch_name LIKE '%AddSampleBanners%';
```

### Version 2.0.1
**Branch:** `feature/v2.0.1-schema-patches`  
**Focus:** Schema Patches for database alterations  
**Status:** ✅ Completed

**What's New:**
- Implemented Schema Patch to add `active_from` and `active_to` columns to existing banner table
- Added date-based banner scheduling functionality
- Extended Banner Model with date getter/setter methods (`getActiveFrom`, `setActiveFrom`, `getActiveTo`, `setActiveTo`)
- Added `isActiveByDate()` helper method for date range validation
- Enhanced Collection with `addActiveDateFilter()` for date-based filtering
- Implemented reversible schema changes via `revert()` method
- Added NULL handling for flexible scheduling (NULL = no date restriction)
- Updated module version to 2.0.1

**Files Changed:**
- `Setup/Patch/Schema/AddActiveDatesToBannerTable.php` - NEW: Schema patch adding active_from/active_to columns
- `Model/Banner.php` - Updated: Added date-related getter/setter methods and isActiveByDate() helper
- `Model/ResourceModel/Banner/Collection.php` - Updated: Added addActiveDateFilter() method
- `etc/module.xml` - Updated version to 2.0.1
- `.github/copilot-instructions.md` - Added comprehensive V2.0.1 implementation guide
- `.github/project_context.md` - Added schema patches best practices and V2.0.1 checklist
- `README.md` - Updated documentation with V2.0.1 details

**Key Concepts Demonstrated:**
- **Schema Patches**: Using SchemaPatchInterface for database alterations
- **Patch Lifecycle**: apply(), revert(), getDependencies(), getAliases() methods
- **Reversible Changes**: Implementing proper rollback via revert() method
- **Patch Tracking**: Automatic tracking in patch_list table
- **DateTime Columns**: Adding DATETIME NULLABLE columns for scheduling
- **NULL Handling**: Treating NULL as "no restriction" for flexible scheduling
- **Date-Based Filtering**: Collection methods for time-based queries
- **Model Extensions**: Adding methods to existing models
- **Migration Strategy**: Patches vs declarative schema modifications
- **Production Safety**: Non-destructive schema alterations

**Database Schema Changes:**
```sql
-- Columns added by AddActiveDatesToBannerTable patch
ALTER TABLE vodacom_sitebanners_banner 
  ADD COLUMN active_from DATETIME NULL COMMENT 'Active From Date/Time',
  ADD COLUMN active_to DATETIME NULL COMMENT 'Active To Date/Time';
```

**Usage Example:**
```php
// Create scheduled banner
$banner = $bannerFactory->create();
$banner->setTitle('Holiday Banner')
    ->setContent('Special holiday promotion!')
    ->setIsActive(1)
    ->setActiveFrom('2024-12-01 00:00:00')  // Start Dec 1st
    ->setActiveTo('2024-12-31 23:59:59')    // End Dec 31st
    ->save();

// Get currently active banners (respects date range)
$collection = $bannerCollectionFactory->create()
    ->addActiveFilter(true)
    ->addActiveDateFilter();  // Filters by current date/time

// Check if banner is active by date
if ($banner->isActiveByDate()) {
    echo "Banner is currently active";
}

// Get banners active on specific date
$collection = $bannerCollectionFactory->create()
    ->addActiveDateFilter('2024-12-15 12:00:00');
```

### Version 2.0.0
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
**Branch:** `feature/v1.0.2-view-with-css-style`  
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
