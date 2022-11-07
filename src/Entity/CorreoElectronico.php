<?php

namespace App\Entity;

use App\Repository\CorreoElectronicoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CorreoElectronicoRepository::class)
 */
class CorreoElectronico
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
    private $destinatario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asunto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cuerpoMensaje;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $destinatarioCC;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $destinatarioCCO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archivoAdjuntos;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinatario(): ?string
    {
        return $this->destinatario;
    }

    public function setDestinatario(string $destinatario): self
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    public function getAsunto(): ?string
    {
        return $this->asunto;
    }

    public function setAsunto(string $asunto): self
    {
        $this->asunto = $asunto;

        return $this;
    }

    public function getCuerpoMensaje(): ?string
    {
        return $this->cuerpoMensaje;
    }

    public function setCuerpoMensaje(string $cuerpoMensaje): self
    {
        $this->cuerpoMensaje = $cuerpoMensaje;

        return $this;
    }

    public function getDestinatarioCC(): ?string
    {
        return $this->destinatarioCC;
    }

    public function setDestinatarioCC(?string $destinatarioCC): self
    {
        $this->destinatarioCC = $destinatarioCC;

        return $this;
    }

    public function getDestinatarioCCO(): ?string
    {
        return $this->destinatarioCCO;
    }

    public function setDestinatarioCCO(?string $destinatarioCCO): self
    {
        $this->destinatarioCCO = $destinatarioCCO;

        return $this;
    }

    public function getArchivoAdjuntos(): ?string
    {
        return $this->archivoAdjuntos;
    }

    public function setArchivoAdjuntos(string $archivoAdjuntos): self
    {
        $this->archivoAdjuntos = $archivoAdjuntos;

        return $this;
    }

    
}
