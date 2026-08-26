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

namespace PrestaShop\Module\DemoEntityImporter\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * The entity the demo importer persists. Module entities living in
 * src/Entity are mapped automatically by the core (annotation driver,
 * active modules only) — no extra Doctrine configuration is needed.
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class DemoNote
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_demo_note", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    public function __construct(string $note)
    {
        $this->note = $note;
        $this->dateAdd = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }
}
