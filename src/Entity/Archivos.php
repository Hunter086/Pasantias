<?php

namespace App\Entity;

use App\Repository\ArchivosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchivosRepository::class)
 */
class Archivos
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
    private $archivo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombreArchivo;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $extencionArchivo;

    /**
     * @ORM\ManyToOne(targetEntity=Pasante::class, inversedBy="archivos")
     */
    private $pasante;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getNombreArchivo(): ?string
    {
        return $this->nombreArchivo;
    }

    public function setNombreArchivo(string $nombreArchivo): self
    {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    public function getExtencionArchivo(): ?string
    {
        return $this->extencionArchivo;
    }

    public function setExtencionArchivo(string $extencionArchivo): self
    {
        $this->extencionArchivo = $extencionArchivo;

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
}
