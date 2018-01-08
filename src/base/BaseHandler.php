<?php

namespace trntv\tactician\base;

use yii\base\BaseObject;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
abstract class BaseHandler extends BaseObject
{
    /**
     * @param BaseCommand $command
     * @return mixed
     */
    abstract public function handle($command);
}
