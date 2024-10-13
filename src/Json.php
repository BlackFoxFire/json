<?php

/**
 * Json.php
 * @Auteur: Christophe Dufour
 * 
 * Facilite l'utilisation du format JSON
 */

namespace Blackfox\Json;

use Blackfox\Json\Enums\AccessMode;

class Json implements \ArrayAccess
{

    /**
     * Propriétés
     */

    // Un tableau associatif représentant un code json
    protected array $json = [];

    /**
     * Constructeur
     * 
     * @param array|object|string $data
     */
    public function __construct(array|object|string $data = [])
    {
        if(!empty($data)) {
            $this->setJson($data);
        }
    }

    /**
     * Getters et Setters
     */

    /**
     * Retourne la valeur de $json
     * 
     * @param bool $format
     *  - Si false, Retourne une chaine de caractère brute
     *  - Si true, Retourne une chaine de caractère structurée
     * 
     * @return string|false
     */
    public function json(bool $format = false): string|false
    {
        return !$format ? json_encode($this->json) : json_encode($this->json, JSON_PRETTY_PRINT);
    }

    /**
     * Modifie la valeur de $json
     * 
     * @param array|object $json
     */
    public function setJson(array|object $json): void
    {
        if(is_array($json)) {
            $this->json = $json;
        }
        
        if(is_object($json)) {
            $this->json = (array) $json;
        }
    }

    /**
     * Méthodes
     */

    /**
     * Décode un chaine json en un tableau associatif
     * Retourne true en cas de succés, sinon false
     * 
     * @param string $json, la chaine json encodée
     * 
     * @return bool
     */
    public function decode(string $json): bool
    {
        $decodedJson = json_decode($json, true);

        if($decodedJson != null) {
            $this->json = $decodedJson;
            return true;
        }

        return false;
    }

    /**
     * Retourne une chaine encodée json
     * 
     * @param bool $format
     *  - Si false, Retourne une chaine de caractère brute
     *  - Si true, Retourne une chaine de caractère structurée
     * 
     * @return string|false
     */
    public function encode(mixed $json, bool $format = false): string|false
    {
        return !$format ? json_encode($json) : json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Retourne l'objet sous forme de chaine de caractère structurée
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->json(true);
    }

    /**
     * Vérifie si une clé existe dans le tableau $json
     * 
     * @param mixed $offset, un index dans le tableau $json
     * 
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->json[$offset]);
    }

    /**
     * Retourne la valeur d'un index dans le tableau $json
     * Retourne null si l'indev du tableau n'existe pas.
     * 
     * @param mixed $offset, un index dans le tableau $json
     * 
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
       return $this->offsetExists($offset) ? $this->json[$offset] : null;
    }

    /**
     * Ajoute ou modifie la valeur d'un index dans le tableau $json
     * 
     * @param mixed $offset, un index dans le tableau $json
     * @param mixed $value, la valeu de l'index $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->json[$offset] = $value;
    }

    /**
     * Supprime une variable dans le tableau $json
     * 
     * @param mixed $offset, un index dans le tableau $json
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->json[$offset]);
    }

    /**
     * Ouvre un fichier en lecture ou en écriture
     * 
     * @param string $filename, $mode
     * 
     * @return mixed
     */
    protected function open(string $filename, AccessMode $mode = AccessMode::Reading): mixed
    {
        if(empty($filename)) {
            throw new \ValueError("Argument #1 (\$filename) ne peut pas être vide");
        }

        if($mode == AccessMode::Reading) {
            if(!is_readable($filename) || !is_file($filename)) {
                throw new \ErrorException("Echec du chargement du fichier $filename", 0, E_USER_ERROR);            
            }
        }

        if(!is_writable(dirname($filename)) && $mode == AccessMode::Writing) {
            throw new \ErrorException("Echec de l'écriture du fichier $filename", 0, E_USER_ERROR);            
        }

        return fopen($filename, $mode->value);
    }

    /**
     * Lit le contenu d'un fichier JSON
     * 
     * @param $filename, le fichier JSON à lire
     */
    public function load(string $filename): bool
    {
        if($handle = $this->open($filename, AccessMode::Reading)) {
            $contends = fread($handle, filesize($filename));
            fclose($handle);
            return $this->decode($contends);
        }

        return false;
    }

    /**
     * Ecrit le contenu d'un fichier JSON
     * 
     *  @param $filename, le fichier JSON à écrire
     */
    public function save($filename): bool
    {
        if($handle = $this->open($filename, AccessMode::Writing)) {
            $return = fwrite($handle, $this);
            fclose($handle);
    
            return true;
        }

        return false;
    }

}
