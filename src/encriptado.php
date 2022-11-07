<?php
    namespace App;
    use Defuse\Crypto\Crypto;
    use Defuse\Crypto\Key;
    use Defuse\Crypto\Key\Exception\WrongKeyOrModifiedCiphertextException;

class encriptado{


    public function encriptar($texto){
	$contenido = "def00000129d8aeeecf55280ee9f097ee81ec16a06fe9710f855975d82246968bf9cffd14c436855527c81d2b57072d6a9dcc9969eae69a486ee6e3745d763ff1d06e09e";
	$clave = Key::loadFromAsciiSafeString($contenido);
	$mensajeCifrado = Crypto::encrypt($texto, $clave);
	return $mensajeCifrado;
    }

    public function desencriptar($texto){
    	$contenido = "def00000129d8aeeecf55280ee9f097ee81ec16a06fe9710f855975d82246968bf9cffd14c436855527c81d2b57072d6a9dcc9969eae69a486ee6e3745d763ff1d06e09e";
	$clave = Key::loadFromAsciiSafeString($contenido);
	try {
    		$mensajeCifrado = Crypto::decrypt($texto, $clave);
	} catch (Ex\BadFormatException $e) {
    		exit("Los datos están corruptos o la clave es incorrecta");
	}
	return $mensajeCifrado;
    }

    public function cambiarBarras($texto){
        for ($i=0; $i <= (strlen($texto)-1); $i++) {
            if ($texto[$i]=='/'){
                $texto[$i]='-';
            }
	    if ($texto[$i]==''){
                $texto[$i]='_';
            }

        }
        return $texto;
    }

    public function ingresarBarras($texto){
        for ($i = 0; $i <= (strlen($texto)-1); $i++) {
            if ($texto[$i]=='-'){
                $texto[$i]='/';
            }
	    if ($texto[$i]=='_'){
                $texto[$i]='';
            }
        }
        return $texto;
    }
}