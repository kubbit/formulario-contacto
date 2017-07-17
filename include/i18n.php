<?php

$translation = array
(
	'Error' => array
	(
		'es' => 'Error',
		'eu' => 'Errorea'
	),
	'Nombre' => array
	(
		'es' => 'Nombre',
		'eu' => 'Izena'
	),
	'Apellidos' => array
	(
		'es' => 'Apellidos',
		'eu' => 'Abizenak'
	),
	'Email' => array
	(
		'es' => 'Email',
		'eu' => 'Posta-e'
	),
	'Teléfono' => array
	(
		'es' => 'Teléfono',
		'eu' => 'Telefonoa'
	),
	'NIF' => array
	(
		'es' => 'NIF',
		'eu' => 'NAN'
	),
	'Mensaje' => array
	(
		'es' => 'Mensaje',
		'eu' => 'Mezua'
	),
	'Fichero' => array
	(
		'es' => 'Fichero',
		'eu' => 'Fitxategia'
	),
	'Modo respuesta' => array
	(
		'es' => 'Modo respuesta',
		'eu' => 'Erantzuteko modua'
	),
	'No responder' => array
	(
		'es' => 'No responder',
		'eu' => 'Erantzunik gabe'
	),
	'Correo electrónico' => array
	(
		'es' => 'Correo electrónico',
		'eu' => 'Posta elektroniko bidez'
	),
	'Por teléfono' => array
	(
		'es' => 'Por teléfono',
		'eu' => 'Telefonoz'
	),
	'Por escrito' => array
	(
		'es' => 'Por escrito',
		'eu' => 'Idatziz'
	),
	'Idioma' => array
	(
		'es' => 'Idioma',
		'eu' => 'Hizkuntza'
	),
	'Enviar' => array
	(
		'es' => 'Enviar',
		'eu' => 'Bidali'
	),
	'Formulario' => array
	(
		'es' => 'Formulario',
		'eu' => 'Formularioa'
	),
	'Es necesario completar este campo de formulario' => array
	(
		'es' => 'Es necesario completar este campo de formulario',
		'eu' => 'Eremu hau beharrezkoa da'
	),
	'Falta un campo requerido: %s' => array
	(
		'es' => 'Falta un campo requerido: %s',
		'eu' => 'Beharrezko eremu bat falta da: %s'
	),
	'El campo introducido no es válido (%s): Escribe un e-mail válido' => array
	(
		'es' => 'El campo introducido no es válido (%s): Escribe un e-mail válido',
		'eu' => 'Idatzitako balioa ez da zuzena (%s): Idatzi posta-e zuzena'
	),
	'El campo introducido no es válido (%s): Escribe un NIF válido' => array
	(
		'es' => 'El campo introducido no es válido (%s): Escribe un NIF válido',
		'eu' => 'Idatzitako balioa ez da zuzena (%s): Idatzi NAN zuzena'
	),
	'Envío correcto' => array
	(
		'es' => 'Envío correcto',
		'eu' => 'Bidalketa zuzena'
	),
	'Error durante el envío' => array
	(
		'es' => 'Error durante el envío',
		'eu' => 'Errorea bidaltzerakoan'
	)
);

function __($token, $lang = null)
{
	global $translation, $lang;

	if (!array_key_exists($token, $translation)
	 || !array_key_exists($lang, $translation[$token]))
		return $token;
	else
		return $translation[$token][$lang];
}

?>
