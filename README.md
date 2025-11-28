# Vodacom_SiteBanners

## Introduction

The **Vodacom_SiteBanners** module is a custom Magento 2 extension designed to demonstrate core architectural concepts, as covered in a foundational training slide deck.

This module enables administrators to create, manage, and display promotional banners on the storefront. Development is structured into six distinct versions, each introducing and isolating a specific Magento 2 feature or pattern.

---

## Module Version Roadmap

Development follows a progressive, branch-based approach to isolate key concepts. Each version corresponds to a major section of the Magento 2 Architecture training.

| Version  | Focus Area (Slides)         | Architectural Concept Demonstrated                                         | Git Branch Name                        |
|----------|----------------------------|---------------------------------------------------------------------------|----------------------------------------|
| V1.0.0   | Routing, Layout, Blocks    | Request Flow (URL → Controller → Block)                                   | `feature/v1.0.0-routing-layout`        |
| V2.0.0   | Database & Models          | Declarative Schema, Models, Resource Models, Collections                   | `feature/v2.0.0-db-models-schema`      |
| V3.0.0   | Admin UI                   | Admin Menu, ACL, UI Components (Grid & Form)                              | `feature/v3.0.0-admin-uicomponents`    |
| V4.0.0   | Service Contracts & API    | Repository/Data Interfaces, Web API (`webapi.xml`)                        | `feature/v4.0.0-service-contract-api`  |
| V5.0.0   | Dependency Injection       | Constructor Injection, Factories, ViewModel Pattern                       | `feature/v5.0.0-di-factories-viewmodel`|
| V6.0.0   | Extensibility (Plugins)    | Before, Around, After Plugins (Interceptors) on core Magento classes      | `feature/v6.0.0-extensibility-plugins` |

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

## Development & Demonstration Notes

To view a specific concept, switch to the corresponding branch:
```bash
git checkout feature/v5.0.0-di-factories-viewmodel
```
After switching branches, always run:
```bash
bin/magento setup:upgrade
bin/magento cache:clean
```
as configuration files (e.g., `di.xml`, `db_schema.xml`) change between versions.
