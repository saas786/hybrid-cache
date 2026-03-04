<?php

namespace Hybrid\Cache\Contracts;

interface LockProvider {
    /**
     * Get a lock instance.
     *
     * @param string      $name
     * @param int         $seconds
     * @param string|null $owner
     *
     * @return \Hybrid\Cache\Contracts\Lock
     */
    public function lock( $name, $seconds = 0, $owner = null );

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param string $name
     * @param string $owner
     *
     * @return \Hybrid\Cache\Contracts\Lock
     */
    public function restoreLock( $name, $owner );
}
