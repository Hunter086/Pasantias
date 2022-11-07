<?php

namespace App\Entity;

use App\Repository\PasantiaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PasantiaRepository::class)
 */
class Pasantia
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
    private $fechaInicio;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaFin;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $estado;


    /**
     * @ORM\Column(type="date")
     */
    private $fechaInicioTramite;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaFinTramite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;


    /**
     * @ORM\Column(type="integer")
     */
    private $pasos;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaUltimaModificacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motivoRechazo;

    /**
     * @ORM\ManyToOne(targetEntity=AreaUnRaf::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaActual;

    /**
     * @ORM\ManyToOne(targetEntity=AreaUnRaf::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaEncargada;

    /**
     * @ORM\ManyToOne(targetEntity=Convenio::class, inversedBy="pasantia")
     */
    private $convenioPasantia;

    /**
     * @ORM\ManyToMany(targetEntity=Pasante::class, inversedBy="pasantias")
     */
    private $pasante;

    /**
     * @ORM\OneToMany(targetEntity=Pago::class, mappedBy="pasantia")
     */
    private $pago;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaModificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ultimoUsuario;

    /**
     * @ORM\OneToMany(targetEntity=ActaCompromiso::class, mappedBy="pasantia")
     */
    private $actaCompromiso;

    public function __construct()
    {
        $this->pasante = new ArrayCollection();
        $this->pago = new ArrayCollection();
        $this->actaCompromiso = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

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


    

    public function getFechaInicioTramite(): ?\DateTimeInterface
    {
        return $this->fechaInicioTramite;
    }

    public function setFechaInicioTramite(\DateTimeInterface $fechaInicioTramite): self
    {
        $this->fechaInicioTramite = $fechaInicioTramite;

        return $this;
    }

    public function getFechaFinTramite(): ?\DateTimeInterface
    {
        return $this->fechaFinTramite;
    }

    public function setFechaFinTramite(\DateTimeInterface $fechaFinTramite): self
    {
        $this->fechaFinTramite = $fechaFinTramite;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPasos(): ?int
    {
        return $this->pasos;
    }

    public function setPasos(int $pasos): self
    {
        $this->pasos = $pasos;

        return $this;
    }

    public function getFechaUltimaModificacion(): ?\DateTimeInterface
    {
        return $this->fechaUltimaModificacion;
    }

    public function setFechaUltimaModificacion(\DateTimeInterface $fechaUltimaModificacion): self
    {
        $this->fechaUltimaModificacion = $fechaUltimaModificacion;

        return $this;
    }

    public function getMotivoRechazo(): ?string
    {
        return $this->motivoRechazo;
    }

    public function setMotivoRechazo(?string $motivoRechazo): self
    {
        $this->motivoRechazo = $motivoRechazo;

        return $this;
    }

    public function getAreaActual(): ?AreaUnRaf
    {
        return $this->areaActual;
    }

    public function setAreaActual(?AreaUnRaf $areaActual): self
    {
        $this->areaActual = $areaActual;

        return $this;
    }

    public function getAreaEncargada(): ?AreaUnRaf
    {
        return $this->areaEncargada;
    }

    public function setAreaEncargada(?AreaUnRaf $areaEncargada): self
    {
        $this->areaEncargada = $areaEncargada;

        return $this;
    }

    public function getConvenio(): ?Convenio
    {
        return $this->convenioPasantia;
    }

    public function setConvenio(?Convenio $convenioPasantia): self
    {
        $this->convenioPasantia = $convenioPasantia;

        return $this;
    }

    /**
     * @return Collection|Pasante[]
     */
    public function getPasante(): Collection
    {
        return $this->pasante;
    }

    public function addPasante(Pasante $pasante): self
    {
        if (!$this->pasante->contains($pasante)) {
            $this->pasante[] = $pasante;
        }

        return $this;
    }

    public function removePasante(Pasante $pasante): self
    {
        $this->pasante->removeElement($pasante);

        return $this;
    }

    /**
     * @return Collection|Pago[]
     */
    public function getPago(): Collection
    {
        return $this->pago;
    }

    public function addPago(Pago $pago): self
    {
        if (!$this->pago->contains($pago)) {
            $this->pago[] = $pago;
            $pago->setPasantia($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): self
    {
        if ($this->pago->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getPasantia() === $this) {
                $pago->setPasantia(null);
            }
        }

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

    /**
     * @return Collection|ActaCompromiso[]
     */
    public function getActaCompromiso(): Collection
    {
        return $this->actaCompromiso;
    }

    public function addActaCompromiso(ActaCompromiso $actaCompromiso): self
    {
        if (!$this->actaCompromiso->contains($actaCompromiso)) {
            $this->actaCompromiso[] = $actaCompromiso;
            $actaCompromiso->setPasantia($this);
        }

        return $this;
    }

    public function removeActaCompromiso(ActaCompromiso $actaCompromiso): self
    {
        if ($this->actaCompromiso->removeElement($actaCompromiso)) {
            // set the owning side to null (unless already changed)
            if ($actaCompromiso->getPasantia() === $this) {
                $actaCompromiso->setPasantia(null);
            }
        }

        return $this;
    }
}
