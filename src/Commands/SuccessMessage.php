<?php

namespace ExceptionLive\ExceptionLiveLaravel\Commands;

class SuccessMessage
{
    /**
     * Success message with links to notices.
     *
     * @param  string  $noticeId
     * @return string
     */
    public static function make(string $noticeId): string
    {
        $message = <<<'EX'

⚡ --- ExceptionLive is installed! -----------------------------------------------
Good news: You're one deploy away from seeing all of your exceptions in
ExceptionLive. For now, we've generated a test exception for you:

    https://exception.live/report/%s

If you ever need help:

    - Check out the documentation: https://github.com/exception-live/docs
    - Email: support@exception.live
⚡ --- End --------------------------------------------------------------------

EX;

        return sprintf($message, $noticeId);
    }
}
