<?php

declare(strict_types=1);

namespace AiSdk\Luma;

use AiSdk\Contracts\BaseProvider;
use AiSdk\Contracts\VideoModelInterface;
use AiSdk\Contracts\VideoProviderInterface;
use AiSdk\Luma\Models\LumaVideoModel;

final class LumaProvider extends BaseProvider implements VideoProviderInterface
{
    public function __construct(public readonly LumaOptions $options) {}

    public function name(): string
    {
        return LumaOptions::PROVIDER_NAME;
    }

    protected function videoModel(string $modelId): VideoModelInterface
    {
        return new LumaVideoModel($modelId, $this->options);
    }
}
