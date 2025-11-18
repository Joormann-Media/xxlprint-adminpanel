<?php

namespace App\Tests\Controller;

use App\Entity\VoiceReference;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class VoiceReferenceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $voiceReferenceRepository;
    private string $path = '/voice/reference/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->voiceReferenceRepository = $this->manager->getRepository(VoiceReference::class);

        foreach ($this->voiceReferenceRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('VoiceReference index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'voice_reference[roomName]' => 'Testing',
            'voice_reference[voiceFileID]' => 'Testing',
            'voice_reference[voiceFilepath]' => 'Testing',
            'voice_reference[voiceFilewave]' => 'Testing',
            'voice_reference[voiceFilemeta]' => 'Testing',
            'voice_reference[newVoiceFilepath]' => 'Testing',
            'voice_reference[newVoiceFilewave]' => 'Testing',
            'voice_reference[newVoiceFilemeta]' => 'Testing',
            'voice_reference[projectId]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->voiceReferenceRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new VoiceReference();
        $fixture->setRoomName('My Title');
        $fixture->setVoiceFileID('My Title');
        $fixture->setVoiceFilepath('My Title');
        $fixture->setVoiceFilewave('My Title');
        $fixture->setVoiceFilemeta('My Title');
        $fixture->setNewVoiceFilepath('My Title');
        $fixture->setNewVoiceFilewave('My Title');
        $fixture->setNewVoiceFilemeta('My Title');
        $fixture->setProjectId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('VoiceReference');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new VoiceReference();
        $fixture->setRoomName('Value');
        $fixture->setVoiceFileID('Value');
        $fixture->setVoiceFilepath('Value');
        $fixture->setVoiceFilewave('Value');
        $fixture->setVoiceFilemeta('Value');
        $fixture->setNewVoiceFilepath('Value');
        $fixture->setNewVoiceFilewave('Value');
        $fixture->setNewVoiceFilemeta('Value');
        $fixture->setProjectId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'voice_reference[roomName]' => 'Something New',
            'voice_reference[voiceFileID]' => 'Something New',
            'voice_reference[voiceFilepath]' => 'Something New',
            'voice_reference[voiceFilewave]' => 'Something New',
            'voice_reference[voiceFilemeta]' => 'Something New',
            'voice_reference[newVoiceFilepath]' => 'Something New',
            'voice_reference[newVoiceFilewave]' => 'Something New',
            'voice_reference[newVoiceFilemeta]' => 'Something New',
            'voice_reference[projectId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/voice/reference/');

        $fixture = $this->voiceReferenceRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getRoomName());
        self::assertSame('Something New', $fixture[0]->getVoiceFileID());
        self::assertSame('Something New', $fixture[0]->getVoiceFilepath());
        self::assertSame('Something New', $fixture[0]->getVoiceFilewave());
        self::assertSame('Something New', $fixture[0]->getVoiceFilemeta());
        self::assertSame('Something New', $fixture[0]->getNewVoiceFilepath());
        self::assertSame('Something New', $fixture[0]->getNewVoiceFilewave());
        self::assertSame('Something New', $fixture[0]->getNewVoiceFilemeta());
        self::assertSame('Something New', $fixture[0]->getProjectId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new VoiceReference();
        $fixture->setRoomName('Value');
        $fixture->setVoiceFileID('Value');
        $fixture->setVoiceFilepath('Value');
        $fixture->setVoiceFilewave('Value');
        $fixture->setVoiceFilemeta('Value');
        $fixture->setNewVoiceFilepath('Value');
        $fixture->setNewVoiceFilewave('Value');
        $fixture->setNewVoiceFilemeta('Value');
        $fixture->setProjectId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/voice/reference/');
        self::assertSame(0, $this->voiceReferenceRepository->count([]));
    }
}
