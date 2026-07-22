# aisdk/luma

<a href="https://github.com/phpaisdk/luma/actions"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/phpaisdk/luma/tests.yml?branch=main&label=Tests"></a>
<a href="https://packagist.org/packages/aisdk/luma"><img alt="Latest Version" src="https://img.shields.io/packagist/v/aisdk/luma"></a>
<a href="https://packagist.org/packages/aisdk/luma"><img alt="License" src="https://img.shields.io/packagist/l/aisdk/luma"></a>

------

Official Luma provider for the PHP AI SDK. Uses Luma Dream Machine's native video-generation API.

## Installation

```bash
composer require aisdk/luma
```

## Basic Usage

```php
use AiSdk\Generate;
use AiSdk\Luma;

Luma::create(['apiKey' => $_ENV['LUMA_API_KEY']]);

$video = Generate::video('A cinematic product reveal')
    ->model(Luma::model('ray-2'))
    ->aspectRatio('16:9')
    ->duration(5)
    ->run(timeout: 600);
```

## Configuration

| Variable | Description | Default |
| --- | --- | --- |
| `LUMA_API_KEY` | API key for authentication | Required |
| `LUMA_BASE_URL` | Base URL for API requests | `https://api.lumalabs.ai/dream-machine/v1` |

## Supported Capabilities

| Capability | Support |
| --- | --- |
| Text-to-video generation | Native |
| Async job polling | Native |
| Image-to-video and other Dream Machine fields | Via raw provider options |

The package creates a `/generations/video` job and polls `/generations/{id}` until the video is available. Use `providerOptions('luma', ['keyframes' => [...], 'resolution' => '720p'])` for supported native Luma request fields.

## Documentation

- [Luma Dream Machine video-generation documentation](https://docs.lumalabs.ai/ue/docs/video-generation)
- [PHP AI SDK documentation](https://phpaisdk.com/docs/luma)
