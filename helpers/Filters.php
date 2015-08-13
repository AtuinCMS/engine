<?php

namespace atuin\engine\helpers;


use atuin\engine\interfaces\EventsInterface;
use atuin\engine\interfaces\ParamsEvent;
use yii\base\Event;


/**
 * Class Filters
 *
 * Filters are global events that can be set alongside all Atuin system by any method.
 *
 * Unlike Hooks, Filters don't return any kind of data, they only launch callbacks.
 *
 *
 * @package atuin\engine\libraries
 */
class Filters implements EventsInterface
{
    /**
     * @return \yii\console\Application|\yii\web\Application
     */
    private static function getResponseEvent()
    {
        return \Yii::$app;
    }

    /**
     * Adds new event
     *
     * @param mixed $name
     * @param mixed $handler
     * @param null $data
     * @param bool $append
     */
    public static function registerAction($name, $handler, $data = null, $append = true)
    {
        // if the $name is an array we are using special feature that
        // lets system to add new events to third part packages
        // but this is very limited, only will work with the common events
        // and if there are specially added events into the package
        if (is_array($name))
        {
            $class = $name[0];
            $name = $name[1];
            Event::on($class, $name, $handler, $data, $append);
        } else
        {
            self::getResponseEvent()->on($name, $handler, $data, $append);
        }
    }

    /**
     * Removes an event
     *
     * Warning: If $event is NULL this method will delete all attached events with that name.
     *
     * @param mixed $name
     * @param null $handler
     */
    public static function unRegisterAction($name, $handler = null)
    {
        // if the $name is an array we are using special feature that
        // lets system to add new events to third part packages
        // but this is very limited, only will work with the common events
        // and if there are specially added events into the package
        if (is_array($name))
        {
            $class = $name[0];
            $name = $name[1];
            Event::off($class, $name, $handler);
        } else
        {
            self::getResponseEvent()->off($name, $handler);
        }
    }


    /**
     * Triggers an event
     *
     * Since the special events that are using global classes are already thrown by
     * the own classes, we will only cover the self::getResponseEvent() triggers
     * 
     * Warning: this trigger is different from the usual ones. In this method you can add
     * extra data manually that will be sent to every listener in the "params" parameter 
     * from the event.
     *
     * @param string $name
     * @param mixed $data
     * @return array|mixed
     */
    public static function triggerAction($name, $data = NULL)
    {
        $event = new ParamsEvent();
        
        if (!is_null($data))
        {
            $event->params = $data;
        }
       
        self::getResponseEvent()->trigger($name, $event);
    }

}