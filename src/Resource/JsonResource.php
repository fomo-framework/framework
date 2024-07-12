<?php

namespace Fomo\Resource;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use stdClass;

class JsonResource
{
    protected Collection|Paginator|stdClass|null $data;

    protected array $response = [];

    protected array $meta = [];

    protected ?int $perPage = null;

    public function __construct(Collection|Paginator|stdClass|array|null $data , int $perPage = null)
    {
        if (is_array($data)){
            $data = $this->arrayToCollection($data);
        }

        $this->data = $data;
        $this->perPage = $perPage;
    }

    public function dontJson(): array|null
    {
        if (is_null($this->data)){
            return null;
        }

        if ($this->data instanceof stdClass){
            return $this->toArray($this->data);
        }

        $this->process();

        return $this->response;
    }

    protected function arrayToCollection(array $data): Collection
    {
        $collection = new Collection();

        foreach ($data as $item){
            $collection->add((object) $item);
        }

        return $collection;
    }

    public function collection(): string
    {
        $this->process();

        if (is_null($this->perPage)){
            return response()->json([
                'data' => $this->response ,
            ]);
        }

        $this->addToMeta([
            'isLastPage' => count($this->data) < $this->perPage ,
            'perPage' => $this->perPage
        ]);

        return response()->json([
            'data' => $this->response ,
            'meta' => $this->meta
        ]);
    }

    public function single(): string
    {
        if (is_null($this->data)){
            return response()->json([
                'data' => null
            ]);
        }

        if ($this->data instanceof Collection){
            return response()->json([
                'data' => $this->toArray($this->data->first())
            ]);
        }

        return response()->json([
            'data' => $this->toArray($this->data)
        ]);
    }

    protected function process(): void
    {
        if (! is_null($this->data)){
            $this->data->map(function ($data){
                $this->response[] = $this->toArray($data);
            });
        }
    }

    public function addToMeta(array $values): self
    {
        foreach ($values as $key => $value)
            $this->meta[$key] = $value;

        return $this;
    }

    protected function toArray($request)
    {
        return $request;
    }
}