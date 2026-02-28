<?php

namespace App\Helpers;

class ModuloHelper
{
    public static function isAtivo($organizacao, string $modulo): bool
    {
        $config = config("modules.{$organizacao->tipo}.modulos", []);
        return $config[$modulo] ?? false;
    }
    
    public static function getLabel($organizacao, string $key): string
    {
        return config("modules.{$organizacao->tipo}.{$key}", $key);
    }
    
    public static function getIcon($organizacao): string
    {
        return config("modules.{$organizacao->tipo}.icon", '📋');
    }
    
    public static function modulosAtivos($organizacao): array
    {
        $modulos = config("modules.{$organizacao->tipo}.modulos", []);
        return array_keys(array_filter($modulos));
    }
    
    public static function getLabelPessoa($organizacao, bool $plural = false): string
    {
        $key = $plural ? 'label_plural' : 'label_singular';
        return config("modules.{$organizacao->tipo}.pessoas.{$key}", 'Pessoa');
    }
    
    public static function getTiposPessoa($organizacao): array
    {
        return config("modules.{$organizacao->tipo}.pessoas.tipos", []);
    }
    
    public static function getEstruturaNivel($organizacao, int $nivel): string
    {
        return config("modules.{$organizacao->tipo}.estrutura.nivel{$nivel}", "Nível {$nivel}");
    }
}

// Helpers globais para Blade
if (!function_exists('moduloAtivo')) {
    function moduloAtivo($organizacao, string $modulo): bool
    {
        return \App\Helpers\ModuloHelper::isAtivo($organizacao, $modulo);
    }
}

if (!function_exists('labelPessoa')) {
    function labelPessoa($organizacao, bool $plural = false): string
    {
        return \App\Helpers\ModuloHelper::getLabelPessoa($organizacao, $plural);
    }
}