<?php

namespace App\Entity;

use App\Repository\ActaCompromisoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActaCompromisoRepository::class)
 */
class ActaCompromiso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Pasantia::class, inversedBy="actaCompromiso")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pasantia;

    /**
     * @ORM\ManyToOne(targetEntity=Pasante::class, inversedBy="actaCompromisos")
     */
    private $pasante;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $estado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPasantia(): ?Pasantia
    {
        return $this->pasantia;
    }

    public function setPasantia(?Pasantia $pasantia): self
    {
        $this->pasantia = $pasantia;

        return $this;
    }

    public function getPasante(): ?Pasante
    {
        return $this->pasante;
    }

    public function setPasante(?Pasante $pasante): self
    {
        $this->pasante = $pasante;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }
}
