<?php include_once 'i18n.php'; ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<title><?php echo __('Formulario'); ?></title>
		<link rel="stylesheet" href="css/formularios.css" type="text/css" />
	</head>
	<body>
<?php if (count($formulario->errores) > 0): ?>
		<div class="error">
			<img src="img/error-white.png" alt="<?php echo __('Error'); ?>" />
		<?php foreach ($formulario->errores as $error): ?>
			<span><?php echo $error; ?></span>
		<?php endforeach; ?>
		</div>
<?php elseif (count($formulario->mensajes) > 0): ?>
		<div class="mensaje">
		<?php foreach ($formulario->mensajes as $mensaje): ?>
			<span><?php echo $mensaje; ?></span>
		<?php endforeach; ?>
		</div>
<?php endif; ?>
		<form method="post" enctype="multipart/form-data">
<?php foreach($formulario->CAMPOS as $campo): ?>
			<div class="field">
	<?php if ($campo[Formulario::PROP_TYPE] !== 'hidden'): ?>
				<label for="<?php echo $campo[Formulario::PROP_ID]; ?>" ><?php echo $campo[Formulario::PROP_TEXT]; ?>
		<?php if ($campo[Formulario::PROP_REQUIRED]): ?>
					<span title="<?php echo __('Es necesario completar este campo de formulario'); ?>">*</span>
		<?php endif; ?>
				</label>
	<?php endif; ?>
	<?php if (isset($formulario->errores[$campo[Formulario::PROP_ID]])): ?>
				<img src="img/error-red.png" alt="<?php echo __('Error'); ?>" />
	<?php endif; ?>
				<div class="flexible">
	<?php switch($campo[Formulario::PROP_TYPE]):
		?><?php case 'memo': ?>
					<textarea id="<?php echo $campo[Formulario::PROP_ID]; ?>" name="<?php echo $campo[Formulario::PROP_ID]; ?>" rows="5"><?php echo $campo[Formulario::PROP_VALUE]; ?></textarea>
		<?php break; ?>
		<?php case 'file': ?>
					<input id="<?php echo $campo[Formulario::PROP_ID]; ?>" type="<?php echo $campo[Formulario::PROP_TYPE]; ?>" name="<?php echo $campo[Formulario::PROP_ID]; ?>" />
		<?php break; ?>
		<?php case 'array': ?>
					<select id="<?php echo $campo[Formulario::PROP_ID]; ?>" name="<?php echo $campo[Formulario::PROP_ID]; ?>">
			<?php foreach ($campo[Formulario::PROP_DATA] as $key => $value): ?>
						<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php endforeach; ?>
					</select>
		<?php break; ?>
		<?php default: ?>
					<input id="<?php echo $campo[Formulario::PROP_ID]; ?>" type="<?php echo $campo[Formulario::PROP_TYPE]; ?>" name="<?php echo $campo[Formulario::PROP_ID]; ?>" value="<?php echo $campo[Formulario::PROP_VALUE]; ?>" />
		<?php break; ?>
	<?php endswitch; ?>
				</div>
			</div>
<?php endforeach; ?>
			<button type="submit" id="botonSubmit" class="boton"><span><?php echo __('Enviar'); ?></span></button>
		</form>
	</body>
</html>
