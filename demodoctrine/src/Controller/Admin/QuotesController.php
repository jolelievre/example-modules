<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\DemoDoctrine\Controller\Admin;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\DemoDoctrine\Entity\Quote;
use PrestaShop\Module\DemoDoctrine\Entity\QuoteLang;
use PrestaShop\Module\DemoDoctrine\Grid\Definition\Factory\QuoteGridDefinitionFactory;
use PrestaShop\Module\DemoDoctrine\Grid\Filters\QuoteFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuotesController extends FrameworkBundleAdminController
{
    /**
     * List quotes
     *
     * @param QuoteFilters $filters
     *
     * @return Response
     */
    public function indexAction(QuoteFilters $filters)
    {
        $quoteGridFactory = $this->get('prestashop.module.demodoctrine.grid.factory.quotes');
        try {
            $quoteGrid = $quoteGridFactory->getGrid($filters);
        } catch (TableNotFoundException $e) {
            return $this->redirectToRoute('ps_demodoctrine_quote_generate');
        }

        return $this->render(
            '@Modules/Demodoctrine/views/templates/admin/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Quotes', 'Modules.Demodoctrine.Admin'),
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'quoteGrid' => $this->presentGrid($quoteGrid),
            ]
        );
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.demodoctrine.grid.definition.factory.quotes'),
            $request,
            QuoteGridDefinitionFactory::GRID_ID,
            'ps_demodoctrine_quote_index'
        );
    }

    /**
     * List quotes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function generateAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->installTables();
            $this->removeAllQuotes();
            $this->insertQuotes();
        }

        return $this->render(
            '@Modules/Demodoctrine/views/templates/admin/generate.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Quotes', 'Modules.Demodoctrine.Admin'),
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            ]
        );
    }

    /**
     * Create quote
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        return $this->redirectToRoute('ps_demodoctrine_quote_index');
    }

    /**
     * Edit quote
     *
     * @param Request $request
     * @param int $quoteId
     *
     * @return Response
     */
    public function editAction(Request $request, $quoteId)
    {
        return $this->redirectToRoute('ps_demodoctrine_quote_index');
    }

    /**
     * Delete quote
     *
     * @param Request $request
     * @param int $quoteId
     *
     * @return Response
     */
    public function deleteAction(Request $request, $quoteId)
    {
        return $this->redirectToRoute('ps_demodoctrine_quote_index');
    }

    /**
     * Delete bulk quotes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteBulkAction(Request $request)
    {
        return $this->redirectToRoute('ps_demodoctrine_quote_index');
    }

    /**
     * @return array[]
     */
    private function getToolbarButtons()
    {
        return [
            'add' => [
                'desc' => $this->trans('Add new quote', 'Modules.Demodoctrine.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('ps_demodoctrine_quote_create'),
            ],
            'generate' => [
                'desc' => $this->trans('Generate quotes', 'Modules.Demodoctrine.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('ps_demodoctrine_quote_generate'),
            ],
        ];
    }

    /**
     * This is an ugly way to install the tables, but PrestaShop doesn't manage
     * module's entities schema update (...yet)
     */
    private function installTables()
    {
        $sqlInstallFile = __DIR__ . '/../../../Resources/data/install.sql';
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->get('doctrine');
        /** @var Connection $connection */
        $connection = $doctrine->getConnection();
        try {
            $connection->exec(file_get_contents($sqlInstallFile));
        } catch (TableExistsException $e) {
            // In case tables are already created
        }
    }

    private function removeAllQuotes()
    {
        $repository = $this->get('prestashop.module.demodoctrine.repository.quote_repository');
        $quotes = $repository->findAll();
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');
        foreach ($quotes as $quote) {
            $em->remove($quote);
        }
        $em->flush();
    }

    private function insertQuotes()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');

        /** @var LangRepository $langRepository */
        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $languages = $langRepository->findAll();

        $quotesDataFile = __DIR__ . '/../../../Resources/data/quotes.json';
        $quotesData = json_decode(file_get_contents($quotesDataFile), true);
        foreach ($quotesData as $quoteData) {
            $quote = new Quote();
            $quote->setAuthor($quoteData['author']);
            /** @var Lang $language */
            foreach ($languages as $language) {
                $quoteLang = new QuoteLang();
                $quoteLang->setLang($language);
                if (isset($quoteData['quotes'][$language->getIsoCode()])) {
                    $quoteLang->setContent($quoteData['quotes'][$language->getIsoCode()]);
                } else {
                    $quoteLang->setContent($quoteData['quotes']['en']);
                }
                $quote->addQuoteLang($quoteLang);
            }
            $em->persist($quote);
        }
        $em->flush();
    }
}