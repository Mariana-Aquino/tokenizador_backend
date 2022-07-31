<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use stdClass;

use function PHPSTORM_META\map;
use function PHPUnit\Framework\isNull;

class TokenC0Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Rresponse;
        $entryMode = $request -> Entry_Mode;
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $response = array();
        $query = "select KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, 
        KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A from test where ";

        /*
        Detectar cual de lso filtros está siendo utilizad.
        Se incermenta la variable $numberFilters para ingresar al switch
        y se configura la bandera para saber cual de estos filtros son utilizados.
        */
        if(!empty($kq2)){$numberFilters++; $flagkq2 = true;}
        if(!empty($codeResponse)){$numberFilters++; $flagCode = true;}
        if(!empty($entryMode)){$numberFilters++; $flagEntry = true;}

        switch($numberFilters){
            case 1:{ //Solo un filtro utilizado
                if($flagkq2){ //Medio de Acceso
                    for($i = 0; $i < count($kq2); $i++){
                        $response = array_merge($response, DB::select($query.
                        "KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                if($flagCode){ //Código de Respuesta
                    for($i = 0; $i < count($codeResponse); $i++){
                        $response = array_merge($response, DB::select($query.
                        "CODIGO_RESPUESTA = ?", [$codeResponse[$i]]));
                    }
                    $array = json_decode(json_encode($response), true); 
                }
                if($flagEntry){ //Entry Mode
                    for($i = 0; $i < count($entryMode); $i++){
                        $response = array_merge($response, DB::select($query.
                        "ENTRY_MODE = ?", [$entryMode[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            case 2:{ //Dos filtros utilizados
                if($flagkq2){ //Medio de Acceso
                    if($flagCode && !$flagEntry){ //Se utiliza el filtro de Medio Acceso con Código de respuesta
                        $firstLength = max($kq2, $codeResponse); //Saber cual es el arreglo mas largo
                        switch($firstLength){
                            case $kq2:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$j], $entryMode[$i]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                        }
                    }else{
                        if(!$flagCode && $flagEntry){ //Se utiliza el filtro de Medio Acceso con Entry Mode
                            $firstLength = max($kq2, $entryMode);
                            switch($firstLength){
                                case $kq2:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$i], $entryMode[$j]]));
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ? ", [$kq2[$j], $entryMode[$i]]));
                                        }
                                        $array = json_decode(json_encode($response), true);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }else{
                    if($flagCode && $flagEntry){
                        $firstLength = max($codeResponse, $entryMode);
                        switch($firstLength){
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$codeResponse[$i], $entryMode[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $entryMode:{
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$codeResponse[$j], $entryMode[$i]]));
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
                break;
            }
            case 3:{ //Los tres filtros son elegidos
                if($flagkq2 && $flagCode && $flagEntry){
                    $firstLength = max($kq2, $codeResponse, $entryMode);
                    switch($firstLength){
                        case $kq2:{
                            $secondLength = max($codeResponse, $entryMode);
                            switch($secondLength){
                                case $codeResponse:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_ RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$j], $entryMode[$z]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$z], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                        }
                        case $codeResponse:{
                            $secondLength = max($kq2, $entryMode);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_ RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$j], $codeResponse[$i], $entryMode[$z]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$i], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                        case $entryMode:{
                            $secondLength = max($kq2, $codeResponse);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$j], $codeResponse[$z], $entryMode[$i]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $codeResponse:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$j], $entryMode[$i]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }
            default: {
                $response = DB::select("select KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, 
                KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A from test");
                $array = json_decode(json_encode($response), true);
            }
        }
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID_Ecommerce = $data['KC0_INDICADOR_DE_COMERCIO_ELEC'];
            $answer[$key] -> Card_Type = $data['KC0_TIPO_DE_TARJETA'];
            $answer[$key] -> ID_CVV2 = $data['KC0_INDICADOR_DE_CVV2_CVC2_PRE'];
            $answer[$key] -> ID_Information = $data['KC0_INDICADOR_DE_INFORMACION_A'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request)
    {
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KC0_INDICADOR_DE_COMERCIO_ELEC', 'KC0_TIPO_DE_TARJETA', 'KC0_INDICADOR_DE_CVV2_CVC2_PRE',
        'KC0_INDICADOR_DE_INFORMACION_A', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM', 'LN_COMER', 'LN_TERM', 'FIID_TARJ', 
        'LN_TARJ'];

        $values[0] = $request->Kq2;
        $values[1] = $request->Code_Response;
        $values[2] = $request->Entry_Mode;
        $values[3] = $request->ID_Ecommerce;
        $values[4] = $request->Card_Type;
        $values[5] = $request->ID_CVV2;
        $values[6] = $request->ID_Information;
        $values[7] = $request->ID_Comer;
        $values[8] = $request->Term_Comer;
        $values[9] = $request->Fiid_Comer;
        $values[10] = $request->Fiid_Term;
        $values[11] = $request->Ln_Comer;
        $values[12] = $request->Ln_Term;
        $values[13] = $request->Fiid_Card;
        $values[14] = $request->Ln_Card;

        $answer = array();
        $answerAllRight = array();
        $response = array();
        $array = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, 
        KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1  from test where ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, 
        KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test";

        //Detectar cuales son los filtros seleccionados para la tabla
        for($key = 0; $key < 15; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($label[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        for($i = 0; $i < count($filteredValues); $i++){
            for($j = 0; $j < count($filteredValues[$i]); $j++){
                if($filteredValues[$i][$j] === null){
                    $filteredValues[$i][$j] = " ";
                }
            }
        }

        if(empty($filteredValues)){//Ningún filtro ha sido seleccionado para la tabla
            $response = DB::select($queryOutFilters);
            $array = json_decode(json_encode($response), true);
        }else{
            if(count($filteredValues) <= 1){ //En caso de que solo exista menos de un valor dentro del arreglo de $filteredValues
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        $response = array_merge($response, DB::select($query.$filteredLabels[$i]." = ?",
                        [$filteredValues[$i][$j]]));
                    }
                }
                $array = json_decode(json_encode($response), true);
            }else{ //En caso de existir más valores dentro del arreglo de $filteredValues

                //Ingresar todos los valores elegidos en el filtro dentro de un solo arreglo para la consulta
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //Variable para el control de la longitud del query
                //Constructor del query (Varias consultas al mismo tiempo)
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        if($j == count($filteredValues[$i]) -1){
                            if($j == 0){
                                if($z == count($arrayValues)){
                                    $query .= "(".$filteredLabels[$i]." = ?)";
                                }else{
                                    $query .= "(".$filteredLabels[$i]." = ?) and ";
                                }
                                $z++;
                            }else{
                                if($z == count($arrayValues)){
                                    $query .= $filteredLabels[$i]." = ?)";
                                    $z = 1;
                                }else{
                                    $query .= $filteredLabels[$i]." = ?) and ";
                                    $z++;
                                }
                            }
                        }else{
                            if($j == 0){
                                $query .= "(".$filteredLabels[$i]." = ? or ";
                                $z++;
                            }else{
                                $query .= $filteredLabels[$i]." = ? or ";
                                $z++;
                            }
                        }
                    }
                }
                $response = DB::select($query, [...$arrayValues]);
                $array = json_decode(json_encode($response), true);
            }
        }
        foreach($array as $key => $data){

                $answer[$key] = new stdClass();
                $answer[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
                $answer[$key] -> entryMode = $data['ENTRY_MODE'];
                $answer[$key] -> ecommerce = $data['KC0_INDICADOR_DE_COMERCIO_ELEC'];
                $answer[$key] -> cardtp = $data['KC0_TIPO_DE_TARJETA'];
                $answer[$key] -> cvv2 = $data['KC0_INDICADOR_DE_CVV2_CVC2_PRE'];
                $answer[$key] -> info = $data['KC0_INDICADOR_DE_INFORMACION_A'];
                $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answer[$key] -> Number_Sec = $data['NUM_SEC'];
                //Separación del decimal y entero para agregar el punto decimal
                $dec = substr($data['MONTO1'], strlen($data['MONTO1'])-2, 2);
                $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
                $answer[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
                $answer[$key]->ID_Comer = $data['ID_COMER'];
                $answer[$key]->Term_Comer = $data['TERM_COMER'];
                $answer[$key]->Fiid_Comer = $data['FIID_COMER'];
                $answer[$key]->Fiid_Term = $data['FIID_TERM'];
                $answer[$key]->Ln_Comer = $data['LN_COMER'];
                $answer[$key]->Ln_Term = $data['LN_TERM'];
                $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
                $answer[$key]->Ln_Card = $data['LN_TARJ'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
