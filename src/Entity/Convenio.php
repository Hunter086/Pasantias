<?php

namespace App\Entity;

use App\Repository\ConvenioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConvenioRepository::class)
 */
class Convenio
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
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $estado;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroExpediente;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $documentoConvenio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tituloExpediente;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaInicioTramite;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaFinTramite;

    /**
     * @ORM\Column(type="string", length=10,nullable=true)
     */
    private $isRenovacionAutomatica;

    /**
     * @ORM\Column(type="integer")
     */
    private $pasos;

    /**
     * @ORM\ManyToOne(targetEntity=AreaUnRaf::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $ultimaArea;

    /**
     * @ORM\ManyToOne(targetEntity=AreaUnRaf::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaSiguiente;

    /**
     * @ORM\ManyToOne(targetEntity=Empresa::class, inversedBy="convenio")
     */
    private $empresa;

    /**
     * @ORM\OneToMany(targetEntity=Pasantia::class, mappedBy="convenioPasantia")
     */
    private $pasantia;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaModificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ultimoUsuario;

    
    public function __construct()
    {
        $this->pasantia = new ArrayCollection();
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

    

    

    

    public function getNumeroExpediente(): ?string
    {
        return $this->numeroExpediente;
    }

    public function setNumeroExpediente(string $numeroExpediente): self
    {
        $this->numeroExpediente = $numeroExpediente;

        return $this;
    }

    public function getDocumentoConvenio(): ?string
    {
        return $this->documentoConvenio;
    }

    public function setDocumentoConvenio(?string $documentoConvenio): self
    {
        $this->documentoConvenio = $documentoConvenio;

        return $this;
    }

   
   
    


    


   

    public function getTituloExpediente(): ?string
    {
        return $this->tituloExpediente;
    }

    public function setTituloExpediente(string $tituloExpediente): self
    {
        $this->tituloExpediente = $tituloExpediente;

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


    public function getIsRenovacionAutomatica(): ?string
    {
        return $this->isRenovacionAutomatica;
    }

    public function setIsRenovacionAutomatica(string $isRenovacionAutomatica): self
    {
        $this->isRenovacionAutomatica = $isRenovacionAutomatica;

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

    public function getUltimaArea(): ?AreaUnRaf
    {
        return $this->ultimaArea;
    }

    public function setUltimaArea(?AreaUnRaf $ultimaArea): self
    {
        $this->ultimaArea = $ultimaArea;

        return $this;
    }

    public function getAreaSiguiente(): ?AreaUnRaf
    {
        return $this->areaSiguiente;
    }

    public function setAreaSiguiente(?AreaUnRaf $areaSiguiente): self
    {
        $this->areaSiguiente = $areaSiguiente;

        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * @return Collection|Pasantia[]
     */
    public function getPasantia(): Collection
    {
        return $this->pasantia;
    }

    public function addPasantium(Pasantia $pasantium): self
    {
        if (!$this->pasantia->contains($pasantium)) {
            $this->pasantia[] = $pasantium;
            $pasantium->setConvenioPasantia($this);
        }

        return $this;
    }

    public function removePasantium(Pasantia $pasantium): self
    {
        if ($this->pasantia->removeElement($pasantium)) {
            // set the owning side to null (unless already changed)
            if ($pasantium->getConvenioPasantia() === $this) {
                $pasantium->setConvenioPasantia(null);
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
}
