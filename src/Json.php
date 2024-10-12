<?php

namespace Blackfox\Json;

class Json implements \ArrayAccess
{

    /**
     * Propriétés
     */

    // Un tableau associatif représentant un code json
    protected array $json;

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
     * @param array|object|string $json
     */
    public function setJson(array|object|string $json): void
    {
        if(is_array($json)) {
            $this->json = $json;
        }
        elseif(is_object($json)) {
            $this->json = (array) $json;
        }
        else {
            $this->json = json_decode($json, true);
        }
    }

    /**
     * Méthodes
     */

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
     * Lit le contenu d'un fichier JSON
     * 
     * @param $file, le fichier JSON à lire
     */
    public function fread($file): void
    {
        if(is_readable($file) && is_file($file)) {
            if($handle = fopen($file, "r")) {
                $contends = fread($handle, filesize($file));
                fclose($handle);
    
                $this->setJson($contends);
            }
        }
        else {
            throw new \Exception("Le fichier $file est introuvable!");
        }
    }

    /**
     * Ecrit le contenu d'un fichier JSON
     * 
     *  @param $file, le fichier JSON à écrire
     */
    public function fwrite($file): int|false
    {
        if(is_writable(dirname($file))) {
            if($handle = fopen($file, "w")) {
                $return = fwrite($handle, $this);
                fclose($handle);
            }
    
            return $return;
        }
        else {
            throw new \Exception("Le dossier " . dirname($file) . " n'est pas accessible en écriture!");    
        }
    }

}
