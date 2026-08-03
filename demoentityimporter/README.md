# Demo: register a custom entity importer

This module shows how a PrestaShop 9.2+ module plugs its own entity importer
into the core import engine (Advanced Parameters > Import).

## How it works

1. The importer class implements
   `PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface`
   ([src/Importer/DemoNoteImporter.php](src/Importer/DemoNoteImporter.php)).
2. The service is registered with `autoconfigure: true`
   ([config/services.yml](config/services.yml)). The core registers the
   `core.import.entity_importer` tag for every autoconfigured service
   implementing the interface — no manual tag needed.
3. The core `EntityImporterRegistry` collects every tagged importer: the
   import page entity dropdown, the mapping screen field list and the batch
   execution all discover the module importer automatically.

## Verify

After installing the module:

```bash
php bin/console debug:container --tag=core.import.entity_importer
```

should list `DemoNoteImporter` next to the core importers.

## Install

```bash
cd modules/demoentityimporter
composer dumpautoload
```

then install the module (BO module manager or `php bin/console prestashop:module install demoentityimporter`).

## Requirements

- PrestaShop 9.2.0 or newer (import engine introduced by [PrestaShop/PrestaShop#41907](https://github.com/PrestaShop/PrestaShop/issues/41907)).
