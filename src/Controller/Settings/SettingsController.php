<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */

namespace App\Controller\Settings;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage settings related pages
 *
 * @author Simcao EI
 */
class SettingsController extends AbstractController
{
    /**
     * Return page with current settings.
     *
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/admin/configuration', name: 'kiwicore_settings')]
    public function listSettings(ManagerRegistry $doctrine): Response
    {
        $settings = $doctrine->getRepository(Settings::class)->findCurrentSettings();

        return $this->render('modules/settings/index.html.twig', [
            'settings' => $settings
        ]);
    }

    /**
     * Return page with form to add/update settings.
     *
     * @param SettingsRepository $settingsRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/configuration/modifier', name: 'kiwicore_settings_update')]
    public function updateSettings(SettingsRepository $settingsRepository, Request $request): Response
    {
        $settings = $settingsRepository->findCurrentSettings();

        if (!$settings)
        {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $settings = $form->getData();
            $settingsRepository->save($settings, true);
            $this->addFlash('success', 'Configuration mise à jour avec succès.');
            return $this->redirectToRoute('kiwicore_settings');

        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>configuration</li><li>modifier la configuration</li>",
            'page_title' => 'Configuration',
            'form' => $form,
        ]);
    }

}