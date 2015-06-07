<?php

namespace trntv\tactician\base;

use yii\base\Object;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
abstract class BaseHandler extends Object
{
    abstract public function handle(BaseCommand $command);
}
