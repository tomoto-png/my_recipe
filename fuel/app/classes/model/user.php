<?php

class Model_User
{
    protected static $table = 'users';

    public static function exists_by_email($email)
    {
        $count = DB::select()
            ->from(static::$table)
            ->where('email', '=', $email)
            ->execute()
            ->count();

        return $count > 0;
    }
}
