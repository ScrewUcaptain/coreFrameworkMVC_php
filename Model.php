<?php

namespace suc\phpmvc;

abstract class Model
{
    public const RULE_REQUIRED = 'required';  //  rule 'required'(user have to a specified this field)
    public const RULE_EMAIL = 'email'; //  rule 'email valid'(user input has to be an email)
    public const RULE_MIN = 'min'; //  rule 'minimum length'(user input's length has to be bigger than this value)
    public const RULE_MAX = 'max';//  rule 'maximum length'(user input's length has to be smaller than this value)
    public const RULE_MATCH = 'match';//  rule 'match'(user input has to match a specified field)
    public const RULE_UNIQUE = 'unique';//  rule 'match'(user input has to match a specified field)
    
    public array $errors = [];

    public function loadData($data)
    {
        foreach($data as $key => $val)
        {
            if(property_exists($this, $key))
            {
                $this->{$key} = $val;
            }
        }
    }

    abstract public function rules() : array; 
    // define this method to 
//                      return [
//              EXEMPLE     'attribute' => [self::RULE_XXX],
        //      OR          'attribute' => [self::RULE_XXX, 'ruleName' => $ruleValue]
        //                  ]
        //
    public function labels() : array
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function validate()
    {
        foreach($this->rules() as $attribute => $rules)
        {
            $value = $this->{$attribute};
            foreach($rules as $rule)
            {
                $ruleName = $rule;
                if(!is_string($ruleName)) // the rule is an array in this case 
                {
                    $ruleName = $rule[0];
                }
                if($ruleName === self::RULE_REQUIRED && !$value)
                {
                     $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) 
                {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min'])
                {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max'])
                {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']})
                {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE)
                {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if($record)
                    {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute,string $rule, $params = [])
    {
        $message = $this->errorMessage()[$rule] ?? '';
        foreach($params as $key => $value)
        {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function errorMessage()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email adress',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'Record for this {field} already exists',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    //for displaying what doesn't pass validation, if multiple rules are not passed the user only see the first one.
    {
        return $this->errors[$attribute][0] ?? false; 
    }
}