<?php
include_once 'include/formulario.class.php';
include_once 'include/i18n.php';

$lang = 'es';

if (filter_has_var(INPUT_GET, 'lang'))
	$lang = filter_input(INPUT_GET, 'lang');

$CAMPOS =
[
/*
 * 	[Texto, Tipo, Requerido, Valores (array), Id Campo, Valor]
 */
	[__('Nombre'), 'text', false, null, 'name'],
	[__('Apellidos'), 'text', true, null, 'surnames'],
	[__('Email'), 'email', false, null, 'mail'],
	[__('Teléfono'), 'text', false, null, 'phone'],
	[__('NIF'), 'nif', true, null, 'nid'],
	[__('Mensaje'), 'memo', true, null, 'message'],
	[__('Modo respuesta'), 'array', false, [0 => __('No responder'), 1 => __('Correo electrónico'), 2 => __('Por teléfono'), 3 => __('Por escrito')], 'reply'],
	[__('Idioma'), 'hidden', false, null, 'lang', $lang]
];
$formulario = new Formulario(Formulario::MODE_GERKUD, 'http://gerkud.example.org/horkonpon/', null, $CAMPOS);

include 'include/formulario.template.php';
?>
