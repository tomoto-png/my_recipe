<?php

class Model_User
{
    protected static $_table_name = 'users';

    public static function exists_by_email($email): bool
    {
        $table = static::$_table_name;
        return (bool) \DB::select('id')
            ->from($table)
            ->where('email', '=', $email)
            ->limit(1)
            ->execute()
            ->current();
    }
}
