<?php

namespace Tower\Validation;

use Tower\DB;
use DateTime;

trait Rules
{
    protected function required(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if (is_string($item) && mb_strlen(trim($item), 'UTF-8') === 0) {
                    $this->saveError($parameters , $index);
                }
                if (is_array($item) && count($item) === 0) {
                    $this->saveError($parameters , $index);
                }
                if (is_null($item)) {
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if (is_string($data) && mb_strlen(trim($data), 'UTF-8') === 0) {
            $this->saveError($parameters);
        }
        if (is_array($data) && count($data) === 0) {
            $this->saveError($parameters);
        }
        if (is_null($data)) {
            $this->saveError($parameters);
        }
    }

    protected function string(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !is_string($item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !is_string($data)){
            $this->saveError($parameters);
        }
    }

    protected function integer(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !is_int($item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !is_int($data)){
            $this->saveError($parameters);
        }
    }

    protected function boolean(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !is_bool($item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !is_bool($data)){
            $this->saveError($parameters);
        }
    }

    protected function array(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !is_array($item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !is_array($data)){
            $this->saveError($parameters);
        }
    }

    protected function email(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && false === filter_var($item, FILTER_VALIDATE_EMAIL)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && false === filter_var($data , FILTER_VALIDATE_EMAIL)){
            $this->saveError($parameters);
        }
    }

    protected function regex(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !preg_match($parameters['value'], $item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !preg_match($parameters['value'], $data)){
            $this->saveError($parameters);
        }
    }

    protected function notRegex(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && preg_match($parameters['value'], $item)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && preg_match($parameters['value'], $data)){
            $this->saveError($parameters);
        }
    }

    protected function max(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) > $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_int($item) && $item > $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_array($item) && count($item) > $parameters['value']){
                        $this->saveError($parameters , $index);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) > $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_int($data) && $data > $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_array($data) && count($data) > $parameters['value']){
                $this->saveError($parameters);
            }
        }
    }

    protected function min(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) < $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_int($item) && $item < $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_array($item) && count($item) < $parameters['value']){
                        $this->saveError($parameters , $index);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) < $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_int($data) && $data < $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_array($data) && count($data) < $parameters['value']){
                $this->saveError($parameters);
            }
        }
    }

    protected function size(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) != $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_int($item) && $item != $parameters['value']){
                        $this->saveError($parameters , $index);
                    } elseif (is_array($item) && count($item) != $parameters['value']){
                        $this->saveError($parameters , $index);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) != $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_int($data) && $data != $parameters['value']){
                $this->saveError($parameters);
            } elseif (is_array($data) && count($data) != $parameters['value']){
                $this->saveError($parameters);
            }
        }
    }

    protected function date(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && !$this->validateDate($item , is_null($parameters['value']) ? 'Y-m-d H:i:s' : $parameters['value'])){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && !$this->validateDate($data , is_null($parameters['value']) ? 'Y-m-d H:i:s' : $parameters['value'])){
            $this->saveError($parameters);
        }
    }

    protected function after(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        $value = $this->getData($this->data , $parameters['value']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && $value && $item <= $value){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && $value && $data <= $value){
            $this->saveError($parameters);
        }
    }

    protected function before(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        $value = $this->getData($this->data , $parameters['value']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item && $value && $item >= $value){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data && $value && $data >= $value){
            $this->saveError($parameters);
        }
    }

    protected function in(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        $array = explode(',' , $parameters['value']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item  && !in_array($item , $array , true)){
                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data  && !in_array($data , $array , true)){
            $this->saveError($parameters);
        }
    }

    protected function nationalCode(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    if(!preg_match('/^[0-9]{10}$/' , $item)){
                        $this->saveError($parameters , $index);
                        return;
                    }

                    for($i = 0; $i < 10; $i++)
                        if(preg_match('/^'.$i.'{10}$/' , $item)){
                            $this->saveError($parameters , $index);
                            return;
                        }

                    for($i = 0, $sum = 0; $i < 9; $i++)
                        $sum += ((10-$i) * intval(substr($item , $i ,1)));

                    $ret = $sum % 11;

                    $parity = intval(substr($item, 9,1));

                    if(($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))
                        return;

                    $this->saveError($parameters , $index);
                }
            }
            return;
        }
        if ($data){
            if(!preg_match('/^[0-9]{10}$/' , $data)){
                $this->saveError($parameters);
                return;
            }

            for($i = 0; $i < 10; $i++)
                if(preg_match('/^'.$i.'{10}$/' , $data)){
                    $this->saveError($parameters);
                    return;
                }

            for($i = 0, $sum = 0; $i < 9; $i++)
                $sum += ((10-$i) * intval(substr($data , $i ,1)));

            $ret = $sum % 11;

            $parity = intval(substr($data, 9,1));

            if(($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))
                return;

            $this->saveError($parameters);
        }
    }

    protected function exists(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    $check = $this->checkDB($item , $parameters['value']);

                    if (!$check){
                        $this->saveError($parameters , $index);
                    }
                }
            }
            return;
        }
        if ($data){
            $check = $this->checkDB($data , $parameters['value']);

            if (!$check){
                $this->saveError($parameters);
            }
        }
    }

    protected function unique(array $parameters): void
    {
        $data = $this->getData($this->data , $parameters['rule']);
        if (str_contains($parameters['rule'] , '*') && $data) {
            foreach ($data as $index => $item){
                if ($item){
                    $check = $this->checkDB($item , $parameters['value']);

                    if ($check){
                        $this->saveError($parameters , $index);
                    }
                }
            }
            return;
        }
        if ($data){
            $check = $this->checkDB($data , $parameters['value']);

            if ($check){
                $this->saveError($parameters);
            }
        }
    }

    protected function checkDB(string $item , string $value): bool
    {
        if (str_contains($value , ','))
            $table = explode(',' , $value);

        if (isset($table))
            $check = DB::table($table[0])->where($table[1] , $item)->exists();
        else
            $check = DB::table($value)->where('id' , $item)->exists();

        return $check;
    }

    protected function validateDate($date, $format = 'Y-m-d H:i:s'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    protected function strlen($value): bool|int
    {
        if (!function_exists('mb_detect_encoding'))
            return strlen($value);

        if (false === $encoding = mb_detect_encoding($value))
            return strlen($value);

        return mb_strlen($value, $encoding);
    }

    protected function saveError(array $parameters , ?int $index = null): void
    {
        if (!is_null($index)){
            $parameters['message'] = str_replace('*' , $index , $parameters['message']);
            $parameters['rule'] = str_replace('*' , $index , $parameters['rule']);
            $this->errors[] = [
                'message' => $parameters['message'] ,
                'field' => [
                    'locale' => $parameters['localeField'] ,
                    'nonLocale' => $parameters['rule'] ,
                ]
            ];
            return;
        }

        $this->errors[] = [
            'message' => $parameters['message'] ,
            'field' => [
                'locale' => $parameters['localeField'] ,
                'nonLocale' => $parameters['rule'] ,
            ]
        ];
    }
}
