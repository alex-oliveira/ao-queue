<?php

if (!function_exists('AoQueue')) {
    /**
     * @return \AoQueue\Tools
     */
    function AoQueue()
    {
        return app('AoQueue');
    }
}