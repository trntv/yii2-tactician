<?php

namespace trntv\tactician\base;

use yii\base\Object;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
abstract class BaseHandler extends Object
{
    /**
     * @param BaseCommand $command
     * @return mixed
     */
    abstract public function handle($command);
}
