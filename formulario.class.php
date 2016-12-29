<?php
include_once 'i18n.php';

//const DEBUG_MODE = true;

class Formulario
{
	const PROP_TEXT = 0;
	const PROP_TYPE = 1;
	const PROP_REQUIRED = 2;
	const PROP_DATA = 3;
	const PROP_ID = 4;
	const PROP_VALUE = 5;

	const MODE_MAIL = 0;
	const MODE_GERKUD = 1;

	var $errores = array();
	var $mensajes = array();
	var $CAMPOS;

	private $sendto;
	private $subject;
	private $mode;

	function Formulario($mode, $sendto, $subject, $campos)
	{
		$this->mode = $mode;
		$this->sendto = $sendto;
		$this->subject = $subject;
		$this->CAMPOS = $campos;

		for ($i = 0; $i < count($this->CAMPOS); $i++)
		{
			if (!isset($this->CAMPOS[$i][self::PROP_ID]))
				$this->CAMPOS[$i][self::PROP_ID] = preg_replace('/ /', '_', strtolower($this->CAMPOS[$i][self::PROP_TEXT]));

			if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST' && $this->CAMPOS[$i][self::PROP_TYPE] !== 'file')
				$this->CAMPOS[$i][self::PROP_VALUE] = filter_input(INPUT_POST, $this->CAMPOS[$i][self::PROP_ID]);
			else if (!array_key_exists(self::PROP_VALUE, $this->CAMPOS[$i]))
				$this->CAMPOS[$i][self::PROP_VALUE] = '';
		}

		if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST')
		{
			$this->validar();
			if (count($this->errores) == 0)
				$this->enviar();
		}
	}

	function validar()
	{
		foreach ($this->CAMPOS as $campo)
		{
			if ($campo[self::PROP_REQUIRED] && empty(filter_input(INPUT_POST, $campo[self::PROP_ID])) && empty($_FILES[$campo[self::PROP_ID]]['name']))
				$this->errores[$campo[self::PROP_ID]] = sprintf(_('Falta un campo requerido: %s'), $campo[self::PROP_TEXT]);

			if (!empty(filter_input(INPUT_POST, $campo[self::PROP_ID])) && $campo[self::PROP_TYPE] === 'email' && !filter_var(filter_input(INPUT_POST, $campo[self::PROP_ID]), FILTER_VALIDATE_EMAIL))
				$this->errores[$campo[self::PROP_ID]] = sprintf(_('El campo introducido no es válido (%s): Escribe un e-mail válido'), $campo[self::PROP_TEXT]);

			if (!empty(filter_input(INPUT_POST, $campo[self::PROP_ID])) && $campo[self::PROP_TYPE] === 'nif' && !$this->validarNIF(filter_input(INPUT_POST, $campo[self::PROP_ID])))
				$this->errores[$campo[self::PROP_ID]] = sprintf(_('El campo introducido no es válido (%s): Escribe un NIF válido'), $campo[self::PROP_TEXT]);
		}
	}

	function validarNIF($dni)
	{
		if (strlen($dni) != 9)
			return false;

		$dni = strtoupper($dni);

		$letra = substr($dni, -1, 1);
		$numero = substr($dni, 0, 8);

		// Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
		$numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);

		$modulo = $numero % 23;
		$letras_validas = 'TRWAGMYFPDXBNJZSQVHLCKE';
		$letra_correcta = substr($letras_validas, $modulo, 1);

		if ($letra_correcta != $letra)
			return false;

		return true;
	}

	function enviar()
	{
		switch ($this->mode)
		{
			case self::MODE_MAIL:
				$this->enviarMail();
				break;
			case self::MODE_GERKUD:
				$this->enviarGerkud();
				break;
		}
	}

	function enviarMail()
	{
		$boundary = sprintf('_%s_', md5(date('r', time())));
		$headers = array();
		$attachments = array();
		foreach ($this->CAMPOS as $campo)
		{
			if ($campo[self::PROP_TYPE] === 'email' && !empty($campo[self::PROP_VALUE]))
			{
				$usuario = $campo[self::PROP_VALUE];

				$headers[] = sprintf('From: %s', $usuario);
			}
			else if ($campo[self::PROP_TYPE] === 'file' && !empty($_FILES[$campo[self::PROP_ID]]['name']))
			{
				$file = $_FILES[$campo[self::PROP_ID]];
				$handle = fopen($file['tmp_name'], "r");
				$contents = fread($handle, $file['size']);

				// generar adjunto MIME usando el contenido del fichero
				$attachment = array();
				$attachment[] = '';
				$attachment[] = sprintf('--%s', $boundary);
				$attachment[] = sprintf('Content-Disposition: attachment; filename="%s"', $file['name']);
				$attachment[] = sprintf('Content-Type: application/octet-stream', $file['type']);
				$attachment[] = 'Content-Transfer-Encoding: base64';
				$attachment[] = '';
				$attachment[] = chunk_split(base64_encode($contents));

				$attachments[] = implode("\r\n", $attachment);
			}
		}

		$body = array();

		// si contiene adjuntos, el tipo de contenido pasa a ser multipart/mixed
		if (count($attachments) == 0)
			$headers[] = 'Content-Type: text/plain; charset="UTF-8"';
		else
		{
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = sprintf('Content-Type: multipart/mixed; boundary="%s"', $boundary);

			$body[] = sprintf('--%s', $boundary);
			$body[] = 'Content-Type: text/plain; charset="UTF-8"';
			$body[] = '';
		}

		// generar cuerpo del mensaje con contenido de texto
		for ($i = 0; $i < count($this->CAMPOS); $i++)
			if (!empty($this->CAMPOS[$i][self::PROP_VALUE]))
				$body[] = sprintf('%s: %s', $this->CAMPOS[$i][self::PROP_TEXT], $this->CAMPOS[$i][self::PROP_VALUE]);

		if (count($attachments) > 0)
		{
			// añadir todos los adjuntos al cuerpo de mensaje
			foreach ($attachments as $attachment)
				$body[] = $attachment;

			// fin de multiparte
			$body[] = sprintf('--%s--', $boundary);
		}

		// enviar correo
		try
		{
			if (mail($this->sendto, $this->subject, implode("\r\n", $body), implode("\r\n", $headers)))
			{
				$this->mensajes[] = 'El formulario se ha procesado correctamente';
				$this->enviarRecibo($usuario);
			}
			else
				$this->errores[] = 'Error al envíar el correo';
		}
		catch (Exception $e)
		{
			$this->errores[] = $e->getMessage();
		}
	}

	function enviarGerkud()
	{
		$json = Array();
		$user = Array();

		$json['version'] = 2;

		foreach ($this->CAMPOS as $campo)
		{
			switch ($campo[self::PROP_ID])
			{
				case 'name':
					$user['fullname'] = $campo[self::PROP_VALUE];
					break;
				case 'surnames':
					$user['surnames'] = $campo[self::PROP_VALUE];
					break;
				case 'phone':
					$user['phone'] = $campo[self::PROP_VALUE];
					break;
				case 'mail':
					$user['mail'] = $campo[self::PROP_VALUE];
					break;
				case 'nid':
					$user['nid'] = $campo[self::PROP_VALUE];
					break;
				case 'message':
					$json['comments'] = $campo[self::PROP_VALUE];
					break;
				case 'reply':
					$user['notify'] = $campo[self::PROP_VALUE];
					break;
				case 'lang':
					$user['language'] = $campo[self::PROP_VALUE];
					break;
			}
		}

		$json['user'] = $user;

		$data = array();
		$data['data'] = json_encode($json);
		$data['key'] = '37d12d33075dc1ecb558042391bc3676';

		if (defined('DEBUG_MODE'))
			$this->errores[] = $data['data'];

		if ($this->post($data))
			$this->mensajes[] = _('Envío correcto');
		else
			$this->errores[] = _('Error durante el envío');
	}

	function post($data)
	{
		$url = $this->sendto;

		$options = array
		(
			'http' => array
			(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		if (defined('DEBUG_MODE'))
			$this->errores[] = $result;

		if ($result === FALSE)
			return false;

		$json = json_decode($result, true);

		if (defined('DEBUG_MODE'))
			$this->errores[] = $json['message'];

		return $json['status'] === 0;
	}

	function enviarRecibo($usuario)
	{
		$headers = array();
		$headers[] = 'Content-Type: text/plain; charset="UTF-8"';
		$headers[] = sprintf('From: %s', $this->sendto);

		$body = array();
		$body[] = 'Hola:';
		$body[] = '';
		$body[] = '';
		$body[] = 'Hemos recibido su solicitud correctamente. Le daremos una respuesta lo antes posible.';
		$body[] = '';
		$body[] = 'Muchas gracias por su interés.';
		$body[] = '';
		$body[] = '';
		$body[] = 'Recibe un saludo cordial.';

		// enviar correo de recibo al usuario
		try
		{
			mail($usuario, 'Solucitud recibida', implode("\r\n", $body), implode("\r\n", $headers));
		}
		catch (Exception $e)
		{
			$this->errores[] = $e->getMessage();
		}
	}
}
?>
