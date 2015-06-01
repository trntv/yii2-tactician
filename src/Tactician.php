<?php

namespace trntv\tactician;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use yii\base\Component;
use yii\base\Event;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Tactician extends Component
{
    const EVENT_BEFORE_HANDLE = 'beforeHandle';
    const EVENT_AFTER_HANDLE = 'afterHandle';

    public $commandNameExtractor;
    public $methodNameInflector;
    public $commandToHandlerMap;
    public $middlewares;

    private $commandBus;

    public function createCommandBus()
    {
        /** @var \League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor $extractor */
        $extractor = \Yii::createObject($this->commandNameExtractor);
        $locator = new InMemoryLocator($this->commandToHandlerMap);
        /** @var \League\Tactician\Handler\MethodNameInflector\MethodNameInflector $inflector */
        $inflector = \Yii::createObject($this->methodNameInflector);

        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            $extractor,
            $locator,
            $inflector
        );

        $middlewares = array_map(function ($config) {
            return \Yii::createObject($config);
        }, $this->middlewares);

        $commandBus = new CommandBus(array_merge([$commandHandlerMiddleware], $middlewares));

        return $commandBus;
    }


    /**
     * @return CommandBus
     */
    public function getCommandBus()
    {
        if ($this->commandBus === null) {
            $this->commandBus = $this->createCommandBus();
        }
        return $this->commandBus;
    }

    /**
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $this->trigger(self::EVENT_BEFORE_HANDLE, new Event([
            'data' => $command
        ]));

        $result = $this->getCommandBus()->handle($command);

        $this->trigger(self::EVENT_AFTER_HANDLE, new Event([
            'data' => $result
        ]));

        return $result;
    }

}