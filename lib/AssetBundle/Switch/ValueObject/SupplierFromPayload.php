<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class SupplierFromPayload
{
    public function __construct(
        public readonly ?string $supplierCode,
        public readonly ?string $supplierCompany,
        public readonly ?string $supplierContact,
        public readonly ?string $supplierStreetName,
        public readonly ?string $supplierStreetNumber,
        public readonly ?string $supplierPostalCode,
        public readonly ?string $supplierPhoneNumber,
        public readonly ?string $supplierEmail,
    ) {
        Assert::nullOrString($this->supplierCode, 'Expected "supplierCode" to be a string, got %s');
        Assert::nullOrString($this->supplierCompany, 'Expected "supplierCompany" to be a string, got %s');
        Assert::nullOrString($this->supplierContact, 'Expected "supplierContact" to be a string, got %s');
        Assert::nullOrString($this->supplierStreetName, 'Expected "supplierStreetName" to be a string, got %s');
        Assert::nullOrString($this->supplierStreetNumber, 'Expected "supplierStreetNumber" to be a string, got %s');
        Assert::nullOrString($this->supplierPostalCode, 'Expected "supplierPostalCode" to be a string, got %s');
        Assert::nullOrString($this->supplierPhoneNumber, 'Expected "supplierPhoneNumber" to be a string, got %s');
        Assert::nullOrString($this->supplierEmail, 'Expected "supplierEmail" to be a string, got %s');
    }
}
