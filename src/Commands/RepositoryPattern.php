<?php

namespace Microoculus\LaravelRepositoryPattern\Commands;

use Illuminate\Console\Command;
use  Microoculus\LaravelRepositoryPattern\Service\RepositoryService;

class RepositoryPattern extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo {name : Class (Singular), e.g User, Place, Car, Post} {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository Pattern with a single command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

       
       
        if( $this->option('path')) {
            $path = $this->option('path');
            RepositoryService::ImplementNow($name, $path);
          }else{
            RepositoryService::ImplementNow($name);
          }

       

        $this->info("Repository pattern implemented for model ". $name);
    }
}
