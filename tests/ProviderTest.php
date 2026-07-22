<?php

declare(strict_types=1);
use AiSdk\Generate;
use AiSdk\Luma;
use AiSdk\Luma\LumaOptions;
use AiSdk\Luma\Models\LumaVideoModel;
use AiSdk\Support\Sdk;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

it('creates and polls Luma video generations', function (): void {
    $client = new class implements ClientInterface
    {
        /** @var list<RequestInterface> */
        public array $requests = [];

        private int $count = 0;

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            $this->requests[] = $request;

            return new Response(200, ['Content-Type' => 'application/json'], json_encode(++$this->count === 1 ? ['id' => 'job-1', 'state' => 'dreaming'] : ['id' => 'job-1', 'state' => 'completed', 'assets' => ['video' => 'https://cdn.example/video.mp4']], JSON_THROW_ON_ERROR));
        }
    };
    $factory = new Psr17Factory;
    Generate::configure(new Sdk($client, $factory, $factory));
    Luma::create(['apiKey' => 'luma-test']);
    $job = Generate::video('A kite flying over a beach')->model(Luma::model('ray-2'))->job();
    $complete = (new LumaVideoModel('ray-2', new LumaOptions('luma-test', sdk: Generate::sdk())))->poll($job);
    expect($job->id)->toBe('job-1')->and($complete->result?->url)->toBe('https://cdn.example/video.mp4')->and((string) $client->requests[0]->getUri())->toBe('https://api.lumalabs.ai/dream-machine/v1/generations/video')->and((string) $client->requests[1]->getUri())->toBe('https://api.lumalabs.ai/dream-machine/v1/generations/job-1');
});
