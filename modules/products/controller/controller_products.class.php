<?php

    session_start();

  include ($_SERVER['DOCUMENT_ROOT'] . "/proyecto_v3/modules/products/utils/functions_products.inc.php");
  include ($_SERVER['DOCUMENT_ROOT'] . "/proyecto_v3/utils/upload.php");
  include ($_SERVER['DOCUMENT_ROOT'] . "/proyecto_v3/utils/common.inc.php");

  if ((isset($_GET["upload"])) && ($_GET["upload"] == true)) {
      $result_avatar = upload_files();
      $_SESSION['result_avatar'] = $result_avatar;
      echo json_encode($result_avatar);
  		//exit;
  }

  if ((isset($_POST['submit_products_json']))) {
	  submit_products();
	}


	function submit_products() {

	    	$jsondata = array();
	    	$productsJSON = json_decode($_POST["submit_products_json"], true);
        $result = validate_products($productsJSON);

				if (empty($_SESSION['result_avatar'])) {
		        $_SESSION['result_avatar'] = array('resultado' => true, 'error' => "", 'datos' => 'media/default-avatar.png');
		    }
    	    	$result_avatar = $_SESSION['result_avatar'];

    	    	if (($result['resultado']) && ($result_avatar['resultado'])) {
                    $arrArgument = array(
                        'cod_prod' => ucfirst($result['datos']['cod_prod']),
                        'name_prod' => ucfirst($result['datos']['name_prod']),
                        'description' => $result['datos']['description'],
                        'color' => $result['datos']['color'],
                        'categoria' => $result['datos']['categoria'],
                        'ciudad' => strtoupper($result['datos']['ciudad']),
                        'province' => strtoupper($result['datos']['province']),
                        'pais' => strtoupper($result['datos']['pais']),
                        'price' => $result['datos']['price'],
                        'date' => $result['datos']['date'],
                        'date_c' => $result['datos']['date_c'],
                        'avatar' => $result_avatar['datos']
                    );


    /////////////////insert into BD////////////////////////
                    $arrValue = false;
                    $path_model = $_SERVER['DOCUMENT_ROOT'] . '/proyecto_v3/modules/products/model/model/';
                    $arrValue = loadModel($path_model, "products_model", "create_products", $arrArgument);
                    //echo json_encode($arrValue);
                    //exit;

                    if ($arrValue)
                        $mensaje = "Su registro se ha efectuado correctamente, para finalizar compruebe que ha recibido un correo de validacion y siga sus instrucciones";
                    else
                        $mensaje = "No se ha podido realizar su alta. Intentelo mas tarde";


                    $_SESSION['products'] = $arrArgument;
                    $_SESSION['msje'] = $mensaje;
                    $callback = "index.php?module=products&view=results";

                    $jsondata["success"] = true;
                    $jsondata["redirect"] = $callback;
                    echo json_encode($jsondata);
                } else {
                    //$error = $result['error'];
                    //$error_avatar = $result_avatar['error'];
                    $jsondata["success"] = false;
                    $jsondata["error"] = $result['error'];
                    $jsondata["error_avatar"] = $result_avatar['error'];

                    $jsondata["success1"] = false;
                    if ($result_avatar['resultado']) {
                        $jsondata["success1"] = true;
                        $jsondata["img_avatar"] = $result_avatar['datos'];
                    }
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode($jsondata);
                }

			}


////////////////////////////
if (isset($_GET["delete"]) && $_GET["delete"] == true) {

    $_SESSION['result_avatar'] = array();
	$result = remove_files();
	//echo json_encode($result);
	if ($result === true) {
        echo json_encode(array("res" => true));
    } else {
        echo json_encode(array("res" => false));
    }

}
if (isset($_GET["load"]) && $_GET["load"] == true) {

    $jsondata = array();
    if (isset($_SESSION['products'])) {
        //echo debug($_SESSION['user']);
        $jsondata["products"] = $_SESSION['products'];
    }
    if (isset($_SESSION['msje'])) {
        //echo $_SESSION['msje'];
        $jsondata["msje"] = $_SESSION['msje'];
    }
    close_session();
    echo json_encode($jsondata);
    //exit;
}

function close_session() {
    unset($_SESSION['products']);
    unset($_SESSION['msje']);
    $_SESSION = array(); // Destruye todas las variables de la sesión
    session_destroy(); // Destruye la sesión
}

/////////////////////////////////////////////////// load_data
if ((isset($_GET["load_data"])) && ($_GET["load_data"] == true)) {

    $jsondata = array();

    if (isset($_SESSION['products'])) {
        $jsondata["products"] = $_SESSION['products'];
        echo json_encode($jsondata);
        //exit;
    } else {
        $jsondata["products"] = "";
        echo json_encode($jsondata);
        //exit;
    }


}

if(  (isset($_GET["load_pais"])) && ($_GET["load_pais"] == true)  ){
  $json = array();

    $url = 'http://www.oorsprong.org/websamples.countryinfo/CountryInfoService.wso/ListOfCountryNamesByName/JSON';

  $path_model=$_SERVER['DOCUMENT_ROOT'].'/proyecto_v3/modules/products/model/model/';
  $json = loadModel($path_model, "products_model", "obtain_paises", $url);

  if($json){

  if (preg_match('/Error/',$json)) {
  $json = "error";

  }
    echo $json;
    //exit;
  }else{
    $json = "error";
    echo $json;
    //exit;
  }
}

/////////////////////////////////////////////////// load_provincias
if(  (isset($_GET["load_provincias"])) && ($_GET["load_provincias"] == true)  ){
  $jsondata = array();
      $json = array();

  $path_model=$_SERVER['DOCUMENT_ROOT'].'/proyecto_v3/modules/products/model/model/';
  $json = loadModel($path_model, "products_model", "obtain_provincias");

  if($json){
    $jsondata["province"] = $json;
    echo json_encode($jsondata);
    //exit;
  }else{
    $jsondata["province"] = "error";
    echo json_encode($jsondata);
    //exit;
  }
}

/////////////////////////////////////////////////// load_poblaciones
if(  isset($_POST['idPoblac']) ){
    $jsondata = array();
      $json = array();

  $path_model=$_SERVER['DOCUMENT_ROOT'].'/proyecto_v3/modules/products/model/model/';
  $json = loadModel($path_model, "products_model", "obtain_poblaciones", $_POST['idPoblac']);

  if($json){
    $jsondata["ciudad"] = $json;
    echo json_encode($jsondata);
    //exit;
  }else{
    $jsondata["ciudad"] = "error";
    echo json_encode($jsondata);
    //exit;
  }
}
