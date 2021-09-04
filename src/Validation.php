<?php

namespace Tower;

use Tower\Validation\Rules;

class Validation
{
    use Rules;

    protected array $rules = [];
    protected array $errorDefaultMessage = [];
    protected array $messages = [];
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
        foreach ($this->rules as $ruleName => $rule){
            $rule = explode('|' , $rule);

            foreach ($rule as $item)
            {
                $itemExplode = explode(':' , $item);
                $item = $itemExplode[0];
                $itemValue = $itemExplode[1] ?? null;

                $message = $this->errorDefaultMessage['message'][$item] ? str_replace(":attribute" , $this->errorDefaultMessage['attribute'][$ruleName] ?? $ruleName , $this->errorDefaultMessage['message'][$item]) : null;

                $message = str_replace(":value" , $itemValue , $message);

                $parameters = [
                    'ruleName' => $ruleName ,
                    'value' => $itemValue ,
                    'message' => $message
                ];
                $this->$item($parameters);
            }
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasMessage(): bool
    {
        if (empty($this->messages))
            return false;

        return true;
    }

    public function firstMessage(): string
    {
        return $this->messages[0];
    }
}