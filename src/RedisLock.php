<?php

namespace Hybrid\Cache;

class RedisLock extends Lock {
    /**
     * The Redis factory implementation.
     *
     * @var \Hybrid\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * Create a new lock instance.
     *
     * @param \Hybrid\Redis\Connections\Connection $redis
     * @param string                               $name
     * @param int                                  $seconds
     * @param string|null                          $owner
     */
    public function __construct( $redis, $name, $seconds, $owner = null ) {
        parent::__construct( $name, $seconds, $owner );

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire() {
        if ( 0 < $this->seconds ) {
            return $this->redis->set( $this->name, $this->owner, 'EX', $this->seconds, 'NX' ) == true;
        }

        return $this->redis->setnx( $this->name, $this->owner ) === 1;
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release() {
        return (bool) $this->redis->eval( LuaScripts::releaseLock(), 1, $this->name, $this->owner );
    }

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease() {
        $this->redis->del( $this->name );
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    protected function getCurrentOwner() {
        return $this->redis->get( $this->name );
    }

    /**
     * Get the name of the Redis connection being used to manage the lock.
     *
     * @return string
     */
    public function getConnectionName() {
        return $this->redis->getName();
    }
}
