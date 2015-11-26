<?php

namespace AgentPlus\Exception\Diary;

class DiaryTypeNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via id
     *
     * @param string $id
     *
     * @return DiaryTypeNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found diary type with id "%s".',
            $id
        );

        return new static($message);
    }
}
