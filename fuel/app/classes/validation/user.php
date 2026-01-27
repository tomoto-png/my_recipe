<?php

class Validation_User
{
    //ユニークバリデーションの作成
    public static function _validation_unique_email($value)
    {
        if ($value === null || $value === '') {
            return true;
        }

        return ! Model_User::exists_by_email($value);
    }
}
