<?php

namespace Microoculus\LaravelRepositoryPattern\Service;
use Illuminate\Support\Str;
use Illuminate\Console\Command;


class RepositoryService {

    private static $namespace = null;
    private static $namespacePath = null;
    private static $module = null;
    private static $moduleNamespace = null;
    private static $response = [];
   
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
                        self::ShowAndCopySuccessMessage();

                   }else{
                        self::MakeInterfaceModuleWise($name);
                        self::MakeRepositoryClassModuleWise($name);
                        self::MakeServiceClassModuleWise($name);
                        self::ShowAndCopySuccessMessage();

                   }

                }catch (\Exception $e){
                    echo "{$module} not found ";
                }

            }else{
                throw new Exception("nwidart/laravel-modules :- not installed");
                exit();
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
            self::ShowAndCopySuccessMessage();

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
        if(file_exists(app_path("/Interfaces/{$namespacePath}/{$name}RepositoryInterface.php"))){
           echo("\033[31m Provided Repository Existed \033[0m \n");
            exit();
        }
        try {
            file_put_contents(app_path("/Interfaces/{$namespacePath}/{$name}RepositoryInterface.php"), $template);
            array_push(self::$response,app_path("/Interfaces/{$namespacePath}/{$name}RepositoryInterface.php"));
        }catch (\Exception $e){
            echo "{$module} not found ";
        }

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

        if(file_exists(app_path("/Repositories/{$namespacePath}/{$name}Repository.php"))){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(app_path("/Repositories/{$namespacePath}/{$name}Repository.php"), $template);
        array_push(self::$response,app_path("/Repositories/{$namespacePath}/{$name}Repository.php"));

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

        if(file_exists(app_path("/Services/{$namespacePath}/{$name}Service.php"))){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(app_path("/Services/{$namespacePath}/{$name}Service.php"), $template);
        array_push(self::$response,app_path("/Services/{$namespacePath}/{$name}Service.php"));

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
        
        if(file_exists(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php", $template); 
        array_push(self::$response,self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php"); 
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

        if(file_exists(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$name}Repository.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$name}Repository.php", $template);
        array_push(self::$response,self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$name}Repository.php"); 

        
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

        if(file_exists(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$name}Service.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$name}Service.php", $template);
        array_push(self::$response,self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$name}Service.php"); 

        
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

        if(file_exists(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }

        file_put_contents(self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php", $template);
        array_push(self::$response,self::$module->getExtraPath("Interfaces").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}RepositoryInterface.php"); 
       
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

        if(file_exists(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Repository.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }

        file_put_contents(self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Repository.php", $template);
        array_push(self::$response,self::$module->getExtraPath("Repositories").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Repository.php"); 

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

        
        if(file_exists(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Service.php")){
            echo("\033[31m Provided Repository Existed \033[0m \n");
             exit();
         }
        file_put_contents(self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Service.php", $template);
        array_push(self::$response,self::$module->getExtraPath("Services").DIRECTORY_SEPARATOR."{$namespacePath}".DIRECTORY_SEPARATOR."{$name}Service.php"); 
    }

    protected static function MakeProviderModuleWise()
    {
        $template =  self::getStubs('RepositoryBackendServiceProvider');

        if (!file_exists($path=app_path('/Repositories/RepositoryBackendServiceProvider.php')))
            file_put_contents(app_path('/Repositories/RepositoryBackendServiceProvider.php'), $template);
    }

    protected static function ShowAndCopySuccessMessage(){

        $responseString = implode("\n\r", self::$response);
        // $a =implode(" \r", self::$response);
        // shell_exec("echo " . escapeshellarg($a) . " | clip");
        echo("\033[32m  ########################################### \n\r");
        echo("\033[32m Repository creation successfully completed also copied to Clipboard \n\r");
        echo($responseString);
        echo("\n\r");
        echo("\033[32m  ########################################### \033[0m \n\r");
        
    }
  
}
