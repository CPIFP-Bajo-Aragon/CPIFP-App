<?php

class Indicador{

    private $profeModelo;


    
    // cargar modelo
    public static function modelo($modelo){
       require_once '../App/modelos/' . $modelo . '.php';
       return new $modelo;
    }




    public static function horasHastaFecha($fechaObjetivo, $id_modulo){
       $profeModelo = self::modelo('ProfesorM');
       //OBTENEMOS EL AÑO LECTIVO
       $lectivo=$profeModelo->obtener_lectivo();
       $id_lectivo=$lectivo[0]->id_lectivo;
       $curso=$lectivo[0]->nombre;
       $fecha_ini=$lectivo[0]->fecha_ini;
       $fecha_fin=$lectivo[0]->fecha_fin;
       
        
       //OBTENER LOS DÍAS FESTIVOS
       $festivos=$profeModelo->ver_festivos($id_lectivo);
     
       $diaActual=$fecha_ini;
       $i =0;
     
       $horario_semana=$profeModelo->horario_semana($id_modulo);
       //CALCULAMOS LOS DIAS RESTANTES DESDE EL PRINCIPO DE CURSO HASTA LA FECHA OBJETIVO
      
       $horasTotales=0;
       
       while($diaActual<=$fechaObjetivo)
       {
          $diaLectivo=true;
          ///BUSCAMOS SI EL DÍA ACTUAL ESTÁ ENTRE LOS FESTIVOS DEL AÑO SI ES FESTIVO $diaLectivo pasará a false
          foreach ($festivos as $diaFestivo)
          {
               if($diaFestivo->fecha==$diaActual) {$diaLectivo=false; }

          }

         //obtenemos el dia de la semandel dia actual 
         $diaSemana=date("N",strtotime($diaActual));
         if($diaSemana>5) $diaLectivo=false;

         if($diaLectivo)
           {           
                      
             $horasDiarias=0;
             foreach($horario_semana as $horariodia)
             {
               if ($horariodia->id_horario==$diaSemana)
                 $horasDiarias=$horariodia->total_horas;
             }
            $horasTotales=$horasTotales+$horasDiarias;
                
           }
        $diaActual=diaSiguiente($diaActual);
        }
            
        return $horasTotales;
    }

    

}
