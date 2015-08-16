<?php

namespace trntv\tactician;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use trntv\tactician\base\BaseCommand;
use Yii;
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
        $commandToHandlerMap = array_map(function ($config) {
            return Yii::createObject($config);
        }, $this->commandToHandlerMap);
        /** @var \League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor $extractor */
        $extractor = \Yii::createObject($this->commandNameExtractor);
        $locator = new InMemoryLocator($commandToHandlerMap);
        /** @var \League\Tactician\Handler\MethodNameInflector\MethodNameInflector $inflector */
        $inflector = \Yii::createObject($this->methodNameInflector);

        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            $extractor,
            $locator,
            $inflector
        );

        $middlewares = array_map(function ($config) {
            return \Yii::createObject($config);
        }, $this->middlewares ?: []);

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
     * @param BaseCommand $command
     * @return mixed
     */
    public function handle(BaseCommand $command)
    {
        return $this->handleInternal($command);
    }

    /**
     * @param BaseCommand[] $commands
     * @return array
     */
    public function handleMultiply($commands)
    {
        $result = [];
        foreach($commands as $k => $command) {
            $result[$k] = $this->handleInternal($command);
        }

        return $result;
    }

    /**
     * @param BaseCommand $command
     * @return mixed
     */
    protected function handleInternal($command)
    {
        $this->trigger(self::EVENT_BEFORE_HANDLE, new Event([
            'data' => $command
        ]));

        if (method_exists($command, 'beforeHandle')) {
            $command->beforeHandle();
        }

        $result = $this->getCommandBus()->handle($command);

        if (method_exists($command, 'afterHandle')) {
            $command->afterHandle($result);
        }

        $this->trigger(self::EVENT_AFTER_HANDLE, new Event([
            'data' => $result
        ]));

        return $result;
    }

}
