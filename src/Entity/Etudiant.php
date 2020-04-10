<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtudiantRepository")
 */
class Etudiant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="etudiant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compte;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Creneau", mappedBy="etudiants")
     */
    private $creneaus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rendu", mappedBy="etudiant")
     */
    private $rendus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="etudiant")
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Filiere", inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=true)
     */
    private $filiere;

    public function __construct()
    {
        $this->creneaus = new ArrayCollection();
        $this->rendus = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompte(): ?Utilisateur
    {
        return $this->compte;
    }

    public function setCompte(?Utilisateur $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
    /**
     * @return Collection|Creneau[]
     */
    public function getCreneaus(): Collection
    {
        return $this->creneaus;
    }

    public function addCreneau(Creneau $creneau): self
    {
        if (!$this->creneaus->contains($creneau)) {
            $this->creneaus[] = $creneau;
            $creneau->addEtudiant($this);
        }

        return $this;
    }

    public function removeCreneau(Creneau $creneau): self
    {
        if ($this->creneaus->contains($creneau)) {
            $this->creneaus->removeElement($creneau);
            $creneau->removeEtudiant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Rendu[]
     */
    public function getRendus(): Collection
    {
        return $this->rendus;
    }

    public function addRendus(Rendu $rendus): self
    {
        if (!$this->rendus->contains($rendus)) {
            $this->rendus[] = $rendus;
            $rendus->setEtudiant($this);
        }

        return $this;
    }

    public function removeRendus(Rendu $rendus): self
    {
        if ($this->rendus->contains($rendus)) {
            $this->rendus->removeElement($rendus);
            // set the owning side to null (unless already changed)
            if ($rendus->getEtudiant() === $this) {
                $rendus->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setEtudiant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getEtudiant() === $this) {
                $note->setEtudiant(null);
            }
        }

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }
}
