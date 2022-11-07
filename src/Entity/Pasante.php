<?php

namespace App\Entity;

use App\Repository\PasanteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PasanteRepository::class)
 */
class Pasante
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $apellido;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $cuil;

    /**
     * @ORM\OneToMany(targetEntity=Contacto::class, mappedBy="pasante", cascade={"persist", "remove"})
     */
    private $contacto;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $legajo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $actaCompromiso;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $estadoPasante;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $provincia;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $localidad;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $direccion;

    /**
     * @ORM\OneToMany(targetEntity=Pago::class, mappedBy="pasante")
     */
    private $pago;

    /**
     * @ORM\ManyToMany(targetEntity=Pasantia::class, mappedBy="pasante")
     */
    private $pasantias;

    /**
     * @ORM\OneToMany(targetEntity=Archivos::class, mappedBy="pasante" ,cascade={"persist", "remove"})
     */
    private $archivos;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaModificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ultimoUsuario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSeguimientodelMes;

    

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaSeguimiento;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $isInformeSeguimiento;

    /**
     * @ORM\OneToMany(targetEntity=ActaCompromiso::class, mappedBy="pasante")
     */
    private $actaCompromisos;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCertificado;


    public function __construct()
    {
        $this->contacto = new ArrayCollection();
        $this->pago = new ArrayCollection();
        $this->pasantias = new ArrayCollection();
        $this->archivos = new ArrayCollection();
        $this->actaCompromisos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getCuil(): ?string
    {
        return $this->cuil;
    }

    public function setCuil(string $cuil): self
    {
        $this->cuil = $cuil;

        return $this;
    }

    /**
     * @return Collection|Contacto[]
     */
    public function getContacto(): Collection
    {
        return $this->contacto;
    }

    public function addContacto(Contacto $contacto): self
    {
        if (!$this->contacto->contains($contacto)) {
            $this->contacto[] = $contacto;
            $contacto->setPasante($this);
        }

        return $this;
    }

    public function removeContacto(Contacto $contacto): self
    {
        if ($this->contacto->removeElement($contacto)) {
            // set the owning side to null (unless already changed)
            if ($contacto->getPasante() === $this) {
                $contacto->setPasante(null);
            }
        }

        return $this;
    }

    public function getLegajo(): ?string
    {
        return $this->legajo;
    }

    public function setLegajo(string $legajo): self
    {
        $this->legajo = $legajo;

        return $this;
    }

    public function getActaCompromiso(): ?string
    {
        return $this->actaCompromiso;
    }

    public function setActaCompromiso(?string $actaCompromiso): self
    {
        $this->actaCompromiso = $actaCompromiso;

        return $this;
    }

    public function getEstadoPasante(): ?string
    {
        return $this->estadoPasante;
    }

    public function setEstadoPasante(string $estadoPasante): self
    {
        $this->estadoPasante = $estadoPasante;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

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
            $pago->setPasante($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): self
    {
        if ($this->pago->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getPasante() === $this) {
                $pago->setPasante(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pasantia[]
     */
    public function getPasantias(): Collection
    {
        return $this->pasantias;
    }

    public function addPasantia(Pasantia $pasantia): self
    {
        if (!$this->pasantias->contains($pasantia)) {
            $this->pasantias[] = $pasantia;
            $pasantia->addPasante($this);
        }

        return $this;
    }

    public function removePasantia(Pasantia $pasantia): self
    {
        if ($this->pasantias->removeElement($pasantia)) {
            $pasantia->removePasante($this);
        }

        return $this;
    }

    /**
     * @return Collection|Archivos[]
     */
    public function getArchivos(): Collection
    {
        return $this->archivos;
    }

    public function addArchivo(Archivos $archivo): self
    {
        if (!$this->archivos->contains($archivo)) {
            $this->archivos[] = $archivo;
            $archivo->setPasante($this);
        }

        return $this;
    }

    public function removeArchivo(Archivos $archivo): self
    {
        if ($this->archivos->removeElement($archivo)) {
            // set the owning side to null (unless already changed)
            if ($archivo->getPasante() === $this) {
                $archivo->setPasante(null);
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

    public function getIsSeguimientodelMes(): ?bool
    {
        return $this->isSeguimientodelMes;
    }

    public function setIsSeguimientodelMes(bool $isSeguimientodelMes): self
    {
        $this->isSeguimientodelMes = $isSeguimientodelMes;

        return $this;
    }


    public function getFechaSeguimiento(): ?\DateTimeInterface
    {
        return $this->fechaSeguimiento;
    }

    public function setFechaSeguimiento(?\DateTimeInterface $fechaSeguimiento): self
    {
        $this->fechaSeguimiento = $fechaSeguimiento;

        return $this;
    }

    public function getIsInformeSeguimiento(): ?string
    {
        return $this->isInformeSeguimiento;
    }

    public function setIsInformeSeguimiento(string $isInformeSeguimiento): self
    {
        $this->isInformeSeguimiento = $isInformeSeguimiento;

        return $this;
    }

    /**
     * @return Collection|ActaCompromiso[]
     */
    public function getActaCompromisos(): Collection
    {
        return $this->actaCompromisos;
    }

    public function addActaCompromiso(ActaCompromiso $actaCompromiso): self
    {
        if (!$this->actaCompromisos->contains($actaCompromiso)) {
            $this->actaCompromisos[] = $actaCompromiso;
            $actaCompromiso->setPasante($this);
        }

        return $this;
    }

    public function removeActaCompromiso(ActaCompromiso $actaCompromiso): self
    {
        if ($this->actaCompromisos->removeElement($actaCompromiso)) {
            // set the owning side to null (unless already changed)
            if ($actaCompromiso->getPasante() === $this) {
                $actaCompromiso->setPasante(null);
            }
        }

        return $this;
    }
    /**
     * Look for actaCompromiso where matches pasantia.
     */
    public function searchActaCompromiso($idPasantia)
    {
        foreach ($this->getActaCompromisos() as $key => $value) {
            $pasantia_id= $value->getPasantia()->getId();
            if ($pasantia_id == $idPasantia && $value->getEstado()!="Eliminada") {
                return $value;
            }
        }
        return null;
    }

    public function getIsCertificado(): ?bool
    {
        return $this->isCertificado;
    }

    public function setIsCertificado(?bool $isCertificado): self
    {
        $this->isCertificado = $isCertificado;

        return $this;
    }

   

    


    

    
}
