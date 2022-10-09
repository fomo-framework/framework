<?php

namespace Fomo\Validation;

use Fomo\Language\Language;

class Validation
{
    use RulesTrait;

    protected array $rules = [];
    protected array $errorDefaultMessage = [];
    protected array $errors = [];
    protected array $data;

    public function __construct(array $data , array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errorDefaultMessage = Language::getInstance()->getErrorMessages();
        $this->validate();
    }

    protected function existsData(array $array, string|int $key): bool
    {
        return array_key_exists($key, $array);
    }

    protected function collapseData(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    protected function getData(mixed $target, string|array|int|null $key, mixed $default = null): mixed
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
                if (! is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = $this->getData($item, $key);
                }

                return in_array('*', $key) ? $this->collapseData($result) : $result;
            }

            if (is_array($target) && $this->existsData($target, $segment)) {
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