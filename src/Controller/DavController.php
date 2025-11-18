<?php

namespace App\Controller;

use App\DAV\CardDAV\CardDavBackend;
use App\DAV\Principal\SymfonyPrincipalBackend;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\DAV\Server;
use Sabre\DAV\Auth\Plugin as AuthPlugin;
use Sabre\DAV\Auth\Backend\AbstractBasic;
use Sabre\DAVACL\Plugin as ACLPlugin;
use Sabre\CardDAV\Plugin as CardDAVPlugin;
use Sabre\CardDAV\AddressBookRoot;
use Sabre\DAVACL\PrincipalCollection;
use Sabre\DAV\Browser\Plugin as BrowserPlugin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\DisableProfiler;

class DavController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/dav/{path}', name: 'dav', requirements: ['path' => '.*'], defaults: ['path' => ''])]
    #[DisableProfiler]
    public function index(Request $request, string $path = ''): Response
    {
        // Workaround: Authorization Header manuell auslesen, falls nicht durch Apache übergeben
$authHeader = $request->headers->get('Authorization');
if ($authHeader && stripos($authHeader, 'Basic ') === 0) {
    $base64 = substr($authHeader, 6);
    $decoded = base64_decode($base64);
    if ($decoded && str_contains($decoded, ':')) {
        [$phpAuthUser, $phpAuthPw] = explode(':', $decoded, 2);
        $_SERVER['PHP_AUTH_USER'] = $phpAuthUser;
        $_SERVER['PHP_AUTH_PW'] = $phpAuthPw;
    }
}


        // === AUTH BACKEND ===
        $authBackend = new class($this->em) extends AbstractBasic {
            public function __construct(private EntityManagerInterface $em) {}

            protected function validateUserPass($username, $password): bool {
                $user = $this->em->getRepository(\App\Entity\User::class)
                    ->findOneBy(['email' => $username]);

                return $user && password_verify($password, $user->getPassword());
            }
        };

        // === BACKENDS ===
        $principalBackend = new SymfonyPrincipalBackend($this->em);
        $cardDavBackend = new CardDavBackend($this->em);

        // === ROOT NODES ===
        $nodes = [
            new PrincipalCollection($principalBackend),
            new AddressBookRoot($principalBackend, $cardDavBackend),
        ];

        // === SERVER ===
        $server = new Server($nodes);
        $server->setBaseUri('/dav/');

        // === PLUGINS ===
        $server->addPlugin(new AuthPlugin($authBackend, 'SabreDAV'));
        $server->addPlugin(new ACLPlugin());
        $server->addPlugin(new CardDAVPlugin());
        $server->addPlugin(new BrowserPlugin());

        // === SERVER AUSFÜHREN ===
        ob_start();
        $server->exec();
        $responseBody = ob_get_clean();

        // === RESPONSE ===
        $response = new Response($responseBody);
        foreach (headers_list() as $header) {
            if (str_contains($header, ':')) {
                [$name, $value] = explode(':', $header, 2);
                $response->headers->set(trim($name), trim($value));
            }
        }
        header_remove(); // Symfony übernimmt

        return $response;
    }
    #[Route('/.well-known/carddav', name: 'well_known_carddav')]
public function wellKnownRedirect(): Response
{
    return $this->redirect('/dav/', 301);
}

}
