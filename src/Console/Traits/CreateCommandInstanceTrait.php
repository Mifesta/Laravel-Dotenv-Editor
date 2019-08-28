<?php

namespace Mifesta\DotenvEditor\Console\Traits;

use Mifsta\DotenvEditor\DotenvEditor;

/**
 * Trait CreateCommandInstanceTrait
 *
 * @package Mifesta\DotenvEditor\Console\Traits
 */
trait CreateCommandInstanceTrait
{
    /**
     * The .env file editor instance
     *
     * @var \Mifesta\DotenvEditor\DotenvEditor
     */
    protected $editor;

    /**
     * Create a new command instance.
     */
    public function __construct(DotenvEditor $editor)
    {
        parent::__construct();

        $this->editor = $editor;
    }

    /**
     * Execute the console command.
     * This is alias of the method fire()
     *
     * @return int
     */
    public function handle()
    {
        return $this->fire();
    }
}
