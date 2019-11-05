<?php

namespace Edwin404\Base\Support;

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\MutexFabric;

class LockHelper
{
    static $instance = null;

    /**
     * @return MutexFabric
     */
    private static function instance()
    {
        if (null === self::$instance) {
            $mysqlLock = new MySqlLock(
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_HOST')
            );
            $mutexFabric = new MutexFabric('mysql', $mysqlLock);
            self::$instance = $mutexFabric;
        }
        return self::$instance;
    }

    public static function startAction($action)
    {
        if (self::instance()->get($action)->acquireLock(0)) {
            return true;
        }
        return false;
    }

    public static function endAction($action)
    {
        self::instance()->get($action)->releaseLock();
    }

    public static function startUserAction($userId, $action)
    {
        return self::startAction('user-' . $userId . '-' . $action);
    }

    public static function endUserAction($userId, $action)
    {
        self::endAction('user-' . $userId . '-' . $action);
    }

    public static function startMemberUserAction($userId, $action)
    {
        return self::startAction('memberUser-' . $userId . '-' . $action);
    }

    public static function endMemberUserAction($userId, $action)
    {
        self::endAction('memberUser-' . $userId . '-' . $action);
    }

}