<?php

namespace App\Tests\Controller;

use App\Entity\SoundReference;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SoundReferenceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $soundReferenceRepository;
    private string $path = '/sound/reference/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->soundReferenceRepository = $this->manager->getRepository(SoundReference::class);

        foreach ($this->soundReferenceRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SoundReference index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'sound_reference[roomName]' => 'Testing',
            'sound_reference[soundFileID]' => 'Testing',
            'sound_reference[soundFilepath]' => 'Testing',
            'sound_reference[soundFilewave]' => 'Testing',
            'sound_reference[newSoundFilepath]' => 'Testing',
            'sound_reference[newSoundFilewave]' => 'Testing',
            'sound_reference[soundFilemeta]' => 'Testing',
            'sound_reference[newSoundFilemeta]' => 'Testing',
            'sound_reference[projectId]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->soundReferenceRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new SoundReference();
        $fixture->setRoomName('My Title');
        $fixture->setSoundFileID('My Title');
        $fixture->setSoundFilepath('My Title');
        $fixture->setSoundFilewave('My Title');
        $fixture->setNewSoundFilepath('My Title');
        $fixture->setNewSoundFilewave('My Title');
        $fixture->setSoundFilemeta('My Title');
        $fixture->setNewSoundFilemeta('My Title');
        $fixture->setProjectId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SoundReference');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new SoundReference();
        $fixture->setRoomName('Value');
        $fixture->setSoundFileID('Value');
        $fixture->setSoundFilepath('Value');
        $fixture->setSoundFilewave('Value');
        $fixture->setNewSoundFilepath('Value');
        $fixture->setNewSoundFilewave('Value');
        $fixture->setSoundFilemeta('Value');
        $fixture->setNewSoundFilemeta('Value');
        $fixture->setProjectId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'sound_reference[roomName]' => 'Something New',
            'sound_reference[soundFileID]' => 'Something New',
            'sound_reference[soundFilepath]' => 'Something New',
            'sound_reference[soundFilewave]' => 'Something New',
            'sound_reference[newSoundFilepath]' => 'Something New',
            'sound_reference[newSoundFilewave]' => 'Something New',
            'sound_reference[soundFilemeta]' => 'Something New',
            'sound_reference[newSoundFilemeta]' => 'Something New',
            'sound_reference[projectId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/sound/reference/');

        $fixture = $this->soundReferenceRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getRoomName());
        self::assertSame('Something New', $fixture[0]->getSoundFileID());
        self::assertSame('Something New', $fixture[0]->getSoundFilepath());
        self::assertSame('Something New', $fixture[0]->getSoundFilewave());
        self::assertSame('Something New', $fixture[0]->getNewSoundFilepath());
        self::assertSame('Something New', $fixture[0]->getNewSoundFilewave());
        self::assertSame('Something New', $fixture[0]->getSoundFilemeta());
        self::assertSame('Something New', $fixture[0]->getNewSoundFilemeta());
        self::assertSame('Something New', $fixture[0]->getProjectId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new SoundReference();
        $fixture->setRoomName('Value');
        $fixture->setSoundFileID('Value');
        $fixture->setSoundFilepath('Value');
        $fixture->setSoundFilewave('Value');
        $fixture->setNewSoundFilepath('Value');
        $fixture->setNewSoundFilewave('Value');
        $fixture->setSoundFilemeta('Value');
        $fixture->setNewSoundFilemeta('Value');
        $fixture->setProjectId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/sound/reference/');
        self::assertSame(0, $this->soundReferenceRepository->count([]));
    }
}
