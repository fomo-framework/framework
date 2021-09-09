<?php

namespace Tower;

use ArrayAccess;
use Illuminate\Support\Enumerable;
use Tower\Validation\Rules;

class Validation
{
    use Rules;

    protected array $rules = [];
    protected array $errorDefaultMessage = [];
    protected array $errors = [];
    protected array $data;

    public function __construct(array $data , array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errorDefaultMessage = Loader::get('errors');
        $this->validate();
    }

    protected function exists(Enumerable|ArrayAccess|array $array, string $key): bool
    {
        if ($array instanceof Enumerable) {
            return $array->has($key);
        }

        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    protected function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    protected function get(string|int|bool|array|float $target, array|string|null $key, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                $result = [];

                foreach ($target as $item) {
                    $result[] = $this->get($item, $key);
                }

                return in_array('*', $key) ? $this->collapse($result) : $result;
            }

            if (array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } else {
                return value($default);
            }
        }

        return $target;
    }

    protected function validate(): void
    {
        foreach ($this->rules as $index => $rule){
            $rule = explode('|' , $rule);
            $indexExplode = explode('.' , $index);
            count($indexExplode) > 1 ? $indexLocal = last($indexExplode) : $indexLocal = $index;

            if ($indexLocal == '*'){
                $lastKey = array_key_last($indexExplode);
                $indexLocal = $indexExplode[$lastKey - 1];
            }

            foreach ($rule as $item) {
                $itemExplode = explode(':' , $item);
                $item = $itemExplode[0];
                $value = $itemExplode[1] ?? null;

                $message = str_replace([":attribute" , ":value"] , [$this->errorDefaultMessage['attribute'][$indexLocal] ?? $indexLocal , $value] , $this->errorDefaultMessage['message'][$item]);

                $localeField = $this->errorDefaultMessage['attribute'][$indexLocal] ?? $indexLocal;

                $this->$item([
                    'rule' => $index ,
                    'value' => $value ,
                    'message' => $message ,
                    'localeField' => $localeField
                ]);
            }
        }
    }

    public function hasError(): bool
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function hasMessage(): bool
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function getMessages(): array
    {
        return array_column($this->errors , 'message');
    }

    public function firstMessage(): string
    {
        return $this->errors[0]['message'];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError(): array
    {
        return $this->errors[0];
    }
}