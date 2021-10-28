<?php declare(strict_types=1);

namespace App\Models;

abstract class AbstractBaseEntity
{
  /**
   * Constructor
   *
   * @param   mixed $args     Arguments
   */
  public function __construct($args=null)
  {
    if ( is_array($args) ) {
      # Decode from array
      $this->fromArray($args);
    } else
    if ( is_string($args) ) {
      # Decoe from JSON string
      $this->fromJSON($args);
    } else {
      # Just clear properties
      $this->clear();
    }
  }

  /**
   * Clear properties
   *
   * @return self
   */
  public function clear()
  {
    return $this;
  }

  /**
   * Return properties as Array
   *
   * @param   array $fields      Array with fields
   *
   * @return  array              Array with properties
   */
  abstract public function fromArray(array $fields): array;

  /**
   * Return properties as Array
   *
   * @param   array $exclude     Array with fields to exclude from final output
   *
   * @return  array              Array with properties
   */
  abstract public function asArray(array $excluded=[]): array;

  /**
   * Decode a JSON document into the properties
   *
   * @param   string $json
   *
   * @return  array
   */
  public function fromJson(string $json): array
  {
    return $this->fromArray( \json_decode($json,true) );
  }

  /**
   * Return properties as JSON document
   *
   * @param   array $exclude      Array keys to exclude from returned JSON
   * @param   int   $options      JSON encode options. Ex. `\JSON_PRETTY_PRINT | \JSON_NUMERIC_CHECK`
   *
   * @return  string              JSON Document
   */
  public function asJson(array $exclude=[], int $options=0): ?string
  {
    return \json_encode($this->asArray($exclude), $options);
  }
}
