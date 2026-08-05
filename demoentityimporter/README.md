# Demo: register a custom entity importer

This module shows how a PrestaShop 9.2+ module plugs its own entity importer
into the core import engine (Advanced Parameters > Import) — and actually
imports something: each CSV row becomes a `DemoNote` Doctrine entity.

## How it works

1. The importer class extends
   `PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\AbstractEntityImporter`
   ([src/Importer/DemoNoteImporter.php](src/Importer/DemoNoteImporter.php)),
   which provides the cursor-resumable batch loop, the phase-id guard and
   the default unit count (implementing `EntityImporterInterface` directly
   also works for full control). It declares two phases — a pausing
   `validation` phase (note required, max 255 characters) and a `database`
   phase that persists one `DemoNote` per row (create, or update when the
   mapped `id` column matches an existing note).
2. The service is registered with `autoconfigure: true`
   ([config/services.yml](config/services.yml)). The core registers the
   `core.import.entity_importer` tag for every autoconfigured service
   implementing the interface — no manual tag needed.
3. The core `EntityImporterRegistry` collects every tagged importer: the
   import page entity dropdown, the mapping screen field list and the batch
   execution all discover the module importer automatically.
4. The importer reuses the core engine services instead of re-implementing
   file handling: `ResumableFileReaderInterface` (cursor-resumable reads of
   the normalized working file — header/skip rows are already stripped at
   normalization) and `RowMapper` (column-to-field mapping).
5. The `DemoNote` entity ([src/Entity/DemoNote.php](src/Entity/DemoNote.php))
   lives in `src/Entity`, which the core maps automatically for active
   modules — no Doctrine configuration needed. Its table is created at
   module install ([demoentityimporter.php](demoentityimporter.php)).

## Verify

After installing the module:

```bash
php bin/console debug:container --tag=core.import.entity_importer
```

should list `DemoNoteImporter` next to the core importers.

A sample import file is provided in [sample/demo_notes.csv](sample/demo_notes.csv):

```csv
id;note
;First imported note
;Second imported note
```

(the empty `id` column means "create"; put an existing note id there to
update it instead).

## Install

```bash
cd modules/demoentityimporter
composer dumpautoload
```

then install the module (BO module manager or `php bin/console prestashop:module install demoentityimporter`).

## Requirements

- PrestaShop 9.2.0 or newer (import engine introduced by [PrestaShop/PrestaShop#41907](https://github.com/PrestaShop/PrestaShop/issues/41907)).
