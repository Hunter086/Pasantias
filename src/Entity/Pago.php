<?php

namespace App\Entity;

use App\Repository\PagoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PagoRepository::class)
 */
class Pago
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
    private $fechaPago;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $mesAbonado;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalAbonado;

    /**
     * @ORM\Column(type="integer")
     */
    private $porcentajedeCobro;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalaCobrar;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=Pasante::class, inversedBy="pago")
     */
    private $pasante;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comprobantePago;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $factura;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notadeCredito;

    /**
     * @ORM\ManyToOne(targetEntity=Pasantia::class, inversedBy="pago")
     */
    private $pasantia;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaCargadePago;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaModificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ultimoUsuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaPago(): ?\DateTimeInterface
    {
        return $this->fechaPago;
    }

    public function setFechaPago(\DateTimeInterface $fechaPago): self
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    public function getMesAbonado(): ?string
    {
        return $this->mesAbonado;
    }

    public function setMesAbonado(string $mesAbonado): self
    {
        $this->mesAbonado = $mesAbonado;

        return $this;
    }

    public function getTotalAbonado(): ?float
    {
        return $this->totalAbonado;
    }

    public function setTotalAbonado(float $totalAbonado): self
    {
        $this->totalAbonado = $totalAbonado;

        return $this;
    }

    public function getPorcentajedeCobro(): ?int
    {
        return $this->porcentajedeCobro;
    }

    public function setPorcentajedeCobro(int $porcentajedeCobro): self
    {
        $this->porcentajedeCobro = $porcentajedeCobro;

        return $this;
    }

    public function getTotalaCobrar(): ?float
    {
        return $this->totalaCobrar;
    }

    public function setTotalaCobrar(?float $totalaCobrar): self
    {
        $this->totalaCobrar = $totalaCobrar;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

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

    public function getComprobantePago(): ?string
    {
        return $this->comprobantePago;
    }

    public function setComprobantePago(?string $comprobantePago): self
    {
        $this->comprobantePago = $comprobantePago;

        return $this;
    }

    public function getFactura(): ?string
    {
        return $this->factura;
    }

    public function setFactura(?string $factura): self
    {
        $this->factura = $factura;

        return $this;
    }

    public function getNotadeCredito(): ?string
    {
        return $this->notadeCredito;
    }

    public function setNotadeCredito(?string $notadeCredito): self
    {
        $this->notadeCredito = $notadeCredito;

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

    public function getFechaCargadePago(): ?\DateTimeInterface
    {
        return $this->fechaCargadePago;
    }

    public function setFechaCargadePago(?\DateTimeInterface $fechaCargadePago): self
    {
        $this->fechaCargadePago = $fechaCargadePago;

        return $this;
    }

    public function getFechaModificacion(): ?\DateTimeInterface
    {
        return $this->fechaModificacion;
    }

    public function setFechaModificacion(\DateTimeInterface $fechaModificacion): self
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    public function getUltimoUsuario(): ?string
    {
        return $this->ultimoUsuario;
    }

    public function setUltimoUsuario(string $ultimoUsuario): self
    {
        $this->ultimoUsuario = $ultimoUsuario;

        return $this;
    }
}
