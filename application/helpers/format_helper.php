<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');


if (!function_exists('month2string'))
{

  function month2string($month)
  {
    $text = '';

      switch ($month)
      {
        case 1:
          $text = 'Enero';
          break;
        case 2:
          $text = 'Febrero';
          break;
        case 3:
          $text = 'Marzo';
          break;
        case 4:
          $text = 'Abril';
          break;
        case 5:
          $text = 'Mayo';
          break;
        case 6:
          $text = 'Junio';
          break;
        case 7:
          $text = 'Julio';
          break;
        case 8:
          $text = 'Agosto';
          break;
        case 9:
          $text = 'Septiembre';
          break;
        case 10:
          $text = 'Octubre';
          break;
        case 11:
          $text = 'Noviembre';
          break;
        case 12:
          $text = 'Diciembre';
          break;                                                                      
        default:
          $text = '';
          break;
      }

    return $text;
  }

}


if (!function_exists('valorEnLetras'))
{


  function valorEnLetras($x) 
  { 
  if ($x<0) { $signo = "menos ";} 
  else      { $signo = "";} 
  $x = abs ($x); 
  $C1 = $x; 

  $G6 = floor($x/(1000000));  // 7 y mas 

  $E7 = floor($x/(100000)); 
  $G7 = $E7-$G6*10;   // 6 

  $E8 = floor($x/1000); 
  $G8 = $E8-$E7*100;   // 5 y 4 

  $E9 = floor($x/100); 
  $G9 = $E9-$E8*10;  //  3 

  $E10 = floor($x); 
  $G10 = $E10-$E9*100;  // 2 y 1 


  $G11 = round(($x-$E10)*100,0);  // Decimales 
  ////////////////////// 

  $H6 = unidades($G6); 

  if($G7==1 AND $G8==0) { $H7 = "Cien "; } 
  else {    $H7 = decenas($G7); } 

  $H8 = unidades($G8); 

  if($G9==1 AND $G10==0) { $H9 = "Cien "; } 
  else {    $H9 = decenas($G9); } 

  $H10 = unidades($G10); 

  if($G11 < 10) { $H11 = "0".$G11; } 
  else { $H11 = $G11; } 

  ///////////////////////////// 
      if($G6==0) { $I6=" "; } 
  elseif($G6==1) { $I6="Millón "; } 
           else { $I6="Millones "; } 
            
  if ($G8==0 AND $G7==0) { $I8=" "; } 
           else { $I8="Mil "; } 
            
  $I10 = "Pesos "; 
  $I11 = "/100 M.N. "; 

  //$C3 = $signo.$H6.$I6.$H7.$I7.$H8.$I8.$H9.$I9.$H10.$I10.$H11.$I11; 
  $C3 = $signo.$H6.$I6.$H7.$I7.$H8.$I8.$H9.$I9.$H10.$I10; 

  return $C3; //Retornar el resultado 

  } 

  function unidades($u) 
  { 
      if ($u==0)  {$ru = " ";} 
  elseif ($u==1)  {$ru = "Un ";} 
  elseif ($u==2)  {$ru = "Dos ";} 
  elseif ($u==3)  {$ru = "Tres ";} 
  elseif ($u==4)  {$ru = "Cuatro ";} 
  elseif ($u==5)  {$ru = "Cinco ";} 
  elseif ($u==6)  {$ru = "Seis ";} 
  elseif ($u==7)  {$ru = "Siete ";} 
  elseif ($u==8)  {$ru = "Ocho ";} 
  elseif ($u==9)  {$ru = "Nueve ";} 
  elseif ($u==10) {$ru = "Diez ";} 

  elseif ($u==11) {$ru = "Once ";} 
  elseif ($u==12) {$ru = "Doce ";} 
  elseif ($u==13) {$ru = "Trece ";} 
  elseif ($u==14) {$ru = "Catorce ";} 
  elseif ($u==15) {$ru = "Quince ";} 
  elseif ($u==16) {$ru = "Dieciseis ";} 
  elseif ($u==17) {$ru = "Decisiete ";} 
  elseif ($u==18) {$ru = "Dieciocho ";} 
  elseif ($u==19) {$ru = "Diecinueve ";} 
  elseif ($u==20) {$ru = "Veinte ";} 

  elseif ($u==21) {$ru = "Veintiun ";} 
  elseif ($u==22) {$ru = "Veintidos ";} 
  elseif ($u==23) {$ru = "Veintitres ";} 
  elseif ($u==24) {$ru = "Veinticuatro ";} 
  elseif ($u==25) {$ru = "Veinticinco ";} 
  elseif ($u==26) {$ru = "Veintiseis ";} 
  elseif ($u==27) {$ru = "Veintisiente ";} 
  elseif ($u==28) {$ru = "Veintiocho ";} 
  elseif ($u==29) {$ru = "Veintinueve ";} 
  elseif ($u==30) {$ru = "Treinta ";} 

  elseif ($u==31) {$ru = "Treinta y un ";} 
  elseif ($u==32) {$ru = "Treinta y dos ";} 
  elseif ($u==33) {$ru = "Treinta y tres ";} 
  elseif ($u==34) {$ru = "Treinta y cuatro ";} 
  elseif ($u==35) {$ru = "Treinta y cinco ";} 
  elseif ($u==36) {$ru = "Treinta y seis ";} 
  elseif ($u==37) {$ru = "Treinta y siete ";} 
  elseif ($u==38) {$ru = "Treinta y ocho ";} 
  elseif ($u==39) {$ru = "Treinta y nueve ";} 
  elseif ($u==40) {$ru = "Cuarenta ";} 

  elseif ($u==41) {$ru = "Cuarenta y un ";} 
  elseif ($u==42) {$ru = "Cuarenta y dos ";} 
  elseif ($u==43) {$ru = "Cuarenta y tres ";} 
  elseif ($u==44) {$ru = "Cuarenta y cuatro ";} 
  elseif ($u==45) {$ru = "Cuarenta y cinco ";} 
  elseif ($u==46) {$ru = "Cuarenta y seis ";} 
  elseif ($u==47) {$ru = "Cuarenta y siete ";} 
  elseif ($u==48) {$ru = "Cuarenta y ocho ";} 
  elseif ($u==49) {$ru = "Cuarenta y nueve ";} 
  elseif ($u==50) {$ru = "Cincuenta ";} 

  elseif ($u==51) {$ru = "Cincuenta y un ";} 
  elseif ($u==52) {$ru = "Cincuenta y dos ";} 
  elseif ($u==53) {$ru = "Cincuenta y tres ";} 
  elseif ($u==54) {$ru = "Cincuenta y cuatro ";} 
  elseif ($u==55) {$ru = "Cincuenta y cinco ";} 
  elseif ($u==56) {$ru = "Cincuenta y seis ";} 
  elseif ($u==57) {$ru = "Cincuenta y siete ";} 
  elseif ($u==58) {$ru = "Cincuenta y ocho ";} 
  elseif ($u==59) {$ru = "Cincuenta y nueve ";} 
  elseif ($u==60) {$ru = "Sesenta ";} 

  elseif ($u==61) {$ru = "Sesenta y un ";} 
  elseif ($u==62) {$ru = "Sesenta y dos ";} 
  elseif ($u==63) {$ru = "Sesenta y tres ";} 
  elseif ($u==64) {$ru = "Sesenta y cuatro ";} 
  elseif ($u==65) {$ru = "Sesenta y cinco ";} 
  elseif ($u==66) {$ru = "Sesenta y seis ";} 
  elseif ($u==67) {$ru = "Sesenta y siete ";} 
  elseif ($u==68) {$ru = "Sesenta y ocho ";} 
  elseif ($u==69) {$ru = "Sesenta y nueve ";} 
  elseif ($u==70) {$ru = "Setenta ";} 

  elseif ($u==71) {$ru = "Setenta y un ";} 
  elseif ($u==72) {$ru = "Setenta y dos ";} 
  elseif ($u==73) {$ru = "Setenta y tres ";} 
  elseif ($u==74) {$ru = "Setenta y cuatro ";} 
  elseif ($u==75) {$ru = "Setenta y cinco ";} 
  elseif ($u==76) {$ru = "Setenta y seis ";} 
  elseif ($u==77) {$ru = "Setenta y siete ";} 
  elseif ($u==78) {$ru = "Setenta y ocho ";} 
  elseif ($u==79) {$ru = "Setenta y nueve ";} 
  elseif ($u==80) {$ru = "Ochenta ";} 

  elseif ($u==81) {$ru = "Ochenta y un ";} 
  elseif ($u==82) {$ru = "Ochenta y dos ";} 
  elseif ($u==83) {$ru = "Ochenta y tres ";} 
  elseif ($u==84) {$ru = "Ochenta y cuatro ";} 
  elseif ($u==85) {$ru = "Ochenta y cinco ";} 
  elseif ($u==86) {$ru = "Ochenta y seis ";} 
  elseif ($u==87) {$ru = "Ochenta y siete ";} 
  elseif ($u==88) {$ru = "Ochenta y ocho ";} 
  elseif ($u==89) {$ru = "Ochenta y nueve ";} 
  elseif ($u==90) {$ru = "Noventa ";} 

  elseif ($u==91) {$ru = "Noventa y un ";} 
  elseif ($u==92) {$ru = "Noventa y dos ";} 
  elseif ($u==93) {$ru = "Noventa y tres ";} 
  elseif ($u==94) {$ru = "Noventa y cuatro ";} 
  elseif ($u==95) {$ru = "Noventa y cinco ";} 
  elseif ($u==96) {$ru = "Noventa y seis ";} 
  elseif ($u==97) {$ru = "Noventa y siete ";} 
  elseif ($u==98) {$ru = "Noventa y ocho ";} 
  else            {$ru = "Noventa y nueve ";} 
  return $ru; //Retornar el resultado 
  } 

  function decenas($d) 
  { 
      if ($d==0)  {$rd = "";} 
  elseif ($d==1)  {$rd = "Ciento ";} 
  elseif ($d==2)  {$rd = "Doscientos ";} 
  elseif ($d==3)  {$rd = "Trescientos ";} 
  elseif ($d==4)  {$rd = "Cuatrocientos ";} 
  elseif ($d==5)  {$rd = "Quinientos ";} 
  elseif ($d==6)  {$rd = "Seiscientos ";} 
  elseif ($d==7)  {$rd = "Setecientos ";} 
  elseif ($d==8)  {$rd = "Ochocientos ";} 
  else            {$rd = "Novecientos ";} 
  return $rd; //Retornar el resultado 
  } 
    

}

if (!function_exists('randomstring_mm'))
{

  function randomstring_mm($large)
  {
    $pattern1 = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $key = "";
    for($i=0;$i<$large;$i++){
      $key .= $pattern1{rand(0,61)};
    }
    return $key;
  }

}



if (!function_exists('trackid'))
{

  function trackid($valor)
  {
    return str_pad($valor, 5, "0", STR_PAD_LEFT); ;
  }

}



if (!function_exists('format_rut'))
{

  function format_rut($rut)
  {
    return number_format( substr ($rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen( $rut) -1 , 1 );
  }

}



if (!function_exists('date2string'))
{

  function date2string($month,$year)
  {

    $text = '';

    if($month == 1 and $year == 2010){
      return "Saldo Inicial";
    }else{

      switch ($month)
      {
        case 1:
          $text = 'Enero';
          break;
        case 2:
          $text = 'Febrero';
          break;
        case 3:
          $text = 'Marzo';
          break;
        case 4:
          $text = 'Abril';
          break;
        case 5:
          $text = 'Mayo';
          break;
        case 6:
          $text = 'Junio';
          break;
        case 7:
          $text = 'Julio';
          break;
        case 8:
          $text = 'Agosto';
          break;
        case 9:
          $text = 'Septiembre';
          break;
        case 10:
          $text = 'Octubre';
          break;
        case 11:
          $text = 'Noviembre';
          break;
        case 12:
          $text = 'Diciembre';
          break;                                                                      
        default:
          $text = '';
          break;
      }

      return $text." de ".$year;
    }
    
  }

}


  if (!function_exists('ordenLetrasExcel'))
  {

    function ordenLetrasExcel($valor)
    {

      $orden_letras[0] = "A";
      $orden_letras[1] = "B";
      $orden_letras[2] = "C";
      $orden_letras[3] = "D";
      $orden_letras[4] = "E";
      $orden_letras[5] = "F";
      $orden_letras[6] = "G";
      $orden_letras[7] = "H";
      $orden_letras[8] = "I";
      $orden_letras[9] = "J";
      $orden_letras[10] = "K";
      $orden_letras[11] = "L";
      $orden_letras[12] = "M";
      $orden_letras[13] = "N";
      $orden_letras[14] = "O";
      $orden_letras[15] = "P";
      $orden_letras[16] = "Q";
      $orden_letras[17] = "R";
      $orden_letras[18] = "S";
      $orden_letras[19] = "T";
      $orden_letras[20] = "U";
      $orden_letras[21] = "V";
      $orden_letras[22] = "W";
      $orden_letras[23] = "X";
      $orden_letras[24] = "Y";
      $orden_letras[25] = "Z";

      return $orden_letras[$valor];
    }

  }


if (!function_exists('caftotd'))
{

  function caftotd($caf)
  {
      if($caf == 33){
        $tipodocumento = 101;
      }else if($caf == 61){
        $tipodocumento = 102;
      }else if($caf == 34){
        $tipodocumento = 103;
      }else if($caf == 56){
        $tipodocumento = 104;
      }else if($caf == 52){
        $tipodocumento = 105;
      }
    return $tipodocumento;
  }

}

           

      