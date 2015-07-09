<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Twig view
 */
class Twig extends Kohana_Twig {

    protected static function env()
    {
        $config = Kohana::$config->load('twig');
        $loader = new Twig_Loader_CFS($config->get('loader'));
        $env = new Twig_Environment($loader, $config->get('environment'));

        $env->addExtension(new Twig_Extension_StringLoader());


        foreach ($config->get('functions') as $key => $value)
        {
            $function = new Twig_SimpleFunction($key, $value["func"], $value["options"]);
            $env->addFunction($function);
        }

        foreach ($config->get('filters') as $key => $value)
        {
            $filter = new Twig_SimpleFilter($key, $value);
            $env->addFilter($filter);
        }
                
                foreach ($config->get('tests') as $key => $value)
                {
                    $test = new Twig_SimpleTest($key, $value);
                    $env->addTest($test);
                }

        return $env;
    }
}