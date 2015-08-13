<?php

namespace atuin\engine\helpers;

use atuin\engine\interfaces\EventsInterface;
use atuin\engine\interfaces\ParamsEvent;
use Yii;
use yii\base\Event;


/**
 * Class Hooks
 * 
 * 
 * Hooks are global events that can be set alongside all Atuin system by any method.
 * 
 * Unlike normal events and Filters, Hooks will return all data emitted from the callbacks of the event.
 * 
 * @package atuin\engine\libraries
 */
class  Hooks implements EventsInterface
{
    /** @var  ResponseEvent */
    private static $response_event;

    /**
     * @return ResponseEvent
     */
    private static function getResponseEvent()
    {
        if (self::$response_event === NULL)
        {
            self::$response_event = new ResponseEvent();
        }

        return self::$response_event;
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
        self::getResponseEvent()->on($name, $handler, $data, $append);
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
        self::getResponseEvent()->off($name, $handler);
    }


    /**
     * Triggers an event
     * 
     * Warning: this trigger is different from the usual ones. In this method you can add
     * extra data manually that will be sent to every listener in the "params" parameter 
     * from the event.
     *
     * @param string $name
     * @param mixed $data
     * @return array|mixed
     */
    public static function triggerAction($name,  $data = NULL)
    {
        $event = new ParamsEvent();
        
        if (!is_null($data))
        {
            $event->params = $data;
        }
        
        return self::getResponseEvent()->trigger($name, $event);
    }

    
    /**
     * Triggers an event and returns it as a HTML text. 
     * Used when we want to launch hooks that return plain html text to avoid
     * having an array as return data.
     * 
     * Warning: this trigger is different from the usual ones. In this method you can add
     * extra data manually that will be sent to every listener in the "params" parameter 
     * from the event.
     *
     * @param string $name
     * @param mixed $data
     * @return array|mixed
     */
    public static function triggerHtmlAction($name,  $data = NULL)
    {
        $return_data = self::triggerAction($name, $data);
        
        return implode('', $return_data);
    }
    
}

class ResponseEvent
{
    private $_events = [];


    /**
     * Attaches an event handler to an event.
     *
     * The event handler must be a valid PHP callback. The following are
     * some examples:
     *
     * ~~~
     * function ($event) { ... }         // anonymous function
     * [$object, 'handleClick']          // $object->handleClick()
     * ['Page', 'handleClick']           // Page::handleClick()
     * 'handleClick'                     // global function handleClick()
     * ~~~
     *
     * The event handler must be defined with the following signature,
     *
     * ~~~
     * function ($event)
     * ~~~
     *
     * where `$event` is an [[Event]] object which includes parameters associated with the event.
     *
     * @param string $name the event name
     * @param callable $handler the event handler
     * @param mixed $data the data to be passed to the event handler when the event is triggered.
     * When the event handler is invoked, this data can be accessed via [[Event::data]].
     * @param boolean $append whether to append new event handler to the end of the existing
     * handler list. If false, the new handler will be inserted at the beginning of the existing
     * handler list.
     * @see off()
     */
    public function on($name, $handler, $data = null, $append = true)
    {
        if ($append || empty($this->_events[$name]))
        {
            $this->_events[$name][] = [$handler, $data];
        } else
        {
            array_unshift($this->_events[$name], [$handler, $data]);
        }
    }

    /**
     * Detaches an existing event handler from this component.
     * This method is the opposite of [[on()]].
     * @param string $name event name
     * @param callable $handler the event handler to be removed.
     * If it is null, all handlers attached to the named event will be removed.
     * @return boolean if a handler is found and detached
     * @see on()
     */
    public function off($name, $handler = null)
    {
        if (empty($this->_events[$name]))
        {
            return false;
        }
        if ($handler === null)
        {
            unset($this->_events[$name]);
            return true;
        } else
        {
            $removed = false;
            foreach ($this->_events[$name] as $i => $event)
            {
                if ($event[0] === $handler)
                {
                    unset($this->_events[$name][$i]);
                    $removed = true;
                }
            }
            if ($removed)
            {
                $this->_events[$name] = array_values($this->_events[$name]);
            }
            return $removed;
        }
    }

    /**
     * Triggers an event.
     * This method represents the happening of an event. It invokes
     * all attached handlers for the event including class-level handlers.
     * @param string $name the event name
     * @param Event $event the event parameter. If not set, a default [[Event]] object will be created.
     * @return array|mixed
     */
    public function trigger($name, Event $event = null)
    {
        $return_data = [];

        if (!empty($this->_events[$name]))
        {
            if ($event === null)
            {
                $event = new Event;
            }
            if ($event->sender === null)
            {
                $event->sender = $this;
            }
            $event->handled = false;
            $event->name = $name;
            foreach ($this->_events[$name] as $handler)
            {
                $event->data = $handler[1];
                $return_data[] = call_user_func($handler[0], $event);
                // stop further handling if the event is handled
                if ($event->handled)
                {
                    break;
                }
            }
        }
        // invoke class-level attached handlers
        // but won't return anything in this case...
        Event::trigger($this, $name, $event);

        return $return_data;
    }
}