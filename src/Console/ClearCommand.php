<?php

namespace Hybrid\Cache\Console;

use Hybrid\Cache\CacheManager;
use Hybrid\Console\Command;
use Hybrid\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand( name: 'cache:clear' )]
class ClearCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush the application cache';

    /**
     * The cache manager instance.
     *
     * @var \Hybrid\Cache\CacheManager
     */
    protected $cache;

    /**
     * The filesystem instance.
     *
     * @var \Hybrid\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new cache clear command instance.
     *
     * @param \Hybrid\Cache\CacheManager    $cache
     * @param \Hybrid\Filesystem\Filesystem $files
     */
    public function __construct( CacheManager $cache, Filesystem $files ) {
        parent::__construct();

        $this->cache = $cache;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->hybrid_core['events']->dispatch(
            'cache:clearing', [ $this->argument( 'store' ), $this->tags() ]
        );

        $successful = $this->cache()->flush();

        $this->flushFacades();

        if ( ! $successful ) {
            $this->components->error( 'Failed to clear cache. Make sure you have the appropriate permissions.' );

            return self::FAILURE;
        }

        $this->hybrid_core['events']->dispatch(
            'cache:cleared', [ $this->argument( 'store' ), $this->tags() ]
        );

        $this->components->info( 'Application cache cleared successfully.' );

        return self::SUCCESS;
    }

    /**
     * Flush the real-time facades stored in the cache directory.
     *
     * @return void
     */
    public function flushFacades() {
        if ( ! $this->files->exists( $storagePath = storage_path( 'framework/cache' ) ) ) {
            return;
        }

        foreach ( $this->files->files( $storagePath ) as $file ) {
            if ( preg_match( '/facade-.*\.php$/', $file ) ) {
                $this->files->delete( $file );
            }
        }
    }

    /**
     * Get the cache instance for the command.
     *
     * @return \Hybrid\Cache\Repository
     */
    protected function cache() {
        $cache = $this->cache->store( $this->argument( 'store' ) );

        return empty( $this->tags() ) ? $cache : $cache->tags( $this->tags() );
    }

    /**
     * Get the tags passed to the command.
     *
     * @return array
     */
    protected function tags() {
        return array_filter( explode( ',', $this->option( 'tags' ) ?? '' ) );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            [ 'store', InputArgument::OPTIONAL, 'The name of the store you would like to clear' ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            [ 'tags', null, InputOption::VALUE_OPTIONAL, 'The cache tags you would like to clear', null ],
        ];
    }
}
