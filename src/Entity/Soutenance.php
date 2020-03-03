<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SoutenanceRepository")
 */
class Soutenance
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $alerte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="soutenances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Prof", inversedBy="soutenances")
     */
    private $evaluateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Creneau", mappedBy="soutenance", orphanRemoval=true)
     */
    private $creneaus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="soutenance", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rendu", mappedBy="soutenance", orphanRemoval=true)
     */
    private $rendus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Prof")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prof;

    public function __construct()
    {
        $this->profs = new ArrayCollection();
        $this->creneaus = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->rendus = new ArrayCollection();
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

    public function getAlerte(): ?\DateTimeInterface
    {
        return $this->alerte;
    }

    public function setAlerte(?\DateTimeInterface $alerte): self
    {
        $this->alerte = $alerte;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return Collection|Prof[]
     */
    public function getEvaluateurs(): Collection
    {
        return $this->profs;
    }

    public function addEvaluateurs(Prof $prof): self
    {
        if (!$this->profs->contains($prof)) {
            $this->profs[] = $prof;
        }

        return $this;
    }

    public function removeEvaluateurs(Prof $prof): self
    {
        if ($this->profs->contains($prof)) {
            $this->profs->removeElement($prof);
        }

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
            $creneau->setSoutenance($this);
        }

        return $this;
    }

    public function removeCreneau(Creneau $creneau): self
    {
        if ($this->creneaus->contains($creneau)) {
            $this->creneaus->removeElement($creneau);
            // set the owning side to null (unless already changed)
            if ($creneau->getSoutenance() === $this) {
                $creneau->setSoutenance(null);
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
            $note->setSoutenance($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getSoutenance() === $this) {
                $note->setSoutenance(null);
            }
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
            $rendus->setSoutenance($this);
        }

        return $this;
    }

    public function removeRendus(Rendu $rendus): self
    {
        if ($this->rendus->contains($rendus)) {
            $this->rendus->removeElement($rendus);
            // set the owning side to null (unless already changed)
            if ($rendus->getSoutenance() === $this) {
                $rendus->setSoutenance(null);
            }
        }

        return $this;
    }

    public function getProf(): ?Prof
    {
        return $this->prof;
    }

    public function setProf(?Prof $prof): self
    {
        $this->prof = $prof;

        return $this;
    }

}
