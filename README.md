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
| V2.0.2   | Data Patches               | Data Patches, Database Seeding, Sample Data Installation                  | `feature/v2.0.2-data-patches`             |
| V3.0.0   | Admin Menu & ACL           | Admin Menu, ACL Resources, Permission Hierarchy                           | `feature/v3.0.0-admin-menu-acl`           |
| V3.0.1   | Grid UI Component          | Grid Configuration, Virtual Types, Data Providers, Actions Column         | `feature/v3.0.1-grid-ui`                  |
| V3.0.2   | Form UI Component          | Form Configuration, DataProvider, Button Classes, Field Validation        | `feature/v3.0.2-form-ui`                  |
| V3.0.3   | CRUD & Mass Actions        | Save/Delete Controllers, Mass Actions, Inline Editing                     | `feature/v3.0.3-crud-mass-actions`        |
| V3.0.4   | PageBuilder/WYSIWYG        | WYSIWYG Editor, Page Builder Integration, Content Filtering              | `feature/v3.0.4-pagebuilder-wysiwyg`      |
| V4.0.1   | Data Interfaces            | BannerInterface, Service Contracts, Type Safety, DI Preferences           | `feature/v4.0.1-data-interfaces`          |
| V4.0.2   | Repository Pattern         | Repository, SearchCriteria, Data Abstraction Layer                        | `feature/v4.0.2-repository-pattern`       |
| V4.0.3   | REST API                   | webapi.xml, REST Endpoints, API Authentication                            | `feature/v4.0.3-rest-api`                 |
| V5.0.1   | DI Fundamentals            | Constructor Injection, Helper Pattern, Interface Injection                | `feature/v5.0.1-di-fundamentals`          |
| V5.0.2   | Factories & Proxies        | Factory Pattern, Proxy Pattern, Lazy Loading                              | `feature/v5.0.2-factories-proxies`        |
| V5.0.3   | ViewModel Pattern          | ArgumentInterface, Layout Injection, Template Logic Separation            | `feature/v5.0.3-viewmodel`                |
| V6.0.1   | Before Plugins             | Before Plugin Implementation, Argument Modification, Logging              | `feature/v6.0.1-before-plugins`           |
| V6.0.2   | After Plugins              | After Plugin Implementation, Result Modification, Plugin Chaining         | `feature/v6.0.2-after-plugins`            |
| V6.0.3   | Around Plugins             | Around Plugin Implementation, Interceptor Chain, Caching Layer            | `feature/v6.0.3-around-plugins`           |

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

**Version 5.0.3** (Current Branch: `feature/v5.0.3-viewmodel`) ✅ **Completed**

This version demonstrates **ViewModel Pattern - Separation of Presentation and Business Logic**:

### ViewModel Architecture
- **BannerViewModel implements ArgumentInterface**:
  - Pure ViewModel without extending framework base classes
  - Injected via layout XML `<argument>` tag
  - Clean separation: Block = rendering, ViewModel = business logic
  - Improved testability (no dependencies on Block/Context)
- **Refactored Block to Generic Template**:
  - Changed from custom `Vodacom\SiteBanners\Block\Banner` to generic `Magento\Framework\View\Element\Template`
  - Block now only renders template, no business logic
  - All data retrieval methods moved to ViewModel
- **Template Updated to Use ViewModel**:
  - Access ViewModel via `$block->getData('view_model')`
  - All business logic calls go through ViewModel
  - Clean template code focused on presentation

### ViewModel Class Dependencies (Constructor Injection)
1. **BannerRepositoryInterface** - Data access via repository pattern (V4.0.2)
2. **BannerHelper** - Leverages existing business logic from V5.0.1
3. **UrlInterface** - URL generation for banner actions
4. **Escaper** - Safe HTML output
5. **LoggerInterface** - Error handling and debugging
6. **FilterProvider** - CMS template filter for Page Builder content (widgets, media URLs, directives)

### ViewModel Public Methods (15+ methods)
- **Data Retrieval**:
  - `getActiveBanners()` - Returns array of BannerInterface with caching
  - `getBanner($id)` - Fetch specific banner by ID
  - `hasBanners()` - Boolean check for banner existence
- **Formatting & Display**:
  - `getFormattedTitle($banner)` - Escaped title with ucfirst
  - `getBannerContent($banner)` - Page Builder content (HTML safe)
  - `getBannerExcerpt($banner, $length)` - Truncated plain text
  - `formatDate($date, $format)` - Date formatting with error handling
- **Business Logic**:
  - `isBannerActive($banner)` - Checks is_active flag AND date range
  - `hasDateRestrictions($banner)` - Check if date-limited
  - `getBannerCssClasses($banner)` - Dynamic CSS classes based on status
- **Statistics & Counts**:
  - `getBannerStatistics()` - Delegates to Helper (total, active, inactive)
  - `getTotalBannersCount()` - Total banner count
  - `getActiveBannersCount()` - Count of active banners
- **Utility Methods**:
  - `getBannerEditUrl($banner)` - Admin edit link generation

### Files Changed in V5.0.3
1. **ViewModel/BannerViewModel.php** (NEW - 330+ lines):
   - Implements ArgumentInterface (ViewModel requirement)
   - 15+ public methods for business logic
   - Instance caching for performance
   - Comprehensive error handling with logger
   - FilterProvider integration for Page Builder content filtering

2. **view/frontend/layout/banners_index_view.xml** (UPDATED):
   - Changed Block class: `Vodacom\SiteBanners\Block\Banner` → `Magento\Framework\View\Element\Template`
   - Added `<argument>` tag to inject BannerViewModel
   - ViewModel injected as object type

3. **view/frontend/templates/view.phtml** (UPDATED):
   - Changed from `$block->method()` to `$viewModel->method()` pattern
   - Uses `$block->escapeHtml()` and `$block->escapeHtmlAttr()` directly (no separate $escaper)
   - Cleaner template focusing on presentation
   - Uses `$block->getData('view_model')` to access ViewModel

4. **etc/module.xml** (UPDATED):
   - Version bumped from 5.0.2 to 5.0.3
   - Added version history comment

5. **README.md** (UPDATED):
   - Added V5.0.3 feature documentation
   - Updated version history and current version marker

### Key Architectural Patterns
- **ArgumentInterface** - Required interface for ViewModels in layout XML
- **Dependency Injection via Layout** - ViewModel injected through XML, not constructor
- **Separation of Concerns** - Clear boundary between presentation (template) and logic (ViewModel)
- **Testability** - ViewModels easier to unit test than Blocks
- **Composition over Inheritance** - ViewModel doesn't extend any framework class
- **Performance** - Instance caching prevents redundant queries

### Why ViewModel vs Block?
- **Blocks**: Tied to layout system, harder to test, mixed responsibilities
- **ViewModels**: Pure PHP classes, easy to test, single responsibility
- **When to use ViewModel**:
  - Complex business logic needed in templates
  - Reusable logic across multiple templates
  - Need better testability
  - Want clean separation of concerns

---

## Previous Version Features

**Version 5.0.2** - **Factory and Proxy Patterns**:

### Factory Pattern Features
- **BannerService with Factory Usage**:
  - `Service/BannerService.php` - Business logic layer using Factories
  - Creates Banner instances using `BannerInterfaceFactory` (auto-generated)
  - Uses `CollectionFactory` for batch operations
  - Demonstrates Factory pattern for object creation
- **Console Command Demonstration**:
  - `Console/Command/DemonstratePatterns.php` - Tests Factory and Proxy patterns
  - Shows BannerInterfaceFactory vs CollectionFactory usage
  - Demonstrates lazy loading with Proxy
  - Benchmarks Factory performance

### Proxy Pattern Features
- **HeavyBannerProcessor with Proxy**:
  - `Helper/HeavyBannerProcessor.php` - Simulates expensive operations
  - Injected as Proxy in di.xml configuration
  - Only instantiates when method is called (lazy loading)
  - Prevents unnecessary resource consumption
- **BannerStats Block with Proxy Injection**:
  - `Block/BannerStats.php` - Demonstrates Proxy usage
  - HeavyBannerProcessor injected as Proxy
  - Processor only loads if statistics are requested
  - Shows performance benefits of lazy loading

### Key Factory & Proxy Concepts
- **Auto-Generated Factories**:
  - Magento creates `*Factory` classes automatically
  - Usage: `$this->bannerFactory->create()` or `$this->bannerFactory->create(['data' => []])`
  - No need to create Factory PHP files manually
- **Proxy Configuration in di.xml**:
  - Add `/Proxy` suffix to class name in di.xml
  - Magento generates Proxy class at runtime
  - Proxy intercepts first method call and instantiates real object
- **When to Use**:
  - **Factory**: Creating new instances (models, collections, data objects)
  - **Proxy**: Injecting expensive classes that may not be used

**Version 5.0.1** - **Dependency Injection Fundamentals**:
- **Helper Class with Constructor Injection**:
  - `Helper/BannerHelper.php` - Business logic encapsulation
  - Injects 5 dependencies via constructor (proper DI pattern)
  - Uses interface injection (BannerRepositoryInterface, not concrete class)
  - Demonstrates type declarations on all constructor parameters
- **Block Refactoring**:
  - Changed from CollectionFactory to BannerHelper injection
  - Block becomes thin delegation layer (separation of concerns)
  - All business logic moved to Helper
  - Maintains backwards compatibility (same public methods)
- **Type Safety**:
  - Strict type declarations on all methods
  - Interface injection for loose coupling
  - Returns array of BannerInterface (not Collection)
- **SearchCriteria Builder Pattern**:
  - FilterBuilder for is_active and date filtering
  - SortOrderBuilder for proper ordering
  - Demonstrates SearchCriteria construction

### Helper Class Dependencies (Constructor Injection)
1. **Context** - Base helper functionality
2. **BannerRepositoryInterface** - Interface injection (not BannerRepository concrete class)
3. **SearchCriteriaBuilder** - Build complex search queries
4. **FilterBuilder** - Create filter conditions
5. **SortOrderBuilder** - Create sort order objects
6. **DateTime** - Current date/time for date filtering

### Refactored Block Class
- **Removed**: CollectionFactory dependency
- **Added**: BannerHelper dependency
- **Methods Changed**:
  - `getActiveBanners()` - Returns `array` instead of `Collection`
  - Delegates to Helper for business logic
  - Template updated: `$banners->getSize()` → `count($banners)`

### Fixed Issues
- **BannerSearchResults Class**: Created proper implementation of BannerSearchResultsInterface
- **di.xml Updated**: Changed preference from generic SearchResults to BannerSearchResults
- **Template Compatibility**: Updated view.phtml to work with array instead of Collection

### Key Architectural Patterns
- **Constructor Injection** over property injection or ObjectManager
- **Interface Injection** over concrete class injection
- **Separation of Concerns**: Business logic in Helper, presentation in Block
- **Type Safety**: Strict types throughout
- **Delegation Pattern**: Block delegates to Helper

---

## Previous Version Features

**Version 4.0.3** - **REST API Exposure**:

### REST API Features
- **Complete CRUD Operations via REST**:
  - `GET /V1/vodacom/banners/:bannerId` - Retrieve single banner
  - `GET /V1/vodacom/banners` - List banners with SearchCriteria
  - `POST /V1/vodacom/banners` - Create new banner
  - `PUT /V1/vodacom/banners/:bannerId` - Update existing banner
  - `DELETE /V1/vodacom/banners/:bannerId` - Delete banner
- **Authentication**:
  - OAuth 1.0a support
  - Token-based authentication via Integration tokens
  - ACL-based permissions for fine-grained access control
- **SearchCriteria Support**:
  - Complex filtering (eq, neq, gt, lt, like, in, etc.)
  - Pagination (currentPage, pageSize)
  - Sorting (field, direction)
  - Multiple filter groups
- **API Documentation**:
  - Comprehensive `README-API.md` with usage examples
  - curl command examples for all endpoints
  - Postman collection guidance
  - Error handling documentation
  - Security best practices

### ACL Resources (Updated)
- **API-Specific Permissions**:
  - `Vodacom_SiteBanners::banner_view` - View banners via API
  - `Vodacom_SiteBanners::banner_api_save` - Create/Update via API
  - `Vodacom_SiteBanners::banner_api_delete` - Delete via API
- **Separate from Admin Permissions**:
  - API resources under `Magento_Backend::content_api`
  - Admin resources under `Magento_Backend::content`
  - Allows different permission sets for admin UI vs API

### Configuration Files
- **etc/webapi.xml**: Maps HTTP methods to repository operations
- **etc/acl.xml**: Defines API resource hierarchy
- **README-API.md**: Complete API documentation with examples

### Key Benefits
- **Leverages V4.0.2 Repository**: No new business logic needed
- **Standard Magento REST**: Compatible with existing API clients
- **Fully Documented**: Ready for integration with external systems
- **Secure by Default**: ACL permissions enforced on all endpoints

---

## Previous Version Features

**Version 3.0.4** - **Page Builder and WYSIWYG Editor Integration**:

### Admin Features (Page Builder)
- **WYSIWYG Editor** with TinyMCE integration
  - Rich text editing with formatting toolbar
  - Drag-and-drop Page Builder interface
  - Toggle between WYSIWYG and HTML code view
  - 500px editor height for better content visibility
- **Page Builder Components**:
  - Rows, columns, tabs, buttons
  - Image and video insertion
  - Widget integration (product widgets, CMS blocks)
  - Variable insertion ({{store url}}, {{media url}})
  - Template directives processing
- **Media Gallery Integration**:
  - Browse and insert images from media library
  - Upload new images directly from editor
  - Automatic image path handling

### Frontend Features (Content Rendering)
- **Page Builder Content Processing**:
  - CMS template filter for directive rendering
  - Widget rendering ({{widget type="..."}})
  - Media URL processing ({{media url="..."}})
  - Dynamic content support
- **Banner Display Logic**:
  - Fetches all active banners from database
  - Filters by is_active flag
  - Filters by active_from/active_to date ranges
  - Orders by sort_order ASC
  - Empty state message when no active banners
- **Date Range Display**:
  - Shows active_from and active_to dates when set
  - Formatted using IntlDateFormatter
  - Helps users understand banner scheduling

### Database Changes
- **Schema Patch**: `ExpandContentFieldForPageBuilder.php`
  - Expands content field from TEXT (64KB) to MEDIUMTEXT (16MB)
  - Required for storing verbose Page Builder HTML
  - Includes revert() method for rollback capability
  - Depends on AddActiveDatesToBannerTable patch (V2.0.1)

### Code Architecture
- **Block Class**: `Block/Banner.php` (NEW in V3.0.4)
  - `getActiveBanners()` - Fetches active banners with date filtering
  - `filterContent()` - Processes Page Builder HTML through CMS filter
  - `getBannerById()` - Retrieves specific banner by ID
  - Uses CollectionFactory and FilterProvider
- **Frontend Integration**:
  - Layout updated with Block class reference
  - Template updated to display dynamic content
  - Page Builder content filtered before display

### Module Dependencies
- Added `Magento_PageBuilder` to module sequence
- Enables Page Builder components in admin forms
- Required for WYSIWYG editor functionality

**Previous versions included:**
- CRUD Operations and Mass Actions (V3.0.3)
- Form UI Component (V3.0.2)
- Grid UI Component (V3.0.1)
- Admin Menu & ACL Foundation (V3.0.0)
- Data Patches with 5 sample banners (V2.0.2)
- Schema Patches for date columns (V2.0.1)
- Declarative Schema with Models (V2.0.0)
- LESS styling (V1.0.3)
- Frontend routing and templates (V1.0.0-V1.0.2)

---

## Version History

### Version 5.0.1 (Current)
**Branch:** `feature/v5.0.1-di-fundamentals`  
**Focus:** Dependency Injection Fundamentals with Helper Pattern  
**Status:** ✅ Completed

**What's New:**
- Created `Helper/BannerHelper.php` with 5 constructor-injected dependencies
- Refactored `Block/Banner.php` to use Helper instead of CollectionFactory
- Moved business logic from Block to Helper (separation of concerns)
- Updated `Model/BannerSearchResults.php` - proper implementation of SearchResultsInterface
- Fixed `etc/di.xml` - changed SearchResults preference to BannerSearchResults
- Updated `view/frontend/templates/view.phtml` - array compatibility (count vs getSize)
- Module version updated to 5.0.1

**Files Created:**
- `Helper/BannerHelper.php` - Business logic with proper DI pattern
- `Model/BannerSearchResults.php` - Type-safe SearchResults implementation

**Files Modified:**
- `Block/Banner.php` - Refactored to delegate to Helper
- `etc/module.xml` - Version updated to 5.0.1
- `etc/di.xml` - Fixed BannerSearchResultsInterface preference
- `view/frontend/templates/view.phtml` - Array compatibility updates

**Key Concepts Demonstrated:**
- Constructor injection with type declarations
- Interface injection (BannerRepositoryInterface) for loose coupling
- Helper pattern for business logic encapsulation
- SearchCriteria builder pattern (FilterBuilder, SortOrderBuilder)
- Block as thin delegation layer
- Strict type safety throughout
- Proper DI container usage (no ObjectManager)

**Breaking Changes:**
- `Block::getActiveBanners()` now returns `array` instead of `Collection`
- Template updated to use `count($banners)` instead of `$banners->getSize()`
- No breaking changes for external API consumers

---

### Version 4.0.3
**Branch:** `feature/v4.0.3-rest-api`  
**Focus:** REST API Exposure  
**Status:** ✅ Completed

**What's New:**
- Created `etc/webapi.xml` with REST endpoint mappings for all CRUD operations
- Updated `etc/acl.xml` with API-specific resource permissions
- Created comprehensive `README-API.md` with usage examples and curl commands
- Updated module version to 4.0.3
- Exposed repository via 5 REST endpoints:
  - GET /V1/vodacom/banners/:bannerId - Retrieve single banner
  - GET /V1/vodacom/banners - List with SearchCriteria support
  - POST /V1/vodacom/banners - Create new banner
  - PUT /V1/vodacom/banners/:bannerId - Update existing banner
  - DELETE /V1/vodacom/banners/:bannerId - Delete banner

**Files Created:**
- `etc/webapi.xml` - REST API route configuration
- `README-API.md` - Complete API documentation (450+ lines)

**Files Modified:**
- `etc/acl.xml` - Added API resource permissions (banner_view, banner_api_save, banner_api_delete)
- `etc/module.xml` - Updated version to 4.0.3

**Key Concepts Demonstrated:**
- **REST API Configuration**: webapi.xml maps HTTP verbs to repository methods
- **API Authentication**: OAuth 1.0a and token-based authentication
- **ACL for APIs**: Separate permissions for API vs admin UI
- **SearchCriteria via REST**: Complex queries through URL parameters
- **Zero Business Logic**: Leverages existing repository from V4.0.2
- **API Documentation**: Professional README-API.md with examples

**API Endpoints:**
```xml
<!-- Get single banner -->
<route url="/V1/vodacom/banners/:bannerId" method="GET">
    <service class="Vodacom\SiteBanners\Api\BannerRepositoryInterface" method="getById"/>
    <resources><resource ref="Vodacom_SiteBanners::banner_view"/></resources>
</route>

<!-- List banners with SearchCriteria -->
<route url="/V1/vodacom/banners" method="GET">
    <service class="Vodacom\SiteBanners\Api\BannerRepositoryInterface" method="getList"/>
    <resources><resource ref="Vodacom_SiteBanners::banner_view"/></resources>
</route>

<!-- Create banner -->
<route url="/V1/vodacom/banners" method="POST">
    <service class="Vodacom\SiteBanners\Api\BannerRepositoryInterface" method="save"/>
    <resources><resource ref="Vodacom_SiteBanners::banner_api_save"/></resources>
</route>
```

**Usage Example:**
```bash
# Create integration in admin: System > Extensions > Integrations
# Get access token and use in API calls

# Get banner by ID
curl -X GET "https://your-store.com/rest/V1/vodacom/banners/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Create new banner
curl -X POST "https://your-store.com/rest/V1/vodacom/banners" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"banner": {"title": "New Banner", "content": "Content here", "is_active": 1}}'

# List active banners with SearchCriteria
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=is_active&searchCriteria[filterGroups][0][filters][0][value]=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Benefits:**
- **Zero Custom Code**: Pure configuration, leverages existing repository
- **Standard Magento REST**: Compatible with existing API clients
- **Complete Documentation**: README-API.md has 450+ lines of examples
- **Secure**: ACL-based permissions enforced
- **Ready for Integration**: External systems can manage banners programmatically
- **SearchCriteria Support**: Complex filtering, sorting, pagination via URL params

**Next Steps:** V5.0.0 will demonstrate Dependency Injection, Factories, and ViewModels

---

### Version 4.0.2
**Branch:** `feature/v4.0.2-repository-pattern`  
**Focus:** Repository Pattern & SearchCriteria  
**Status:** ✅ Completed

**What's New:**
- Created `Api/BannerRepositoryInterface` with full CRUD + getList methods
- Implemented `Model/BannerRepository` with SearchCriteria support
- Added instance caching in repository for performance optimization
- Refactored `Save` controller to use repository instead of ResourceModel
- Refactored `Delete` controller to use repository's `deleteById()` method
- Refactored `MassDelete` controller to use repository's `delete()` method
- Updated `etc/di.xml` with repository preference binding
- Updated module version to 4.0.2

**Files Created:**
- `Api/BannerRepositoryInterface.php` - Repository contract with save, getById, delete, deleteById, getList
- `Model/BannerRepository.php` - Repository implementation with CollectionProcessor support

**Files Modified:**
- `Controller/Adminhtml/Banner/Save.php` - Use BannerRepositoryInterface instead of ResourceModel
- `Controller/Adminhtml/Banner/Delete.php` - Use repository deleteById() method
- `Controller/Adminhtml/Banner/MassDelete.php` - Use repository delete() method
- `etc/di.xml` - Added BannerRepositoryInterface preference
- `etc/module.xml` - Updated version to 4.0.2

**Key Concepts Demonstrated:**
- **Repository Pattern**: Complete data abstraction layer
- **SearchCriteria Support**: Using CollectionProcessor for complex queries
- **Instance Caching**: Repository caches loaded entities to prevent duplicate queries
- **Exception Handling**: CouldNotSaveException, CouldNotDeleteException, NoSuchEntityException
- **Service Layer Separation**: Controllers no longer know about database/ResourceModel
- **Interface-Based Development**: All operations through BannerRepositoryInterface
- **Dependency Injection**: Proper constructor injection of repository interface

**Repository Methods:**
```php
public function save(BannerInterface $banner): BannerInterface;
public function getById(int $bannerId): BannerInterface;
public function getList(SearchCriteriaInterface $searchCriteria): BannerSearchResultsInterface;
public function delete(BannerInterface $banner): bool;
public function deleteById(int $bannerId): bool;
```

**Usage Example:**
```php
// Before V4.0.2 (direct ResourceModel)
public function __construct(
    BannerFactory $bannerFactory,
    BannerResource $bannerResource
) {}

// After V4.0.2 (repository pattern)
public function __construct(
    BannerRepositoryInterface $bannerRepository
) {}
```

**Benefits:**
- Complete abstraction from database implementation
- Easy to mock for unit tests
- SearchCriteria enables complex filtering without SQL
- Foundation for REST API (V4.0.3)
- Follows Magento 2 best practices

**Next Steps:** V4.0.3 will expose repository via REST API using webapi.xml

---

### Version 4.0.1
**Branch:** `feature/v4.0.1-data-interfaces`  
**Focus:** Data Interfaces & Service Contracts  
**Status:** ✅ Completed

**What's New:**
- Created `Api/Data/BannerInterface` with all getter/setter method contracts
- Created `Api/Data/BannerSearchResultsInterface` for repository pattern
- Updated `Model/Banner` to implement `BannerInterface`
- Refactored all model methods to use interface constants (BANNER_ID, TITLE, etc.)
- Fixed return types to match interface specifications (nullable where appropriate)
- Added DI preferences in `etc/di.xml` for interface binding
- Updated module version to 4.0.1

**Files Changed:**
- `Api/Data/BannerInterface.php` - NEW: Data contract interface with all banner fields
- `Api/Data/BannerSearchResultsInterface.php` - NEW: Search results interface
- `Model/Banner.php` - Updated to implement BannerInterface, use constants, fixed return types
- `etc/di.xml` - Added interface-to-implementation preferences
- `etc/module.xml` - Updated version to 4.0.1

**Key Concepts Demonstrated:**
- **Service Contracts**: @api annotation for stable public interfaces
- **Type Safety**: Strict type declarations on all methods
- **Interface Constants**: Using constants instead of magic strings for data keys
- **Nullable Types**: Proper use of `?int`, `?string`, `?bool` for optional fields
- **DI Preferences**: Binding interfaces to concrete implementations
- **Backward Compatibility**: All existing functionality preserved
- **API Readiness**: Foundation for REST/SOAP API exposure (V4.0.3)
- **Return Type Covariance**: Methods return BannerInterface instead of self

**Usage Example:**
```php
// Before V4.0.1 (concrete class)
public function __construct(Banner $banner) {}

// After V4.0.1 (interface - preferred)
public function __construct(BannerInterface $banner) {}
```

**Interface Compliance:**
- All admin CRUD operations work identically to V3.0.4
- No breaking changes to existing functionality
- Models now satisfy service contract requirements
- Ready for repository pattern implementation (V4.0.2)

**Next Steps:** V4.0.2 will implement Repository Pattern and SearchCriteria for data abstraction.

---

### Version 3.0.4
**Branch:** `feature/v3.0.4-pagebuilder-wysiwyg`  
**Focus:** Page Builder and WYSIWYG Editor Integration  
**Status:** ✅ Completed

**What's New:**
- Added Magento_PageBuilder module dependency
- Created schema patch to expand content field to MEDIUMTEXT (16MB)
- Changed content field from textarea to WYSIWYG editor in admin form
- Enabled Page Builder drag-and-drop interface
- Created frontend Block class for Page Builder content processing
- Updated frontend template to display dynamic banner content
- Implemented CMS template filter for directive/widget rendering
- Added date range filtering in frontend display
- Comprehensive code documentation for V3.0.4 changes

**Files Changed:**
- `etc/module.xml` - Updated version to 3.0.4, added Magento_PageBuilder dependency
- `Setup/Patch/Schema/ExpandContentFieldForPageBuilder.php` - NEW: Schema patch for MEDIUMTEXT
- `view/adminhtml/layout/vodacom_sitebanners_banner_edit.xml` - Added `<update handle="editor"/>` for Page Builder
- `view/adminhtml/ui_component/vodacom_sitebanners_banner_form.xml` - Changed content field to WYSIWYG with Page Builder
- `Block/Banner.php` - NEW: Frontend block for content processing
- `view/frontend/layout/banners_index_view.xml` - Added Block class reference
- `view/frontend/templates/view.phtml` - Updated to display dynamic content with filtering
- `README.md` - Updated documentation with V3.0.4 details

**Key Concepts Demonstrated:**
- **WYSIWYG Integration**: formElement="wysiwyg" with template="ui/form/field"
- **Page Builder JavaScript Loading**: `<update handle="editor"/>` in layout XML (CRITICAL)
- **Element Template Configuration**: elementTmpl="ui/form/element/wysiwyg" for proper HTML IDs
- **Schema Modification**: Using patches to alter existing database columns
- **Content Filtering**: FilterProvider->getPageFilter() for directive processing
- **Frontend Block Pattern**: Creating Block classes for business logic
- **Dynamic Content Rendering**: Database-driven frontend content display
- **Date-Based Filtering**: Complex collection queries with NULL handling
- **Template Directives**: {{media url}}, {{widget type}}, {{store url}} processing
- **Media Storage**: MEDIUMTEXT for large HTML content (Page Builder generates verbose HTML)
- **Accessibility**: Proper label configuration to avoid console warnings

**CRITICAL Page Builder Configuration:**

**Layout XML** (Required for modals to work):
```xml
<!-- view/adminhtml/layout/vodacom_sitebanners_banner_edit.xml -->
<page>
    <update handle="styles"/>
    <!-- CRITICAL: Loads Page Builder JavaScript/CSS -->
    <update handle="editor"/>
    <body>
        <referenceContainer name="content">
            <uiComponent name="vodacom_sitebanners_banner_form"/>
        </referenceContainer>
    </body>
</page>
```

**Form Field Configuration:**
```xml
<!-- view/adminhtml/ui_component/vodacom_sitebanners_banner_form.xml -->
<field name="content" sortOrder="30" template="ui/form/field" formElement="wysiwyg">
    <settings>
        <additionalClasses>
            <class name="admin__field-wide">true</class>
        </additionalClasses>
        <label translate="true">Content</label>
        <dataScope>content</dataScope>
        <elementTmpl>ui/form/element/wysiwyg</elementTmpl>
    </settings>
    <formElements>
        <wysiwyg>
            <settings>
                <rows>8</rows>
                <wysiwyg>true</wysiwyg>
            </settings>
        </wysiwyg>
    </formElements>
</field>
```

**Note**: Page Builder is enabled globally via Stores > Configuration > Content Management > Advanced Content Tools

**Database Verification:**
```bash
# Check content field is MEDIUMTEXT
docker exec -it hyva-tutorials-db-1 mariadb -u magento -pmagento magento \
  -e "DESCRIBE vodacom_sitebanners_banner;"

# Verify patch tracking
docker exec -it hyva-tutorials-db-1 mariadb -u magento -pmagento magento \
  -e "SELECT * FROM patch_list WHERE patch_name LIKE '%ExpandContent%';"
```

**Testing (V3.0.4):**
1. ✅ Edit any banner in admin
2. ✅ Verify WYSIWYG editor appears with Page Builder toolbar
3. ✅ Test Page Builder drag-and-drop interface:
   - Add Row > Add Column > Add Text
   - Add Heading, Button, Image elements
   - Configure element styles and layout
4. ✅ Toggle between WYSIWYG and HTML code view
5. ✅ Insert media from gallery
6. ✅ Insert widget (e.g., CMS block widget)
7. ✅ Save banner with rich content
8. ✅ View frontend at `/banners/index/view`
9. ✅ Verify Page Builder content renders correctly:
   - Styling preserved
   - Images display with correct URLs
   - Widgets render properly
   - Layout structure maintained
10. ✅ Test date filtering (only shows banners within date range)
11. ✅ Test empty state when no active banners

**Frontend URL:**
```
http://your-magento-site.local/banners/index/view
```

**Next Steps (V4.0.0):**
- Implement BannerRepositoryInterface (Repository pattern)
- Create BannerInterface and BannerSearchResultsInterface
- Expose REST API endpoints at `/V1/banners`
- Add SearchCriteria support for flexible queries
- Implement service contract architecture

### Version 3.0.3
**Branch:** `feature/v3.0.3-crud-mass-actions`  
**Focus:** CRUD Operations and Mass Actions  
**Status:** ✅ Completed

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

### Version 6.0.1
**Branch:** `feature/v6.0.1-before-plugins`  
**Focus:** Before Plugin Implementation  
**Status:** ✅ Completed

**What's New:**
- Created Before Plugin: `Plugin/BannerRepositoryTitleSanitizer.php`
  - Automatically sanitizes banner titles before save
  - Trims whitespace and converts to Title Case
  - Demonstrates argument modification pattern (returns array)
- Created Before Plugin: `Plugin/BannerRepositorySaveLogger.php`
  - Logs all banner CREATE/UPDATE operations
  - Provides audit trail for banner modifications
  - Demonstrates logging pattern (returns void)
- Configured plugins in `etc/di.xml` with proper sortOrder
  - TitleSanitizer (sortOrder 5) - executes first
  - SaveLogger (sortOrder 10) - executes second, logs sanitized title
- Updated module version to 6.0.1

**Files Changed:**
- `Plugin/BannerRepositoryTitleSanitizer.php` - NEW: Title sanitization before plugin
- `Plugin/BannerRepositorySaveLogger.php` - NEW: Save operation logging before plugin
- `etc/di.xml` - Added plugin configuration with sortOrder
- `etc/module.xml` - Updated version to 6.0.1
- `README.md` - Updated documentation with V6.0.1 details

**Key Concepts Demonstrated:**
- **Before Plugin Basics**: Method naming convention `before{MethodName}`
- **Argument Modification**: Returning array to modify method parameters
- **Logging Pattern**: Returning void when not modifying arguments
- **Plugin Priority**: Using sortOrder to control execution order
- **Multiple Plugins**: Chaining multiple plugins on same method
- **Audit Trail**: Implementing logging for compliance/debugging
- **Data Sanitization**: Automatic data cleaning without changing business logic

**Plugin Execution Flow:**
```
save() method call:
1. TitleSanitizer::beforeSave() (sortOrder 5)
   - Sanitizes title (trim, Title Case)
   - Returns modified banner
2. SaveLogger::beforeSave() (sortOrder 10)
   - Logs CREATE or UPDATE operation
   - Logs sanitized title
   - Returns void (no modification)
3. Original BannerRepository::save() executes
   - Receives sanitized banner
   - Saves to database
```

**Testing Before Plugins:**
```bash
# Run setup:upgrade to apply changes
bin/magento setup:upgrade
bin/magento cache:flush

# Edit a banner in admin with messy title: "  test   BANNER  "
# Save banner

# Check logs
tail -f var/log/system.log

# Expected log entries:
# 1. "Banner title sanitized" - original: "  test   BANNER  ", sanitized: "Test Banner"
# 2. "Banner UPDATE operation initiated: Test Banner"
# 3. Banner saved with clean title in database
```

**Real-World Use Cases:**
- **Title Sanitization**: Ensures consistent formatting across all banners
- **Audit Logging**: Track who created/modified banners and when
- **Data Validation**: Enforce data quality rules without modifying repository
- **Compliance**: Meet regulatory requirements for activity logging
- **Debugging**: Trace save operations during development

**Next Steps:**
- V6.0.2: Implement After Plugins to modify return values and enhance banner data
- V6.0.3: Implement Around Plugins for caching and performance optimization

---

### Version 6.0.2
**Branch:** `feature/v6.0.2-after-plugins`  
**Focus:** After Plugin Implementation - Return Value Modification  
**Status:** ✅ Completed

**What's New:**
- Created After Plugin: `Plugin/BannerDataEnhancer.php`
  - Enhances banner data after save() and getById() operations
  - Calculates display status (active, scheduled, expired, inactive)
  - Calculates days remaining until expiration
  - Logs computed fields for audit/debugging
  - Demonstrates return value modification pattern
- Created After Plugin: `Plugin/BannerSearchResultsEnhancer.php`
  - Enhances getList() search results with aggregate statistics
  - Calculates total active, scheduled, expired, inactive banners
  - Provides overview of banner collection status
  - Demonstrates processing multiple items in results
- Created After Plugin: `Plugin/BannerDeleteValidator.php`
  - Logs delete() and deleteById() operation results
  - Validates deletion success/failure
  - Provides audit trail for delete operations
  - Demonstrates handling bool return type
- Configured after plugins in `etc/di.xml` with proper sortOrder
  - DataEnhancer (sortOrder 5) - executes first
  - SearchResultsEnhancer (sortOrder 10) - executes second
  - DeleteValidator (sortOrder 15) - executes third
- Updated module version to 6.0.2

**Files Changed:**
- `Plugin/BannerDataEnhancer.php` - NEW: After plugin for save/getById enhancement
- `Plugin/BannerSearchResultsEnhancer.php` - NEW: After plugin for getList statistics
- `Plugin/BannerDeleteValidator.php` - NEW: After plugin for delete logging
- `etc/di.xml` - Added after plugin configuration with sortOrder
- `etc/module.xml` - Updated version to 6.0.2
- `README.md` - Updated documentation with V6.0.2 details

**Key Concepts Demonstrated:**
- **After Plugin Basics**: Method naming convention `after{MethodName}`
- **Return Value Modification**: Receiving and potentially modifying method results
- **Multiple Return Types**: Handling BannerInterface, BannerSearchResultsInterface, bool
- **Computed Fields**: Adding real-time calculations to results
- **Aggregate Statistics**: Processing collections for summary data
- **Error Handling**: Never throwing exceptions in after plugins
- **Chaining After Plugins**: Multiple after plugins processing same result sequentially

**Plugin Execution Flow:**
```
Complete flow with Before + After plugins:

save() method call:
1. BEFORE Plugins Execute:
   - TitleSanitizer::beforeSave() (sortOrder 5) - Sanitize title
   - SaveLogger::beforeSave() (sortOrder 10) - Log operation

2. ORIGINAL METHOD Executes:
   - BannerRepository::save() - Save to database

3. AFTER Plugins Execute:
   - DataEnhancer::afterSave() (sortOrder 5) - Calculate status & days remaining
   - SearchResultsEnhancer::afterGetList() (sortOrder 10) - Add statistics (for getList)
   - DeleteValidator::afterDelete() (sortOrder 15) - Log result (for delete)

4. Final enhanced result returned to caller
```

**After Plugin Method Signatures:**
```php
// Pattern for returning single object
public function afterSave(
    BannerRepositoryInterface $subject,  // Repository instance
    BannerInterface $result,              // Original method return value
    BannerInterface $banner               // Original method parameter (optional, for context)
): BannerInterface {                      // Must return same type
    // Enhance $result here
    return $result;
}

// Pattern for returning bool
public function afterDelete(
    BannerRepositoryInterface $subject,
    bool $result,                         // Success/failure
    BannerInterface $banner
): bool {
    // Log or validate result
    return $result;
}

// Pattern for returning search results
public function afterGetList(
    BannerRepositoryInterface $subject,
    BannerSearchResultsInterface $result  // Collection results
): BannerSearchResultsInterface {
    // Process collection, add statistics
    return $result;
}
```

**Testing After Plugins:**
```bash
# Run setup:upgrade to apply changes
bin/magento setup:upgrade
bin/magento cache:flush

# Test 1: Save a banner with future expiration date
# - Create banner with active_to = 7 days from now
# - Check logs for "Days until expiration: 7"
# - Check logs for "Display status: active"

# Test 2: Fetch banner grid (getList)
# - Navigate to Vodacom > Site Banners in admin
# - Check logs for statistics summary:
#   "Banner search results enhanced - Total: X, Active: Y, Scheduled: Z, Expired: W"

# Test 3: Delete a banner
# - Delete any banner from grid
# - Check logs for "Banner successfully deleted - ID: X, Title: 'Y'"

tail -f var/log/system.log | grep -E "(Banner|enhanced|deleted)"
```

**Real-World Use Cases:**
- **Display Status Badges**: Show "Active", "Scheduled", "Expired" badges in admin grid
- **Expiration Alerts**: Warn users when banners are about to expire
- **Dashboard Statistics**: Display overview of banner health (active vs expired)
- **Audit Compliance**: Log all deletion operations for regulatory requirements
- **Performance Insights**: Calculate metrics without modifying database schema
- **Data Enrichment**: Add computed fields like view counts, click rates, engagement scores

**Before vs After Plugin Comparison:**
| Aspect | Before Plugin (V6.0.1) | After Plugin (V6.0.2) |
|--------|------------------------|------------------------|
| **Execution** | Before original method | After original method |
| **Input** | Method arguments | Method return value |
| **Output** | Modified arguments (array) OR void | Modified return value (same type) |
| **Use Case** | Validate, sanitize, log inputs | Enhance, transform, log outputs |
| **Return Type** | `array` or `void` | Same as original method |
| **Example** | Sanitize title before save | Add computed fields after save |

**Next Steps:**
- V6.0.3: Implement Around Plugins for caching, performance optimization, and complete control

---

## Next Steps

To explore more advanced concepts:
- **V2.0.0**: Learn about database schema, models, and collections
- **V3.0.0**: Create admin grids and forms with UI Components
- **V4.0.0**: Implement service contracts and REST APIs
- **V5.0.0**: Master dependency injection and ViewModels
- **V6.0.x**: Master plugin patterns (Before, After, Around)
- **V6.0.0**: Extend core functionality using plugins
