<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserDashboardConfigRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UserDashboardConfigRepository $dashboardConfigRepo,
        private Environment $twig
    ) {}

    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Kein gültiger Benutzer angemeldet.');
        }

        $configs = $this->dashboardConfigRepo->findBy(
            ['user' => $user, 'isVisible' => true],
            ['sortOrder' => 'ASC']
        );

        foreach ($configs as $config) {
            // JSON settings decode fallback
            if (!is_array($config->getSettings())) {
                try {
                    $decoded = json_decode($config->getSettings(), true);
                    $config->setSettings(is_array($decoded) ? $decoded : []);
                } catch (\Throwable) {
                    $config->setSettings([]);
                }
            }

            // ➕ NEU: Entscheide, ob content Twig-Code ist oder Pfad
            $content = $config->getModule()?->getContent() ?? '';

            try {
                if (str_ends_with($content, '.html.twig')) {
                    // 🔁 Es ist ein Template-Pfad → render Twig-Datei
                    $rendered = $this->twig->render($content, ['app' => ['user' => $user]]);
                } else {
                    // 🔧 Inline-Twig-Template → createTemplate()
                    $rendered = $this->twig->createTemplate($content)->render(['app' => ['user' => $user]]);
                }
            } catch (\Throwable $e) {
                $rendered = "<div class='text-danger'>❌ Fehler beim Rendern: {$e->getMessage()}</div>";
            }

            $config->renderedContent = $rendered;
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'page_title' => 'Dashboard',
            'modules' => $configs,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Adminpanel');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // Hier ggf. weitere Menüeinträge hinzufügen
    }
}
