<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MdirectorController extends AbstractController
{

    public function __construct(HttpClientInterface $httpClient){
        $this->httpClient = $httpClient;
    }

    #[Route('/mdirector', name: 'app_mdirector')]
    //RECOGE LOS ENVÍOS
    public function index(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_delivery')
            ->setMethod('get')
            ->setParameters([
                'envId' => null, //Recoge el email o sms enviado con el envId correspondiente
                'camId' => null, //Recoge todos los mensajes enviados con el id de campaña correspondiente
                'campaignName' => null, //Lo mismo que el de arriba pero con nombre
                'date' => null, //Recoge todos los mensajes enviados en la fecha correspondiente
                'endDate' => null //Lo mismo que el de arriba pero con fecha final
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents())->data,

        ]);
    }

    #[Route('/mdirector/envio/crear', name: 'app_mdirector_envio_crear')]
    //SE USA PARA CREAR LO ENVÍOS ¡¡¡¡¡¡¡ESTO NO ENVÍA NADA, SOLO CREA LOS ENVÍOS. LOS ENVÍOS SE REALIZAN CON EL MÉTODO PROGRAMAR!!!!!!!!!!
    public function crearEnvio(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_delivery')
            ->setMethod('post')
            ->setParameters([
                //-------------- OBLIGATORIO ----------------
                'type' => 'email', //email, sms, smsflyer
                //-------------- OBLIGATORIO ----------------
                'name' => 'envio test awazon', //El nombre para identificar el envío de mensajes 
                //---------------- OBLIGATORIO EN EMAIL, NO APLICABLE EN SMS NI SMSFLYER ---------------- 
                'subject' => 'Mensaje para gente guapa', //Asunto del correo
                //----------------  OPCIONAL EN EMAIL, NO APLICABLE EN RESTO ----------------    
                'preheader' => null, //Sigo sin saber qué coño es el preheader, supongo que son las etiquetas meta o algo de eso \._./
                //---------------- OBLIGATORIO SI NO SE ESPECIFICA campaignName ---------------- 
                'campaign' => null, //El ID de la campaña ya existente
                //---------------- OBLIGATORIO SI NO SE ESPECIFICA campaign ---------------- 
                'campaignName' => 'Campaña de guapos en inteligentes', //Buscará una campaña con el nombre, si no la encuentra, creará una nueva y la asociará al envío
                //-------------- OBLIGATORIO ----------------
                'language' => 'es', //El idioma para el envío. Acepta códigos como "es", "en", "pt", "id"...
                //---------------- OBLIGATORIO SI NO SE ESPECIFICA templateId ---------------- 
                'creativity' => null, //El contenido de la creatividad (npi de lo que es) codificado en base64 (OBLIGATORIO). El formato puede ser html o zip. EN CASO DE SMS O SMSFLYER CORRESPONDE AL MENSAJE QUE RECIBIRÁ EL CONTACTO. En caso de smsflyer es imprescindible que el contenido del mensaje incluya la variable "[----SMS----URL----]"
                //---------------- OBLIGATORIO SI NO SE ESPECIFICA creativity ---------------- 
                'templateId' => 'bf98ca6c-b4c1-3df8-a339-43b6007029aa', //El ID de la plantilla cuyo contenido será importado en la creación del envío (ESTE CAMPO TIENE PREFERENCIA SOBRE CREATIVITY, ES DECIR, SI RELLENAS AMBOS, CREATIVITY SERÁ BASURILLA)
                //---------------- OPCIONAL si se especifica templateId y la plantilla tiene campos personalizables ---------------- 
                'templateVariables' => ['nombre' => 'guapo'], //Los valores de estos parámetros tienen prioridad sobre los valores de los campos personalizados. FORMATO ARRAY
                //---------------- OBLIGATORIO EN SMSFLYER, NO APLICABLE EN RESTO ---------------- 
                'flyerCreativity' => null, //El contenido de la creatividad enlazada al sms codificado en base64 (OBLIGATORIO)
                //-------------- OBLIGATORIO ----------------
                'segments' => "[\"LIST-5\"]", //La lista de identificadores de segmento para el envío (FORMATO JSON ["1", "66", "77"]). Es posible especificar listas con la etiqueta "LIST-", segmentos (a partir del id del segmento) y grupos de segmentos prefijando el identificador con la etiqueta "SEG_GRU-", por ejemplo: [ "LIST-23", "88", "SEG_GRU-1" ]. En este último ejemplo estaríamos especificando que el envío debe realizarse a la lista 23, al segmento 88 y al grupo 1.
                //-------------- OPCIONAL ----------------
                'addHeader' => null, //Valores 0 y 1 y determina si se añade cabecera de envío
                //-------------- OPCIONAL ----------------
                'addSpamHeader' => null, //Valores 0 y 1 y determina si se añade cabecera de spam
                //-------------- OPCIONAL ----------------
                'addGALinks' => null, //Valores 0 y 1 y determina si se añaden las variables para sincronizar con Google Analytics
                //-------------- OPCIONAL ----------------
                'tags' => "[\"Bellos\", \"Hermosos\"]", //Determina las etiquetas que clasificarán este envío (OBLIGATORIO JSON => ["Ladreones", "Piratas", "Primavera"])
                //---------------- OBLIGATORIO EN SMS Y SMSFLYER, OPCIONAL EN EMAIL ---------------- 
                'fromName' => "La persona más bella del mundo", //Especifica el nombre del remitente
                //---------------- OPCIONAL EMAIL, NO APLICABLE EN RESTO ---------------- 
                'customSender' => null, //Dirección de correo alternativa para el remitente. El envío tomará la dirección que especifiques aquí en lugar de la que está configurada en las preferencias de la empresa SOLO DISPONIBLE SI MDIRECTOR HABILITÓ LA OPCIÓN NOSOTROS NO LA TENEMOS
                //---------------- OPCIONAL EN EMAIL, NO APLICABLE EN RESTO ---------------- 
                'replyToName' => "Raúl el hermoso", //Especifica el nombre que aparecerá cuando van a responder al mensaje de correo
                //---------------- OPCIONAL EN EMAIL, NO APLICABLE EN RESTO ---------------- 
                'replyToEmail' => "rfernandez@betandeal.com", //Especifica el correo al que van a responder al mensaje de correo
                //---------------- OPCIONAL EN EMAIL, NO APLICABLE EN RESTO ---------------- 
                'creativityText' => null, //Contiene la versión en texto plano para la creatividad (OBLIGATORIO UTF-8)
                //---------------- OPCIONAL EN EMAIL, NO APLICABLE EN RESTO ---------------- 
                'autoCreativityText' => null, //Valores 0 y 1. Determina si MDirector creará una versión en texto de la creatividad de forma automática. Si se asigna en 1 se ignorará el contenido del parámetro CreativityText
                //-------------- OPCIONAL ----------------
                'reminder' => 1, //Valores 0 y 1. Determina si se envía un recordatorio con un resumen de las estadísticas del envío a las direcciones en reminderAddresses el día especificado en reminderDate
                //-------------- OPCIONAL ----------------
                'reminderDate' => '2025-03-22', //YYYY-MM-DD
                //-------------- OPCIONAL ----------------
                'reminderAddresses' => 'raulito72001@gmail.com,jespinosa@betandeal.com,amoratilla@betandeal.com', //Los correos separados por comas a los que se va a enviar el recordatorio
                //---------------- OPCIONAL EN SMS Y SMSFLYER ----------------
                'trackSmsLinks' => null, //Valores 0 y 1, determina si quieres que MDirector se encargue de acortar los enlaces y gestionarlos para ofrecerte estadísticas de clicks
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);


    }

    #[Route('/mdirector/envio/programar', name: 'app_mdirector_envio_programar')]
    //SE USA PARA PROGRAMAR LOS ENVÍOS
    public function programar(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_delivery')
            ->setMethod('put')
            ->setParameters([
                'envId' => 30, //OBLIGATORIO
                'date' => 'now', //OBLIGATORIO en formato 'YYYY-MM-DD' o 'now', en caso de que se quiera enviar al momento
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/envio/borrar', name: 'app_mdirector_envio_borrar')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function Borrar(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_delivery')
            ->setMethod('delete')
            ->setParameters([
                'envId' => 27, //OBLIGATORIO
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }
    
    // ---------------------------------------------------------------------------------------------------

    #[Route('/mdirector/contacto/crear', name: 'app_mdirector_contacto_crear')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function CrearContacto(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri(uri: 'https://api.mdirector.com/api_contact')
            ->setMethod('post')
            ->setParameters([
                //OPCIONAL
                'listId' => 5, //Identificador de la lista (si no se escribe, se usa la por defecto)
                //OPCIONAL
                'allow-unsubscribed' => null, //Determina si el contacto se daría de alta incluso aúnque ya estuviera en la lista de bajas. POR DEFECTO FALSE
                //OPCIONAL
                'send-notifications' => null, //Determina si, en caso de haber una campaña de registro vigente, se enviaría al contacto tras darse de alta. POR DEFECTO FALSE
                //OPCIONAL
                'hasDoubleOptIn' => null, //Se enviará un correo de confirmación al contacto de forma previa a la suscripción. El contacto no será dado de alta en la lista hasta que confirme la suscripción mediante un enlace incluido en este correo. POR DEFECTO FALSE
                //OPCIONAL
                'name' => null,
                //OPCIONAL
                'surname1' => null,
                //OPCIONAL
                'surname2' => null,
                //OPCIONAL
                'gender' => null, // M | F | X
                //OPCIONAL
                'birthday' => null, //DD-MM-AAAA
                //OPCIONAL
                'reference' => null,
                //OBLIGATORIO SI NO SE ESPECIFICA MOVIL, EN CASO CONTRARIO OPCIONAL
                'email' => 'jespinosa@betandeal.com',
                //OBLIGATORIO SI NO SE ESPECIFICA EMAIL, EN CASO CONTRARIO, OPCIONAL
                'movil' => null,
                //OPCIONAL
                'city' => null,
                //OPCIONAL
                'province' => null,
                //OPCIONAL
                'country' => null,
                //OPCIONAL
                'cp' => null, //Código postal
                //OPCIONAL
                'mdTags' => ['guapo', 'hermoso', 'bello', 'inteligente', 'majo', 'big boss'], //Etiquetas que se asignaran al contacto. Pueden ser enviadas en formato json: [ "etiquetaUno","etiquetaDos", "etiquetaTres"] o simplemente una lista de valores separados por comas: 'etiquetaUno, etiquetaDos, etiquetaTres'.
                //OPCIONAL
                '...' => null, //Añades los parámetros que te den la gana
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/contacto/modificar', name: 'app_mdirector_contacto_modificar')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function ModificarContacto(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri(uri: 'https://api.mdirector.com/api_contact')
            ->setMethod('put')
            ->setParameters([
                //OBLIGATORIO
                'listId' => 5, //Identificador de la lista
                //OBLIGATORIO
                'conId' => 1, //Identificador del contacto
                //OPCIONAL
                'name' => 'Raúl',
                //OPCIONAL
                'surname1' => null,
                //OPCIONAL
                'surname2' => null,
                //OPCIONAL
                'gender' => null, // M | F | X
                //OPCIONAL
                'birthday' => null, //DD-MM-AAAA
                //OPCIONAL
                'reference' => null,
                //OPCIONAL
                'email' => null,
                //OPCIONAL
                'movil' => null,
                //OPCIONAL
                'city' => null,
                //OPCIONAL
                'province' => null,
                //OPCIONAL
                'country' => null,
                //OPCIONAL
                'cp' => null, //Código postal
                //OPCIONAL
                'mdTags' => null, //Etiquetas que se asignaran al contacto. Pueden ser enviadas en formato json: [ "etiquetaUno","etiquetaDos", "etiquetaTres"] o simplemente una lista de valores separados por comas: 'etiquetaUno, etiquetaDos, etiquetaTres'.
                //OPCIONAL
                '...' => null, //Añades los parámetros que te den la gana
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/contacto/get', name: 'app_mdirector_contacto_get')]
    //SE USA PARA OBTENER UN CONTACTO
    public function GetContactos(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri(uri: 'https://api.mdirector.com/api_contact')
            ->setMethod('get')
            ->setParameters([
                //OBLIGATORIO
                'listId' => 5, //Identificador de la lista POR DEFECTO 1
                //OBLIGATORIO SI NO SE ESPECIFICA MOBILE, EN CASO CONTRARIO, OPCIONAL
                'email' => 'jespinosa@betandeal.com',
                //OBLIGATORIO SI NO SE ESPECIFICA EMAIL, EN CASO CONTRARIO, OPCIONAL
                'mobile' => null,
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/contacto/borrar', name: 'app_mdirector_contacto_borrar')]
    //SE USA PARA BORRAR UN CONTACTO
    public function borrarContacto(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri(uri: 'https://api.mdirector.com/api_contact')
            ->setMethod('delete')
            ->setParameters([
                //OBLIGATORIO
                'listId' => null, //Identificador de la lista
                //OBLIGATORIO SI UNSUBSCRIBE ES FALSE O SE ESPECIFICA SUBID
                'conId' => null, //Identificador del contacto
                //OBLIGATORIO SI UNSUBSCRIBE ES TRUE
                'email' => null,
                //OBLIGATORIO SI UNSUBSCRIBE ES TRUE
                'mobile' => null,
                //OPCIONAL
                'unsubscribe' => null, //Determina si debe tratarse la baja como una desuscripción. POR DEFECTO FALSE
                //OBLIGATORIO
                'reason' => null, //Motivo de la baja
                //OPCIONAL
                'subId' => null, //Identificador del subenvío. Si especifica este parámetro debe también especificar los parámetros 'listId' y 'conId'
                //OPCIONAL
                'ip' => null, //IP del contacto
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents())->data,

        ]);
    }

    // ------------------------------------------------------------------------------------

    #[Route('/mdirector/lista/get', name: 'app_mdirector_lista_get')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function getLista(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_list')
            ->setMethod('get')
            ->setParameters([])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents())->lists,

        ]);
    }

    #[Route('/mdirector/lista/crear', name: 'app_mdirector_lista_crear')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function crearLista(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_list')
            ->setMethod('post')
            ->setParameters([
                'listName' => 'testRaulSymfony2',
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/lista/cambiar', name: 'app_mdirector_lista_cambiar')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function changeLista(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_list')
            ->setMethod('put')
            ->setParameters([
                'listName' => 'cambioTestRaulSymfony3',
                'listId' => 7,
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

    #[Route('/mdirector/lista/borrar', name: 'app_mdirector_lista_borrar')]
    //SE USA PARA BORRAR UN ENVÍO PROGRAMADO
    public function deleteLista(): Response
    {

        $companyId = '118312';
        $secret = '9264f852d5409bfc8edad97f2cf5f208d147acdc746a0a9f096a2c8c29ddabe2f056bfc0592b4c32';

        $client = (new \MDOAuth\OAuth2\Wrapper\MDirector\Factory())->create($companyId, $secret);
        $response = $client->setUri('https://api.mdirector.com/api_list')
            ->setMethod('delete')
            ->setParameters([
                'listId' => [3], //El id de la lista a modificar. También es posible enviar una lista de identificadores para eliminar varias listas de forma simultánea. Pueden ser enviados en formato json, ej: [ 123, 456, 789 ]
            ])
            ->setUserAgent('MyOwnUserAgent 1.0')
            ->request();

        return $this->render('mdirector/index.html.twig', [
            'controller_name' => 'MdirectorController',
            'data'=> json_decode($response->getBody()->getContents()),

        ]);
    }

}
