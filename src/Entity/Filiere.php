<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FiliereRepository")
 */
class Filiere
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="filiere", orphanRemoval=true)
     */
    private $modules;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Diplome", inversedBy="filieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $diplome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $annee;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiant", mappedBy="filiere", orphanRemoval=true)
     */
    private $etudiants;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Prof", inversedBy="filieres")
     */
    private $prof;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->prof = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setFiliere($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getFiliere() === $this) {
                $module->setFiliere(null);
            }
        }

        return $this;
    }

    public function getDiplome(): ?Diplome
    {
        return $this->diplome;
    }

    public function setDiplome(?Diplome $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }
    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->setFiliere($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            // set the owning side to null (unless already changed)
            if ($etudiant->getFiliere() === $this) {
                $etudiant->setFiliere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Prof[]
     */
    public function getProf(): Collection
    {
        return $this->prof;
    }

    public function addProf(Prof $prof): self
    {
        if (!$this->prof->contains($prof)) {
            $this->prof[] = $prof;
        }

        return $this;
    }

    public function removeProf(Prof $prof): self
    {
        if ($this->prof->contains($prof)) {
            $this->prof->removeElement($prof);
        }

        return $this;
    }
}
