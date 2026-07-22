<?php

declare(strict_types=1);

namespace AiSdk\Luma;

use AiSdk\Support\Sdk;
use AiSdk\Utils\Support\Env;
use AiSdk\Utils\Support\Url;

final class LumaOptions
{
    public const string PROVIDER_NAME = 'luma';

    public const string DEFAULT_BASE_URL = 'https://api.lumalabs.ai/dream-machine/v1';

    /** @param array<string,string> $headers */
    public function __construct(public readonly string $apiKey, public readonly string $baseUrl = self::DEFAULT_BASE_URL, public readonly array $headers = [], public readonly ?Sdk $sdk = null) {}

    /** @param array<string,mixed> $config */
    public static function fromArray(array $config = []): self
    {
        return new self(Env::loadApiKey($config['apiKey'] ?? null, 'LUMA_API_KEY', self::PROVIDER_NAME), Url::withoutTrailingSlash(Env::loadOptionalSetting($config['baseUrl'] ?? null, 'LUMA_BASE_URL') ?? self::DEFAULT_BASE_URL), is_array($config['headers'] ?? null) ? $config['headers'] : [], ($config['sdk'] ?? null) instanceof Sdk ? $config['sdk'] : null);
    }

    /** @return array<string,string> */
    public function authHeaders(): array
    {
        return array_merge(['Authorization' => 'Bearer '.$this->apiKey], $this->headers);
    }
}
