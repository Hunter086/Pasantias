<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmpresaRepository::class)
 */
class Empresa
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(type="string", length=150)
     * 
     * 
     */
    
    private $nombre;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $cuit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $razonSocial;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $situaciondeIva;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @ORM\OneToMany(targetEntity=Contacto::class, mappedBy="empresa", cascade={"persist", "remove"})
     */
    private $contacto;

    /**
     * @ORM\OneToMany(targetEntity=Convenio::class, mappedBy="empresa")
     */
    private $convenio;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $estado;

    

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
     * @ORM\Column(type="string", length=255)
     */
    private $ultimoUsuario;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaModificacion;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSeguimientodelmes;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaSeguimiento;

    public function __construct()
    {
        $this->contacto = new ArrayCollection();
        $this->convenio = new ArrayCollection();
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

    public function getCuit(): ?string
    {
        return $this->cuit;
    }

    public function setCuit(string $cuit): self
    {
        $this->cuit = $cuit;

        return $this;
    }

    

   

    


    public function getRazonSocial(): ?string
    {
        return $this->razonSocial;
    }

    public function setRazonSocial(string $razonSocial): self
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    

    public function getSituaciondeIva(): ?string
    {
        return $this->situaciondeIva;
    }

    public function setSituaciondeIva(string $situaciondeIva): self
    {
        $this->situaciondeIva = $situaciondeIva;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

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
            $contacto->setEmpresa($this);
        }

        return $this;
    }

    public function removeContacto(Contacto $contacto): self
    {
        if ($this->contacto->removeElement($contacto)) {
            // set the owning side to null (unless already changed)
            if ($contacto->getEmpresa() === $this) {
                $contacto->setEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Convenio[]
     */
    public function getConvenio(): Collection
    {
        return $this->convenio;
    }

    public function addConvenio(Convenio $convenio): self
    {
        if (!$this->convenio->contains($convenio)) {
            $this->convenio[] = $convenio;
            $convenio->setEmpresa($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenio->removeElement($convenio)) {
            // set the owning side to null (unless already changed)
            if ($convenio->getEmpresa() === $this) {
                $convenio->setEmpresa(null);
            }
        }

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

    public function getUltimoUsuario(): ?string
    {
        return $this->ultimoUsuario;
    }

    public function setUltimoUsuario(string $ultimoUsuario): self
    {
        $this->ultimoUsuario = $ultimoUsuario;

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

    public function getIsSeguimientodelmes(): ?bool
    {
        return $this->isSeguimientodelmes;
    }

    public function setIsSeguimientodelmes(?bool $isSeguimientodelmes): self
    {
        $this->isSeguimientodelmes = $isSeguimientodelmes;

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
    
}
