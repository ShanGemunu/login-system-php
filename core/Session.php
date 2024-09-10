<?php

namespace app\core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /** 
     *    set flash message as session variable, use key to set message, 
     *    use remove for unset key after next request  
     *    @param string $key
     *    @param string $message
     *    @return void
     */
    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    /** 
     *    get flash message 
     *    @param string $key
     *    @return string|bool
     */
    public function getFlash(string $key): string|bool
    {

        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    /** 
     *    set session variable 
     *    @param string $key
     *    @param string $value
     *    @return void
     */
    public function set($key, $value) : void
    {
        $_SESSION[$key] = $value;
    }

    /** 
     *    get session variable 
     *    @param string $key
     *    @return 
     */
    public function get($key)
    {

        return $_SESSION[$key] ?? false;
    }

    /** 
     *    unset session variable 
     *    @param string $key
     *    @return void
     */
    public function remove($key) : void
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    /** 
     *    unset flash variables whose remove attribute 'false' 
     *    @return void
     */
    private function removeFlashMessages() : void
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}