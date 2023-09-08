<?php

namespace Microoculus\LaravelRepositoryPattern\Service;
use Illuminate\Support\Str;



class RepositoryService {

    private static $namespace = null;
    private static $namespacePath = null;
    private static $module = null;
    private static $moduleNamespace = null;
   
    protected static function getStubs($type)
    {

        return  is_null(self::$namespace) ? file_get_contents(resource_path("vendor/laravel-repository-pattern/stubs/{$type}.stub")) : file_get_contents(resource_path("vendor/laravel-repository-pattern/stubs/{$type}Namespace.stub"))  ;
       
    }
    protected static function getModuleWiseStubs($type)
    {

        return   file_get_contents(resource_path("vendor/laravel-repository-pattern/stubs/{$type}.stub"))  ;
       
    }

    public static function ImplementNow($name, $path=null, $module = null)
    {

        

        if(!is_null($module)){

            if(\Composer\InstalledVersions::isInstalled('nwidart/laravel-modules')){
                try {
                    self::$module = app('modules')->findOrFail($module);
                    self::$moduleNamespace = \Module::config('namespace')."\\". self::$module->getStudlyName();
                  

                    if(!file_exists(self::$module->getExtraPath('Repositories'))){
                        mkdir(self::$module->getExtraPath('Repositories'), 0777, true);
                    }

                    if(!file_exists(self::$module->getExtraPath('Interfaces'))){
                        mkdir(self::$module->getExtraPath('Interfaces'), 0777, true);
                    }

                    if(!file_exists(self::$module->getExtraPath('Services'))){
                        mkdir(self::$module->getExtraPath('Services'), 0777, true);
                    }

                   if(!is_null($path)){

                        $option_path = $path;
                        $namespace = self::$namespace  =  trim(implode('\\', array_slice(explode('\\',  $option_path), 0)), '\\');
                        $path_array = explode('\\',  $option_path);
                        $namespacePath = self::$namespacePath = implode(DIRECTORY_SEPARATOR , $path_array);

                     
                        if(!file_exists(self::$module->getExtraPath("Repositories".DIRECTORY_SEPARATOR."{$namespacePath}"))){
                            mkdir(self::$module->getExtraPath("Repositories".DIRECTORY_SEPARATOR."{$namespacePath}"), 0777, true);
                        }
                        if(!file_exists(self::$module->getExtraPath("Interfaces".DIRECTORY_SEPARATOR."{$namespacePath}"))){
                            mkdir(self::$module->getExtraPath("Interfaces".DIRECTORY_SEPARATOR."{$namespacePath}"), 0777, true);
                        }
                        if(!file_exists(self::$module->getExtraPath("Services".DIRECTORY_SEPARATOR."{$namespacePath}"))){
                            mkdir(self::$module->getExtraPath("Services".DIRECTORY_SEPARATOR."{$namespacePath}"), 0777, true);
                        }

                        self::MakeInterfaceWithPathModuleWise($name);
                        self::MakeRepositoryClassWithPathModuleWise($name);
                        self::MakeServiceClassWithPathModuleWise($name);

                   }else{
                        self::MakeInterfaceModuleWise($name);
                        self::MakeRepositoryClassModuleWise($name);
                        self::MakeServiceClassModuleWise($name);

                   }

                }catch (\Exception $e){
                    echo "{$module} not found ";
                }

            }else{
                throw new Exception("nwidart/laravel-modules :- not installed");
            }

        }else{

            
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

    /***
     * Module wise funcionality
     ***/ 
    protected static function MakeInterfaceModuleWise($name)
    {
        
        $moduleNamespace = self::$moduleNamespace ;
        $template = str_replace(
            ['{{modelName}}', '{{moduleNamespace}}'],
            [$name, $moduleNamespace],

            self::getModuleWiseStubs('RepositoryInterfaceModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php", $template);  
    }
    protected static function MakeRepositoryClassModuleWise($name)
    {
      
        $moduleNamespace = self::$moduleNamespace ;
        $entitiesNamespace = self::$moduleNamespace."\\Entities";
        $template = str_replace(
            ['{{modelName}}', '{{moduleNamespace}}','{{entitiesNamespace}}'],
            [$name, $moduleNamespace, $entitiesNamespace],
            self::getModuleWiseStubs('RepositoryModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$name}Repository.php", $template);

        
    }
    protected static function MakeServiceClassModuleWise($name)
    {
       
        $moduleNamespace = self::$moduleNamespace ;
        $entitiesNamespace = self::$moduleNamespace."\\Entities";

        $template = str_replace(
            ['{{modelName}}','{{moduleNamespace}}', '{{serviceProperty}}'],
            [$name, $moduleNamespace, Str::camel($name)],
            self::getModuleWiseStubs('ServiceModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$name}Service.php", $template);

        
    }

    /***
     * Module wise funcionality -- withpath
     ***/

    protected static function MakeInterfaceWithPathModuleWise($name)
    {
        
        $namespacePath = self::$namespacePath;
        $namespace = self::$moduleNamespace."\\Interfaces\\".self::$namespace;
        $template = str_replace(
            ['{{modelName}}','{{nameSpace}}'],
            [$name, $namespace],

            self::getModuleWiseStubs('RepositoryInterfaceWithPathModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php", $template);

       
    }

    protected static function MakeRepositoryClassWithPathModuleWise($name)
    {
        
        $namespacePath = self::$namespacePath;
        $namespace = self::$moduleNamespace."\\Repositories\\".self::$namespace;
        $moduleNamespace = self::$moduleNamespace."\\Entities";
        $template = str_replace(
            ['{{modelName}}','{{nameSpace}}', '{{moduleNamespace}}'],
            [$name, $namespace, $moduleNamespace],

            self::getModuleWiseStubs('RepositoryWithPathModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Repository.php", $template);

    }
    protected static function MakeServiceClassWithPathModuleWise($name)
    {
        $namespacePath = self::$namespacePath;
        $namespace = self::$moduleNamespace."\\Services\\".self::$namespace;
        $repoPath =   self::$moduleNamespace."\\Repositories\\".self::$namespace;;
        $template = str_replace(
            ['{{modelName}}','{{nameSpace}}', '{{serviceProperty}}', '{{repoPath}}'],
            [$name, $namespace, Str::camel($name),  $repoPath],
            self::getModuleWiseStubs('ServiceWithPathModuleWise')
        );
        file_put_contents(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Service.php", $template);
    }

    protected static function MakeProviderModuleWise()
    {
        $template =  self::getStubs('RepositoryBackendServiceProvider');

        if (!file_exists($path=app_path('/Repositories/RepositoryBackendServiceProvider.php')))
            file_put_contents(app_path('/Repositories/RepositoryBackendServiceProvider.php'), $template);
    }
  
}
