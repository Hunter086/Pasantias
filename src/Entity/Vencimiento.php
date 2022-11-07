<?php

namespace App\Entity;

use App\Repository\VencimientoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VencimientoRepository::class)
 */
class Vencimiento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_vencimiento;

    /**
     * @ORM\ManyToOne(targetEntity=Pasantia::class)
     */
    private $pasantia;

    /**
     * @ORM\ManyToOne(targetEntity=Convenio::class)
     */
    private $convenio;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $estado;

    public function __construct()
    {
        $this->pasantia_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaVencimiento(): ?\DateTimeInterface
    {
        return $this->fecha_vencimiento;
    }

    public function setFechaVencimiento(\DateTimeInterface $fecha_vencimiento): self
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

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

    public function getConvenio(): ?Convenio
    {
        return $this->convenio;
    }

    public function setConvenio(?Convenio $convenio): self
    {
        $this->convenio = $convenio;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

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
