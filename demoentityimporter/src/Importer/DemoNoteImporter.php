<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\Module\DemoEntityImporter\Importer;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;

/**
 * Minimal importer example. Because the service is autoconfigured (see
 * config/services.yml), the core automatically tags it and it appears in
 * the EntityImporterRegistry next to the core importers: the import page
 * entity dropdown, the mapping screen and the batch execution all pick it
 * up with no extra wiring.
 *
 * A real importer would validate rows in a 'validation' phase and dispatch
 * CQRS commands (or its own persistence) in a 'database' phase — see
 * PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter
 * in the core for the reference implementation.
 */
final class DemoNoteImporter implements EntityImporterInterface
{
    public const ENTITY_TYPE = 'demo_note';

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function getFields(): EntityFieldCollectionInterface
    {
        return EntityFieldCollection::createFromArray([
            new EntityField('id', 'ID'),
            new EntityField('note', 'Note', '', true),
        ]);
    }

    public function getPhases(): array
    {
        return [
            new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_VALIDATION, 'Validating notes', true),
            new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_DATABASE, 'Importing notes'),
        ];
    }

    public function countPhaseUnits(ImportPhaseDefinition $phase, ImportRunContext $context): int
    {
        // 0 units = the phase is skipped; this demo importer does no real work
        return 0;
    }

    public function processPhaseBatch(ImportPhaseDefinition $phase, ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return new PhaseBatchResult(0);
    }
}
