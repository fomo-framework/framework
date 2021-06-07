<?php

namespace Tower;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use stdClass;

class JsonResponse
{
    protected Collection|Paginator|stdClass|null $collection;

    protected array $response = [];
    protected ?int $perePage = null;

    protected bool $isRelation;

    public function __construct(Collection|Paginator|stdClass|null $collection , bool $isRelation = false , int $perPage = null)
    {
        $this->collection = $collection;
        $this->isRelation = $isRelation;
        $this->perPage = $perPage;
        if ($collection instanceof Paginator || $collection instanceof Collection)
            $this->process();
    }

    public function collection(): Response|array
    {
        if ($this->isRelation == true)
        {
            return $this->response;
        }

        if (! is_null($this->perPage))
            return json([
                'data' => $this->response ,
                'meta' => [
                    'isLastPage' => count($this->collection) < $this->perPage ? true : false  ,
                    'perPage' => $this->perPage ,
                ]
            ]);

        return json([
            'data' => $this->response ,
        ]);
    }

    public function single(): Response
    {
        if (is_null($this->collection))
            return json([
                'data' => []
            ]);

        return json([
            'data' => $this->toArray($this->collection)
        ]);
    }

    protected function process(): void
    {
        $this->collection->map(function ($collection){
            array_push($this->response , $this->toArray($collection));
        });
    }

    protected function toArray($request)
    {
        return $request;
    }
}
