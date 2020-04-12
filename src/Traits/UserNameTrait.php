<?php

namespace IHelpShopping\Traits;

trait UserNameTrait
{
    protected $firstName;
    protected $middleName = null;
    protected $lastName;

    public function __toString(): string
    {
        $middleName = empty($this->getMiddleName()) ? ' ': ' '.$this->getMiddleName().' ';

        return sprintf(
            '%s%s%s',
            $this->getFirstName(),
            $middleName,
            $this->getLastName()
        );
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
