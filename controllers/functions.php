<?php 
require_once __DIR__ . '/../config/database.php';

/**
 * Obtiene datos de posiciones laborales desde la base de datos y los devuelve como una cadena codificada en JSON.
 *
 * @return string Cadena codificada en JSON que contiene los detalles de las posiciones laborales.
 */
function obtenerDatosPosiciones() {
    global $conn;
    $query = "SELECT JobTitleID, JobTitleCode, JobTitleDescription FROM Table_JobTitles";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $resultados = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultado = array(
            "name" => $row["JobTitleDescription"],
            "description" => $row["JobTitleDescription"],
            "id" => $row["JobTitleID"],
            "code" => $row["JobTitleCode"]
        );
        $resultados[] = $resultado;
    }

    return json_encode($resultados);
}

/**
 * Genera un ejemplo de posicion laboral y lo devuelve como una cadena codificada en JSON.
 *
 * @return string Cadena codificada en JSON que contiene el detalle de la posicion laboral generada.
 */
function obtenerDatosPosiciones_Test() {
    $resultados = array();

    $resultado = array(
        "name" => 'TituloPosicion',
        "description" => 'DescripcionPosicion',
        "id" => '000000019933',
        "code" => '000000019933'
    );
    $resultados[] = $resultado;
    
    return json_encode($resultados);
}

/**
 * Obtiene datos de trabajadores desde la base de datos y los devuelve como una cadena codificada en JSON.
 *
 * @return string Cadena codificada en JSON que contiene los detalles de los trabajadores.
 */
function obtenerDatosTrabajadores() {
    global $conn;
    $query = "SELECT
    Usuario.EmployeeID AS idEmpleado, 
    Usuario.Name AS Nombre, 
    Usuario.SecondName1 + ' ' + Usuario.SecondName2 AS Apellido, 
    UserEmail.EmailAddress as Correo,
    CASE Empleado.Sex WHEN 'M' THEN 'Mujer' WHEN 'H' THEN 'Hombre' ELSE 'Otro' END AS Genero, 
    Direcciones.CompleteAddress AS Calle,
    Direcciones.AddressStreetNumber AS Numero,
    Direcciones.AddressPostalCode AS CodigoPostal,
    Direcciones.AddressMunicipality AS Ciudad,
    Direcciones.AddressProvince AS Provincia,
    Direcciones.AddressCountryName AS Pais,
    CONVERT(VARCHAR, DatosEmpleado.BirthDate, 23) AS Nacimiento
    FROM [A3LABORAL].[dbo].[A3VEmployees] AS Usuario
    INNER JOIN [A3LABORAL].[dbo].[A3VEmail1] AS UserEmail ON UserEmail.EmployeeID = Usuario.EmployeeID
    INNER JOIN [A3LABORAL].[dbo].[Employees] AS Empleado ON Empleado.EmployeeID = Usuario.EmployeeID
    INNER JOIN [A3LABORAL].[dbo].[A3VEmployeesWGP_PersonalIdentification] AS DatosEmpleado ON DatosEmpleado.EmployeeID = Usuario.EmployeeID
    INNER JOIN [A3LABORAL].[dbo].[A3VEmployeesWGP_Address] AS Direcciones  ON Direcciones.EmployeeID = Usuario.EmployeeID";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $resultados = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultado = array(
            "id" => $row["idEmpleado"],
            "name" => $row["Nombre"],
            "lastName" => $row["Apellido"],
            "email" => array(
                "professional" => $row["Correo"]
            ), 
            "extraData" => array(
                "genero" => $row["Genero"],
                "calle" => $row["Calle"],
                "numero" => $row["Numero"], 
                "codigopostal" => $row["CodigoPostal"], 
                "ciudad" => $row["Ciudad"], 
                "provincia" => $row["Provincia"], 
                "pais" => $row["Pais"], 
                "nacimiento" => $row["Nacimiento"],
                "nivelformativo" => ""
            )
        );

        $resultados[] = $resultado;
    }
    return json_encode($resultados);
}

/**
 * Genera un dato de un trabajodor de ejmplo y lo devuelve como una cadena codificada en JSON.
 *
 * @return string Cadena codificada en JSON que contiene los detalles del trabajador generado.
 */
function obtenerDatosTrabajadores_Test() {
    $resultados = array();

    $resultado = array(
        "id" => "A89F7A3E-129C-4499-83EA-2C5399EDFF1D",
        "name" => "Nombre Prueba",
        "lastName" => "Apellidos",
        "email" => array(
            "professional" => ""
        ), 
        "extraData" => array(
            "genero" => "Hombre",
            "calle" => "Calle Test",
            "numero" => "1", 
            "codigopostal" => "04700", 
            "ciudad" => "El Ejido", 
            "provincia" => "Almeria", 
            "pais" => "España", 
            "nacimiento" => "1999-02-06",
            "nivelformativo" => "",
        )
        
    );

        $resultados[] = $resultado;
    
    return json_encode($resultados);
}

/**
 * Envia los datos a través de una solicitud POST usando cURL.
 *
 * @param string $url     La URL a la cual se enviarán los datos.
 * @param array  $data    Los datos que se enviarán en el cuerpo de la solicitud POST.
 * @param string $secret  El secreto que se enviará como encabezado 'x-secret'.
 */
function enviarDatos($url, $data, $secret) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'x-secret: '.$secret
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}


