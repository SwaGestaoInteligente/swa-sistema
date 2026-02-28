<?php
namespace App\Helpers;
class ModuloHelper {
    public static function isAtivo($org, $mod) {
        return config("modules.{$org->tipo}.modulos.{$mod}") ?? false;
    }
}
function moduloAtivo($org, $mod) { return ModuloHelper::isAtivo($org, $mod); }
