<?php

namespace App\DAV;

use Sabre\DAV\Server;
use Sabre\DAVACL\PrincipalCollection;
use Sabre\DAV\Auth\Plugin as AuthPlugin;
use Sabre\DAV\Locks\Plugin as LocksPlugin;
use Sabre\DAVACL\Plugin as AclPlugin;
use Sabre\DAV\Browser\Plugin as BrowserPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DavServer
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function exec(): void
    {
        $principalBackend = new PrincipalBackend($this->container);
        $carddavBackend = new CardDavBackend($this->container);

        $nodes = [
            new PrincipalCollection($principalBackend),
            new AddressBookRoot($principalBackend, $carddavBackend),
        ];

        $server = new Server($nodes);
        $server->setBaseUri('/dav/');

        $authBackend = new SymfonyAuthBackend($this->container);
        $server->addPlugin(new AuthPlugin($authBackend));
        $server->addPlugin(new BrowserPlugin()); // für Test im Browser
        $server->addPlugin(new LocksPlugin());
        $server->addPlugin(new AclPlugin());

        $server->exec();
    }
}

