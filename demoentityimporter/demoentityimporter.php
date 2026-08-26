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

if (!defined('_PS_VERSION_')) {
    exit;
}

class DemoEntityImporter extends Module
{
    public function __construct()
    {
        $this->name = 'demoentityimporter';
        $this->author = 'PrestaShop';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '9.3.0', 'max' => '9.99.99'];

        parent::__construct();

        $this->displayName = $this->trans('Demo - register a custom entity importer', [], 'Modules.Demoentityimporter.Admin');
        $this->description = $this->trans('Shows how a module registers its own importer into the core import engine and persists a Doctrine entity.', [], 'Modules.Demoentityimporter.Admin');
    }

    public function install(): bool
    {
        return parent::install() && $this->installDatabase();
    }

    public function uninstall(): bool
    {
        return parent::uninstall() && $this->uninstallDatabase();
    }

    /**
     * Creates the table backing the DemoNote Doctrine entity
     * (src/Entity/DemoNote.php).
     */
    private function installDatabase(): bool
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'demo_note` (
                `id_demo_note` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `note` VARCHAR(255) NOT NULL,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_demo_note`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;
        ');
    }

    private function uninstallDatabase(): bool
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'demo_note`');
    }
}
