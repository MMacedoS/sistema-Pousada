<?php

namespace App\Utils;

class Validator {
    protected $data;
    protected $errors = [];

    public function __construct($data) {
        $this->data = $data;
    }

    public function validate(array $rules) {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    protected function applyRule($field, $rule) {
        if (strpos($rule, ':') !== false) {
            list($ruleName, $parameter) = explode(':', $rule);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        if (!method_exists($this, $ruleName)) {
            throw new \Exception("Regra de validação $ruleName não existe.");
        }

        $this->{$ruleName}($field, $parameter);
    }

    protected function required($field) {
        if (empty($this->data[$field])) {
            $this->errors[$field][] = 'Este campo é obrigatório.';
        }
    }

    protected function min($field, $min) {
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = "Este campo deve ter no mínimo $min caracteres.";
        }
    }

    protected function max($field, $max) {
        if (strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = "Este campo deve ter no máximo $max caracteres.";
        }
    }

    protected function email($field) {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Este campo deve ser um endereço de email válido.';
        }
    }

    public function getErrors() {
        return $this->errors;
    }
}
