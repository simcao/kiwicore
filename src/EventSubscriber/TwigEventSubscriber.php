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

/** @noinspection PhpUnusedParameterInspection */

namespace App\EventSubscriber;

use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Inject settings as Twig Global variable
 *
 * @author Simcao EI
 */
class TwigEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * Constructor.
     *
     * @param Environment $twig
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(Environment $twig, SettingsRepository $settingsRepository)
    {
        $this->twig = $twig;
        $this->settingsRepository = $settingsRepository;
    }

    public function onKernelController(ControllerEvent $event): void
    {
       $this->twig->addGlobal('settings', $this->settingsRepository->findCurrentSettings());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
