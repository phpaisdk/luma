<?php

declare(strict_types=1);

namespace AiSdk\Luma\Models;

use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\VideoModelInterface;
use AiSdk\Exceptions\InvalidResponseException;
use AiSdk\Luma\LumaOptions;
use AiSdk\Requests\VideoRequest;
use AiSdk\Responses\VideoJob;
use AiSdk\Responses\VideoJobStatus;
use AiSdk\Results\VideoData;
use AiSdk\Support\Usage;
use AiSdk\Utils\Support\Url;

final class LumaVideoModel extends BaseModel implements VideoModelInterface
{
    public function __construct(private readonly string $modelId, private readonly LumaOptions $options) {}

    public function provider(): string
    {
        return LumaOptions::PROVIDER_NAME;
    }

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function generate(VideoRequest $request): VideoJob
    {
        $o = $request->providerOptionsFor($this->provider());
        $body = array_replace(['generation_type' => 'video', 'prompt' => $request->prompt, 'model' => $this->modelId], $o);
        if ($request->output?->aspectRatio) {
            $body['aspect_ratio'] = $request->output->aspectRatio;
        } if ($request->output?->duration) {
            $body['duration'] = $request->output->duration . 's';
        } $p = $this->runner($this->options->sdk)->postJson(Url::joinPath($this->options->baseUrl, '/generations/video'), $body, $this->options->authHeaders(), $this->provider());
        $id = (string) ($p['id'] ?? '');
        if ($id === '') {
            throw new InvalidResponseException('Luma returned no generation id.');
        }

        return new VideoJob($id, $this->provider(), $this->modelId, rawResponse: $p, providerMetadata: ['luma' => []]);
    }

    public function poll(VideoJob $job): VideoJob
    {
        $p = $this->runner($this->options->sdk)->getJson(Url::joinPath($this->options->baseUrl, '/generations/' . rawurlencode($job->id)), $this->options->authHeaders(), $this->provider());
        $status = (string) ($p['state'] ?? $p['status'] ?? '');
        $url = $p['assets']['video'] ?? null;
        if ($status === 'completed' && is_string($url) && $url !== '') {
            return new VideoJob($job->id, $job->provider, $job->modelId, VideoJobStatus::Succeeded, new VideoData(url: $url), usage: new Usage(), rawResponse: $p, providerMetadata: $job->providerMetadata);
        }

        return new VideoJob($job->id, $job->provider, $job->modelId, in_array($status, ['failed', 'error'], true) ? VideoJobStatus::Failed : VideoJobStatus::Running, errorMessage: in_array($status, ['failed', 'error'], true) ? (string) ($p['failure_reason'] ?? 'Luma generation failed.') : null, rawResponse: $p, providerMetadata: $job->providerMetadata);
    }
}
