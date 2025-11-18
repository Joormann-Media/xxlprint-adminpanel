<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserDevice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;

class UserDeviceManagerService
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $httpClient,
        MailerInterface $mailer,
        string $geoApiToken,
        string $adminMail
    ) {
        $this->em = $em;
        $this->httpClient = $httpClient;
        $this->mailer = $mailer;
        $this->geoApiToken = $geoApiToken;
        $this->adminMail = $adminMail;
    }
    

    public function checkOrRegisterDevice(Request $request, User $user): UserDevice
    {
        $fingerprint = $this->generateFingerprint($request->headers);
        $userAgent = $request->headers->get('User-Agent');
        $ip = $request->getClientIp();

        $existingDevice = $this->em->getRepository(UserDevice::class)->findOneBy([
            'user' => $user,
            'deviceFingerprint' => $fingerprint
        ]);

        if ($existingDevice) {
            return $existingDevice;
        }

        $device = new UserDevice();
        $device->setUser($user);
        $device->setDeviceName($this->getDeviceName($request->headers));
        $device->setDeviceFingerprint($fingerprint);
        $device->setIpAddress($ip);
        $device->setUserAgent($userAgent);
        $device->setRegisteredAt(new \DateTime());
        $device->setIsTrusted(false);
        $device->setIsActive(true);

        $this->em->persist($device);
        $this->em->flush();

        return $device;
    }

    public function isTrustedDevice(UserDevice $device): bool
    {
        return $device->isTrusted();
    }

    public function trustDevice(UserDevice $device): void
    {
        $device->setIsTrusted(true);
        $this->em->flush();
    }

    public function untrustDevice(UserDevice $device): void
    {
        $device->setIsTrusted(false);
        $this->em->flush();
    }

    public function getDevicesForUser(User $user): array
    {
        return $this->em->getRepository(UserDevice::class)->findBy(['user' => $user]);
    }

    private function generateFingerprint(HeaderBag $headers): string
    {
        $userAgent = $headers->get('User-Agent');
        $accept = $headers->get('Accept');
        $encoding = $headers->get('Accept-Encoding');
        $language = $headers->get('Accept-Language');

        return hash('sha256', $userAgent . $accept . $encoding . $language);
    }

    private function getDeviceName(HeaderBag $headers): string
    {
        $userAgent = $headers->get('User-Agent');
        if (strpos($userAgent, 'Windows') !== false) return 'Windows Device';
        if (strpos($userAgent, 'Macintosh') !== false) return 'macOS Device';
        if (strpos($userAgent, 'Android') !== false) return 'Android Device';
        if (strpos($userAgent, 'iPhone') !== false) return 'iPhone';
        return 'Unknown Device';
    }
}
