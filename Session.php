<?php

namespace app\core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {   
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage)
        { // Mark to be removed
             $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
    public function setFlashMessage($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlashMessage($key) : string|bool
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public function get($key) : string|bool
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key) : void
    {
        unset($_SESSION[$key]);
    } 

    public function __destruct()
    { //Iterate over marked to be removed flashMessages
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage)
        {
            if($flashMessage["remove"])
            {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}