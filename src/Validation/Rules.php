<?php

namespace Tower\Validation;

use Tower\DB;
use DateTime;

trait Rules
{
    protected function required(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if (! $item || empty($item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if (! $data || empty($data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function string(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && !is_string($item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && !is_string($data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function integer(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && !is_int($item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && !is_int($data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function boolean(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && !is_bool($item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && !is_bool($data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function array(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && !is_array($item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && !is_array($data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function email(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && false === filter_var($item, FILTER_VALIDATE_EMAIL)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && false === filter_var($data , FILTER_VALIDATE_EMAIL)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function regex(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && !preg_match($parameters['value'], $item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && !preg_match($parameters['value'], $data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function notRegex(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && preg_match($parameters['value'], $item)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && preg_match($parameters['value'], $data)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function max(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) > $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                    if (is_int($item) && $item > $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) > $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
            if (is_int($data) && $data > $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
        }
    }

    protected function min(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) < $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                    if (is_int($item) && $item < $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) < $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
            if (is_int($data) && $data < $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
        }
    }

    protected function size(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    if (is_string($item) && $this->strlen($item) != $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                    if (is_int($item) && $item != $parameters['value']){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                }
            }
            return;
        }

        if ($data){
            if (is_string($data) && $this->strlen($data) != $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
            if (is_int($data) && $data != $parameters['value']){
                array_push($this->messages , $parameters['message']);
            }
        }
    }

    protected function date(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && ! $this->validateDate($item , is_null($parameters['value']) ? 'Y-m-d H:i:s' : $parameters['value'])){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && ! $this->validateDate($data , is_null($parameters['value']) ? 'Y-m-d H:i:s' : $parameters['value'])){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function after(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        $value = $this->get($this->data , $parameters['value']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && $value && $item <= $value){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && $value && $data <= $value){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function before(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        $value = $this->get($this->data , $parameters['value']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item && $value && $item >= $value){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data && $value && $data >= $value){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function in(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        $array = explode(',' , $parameters['value']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item  && !in_array($item , $array)){
                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data  && !in_array($data , $array)){
            array_push($this->messages , $parameters['message']);
        }
    }

    protected function nationalCode(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    if(! preg_match('/^[0-9]{10}$/' , $item)){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                        return;
                    }

                    for($i = 0; $i < 10; $i++)
                        if(preg_match('/^'.$i.'{10}$/' , $item)){
                            $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                            array_push($this->messages , $parameters['message']);
                            return;
                        }

                    for($i = 0, $sum = 0; $i < 9; $i++)
                        $sum += ((10-$i) * intval(substr($item , $i ,1)));

                    $ret = $sum % 11;

                    $parity = intval(substr($item, 9,1));

                    if(($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))
                        return;

                    $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                    array_push($this->messages , $parameters['message']);
                }
            }
            return;
        }
        if ($data){
            if(! preg_match('/^[0-9]{10}$/' , $data)){
                array_push($this->messages , $parameters['message']);
                return;
            }

            for($i = 0; $i < 10; $i++)
                if(preg_match('/^'.$i.'{10}$/' , $data)){
                    array_push($this->messages , $parameters['message']);
                    return;
                }

            for($i = 0, $sum = 0; $i < 9; $i++)
                $sum += ((10-$i) * intval(substr($data , $i ,1)));

            $ret = $sum % 11;

            $parity = intval(substr($data, 9,1));

            if(($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))
                return;

            array_push($this->messages , $parameters['message']);
        }
    }

    protected function exists(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    $check = $this->checkDB($item , $parameters['value']);

                    if (!$check){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                }
            }
            return;
        }
        if ($data){
            $check = $this->checkDB($data , $parameters['value']);

            if (!$check){
                array_push($this->messages , $parameters['message']);
            }
        }
    }

    protected function unique(array $parameters): void
    {
        $data = $this->get($this->data , $parameters['ruleName']);
        if (is_array($data)){
            foreach ($data as $index => $item){
                if ($item){
                    $check = $this->checkDB($item , $parameters['value']);

                    if ($check){
                        $parameters['message'] = str_replace('*' , $index , $parameters['message']);
                        array_push($this->messages , $parameters['message']);
                    }
                }
            }
            return;
        }
        if ($data){
            $check = $this->checkDB($data , $parameters['value']);

            if ($check){
                array_push($this->messages , $parameters['message']);
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
}
