<?php

namespace AgentPlus\Exception\Diary;

class DiaryNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via diary id
     *
     * @param string $id
     *
     * @return DiaryNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found diary with id "%s".',
            $id
        );

        return new static($message);
    }
}
