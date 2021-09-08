<?php

namespace Tower;

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

    protected function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    protected function get(array $array, array|string|null $key, ?string $default = null): string|int|bool|array|float|null
    {
        if (is_null($key)) {
            return $array;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $array;
            }

            if ($segment === '*') {
                $result = [];

                foreach ($array as $item) {
                    $result[] = $this->get($item, $key);
                }

                return in_array('*', $key) ? $this->collapse($result) : $result;
            }

            if (array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    protected function validate(): void
    {
        foreach ($this->rules as $index => $rule){
            $rule = explode('|' , $rule);
            $indexExplode = explode('.' , $index);
            count($indexExplode) > 1 ? $indexLocal = last($indexExplode) : $indexLocal = $index;

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