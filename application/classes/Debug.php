<?php

	class Debug extends Kohana_Debug {

		public static function email($view) {
			$sLogFileName = 'email.log';
			$sLogFilePath = dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . $sLogFileName;
			$rFile = fopen($sLogFilePath, 'a+');
			fwrite($rFile, "New email " . $view . " " . date('d.m.Y H:i:s') . "\n");
			fwrite($rFile, "short_open_tag = " . ini_get('short_open_tag') . "\n");
			$sGlobalsDump = '';
			ob_start();
			var_dump($GLOBALS);
			$sGlobalsDump = ob_get_clean();
			fwrite($rFile, "\$GLOBALS = " . $sGlobalsDump . "\n");
			fwrite($rFile, "----------------------------------------------------------\n\n\n");
			fclose($rFile);
		}

	}