<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class CategoryFromPayload
{
    public function __construct(
        public readonly ?string $brand,
        public readonly ?string $campaign,
        public readonly ?string $market,
        public readonly ?string $segment,
        public readonly ?string $platform,
    ) {
        Assert::nullOrString($this->brand, 'Expected "brand" to be a string, got %s');
        Assert::nullOrString($this->campaign, 'Expected "campaign" to be a string, got %s');
        Assert::nullOrString($this->market, 'Expected "market" to be a string, got %s');
        Assert::nullOrString($this->segment, 'Expected "segment" to be a string, got %s');
        Assert::nullOrString($this->platform, 'Expected "platform" to be a string, got %s');
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'brand' => $this->brand,
            'campaign' => $this->campaign,
            'market' => $this->market,
            'segment' => $this->segment,
            'platform' => $this->platform,
        ];
    }
}
