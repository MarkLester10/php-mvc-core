<?php

namespace marklester\phpmvc\form;

use marklester\phpmvc\Model;

abstract class BasedField
{
    public Model $model;
    public string $attribute;

    public function __construct(Model $model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderInput(): string; //it may be an input tag, select tag, or textarea

    //magic method - what ever is return from this it will be printed
    public function __toString()
    {
        return sprintf(
            '
         <div class="form-group">
             <label>%s</label>
             %s
             <div class="invalid-feedback">%s</div>
         </div>
         ',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}
