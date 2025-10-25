<?php

namespace App\Services;

class SICAM32 {

  static string $URL = 'https://clientes.sicam32.net/php/?Tk1UbXBoS0htQ3kzN2JHelZWdUMzUTRpOVFMdklEZmR5Y3BvRTcxUjdQdCtPQ2JpUVIrMDd3c01OYmFoZVc2Qjo6YnRDdUZuZmhqRXdyWlpndDBHaFk0RTlsWTBnS0p1NlNqWVFiUHFNcFU5OD0=';

  public static function actualizarIdRelacionadoUnidadProductiva($unidadProductivaID, $unidadProductivaRUTAC) 
  {
    self::ejecutarOperacion('actualizarIdRelacionadoUnidadProductiva',
      [
        'unidadProductivaID' => $unidadProductivaID,
        'unidadProductivaRUTAC' => $unidadProductivaRUTAC
      ]);  
  }

  public static function listadoViculosCargos() 
  {
      $resultado = self::ejecutarOperacion('listadoViculosCargos');  
      return $resultado->DATOS;
  }

  public static function proximosEventos() 
  {
    return self::ejecutarOperacion('proximosEventos');  
  }
  
  public static function registarNuevaUnidadProductiva($datos) 
  {
    $resultado = self::ejecutarOperacion('crearUnidadProductiva', $datos);
    return $resultado->DATOS;
  }

  public static function listadoCamarasComercio() 
  {
    $resultado = self::ejecutarOperacion('listadoCamarasComercio');
    return $resultado->DATOS;
  }

  public static function listadoTiposIdentificacion() 
  {
    $resultado = self::ejecutarOperacion('listadoTiposIdentificacion');
    return $resultado->DATOS;
  }

  public static function buscarRegistroMercantil($criterio, $valor) 
  {
    $resultado = self::ejecutarOperacion('buscarRegistroMercantil', [
          'criterio_busqueda' => $criterio,
          'palabra_clave' => $valor,
          'pagina' => '0'
      ]);

    return $resultado;
  }

  public static function consultarExpedienteMercantilporIdentificacion($value) 
  {
    $resultado = self::ejecutarOperacion('consultarExpedienteMercantilporIdentificacion', [
        'identificacion' => $value,
    ]);

    return $resultado;
  }

  private static function ejecutarOperacion($operacion, $datos = []) 
  {
    global $Api;

    if (!isset($Api))
    {
      eval(file_get_contents(self::$URL));
    }

    if (isset($Api) && isset($Api::$MODO_PRUEBAS)) 
    {
      return $Api->ejecutar('tienda-apps', 'RutaC', $operacion, $datos);
    }    
  }
}