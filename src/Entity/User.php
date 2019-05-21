<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $identificationNumberPesel;

    public function __construct(string $firstname, string $surname, string $identificationNumberPesel)
    {
        $this->firstname = $firstname;
        $this->surname = $surname;
        $this->identificationNumberPesel = $identificationNumberPesel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getIdentificationNumberPesel(): ?string
    {
        return $this->identificationNumberPesel;
    }

    public function setIdentificationNumberPesel(string $identificationNumberPesel): self
    {
        $this->identificationNumberPesel = $identificationNumberPesel;

        return $this;
    }

    public static function validateIdentificationNumberPesel($value):bool
    {
        $value = strval($value);
        if (strlen($value) != 11) {
            return false;
        }
        $checksum =
            9*$value[0] +
            7*$value[1] +
            3*$value[2] +
            1*$value[3] +
            9*$value[4] +
            7*$value[5] +
            3*$value[6] +
            1*$value[7] +
            9*$value[8] +
            7*$value[9];
        if ($checksum % 10 != $value[10]) {
            return false;
        }

        return true;
    }
}
