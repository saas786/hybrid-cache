<?php

namespace Hybrid\Cache;

use InvalidArgumentException;
use function Hybrid\app;

if ( ! function_exists( __NAMESPACE__ . '\\cache' ) ) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param string|array<string, mixed>|null $key key|data
     * @param mixed                            $default default|expiration|null
     *
     * @return ($key is null ? \Hybrid\Cache\CacheManager : ($key is string ? mixed : bool))
     *
     * @throws \InvalidArgumentException
     */
    function cache( $key = null, $default = null ) {
        if ( is_null( $key ) ) {
            return app( 'cache' );
        }

        if ( is_string( $key ) ) {
            return app( 'cache' )->get( $key, $default );
        }

        if ( ! is_array( $key ) ) {
            throw new InvalidArgumentException(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        return app( 'cache' )->put( key( $key ), array_first( $key ), ttl: $default );
    }
}
