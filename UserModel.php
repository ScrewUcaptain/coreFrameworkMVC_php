<?php 

namespace suc\phpmvc;

use suc\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName() : string;
}