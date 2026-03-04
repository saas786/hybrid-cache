<?php

namespace Hybrid\Cache;

use Hybrid\Cache\Contracts\Store;

abstract class TaggableStore implements Store {
    /**
     * Begin executing a new tags operation.
     *
     * @param mixed $names
     *
     * @return \Hybrid\Cache\TaggedCache
     */
    public function tags( $names ) {
        return new TaggedCache( $this, new TagSet( $this, is_array( $names ) ? $names : func_get_args() ) );
    }
}
