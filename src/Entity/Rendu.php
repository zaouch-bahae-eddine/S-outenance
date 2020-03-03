<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RenduRepository")
 */
class Rendu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etudiant", inversedBy="rendus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Soutenance", inversedBy="rendus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $soutenance;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rendu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getSoutenance(): ?Soutenance
    {
        return $this->soutenance;
    }

    public function setSoutenance(?Soutenance $soutenance): self
    {
        $this->soutenance = $soutenance;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getRendu(): ?string
    {
        return $this->rendu;
    }

    public function setRendu(?string $rendu): self
    {
        $this->rendu = $rendu;

        return $this;
    }
}
