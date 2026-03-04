<?php

namespace Hybrid\Cache\Contracts;

interface Factory {
    /**
     * Get a cache store instance by name.
     *
     * @param string|null $name
     *
     * @return \Hybrid\Cache\Contracts\Repository
     */
    public function store( $name = null );
}
