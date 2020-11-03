<?php

namespace marklester\phpmvc;

use marklester\phpmvc\db\DBModel;

abstract class UserModel extends DBModel
{
    abstract public function getDisplayName(): string;
}
