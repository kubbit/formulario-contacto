<?php
include_once 'formulario.class.php';
include_once 'i18n.php';

$lang = 'es';

if (filter_has_var(INPUT_GET, 'lang'))
	$lang = filter_input(INPUT_GET, 'lang');

$CAMPOS =
[
/*
 * 	[Texto, Tipo, Requerido, Valores (array), Id Campo, Valor]
 */
	[_('Nombre'), 'text', false, null, 'name'],
	[_('Apellidos'), 'text', true, null, 'surnames'],
	[_('Email'), 'email', false, null, 'mail'],
	[_('Teléfono'), 'text', false, null, 'phone'],
	[_('NIF'), 'nif', true, null, 'nid'],
	[_('Mensaje'), 'memo', true, null, 'message'],
	[_('Modo respuesta'), 'array', false, [0 => _('No responder'), 1 => _('Correo electrónico'), 2 => _('Por teléfono'), 3 => _('Por escrito')], 'reply'],
	[_('Idioma'), 'hidden', false, null, 'lang', $lang]
];
$formulario = new Formulario(Formulario::MODE_GERKUD, 'http://gerkud.example.org/horkonpon/', null, $CAMPOS);

include 'formulario.template.php';
?>
