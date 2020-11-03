<?php


namespace marklester\phpmvc\form;

use marklester\phpmvc\Model;

class InputField extends BasedField
{
    public string $type;


    public function __construct(Model $model, $attribute, $type)
    {
        parent::__construct($model, $attribute);
        $this->type = $type;
    }

    public function renderInput(): string
    {
        return sprintf(
            '<input type="%s" name="%s" value="%s" class="form-control %s">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
        );
    }
}
