<?php

namespace App\Http\Controllers;

use App\Datas;
use Illuminate\Http\Request;
use App\User;

use Auth;
use DB;
use DOMDocument;
use ZipArchive;

class DatasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        return redirect()->route('search_data');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        return view('data.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        $archivo_xml    = $request->file('xml_file');
        $imagen_icon    = $request->file('icon');
        $imagen_preview = $request->file('preview');
        
        $myfile       = fopen($archivo_xml, "r") or die("Unable to open file!");
        $xml_original = fread($myfile, filesize($archivo_xml));
        fclose($myfile);
        
        $xml_replaced = str_replace('version="1.1"', 'version="1.0"', $xml_original);
        $xml_charter  = $this->generar_xml_charter($xml_replaced);
        
        $date           = date("d-m-y-h.i");
        $productId      = simplexml_load_string($xml_replaced) or die("Error: Cannot create object");
		$nombre         = $productId->productId;
        $ruta = "/var/www/html/charter.abae.gob.ve/IMAGENES_SUBIDAS/";
        if (!file_exists($ruta)) {
            //mkdir($carpeta, 0777, true); // default, widest possible access
            mkdir($ruta, 0755, true); // Everything for owner, read and execute for others
        }
        $carpeta_subida = $ruta.$date."-".$nombre;
        
        $this->copia_archivo($carpeta_subida, $nombre, $date, $imagen_icon, "icon.jpg");
        $this->copia_archivo($carpeta_subida, $nombre, $date, $imagen_preview, "preview.jpg");
        
        $arr = array(
            'folder_name'  => $date."-".$nombre,
            'xml_original' => $xml_original,
            'xml_charter'  => $xml_charter
        );
        
        Datas::create($arr);
        return redirect()->back()->with('success', 'Datos guardados correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Datas  $datas
     * @return \Illuminate\Http\Response
     */
    /*
    public function show(Datas $datas)
    {
        //
    }
    */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Datas  $datas
     * @return \Illuminate\Http\Response
     */
    /*
    public function edit(Datas $datas)
    {
        //
    }
    */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Datas  $datas
     * @return \Illuminate\Http\Response
     */
    /*
    public function update(Request $request, Datas $datas)
    {
        //
    }
    */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Datas  $datas
     * @return \Illuminate\Http\Response
     */
    /*
    public function destroy(Datas $datas)
    {
        //
    }
    */
    
    function search(Request $request) {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        $search = $request->input('q');
        
        if($search != "") {
            $datas = Datas::where(function ($query) use ($search) {
                $query->where('folder_name', 'like', '%'.$search.'%')
                    ->orWhere('created_at', 'like', '%'.$search.'%')
                    ->orderBy('created_at', 'desc');
            })->paginate(10);
            $datas->appends(['q' => $search]);
        }
        else {
            $datas = Datas::paginate(10);
        }
        return view('data.index')->with('datas', $datas);
    }
    
    function download_xml($id, $file) {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        $data = Datas::findOrFail($id);
        
        $xml_file_name = $data->folder_name.".xml";
        $doc = new DOMDocument();
        $xml_replaced = str_replace('version="1.1"', 'version="1.0"', $data->$file); // OJO
        $doc->loadXML($xml_replaced);        
        $doc->save($xml_file_name);

        header("Content-Description: File Transfer");
        header('Content-Type: application/xml');
        header("Content-Disposition: attachment; filename=$xml_file_name");
        header('Content-Length: ' . filesize($xml_file_name));
        readfile($xml_file_name);

        unlink($xml_file_name);
    }
    
    function download_all($id) {
        if(!isset(Auth::user()->email)) {
            return view('login.login');
        }
        
        $data = Datas::findOrFail($id);
        
        $carpeta     = $data->folder_name;
        $xml_charter = $data->xml_charter;
        $ruta = "/var/www/html/charter.abae.gob.ve/IMAGENES_SUBIDAS/";

        $zipname = $carpeta.'.zip';
        $zip = new ZipArchive;
        $res = $zip->open($zipname, ZipArchive::CREATE);
        if ($res === TRUE) {
            $ruta_completa = $ruta.$carpeta."/";
            if($handle = opendir($ruta_completa)) {
                while(false !== ($entry = readdir($handle))) {
                    if($entry != "." && $entry != "..") {
                        $zip->addFile($ruta_completa.$entry, basename($ruta_completa.$entry).'.jpg');
                    }
                }
                $zip->addFromString("metadata.xml", $xml_charter);
                $zip->close();
                closedir($handle);
            }
        }
        else {
            echo "ERROR AL CREAR EL ARCHIVO ZIP";
        }

        header("Content-Description: File Transfer");
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=$zipname");
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);

        unlink($zipname);
    }
    
    function copia_archivo($carpeta_subida, $nombre, $date, $archivo, $tipo) {
            if (!file_exists($carpeta_subida)) {
                //mkdir($carpeta, 0777, true); // default, widest possible access
                mkdir($carpeta_subida, 0755, true); // Everything for owner, read and execute for others
            }

            $ruta_destino_archivo = $carpeta_subida."/".$date."-".$nombre."-".$tipo;
            $upload = move_uploaded_file(strval($archivo), strval($ruta_destino_archivo));
            if(!$upload) {
                /*
                echo "<script>
                            alert('Error al subir la imagen');
                    </script>";
                */
                return redirect()->back()->with('error', 'Error al subir la imagen');
            }
    }
    
    function generar_xml_charter($myXmlString) {        
        /*Leemos el XML entrante*/
        $doc = new DOMDocument();
        $doc->loadXML($myXmlString);

        //Con la funcion getElementsByTagName extraigo el valor de las etiquetas contenidas en el nodo padre productMeta
        $productMetas = $doc->getElementsByTagName( "productMeta" );

        //Recorro el XML entrante
        foreach( $productMetas as $valor ) {
            $shortName=0;
            //Extraigo cada uno de los valores de las etiquetas que me interesan de XML entrante
            $productId2 = $valor->getElementsByTagName( "productId" );
            $productId = $productId2->item(0)->nodeValue;

            $sceneId2 = $valor->getElementsByTagName( "sceneId" );
            $sceneId = $sceneId2->item(0)->nodeValue;

            $satelliteId2 = $valor->getElementsByTagName( "satelliteId" );
            $satelliteId = $satelliteId2->item(0)->nodeValue;

            $sensorId2 = $valor->getElementsByTagName( "sensorId" );
            $sensorId = $sensorId2->item(0)->nodeValue;

            $productDates = $valor->getElementsByTagName( "productDate" );
            $productDate = $productDates->item(0)->nodeValue;

            $imagingStartTimes = $valor->getElementsByTagName( "imagingStartTime" );
            $imagingStartTime = $imagingStartTimes->item(0)->nodeValue;

            $imagingStopTimes = $valor->getElementsByTagName( "imagingStopTime" );
            $imagingStopTime = $imagingStopTimes->item(0)->nodeValue;

            $dataUpperLeftLats = $valor->getElementsByTagName( "dataUpperLeftLat" );
            $dataUpperLeftLat = $dataUpperLeftLats->item(0)->nodeValue;

            $dataUpperLeftLongs = $valor->getElementsByTagName( "dataUpperLeftLong" );
            $dataUpperLeftLong = $dataUpperLeftLongs->item(0)->nodeValue;

            $dataUpperRightLats = $valor->getElementsByTagName( "dataUpperRightLat" );
            $dataUpperRightLat = $dataUpperRightLats->item(0)->nodeValue;

            $dataUpperRightLongs = $valor->getElementsByTagName( "dataUpperRightLong" );
            $dataUpperRightLong = $dataUpperRightLongs->item(0)->nodeValue;

            $dataLowerLeftLats = $valor->getElementsByTagName( "dataLowerLeftLat" );
            $dataLowerLeftLat = $dataLowerLeftLats->item(0)->nodeValue;

            $dataLowerLeftLongs = $valor->getElementsByTagName( "dataLowerLeftLong" );
            $dataLowerLeftLong = $dataLowerLeftLongs->item(0)->nodeValue;

            $dataLowerRightLats = $valor->getElementsByTagName( "dataLowerRightLat" );
            $dataLowerRightLat = $dataLowerRightLats->item(0)->nodeValue;

            $dataLowerRightLongs = $valor->getElementsByTagName( "dataLowerRightLong" );
            $dataLowerRightLong = $dataLowerRightLongs->item(0)->nodeValue;


            $timeinic  = $imagingStartTime = $imagingStartTimes->item(0)->nodeValue;
            $time_desglo = explode(" ", $timeinic);
            $resultados= $time_desglo[0] . "-" . $time_desglo[1] . "-" . $time_desglo[2]  . "T" . substr($time_desglo[3] , 0 , 12)  . "+0000" ;


            $timefinal  = $imagingStopTime = $imagingStopTimes->item(0)->nodeValue;
            $time_desglo2 = explode(" ", $timefinal);
            $resultados2= $time_desglo2[0] . "-" . $time_desglo2[1] . "-" . $time_desglo2[2]  . "T" . substr($time_desglo2[3] , 0 , 12) . "+0000" ;

            $sensorId  = $sensorId = $sensorId2->item(0)->nodeValue;
            $time_desglo3 = explode(" ", $sensorId);
            $resultados3= substr($time_desglo3[0] , 0 , 3 ) ;

            // ERROR $name no está definido
            /*
            $name  = $file;
            $name2 = explode(" ", $name);
            $name3 = substr($name2[0] , 27, -4 );
            */

            if ($resultados3 =="PAN"):
                $shortName = "PMC";
                $operationalMode = "PAN";

            elseif ($resultados3 == "MSS"):
                $shortName = "PMC";
                $operationalMode = "MSS";

            else:/*sensor WMC*/
                $shortName = "WMC";
                $operationalMode = "MSS";
            endif;
        
        //}

        /*Primero Creo un arreglo donde guardare los valores de las etiquetas obtenidas del XML entrante*/
        $productMetas = array();
        $productMetas [] = array(
        //"status" => "ARCHIVED",
        //"status" => strtoupper($status), // ERROR $status no está definido
        "status" => strtoupper('PRUEBA'), // ERROR $status no está definido
        "identifier" => "ABAE",
        "type" => "QUICKLOOK",
        "productDate" => $productDate = $productDates->item(0)->nodeValue,
        "parentIdentifier" => "urn:ogc:def:EOP:ABAE",
        "beginPosition" => $resultados,
        "endPosition" =>  $resultados2,
        "shortName" => $satelliteId = $satelliteId2->item(0)->nodeValue,
        "shortName2" => $shortName,
        "operationalMode" => $operationalMode,
        "sensorType" => " ",
        "EPSG" => "EPSG:4326",
        "type" => "QUICKLOOK",
        "posList" => $dataUpperLeftLat = $dataUpperLeftLats->item(0)->nodeValue . " " . $dataUpperLeftLong = $dataUpperLeftLongs->item(0)->nodeValue . " " . $dataUpperRightLat = $dataUpperRightLats->item(0)->nodeValue . " " . $dataUpperRightLong = $dataUpperRightLongs->item(0)->nodeValue . " " . $dataLowerLeftLat = $dataLowerLeftLats->item(0)->nodeValue . " " . $dataLowerLeftLong = $dataLowerLeftLongs->item(0)->nodeValue . " " . $dataLowerRightLat = $dataLowerRightLats->item(0)->nodeValue . " " . $dataLowerRightLong = $dataLowerRightLongs->item(0)->nodeValue . " " . $dataUpperLeftLat = $dataUpperLeftLats->item(0)->nodeValue . " " . $dataUpperLeftLong = $dataUpperLeftLongs->item(0)->nodeValue
        );
        
    }

         /*Creamos el XML Saliente*/
        $doc = new DOMDocument("1.0", "UTF-8");

        /*Le indico al programa que le de formato al Documento para que no lo guarde de forma linear*/
        $doc->formatOutput = true;

        /*Creo el nodo padre o etiqueta Principal del Documento*/

        $root = $doc->createElementNS("http://miranda.abae.gob.ve", "eos:EarthObservation");
        $root->setAttribute("xmlns:eop","http://miranda.abae.gob.ve/index/popcontentinfo.html?conid=209608");
        $root->setAttribute("xmlns:gml","http://www.opengis.net/gml");
        $root->setAttribute("version","1.0");

        $doc->appendChild( $root );

         /*recorro el arreglo donde guarde los datos del XML entrante*/
        foreach( $productMetas as $valor ) {
            /*Declaro los Nodos Padres*/

            // 1-Creamos un nuevo elemento del árbol
            $metaDataProperty=$doc->createElement("gml:metaDataProperty");
            // 2-Lo guardamos y añadimos dentro del nivel de $root 
            $root->appendChild($metaDataProperty); 

            // 1-Creamos un nuevo elemento del árbol
            $EarthObservationMetaData=$doc->createElement("eop:EarthObservationMetaData"); 
            // 2-Lo guardamos y añadimos dentro del nivel de $metaDataProperty
            $metaDataProperty->appendChild($EarthObservationMetaData);

            // 1-Creamos un nuevo elemento del árbol
            $validTime=$doc->createElement("gml:validTime");
            // 2-Lo guardamos y añadimos dentro del nivel de $root
            $root->appendChild($validTime);

            // 1-Creamos un nuevo elemento del árbol
            $TimePeriod=$doc->createElement("gml:TimePeriod");
            // 2-Lo guardamos y añadimos dentro del nivel de $validTime 
            $validTime->appendChild($TimePeriod);

            // 1-Creamos un nuevo elemento del árbol
            $using=$doc->createElement("gml:using");
            // 2-Lo guardamos y añadimos dentro del nivel de $root
            $root->appendChild($using);

            // 1-Creamos un nuevo elemento del árbol
            $EarthObservationEquipment=$doc->createElement("eop:EarthObservationEquipment");
            // 2-Lo guardamos y añadimos dentro del nivel de $using
            $using->appendChild($EarthObservationEquipment);

            // 1-Creamos un nuevo elemento del árbol
            $platform=$doc->createElement("eop:platform"); 
            // 2-Lo guardamos y añadimos dentro del nivel de $EarthObservationEquipment
            $EarthObservationEquipment->appendChild($platform);

            // 1-Creamos un nuevo elemento del árbol
            $Platform=$doc->createElement("eop:Platform"); 
            // 2-Lo guardamos y añadimos dentro del nivel de $platform
            $platform->appendChild($Platform);

            // 1-Creamos un nuevo elemento del árbol
            $instrument=$doc->createElement("eop:instrument");
            // 2-Lo guardamos y añadimos dentro del nivel de $using y $EarthObservationEquipment
            $using->appendChild($instrument);
            $EarthObservationEquipment->appendChild($instrument);

            // 1-Creamos un nuevo elemento del árbol
            $Instrument=$doc->createElement("eop:Instrument");
            // 2-Lo guardamos y añadimos dentro del nivel de $instrument
            $instrument->appendChild($Instrument);

            // 1-Creamos un nuevo elemento del árbol
            $sensor=$doc->createElement("eop:sensor");
            // 2-Lo guardamos y añadimos dentro del nivel de $using y $EarthObservationEquipment 
            $using->appendChild($sensor);
            $EarthObservationEquipment->appendChild($sensor);

            // 1-Creamos un nuevo elemento del árbol
            $Sensor=$doc->createElement("eop:Sensor");
            // 2-Lo guardamos y añadimos dentro del nivel de $sensor
            $sensor->appendChild($Sensor);

            // 1-Creamos un nuevo elemento del árbol
            $target=$doc->createElement("gml:target");
            // 2-Lo guardamos y añadimos dentro del nivel de $root
            $root->appendChild($target);

            // 1-Creamos un nuevo elemento del árbol
            $Footprint=$doc->createElement("eop:Footprint");
            // 2-Lo guardamos y añadimos dentro del nivel de $target
            $target->appendChild($Footprint);

            // 1-Creamos un nuevo elemento del árbol
            $referenceSystemIdentifier=$doc->createElement("eop:referenceSystemIdentifier","EPSG:4326");
            $ElementAttribute = $doc->createAttribute("codeSpace");
            $ElementAttribute->value = "EPSG";
            $referenceSystemIdentifier->appendChild($ElementAttribute);
            // 2-Lo guardamos y añadimos dentro del nivel de $target
            $Footprint->appendChild($referenceSystemIdentifier);

            // 1-Creamos un nuevo elemento del árbol
            $multiExtentOf=$doc->createElement("gml:multiExtentOf");
            // 2-Lo guardamos y añadimos dentro del nivel de $Footprint
            $Footprint->appendChild($multiExtentOf);

            // 1-Creamos un nuevo elemento del árbol
            $surfaceMembers=$doc->createElement("gml:surfaceMembers");
            // 2-Lo guardamos y añadimos dentro del nivel de $MultiSurface
            $multiExtentOf->appendChild($surfaceMembers);

            // 1-Creamos un nuevo elemento del árbol
            $Polygon=$doc->createElement("gml:Polygon");
            // 2-Lo guardamos y añadimos dentro del nivel de $surfaceMembers
            $surfaceMembers->appendChild($Polygon);

            // 1-Creamos un nuevo elemento del árbol
            $exterior=$doc->createElement("gml:exterior");
            // 2-Lo guardamos y añadimos dentro del nivel de $Polygon
            $Polygon->appendChild($exterior);

            // 1-Creamos un nuevo elemento del árbol
            $LinearRing=$doc->createElement("gml:LinearRing");
            // 2-Lo guardamos y añadimos dentro del nivel de $exterior
            $exterior->appendChild($LinearRing);

            // 1-Creamos un nuevo elemento del árbol
            $fileName=$doc->createElement("eop:fileName","ICON.JPG" );
            // 2-Lo guardamos y añadimos dentro del nivel de $exterior
            $root->appendChild($fileName);

            // 1-Creamos un nuevo elemento del árbol
            $fileName=$doc->createElement("eop:fileName","PREVIEW.JPG" );
            // 2-Lo guardamos y añadimos dentro del nivel de $exterior
            $root->appendChild($fileName);

            // 1-Creamos un nuevo elemento del árbol
            $BrowseInformation=$doc->createElement("eop:BrowseInformation" );
            // 2-Lo guardamos y añadimos dentro del nivel de $exterior
            $root->appendChild($BrowseInformation);

            /*----------------------------------------------------------------------------------------------------*/

            /*Declaro las etiquetas dentro de los nodos*/

            // 1-Creamos un nuevo elemento llamado eop:identifier
            $identifier=$doc->createElement("eop:identifier");
            // 2-Le asigno el valor del arreglo llamado $productMetas
            $identifier->appendChild(
            $doc->createTextNode( $valor["identifier"] )
            );
            // 3-Lo añadimos dentro del nodo $metaDataProperty y $EarthObservationMetaData 
            $metaDataProperty->appendChild($identifier);
            $EarthObservationMetaData->appendChild($identifier);

            // 1-Creamos un nuevo elemento llamado eop:parentIdentifier
            $parentIdentifier=$doc->createElement("eop:parentIdentifier");
            // 2-Le asigno el valor del arreglo llamado $productMetas 
            $parentIdentifier->appendChild(
            $doc->createTextNode( $valor["parentIdentifier"] )
            );
            // 3-Lo añadimos dentro del nodo $metaDataProperty y $EarthObservationMetaData 
            $metaDataProperty->appendChild($parentIdentifier);
            $EarthObservationMetaData->appendChild($parentIdentifier);

            // 1-Creamos un nuevo elemento llamado eop:status
            $status=$doc->createElement("eop:status");
            // 2-Le asigno el valor del arreglo llamado $productMetas 
            $status->appendChild(
            $doc->createTextNode( $valor["status"] )
            );
            // 3-Lo añadimos dentro del nodo $metaDataProperty y $EarthObservationMetaData 
            $metaDataProperty->appendChild($status);
            $EarthObservationMetaData->appendChild($status);

            //realizo las mismas instrucciones anteriormente realizadas con las otras etiquetas
            $beginPosition=$doc->createElement("gml:beginPosition"); 
            $beginPosition->appendChild(
            $doc->createTextNode( $valor["beginPosition"] )
            );
            $validTime->appendChild($beginPosition);
            $TimePeriod->appendChild($beginPosition);

            $endPosition=$doc->createElement("gml:endPosition"); 
            $endPosition->appendChild(
            $doc->createTextNode( $valor["endPosition"] )
            );
            $validTime->appendChild($endPosition);
            $TimePeriod->appendChild($endPosition);

            $shortName=$doc->createElement("eop:shortName"); 
            $shortName->appendChild(
            $doc->createTextNode( $valor["shortName"] )
            );
            $using->appendChild($shortName);
            $EarthObservationEquipment->appendChild($shortName);
            $platform->appendChild($shortName);
            $Platform->appendChild($shortName);

            $shortName=$doc->createElement("eop:shortName"); 
            $shortName->appendChild(
            $doc->createTextNode( $valor["shortName2"] )
            );
            $instrument->appendChild($shortName);
            $Instrument->appendChild($shortName);

            $sensorType=$doc->createElement("eop:sensorType"); 
            $sensorType->appendChild(
            $doc->createTextNode( $valor["sensorType"] )
            );
            $sensor->appendChild($sensorType);
            $Sensor->appendChild($sensorType);

            $operationalMode=$doc->createElement("eop:operationalMode"); 
            $operationalMode->appendChild(
            $doc->createTextNode( $valor["operationalMode"] )
            );
            $sensor->appendChild($operationalMode);
            $Sensor->appendChild($operationalMode);

            $posList=$doc->createElement("gml:posList"); 
            $posList->appendChild(
            $doc->createTextNode( $valor["posList"] )
            );
            $target->appendChild($posList);
            $Footprint->appendChild($posList);
            $multiExtentOf->appendChild($posList);
            $surfaceMembers->appendChild($posList);
            $Polygon->appendChild($posList);
            $exterior->appendChild($posList);
            $LinearRing->appendChild($posList);

            // 1-Creamos un nuevo elemento llamado eop:identifier
            $type=$doc->createElement("eop:type");
            // 2-Le asigno el valor del arreglo llamado $productMetas
            $type->appendChild(
            $doc->createTextNode( $valor["type"] )
            );
            // 3-Lo añadimos dentro del nodo $metaDataProperty y $EarthObservationMetaData
            $BrowseInformation->appendChild ($type);
        }
        
        //$xml_charter = htmlentities($doc->saveXML());
        $xml_charter = $doc->saveXML();
        return strval($xml_charter);
    }
}