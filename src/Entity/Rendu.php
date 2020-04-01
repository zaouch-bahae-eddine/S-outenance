<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @ORM\Column(type="float", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rendu;

    private  $renduFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameFile;

    /**
     * @param mixed $renduFile
     */
    public function setRenduFile($renduFile): void
    {
        $this->renduFile = $renduFile;
    }

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

    public function setRendu($rendu,$path): self
    {
        /** @var UploadedFile $rendu*/
        if($rendu) {
            if(file_exists($path.$this->getRendu())&&$path.$this->getRendu()!=$path){
                unlink($path.$this->getRendu());
            }
        }
        try {
            $fileName = $this->generateUniqueFileName().'.'.$rendu->guessExtension();
            $this->rendu ='/public/uploads/rendu/'.$fileName;

            $rendu->move(
                $path.'/public/uploads/rendu/',
                $fileName
            );
        } catch (FileException $e) {
            throw new \Exception("Un probleme dans le telechargement du fichier.");
        }
        $this->setNameFile($rendu->getClientOriginalName());
        return $this;
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    public function getNameFile(): ?string
    {
        return $this->nameFile;
    }

    public function setNameFile(string $nameFile): self
    {
        $this->nameFile = $nameFile;

        return $this;
    }
}
