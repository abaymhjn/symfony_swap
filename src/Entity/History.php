<?php
// src/Entity/History.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="history")
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="firstIn")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    private $firstIn;

    /**
     * @ORM\Column(type="integer", name="secondIn")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    private $secondIn;

    /**
     * @ORM\Column(type="integer", name="firstOut", nullable=true)
     * * @Assert\Type(type="integer")
     */
    private $firstOut;

    /**
     * @ORM\Column(type="integer", name="secondOut", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $secondOut;

    /**
     * @ORM\Column(type="datetime", name="creationDate")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", name="updateDate", nullable=true)
     */
    private $updateDate;

    // Add getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    // Add getters and setters for other properties

    public function getFirstIn(): ?int
    {
        return $this->firstIn;
    }

    public function setFirstIn(int $firstIn): self
    {
        $this->firstIn = $firstIn;

        return $this;
    }

    public function getSecondIn(): ?int
    {
        return $this->secondIn;
    }

    public function setSecondIn(int $secondIn): self
    {
        $this->secondIn = $secondIn;

        return $this;
    }

    public function getFirstOut(): ?int
    {
        return $this->firstOut;
    }

    public function setFirstOut(int $firstOut): self
    {
        $this->firstOut = $firstOut;

        return $this;
    }

    public function getSecondOut(): ?int
    {
        return $this->secondOut;
    }

    public function setSecondOut(int $secondOut): self
    {
        $this->secondOut = $secondOut;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
    
    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }
}
