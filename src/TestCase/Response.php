<?php

namespace Tower\TestCase;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

class Response
{
    protected GuzzleResponse $response;

    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    public function assertSuccess(): self
    {
        PHPUnit::assertTrue(
            $this->isSuccess(),
            "message : {$this->body()}\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        return $this;
    }

    public function assertStatus(int $status): self
    {
        PHPUnit::assertSame(
            $this->status() ,
            $status ,
            "message : {$this->body()}\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        return $this;
    }

    public function assertOk(): self
    {
        return $this->assertStatus(200);
    }

    public function assertCreated(): self
    {
        return $this->assertStatus(201);
    }

    public function assertNotFound(): self
    {
        return $this->assertStatus(404);
    }

    public function assertForbidden(): self
    {
        return $this->assertStatus(403);
    }

    public function assertUnauthorized(): self
    {
        return $this->assertStatus(401);
    }

    public function assertUnprocessable(): self
    {
        return $this->assertStatus(422);
    }

    public function assertNoContent(int $status = 204): self
    {
        $this->assertStatus($status);

        PHPUnit::assertEmpty(
            $this->body(),
            "message : response content is not empty.\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        return $this;
    }

    public function assertRedirect(?string $uri = null): self
    {
        PHPUnit::assertTrue(
            $this->isRedirect(),
            "message : is not a redirect.\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        if (! is_null($uri)) {
            $this->assertLocation($uri);
        }

        return $this;
    }

    public function assertLocation(string $uri): self
    {
        PHPUnit::assertEquals(
            $uri, $this->header('Location')
        );

        return $this;
    }

    public function assertHeader(string $header, string $value = null): self
    {
        PHPUnit::assertTrue(
            $this->header($header),
            "header [$header] not present on response.\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        $actual = $this->header($header);

        if (! is_null($value)) {
            PHPUnit::assertEquals(
                $value, $this->header($header) ,
                "header [$header] was found, but value [$actual] does not match [$value].\nstatus : {$this->status()}\n--------------------------------------------------------------"
            );
        }

        return $this;
    }

    public function assertHeaderMissing(string $header): self
    {
        PHPUnit::assertFalse(
            $this->hasHeader($header),
            "unexpected header [$header] is present on response.\nstatus : {$this->status()}\n--------------------------------------------------------------"
        );

        return $this;
    }

    public function assertDownload(string $filename = null): self
    {
        $contentDisposition = explode(';', $this->header('content-disposition'));

        if (trim($contentDisposition[0]) !== 'attachment') {
            PHPUnit::fail(
                'Response does not offer a file download.'.PHP_EOL.
                'Disposition ['.trim($contentDisposition[0]).'] found in header, [attachment] expected.'
            );
        }

        if (! is_null($filename)) {
            if (isset($contentDisposition[1]) &&
                trim(explode('=', $contentDisposition[1])[0]) !== 'filename') {
                PHPUnit::fail(
                    'Unsupported Content-Disposition header provided.'.PHP_EOL.
                    'Disposition ['.trim(explode('=', $contentDisposition[1])[0]).'] found in header, [filename] expected.'
                );
            }

            $message = "Expected file [{$filename}] is not present in Content-Disposition header.";

            if (! isset($contentDisposition[1])) {
                PHPUnit::fail($message);
            } else {
                PHPUnit::assertSame(
                    $filename,
                    isset(explode('=', $contentDisposition[1])[1])
                        ? trim(explode('=', $contentDisposition[1])[1])
                        : '',
                    $message
                );

                return $this;
            }
        } else {
            PHPUnit::assertTrue(true);

            return $this;
        }
    }

    public function assertSee(string|array $value, bool $escape = true): self
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map('e', ($value)) : $value;

        foreach ($values as $value) {
            PHPUnit::assertStringContainsString((string) $value, $this->body());
        }

        return $this;
    }

    public function assertSeeText(string|array $value, bool $escape = true): self
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map('e', ($value)) : $value;

        tap(strip_tags($this->body()), function ($content) use ($values) {
            foreach ($values as $value) {
                PHPUnit::assertStringContainsString((string) $value, $content);
            }
        });

        return $this;
    }

    public function assertDontSee(string|array $value, bool $escape = true): self
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map('e', ($value)) : $value;

        foreach ($values as $value) {
            PHPUnit::assertStringNotContainsString((string) $value, $this->body());
        }

        return $this;
    }

    public function assertDontSeeText(string|array $value, bool $escape = true): self
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map('e', ($value)) : $value;

        tap(strip_tags($this->body()), function ($content) use ($values) {
            foreach ($values as $value) {
                PHPUnit::assertStringNotContainsString((string) $value, $content);
            }
        });

        return $this;
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function json(): object|array
    {
        return json_decode($this->body() , true);
    }

    public function object(): object|array
    {
        return json_decode($this->body() , false);
    }

    public function collect(): Collection
    {
        return new Collection(json_decode($this->body() , true));
    }

    public function header(string $header): string
    {
        return $this->response->getHeaderLine($header);
    }

    public function hasHeader(string $header): string
    {
        return (bool) $this->response->getHeaderLine($header);
    }

    protected function headers(): array
    {
        return collect($this->response->getHeaders())->mapWithKeys(function ($v, $k) {
            return [$k => $v];
        })->all();
    }

    public function isSuccess(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isOk(): bool
    {
        return $this->status() === 200;
    }

    public function isRedirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function isClientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function isServerError(): bool
    {
        return $this->status() >= 500;
    }

    public function isFailed(): bool
    {
        return $this->isServerError() || $this->isClientError();
    }

    public function offsetExists(int $offset): bool
    {
        return isset($this->json()[$offset]);
    }

    public function offsetGet(int $offset): object|array
    {
        return $this->json()[$offset];
    }
}