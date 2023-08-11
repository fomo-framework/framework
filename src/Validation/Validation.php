<?php

namespace Fomo\Validation;

use Fomo\Facades\Contracts\InstanceInterface;
use Fomo\Facades\Language;

class Validation implements InstanceInterface
{
    use RulesTrait;

    protected array $errorDefaultMessage = [];
    protected array $errors = [];
    protected array $data;

    public function __construct()
    {
        $this->errorDefaultMessage = app()->make('language')->getErrorMessages();
    }

    public function validate(array $data , array $rules): self
    {
        $this->data = $data;

        foreach ($rules as $index => $rule){
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

        return $this;
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

    public function getInstance(): self
    {
        return $this;
    }

    protected function existsData(array $array, string|int $key): bool
    {
        return array_key_exists($key, $array);
    }

    protected function collapseData(array $array): array
    {
        $results = [];

        foreach ($array as $values) {

            if (is_null($values)) {
                continue;
            }
            
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
}