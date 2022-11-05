<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class Employee implements JsonSerializable
{
    private ?int $id;

    private ?int $companyId;
    private ?int $agencyId;
    private ?int $employeeId;

    private string $name;

    private string $firstName;

    private string $lastName;

    public function __construct(?int $id, ?int $companyId, ?int $agencyId, ?int $employeeId, string $name, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->agencyId = $agencyId;
        $this->employeeId = $employeeId;
        $this->name = strtoupper($name);
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }
    public function getAgencyId(): ?int
    {
        return $this->agencyId;
    }
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function getname(): string
    {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'companyId' => $this->companyId,
            'agencyId' => $this->agencyId,
            'employeeId' => $this->employeeId,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}
