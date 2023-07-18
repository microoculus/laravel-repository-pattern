<?php

namespace Microoculus\LaravelRepositoryPattern\Service;
use Illuminate\Support\Str;



class RepositoryService {

    private static $namespace = null;
    private static $namespacePath = null;
   
    protected static function getStubs($type)
    {

        return  is_null(self::$namespace) ? file_get_contents(resource_path("vendor/laravel-repository-pattern/stubs/{$type}.stub")) : file_get_contents(resource_path("vendor/laravel-repository-pattern/stubs/{$type}Namespace.stub"))  ;
       
    }

    public static function ImplementNow($name, $path=null)
    {

        $option_path = $path;
        if (!file_exists($path=app_path('/Repositories')))
            mkdir($path, 0777, true);

        if (!file_exists($path=app_path('/Interfaces')))
            mkdir($path, 0777, true);

        if (!file_exists($path=app_path('/Services')))
            mkdir($path, 0777, true);

        if(!is_null($option_path)){

           $namespace = self::$namespace  =  trim(implode('\\', array_slice(explode('\\',  $option_path), 0)), '\\');
          
            $path_array = explode('\\',  $option_path);
            $namespacePath = self::$namespacePath = implode(DIRECTORY_SEPARATOR , $path_array);

           
            if (!file_exists($path=app_path("/Services/{$namespacePath}")))
                mkdir($path, 0777, true);
   
            if (!file_exists($path=app_path("/Repositories/{$namespacePath}")))
            mkdir($path, 0777, true);
            if (!file_exists($path=app_path("/Interfaces/{$namespacePath}")))
            mkdir($path, 0777, true);
        }

      
        // self::MakeProvider();
        self::MakeInterface($name);
        self::MakeRepositoryClass($name);
        self::MakeServiceClass($name);
    }


    protected static function MakeInterface($name)
    {
        $namespacePath = self::$namespacePath;
        $namespace = self::$namespace;

        $template = str_replace(
            ['{{modelName}}','{{nameSpace}}'],
            [$name, $namespace],

            self::GetStubs('RepositoryInterface')
        );
        file_put_contents(app_path("/Interfaces/{$namespacePath}/{$name}RepositoryInterface.php"), $template);

    }

    protected static function MakeRepositoryClass($name)
    {
        $namespacePath = self::$namespacePath;
        $namespace = self::$namespace;

        $template = str_replace(
           ['{{modelName}}','{{nameSpace}}'],
            [$name, $namespace],
            self::GetStubs('Repository')
        );

        file_put_contents(app_path("/Repositories/{$namespacePath}/{$name}Repository.php"), $template);

    }
    protected static function MakeServiceClass($name)
    {
        $namespacePath = self::$namespacePath;
        $namespace = self::$namespace;

        $template = str_replace(
            ['{{modelName}}','{{nameSpace}}', '{{serviceProperty}}'],
            [$name, $namespace, Str::camel($name)],
            self::GetStubs('Service')
        );

        file_put_contents(app_path("/Services/{$namespacePath}/{$name}Service.php"), $template);

    }

    protected static function MakeProvider()
    {
        $template =  self::getStubs('RepositoryBackendServiceProvider');

        if (!file_exists($path=app_path('/Repositories/RepositoryBackendServiceProvider.php')))
            file_put_contents(app_path('/Repositories/RepositoryBackendServiceProvider.php'), $template);
    }

  
}
