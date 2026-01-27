<?php

class Validation_User
{
    //ユニークバリデーションの作成
    public static function _validation_unique_email($value)
    {
        \Log::debug('unique_email called: ' . $value);
        if ($value === null || $value === '') {
            return true;
        }

        return ! Model_User::exists_by_email($value);
    }
}
