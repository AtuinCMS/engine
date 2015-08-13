<?php
namespace atuin\engine\interfaces;


use yii\base\Event;

interface EventsInterface
{
    /**
     * Adds new event
     *
     * @param mixed $name
     * @param mixed $handler
     * @param null $data
     * @param bool $append
     */
    public static function registerAction($name, $handler, $data = null, $append = true);

    /**
     * Removes an event
     *
     * Warning: If $event is NULL this method will delete all attached events with that name.
     *
     * @param mixed $name
     * @param null $handler
     */
    public static function unRegisterAction($name, $handler = null);


    /**
     * Triggers an event
     *
     * Since the special events that are using global classes are already thrown by
     * the own classes, we will only cover the self::getDerp() trigers
     *
     *
     * @param string $name
     * @param mixed $data
     * @return array|mixed
     */
    public static function triggerAction($name, $data = NULL);
}

class ParamsEvent extends Event
{
    public $params = [];
}