<?php

namespace App\Tests\Controller;

use App\Entity\SshApiKeys;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SshApiKeysControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $sshApiKeyRepository;
    private string $path = '/ssh/api/keys/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->sshApiKeyRepository = $this->manager->getRepository(SshApiKeys::class);

        foreach ($this->sshApiKeyRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SshApiKey index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ssh_api_key[sshapikey]' => 'Testing',
            'ssh_api_key[sshapikeyDescription]' => 'Testing',
            'ssh_api_key[sshapikeyExpiration]' => 'Testing',
            'ssh_api_key[sshapikeyCreate]' => 'Testing',
            'ssh_api_key[sshapikeyUpdate]' => 'Testing',
            'ssh_api_key[sshapikeyValid]' => 'Testing',
            'ssh_api_key[sshapikeyOwner]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->sshApiKeyRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new SshApiKeys();
        $fixture->setSshapikey('My Title');
        $fixture->setSshapikeyDescription('My Title');
        $fixture->setSshapikeyExpiration('My Title');
        $fixture->setSshapikeyCreate('My Title');
        $fixture->setSshapikeyUpdate('My Title');
        $fixture->setSshapikeyValid('My Title');
        $fixture->setSshapikeyOwner('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SshApiKey');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new SshApiKeys();
        $fixture->setSshapikey('Value');
        $fixture->setSshapikeyDescription('Value');
        $fixture->setSshapikeyExpiration('Value');
        $fixture->setSshapikeyCreate('Value');
        $fixture->setSshapikeyUpdate('Value');
        $fixture->setSshapikeyValid('Value');
        $fixture->setSshapikeyOwner('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ssh_api_key[sshapikey]' => 'Something New',
            'ssh_api_key[sshapikeyDescription]' => 'Something New',
            'ssh_api_key[sshapikeyExpiration]' => 'Something New',
            'ssh_api_key[sshapikeyCreate]' => 'Something New',
            'ssh_api_key[sshapikeyUpdate]' => 'Something New',
            'ssh_api_key[sshapikeyValid]' => 'Something New',
            'ssh_api_key[sshapikeyOwner]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ssh/api/keys/');

        $fixture = $this->sshApiKeyRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getSshapikey());
        self::assertSame('Something New', $fixture[0]->getSshapikeyDescription());
        self::assertSame('Something New', $fixture[0]->getSshapikeyExpiration());
        self::assertSame('Something New', $fixture[0]->getSshapikeyCreate());
        self::assertSame('Something New', $fixture[0]->getSshapikeyUpdate());
        self::assertSame('Something New', $fixture[0]->getSshapikeyValid());
        self::assertSame('Something New', $fixture[0]->getSshapikeyOwner());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new SshApiKeys();
        $fixture->setSshapikey('Value');
        $fixture->setSshapikeyDescription('Value');
        $fixture->setSshapikeyExpiration('Value');
        $fixture->setSshapikeyCreate('Value');
        $fixture->setSshapikeyUpdate('Value');
        $fixture->setSshapikeyValid('Value');
        $fixture->setSshapikeyOwner('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ssh/api/keys/');
        self::assertSame(0, $this->sshApiKeyRepository->count([]));
    }
}
