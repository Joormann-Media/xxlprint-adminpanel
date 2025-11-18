<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\ModuleManager;

#[ORM\Entity]
class ModuleBreadcrumb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ModuleManager::class, inversedBy: 'breadcrumbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModuleManager $module = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $route = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'integer')]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $routeParameters = null;
}
