<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ApiResource(
    collectionOperations: ['get' => ['currencies' => ['groups' => 'currency:list']]],
    itemOperations: ['get' => ['path' => '/currency/{id}', 'currency' => ['groups' => 'currency:item']]],
    order: ['name' => 'ASC'],
    paginationEnabled: true,
)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['currency:list', 'currency:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['currency:list', 'currency:item'])]
    private $name;

    #[ORM\Column(type: 'float')]
    #[Groups(['currency:list', 'currency:item'])]
    private $rate;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['currency:list', 'currency:item'])]
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
