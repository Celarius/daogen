<?php
/**
 * Nofuzz based Controller with basic CRUD operations for table
 */
##############################################################################################################

class Controller
{
  protected $table;
  protected $options;
  protected $namespace;
  protected $package;
  protected $extends;

  /**
   * Constructor
   *
   * @param      <type>  $table    The table
   * @param      array   $options  The options
   */
  public function __construct($table=null, array $options=[])
  {
    $this->table = $table;
    $this->options = $options;
    $this->namespace = $this->formatNamespace($options['namespace'] ?? '');
    $this->package = $options['package'] ?? '[Package]';
    $this->extends = $options['extends'] ?? '';
  }

  /**
   * Formats the Namespace correctly
   *
   * Adds a "\" in front of the namespace if given, empty otherwise
   *
   * @param      string  $namespace  The namespace
   *
   * @return     string
   */
  protected function formatNamespace(string $namespace)
  {
    if (!empty($namespace)) {
      $namespace = '\\' . trim($namespace,'\\/');
    }

    return $namespace;
  }

  /**
   * Output as a PHP Source
   *
   * @return string
   */
  public function getPhpSource()
  {
    global $daoGenVersion;

    $s  = '';
    $s .= "<?php declare(strict_types=1);".PHP_EOL;

    # DocBlock
    $s .= "/** ".PHP_EOL;
    $s .= " * ".$this->table->getClassName().'Controller.php'.PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= " *    Controller for table ".$this->table->getTableName().PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= " *  Generated with DaoGen v".$daoGenVersion.PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= ' * @since     '.(new \DateTime('now',new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z').PHP_EOL;
    $s .= ' * @package   '.$this->package.PHP_EOL;
    $s .= ' * @namespace '.$this->namespace.PHP_EOL;
    $s .= " */".PHP_EOL;
    $s .= "#########################################################################################".PHP_EOL;

    # Generate JSON Model for the table
    $s .= "/*".PHP_EOL;
    $s .= "JSON Model:".PHP_EOL;
    $s .= "{".PHP_EOL;
    $str = '';
    foreach ($this->table->getFields() as $field) {
      $str .= '  "'.$field->getName().'": '.$field->getDefault('json').','.PHP_EOL;
    }
    $s .= rtrim($str,','.PHP_EOL).PHP_EOL;
    $s .= "}".PHP_EOL;
    $s .= "*/".PHP_EOL;
    $s .= "#########################################################################################".PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'namespace App\\Controllers'.$this->namespace.';'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= 'use \\Spin\\Core\\Controller;'.PHP_EOL;
    // Do we custom extend controllers??
    if (!empty($this->extends)) {
      $s .= 'use \\App\\Controllers\\'.$this->extends.';'.PHP_EOL;
    }
    if ($this->table->hasField('uuid')) {
      $s .= PHP_EOL;
      $s .= '# Helpers'.PHP_EOL;
      $s .= 'use \\Spin\\Helpers\\UUID;'.PHP_EOL;
    }
    $s .= PHP_EOL;

    $s .= '# Guzzle'.PHP_EOL;
    $s .= 'use \GuzzleHttp\Psr7\Request;'.PHP_EOL;
    $s .= 'use \GuzzleHttp\Psr7\Response;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '# Entity & Model'.PHP_EOL;
    $s .= 'use \\App\\Models'.$this->namespace.'\\'.$this->table->getClassName().'Entity;'.PHP_EOL;
    $s .= 'use \\App\\Models'.$this->namespace.'\\Db\\'.$this->table->getClassName().'Dao;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= "class ".$this->table->getClassName()."Controller extends ".( !empty($this->extends) ? $this->extends : 'Controller').PHP_EOL;
    $s .= "{".PHP_EOL;

    // # Initialize
    // $s .= ' /**'.PHP_EOL;
    // $s .= '   * Initialize Controller'.PHP_EOL;
    // $s .= '   *'.PHP_EOL;
    // $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    // $s .= '   */'.PHP_EOL;
    // $s .= '  public function initialize(array $args)'.PHP_EOL;
    // $s .= '  {'.PHP_EOL;
    // $s .= '    parent::initialize($args);'.PHP_EOL;
    // $s .= '  }'.PHP_EOL;
    // $s .= PHP_EOL;

    $s .= ' /** @var  array Payload data */'.PHP_EOL;
    $s .= ' protected $body;'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # Verify GET
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Verify GET headers & params'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     null|Response   If a response is returned, this should be sent to caller'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function verifyGET(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    return null;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # GET
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Handle GET requests'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     Response'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function handleGET(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $response = $this->verifyGET($args);'.PHP_EOL;
    $s .= '    if ($response) return $response;'.PHP_EOL;
    $s .= PHP_EOL;
    if ($this->table->hasField('uuid')) {
      $s .= '    $items = null;'.PHP_EOL;
      $s .= '    $par_uuid = $args[\'uuid\'] ?? null;'.PHP_EOL;
      $s .= PHP_EOL;
      $s .= '    if (!empty($par_uuid)) {'.PHP_EOL;
      $s .= '      $item = (new '.$this->table->getClassName().'Dao())->fetchBy(\'uuid\',$par_uuid);'.PHP_EOL;
      $s .= '      if ($item) $items[] = $item;'.PHP_EOL;
      $s .= '    } else {'.PHP_EOL;
      $s .= '      $items = (new '.$this->table->getClassName().'Dao())->fetchAll();'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    $par_code = $args[\'code\'] ?? null;'.PHP_EOL;
      $s .= PHP_EOL;
      $s .= '    if (!empty($par_code)) {'.PHP_EOL;
      $s .= '      $item = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$par_code);'.PHP_EOL;
      $s .= '      if ($item) $items[] = $item;'.PHP_EOL;
      $s .= '    } else {'.PHP_EOL;
      $s .= '      $items = (new '.$this->table->getClassName().'Dao())->fetchAll();'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    } else {
      $s .= '    $items = (new '.$this->table->getClassName().'Dao())->fetchAll();'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    if (is_null($items)) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Not found\',\'\',404);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    $data = [];'.PHP_EOL;
    $s .= '    foreach ($items as $item)'.PHP_EOL;
    $s .= '      $data[] = $item->asArray([\'id\']);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return responseJson($data);'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # Verify POST
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Verify POST headers & body, decodes contents into $this->body'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     null|Response   If a response is returned, this should be sent to caller'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function verifyPOST(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    # Validate HTTP Request "Content-type"'.PHP_EOL;
    $s .= '    if (!preg_match(\'/application\/json/i\',(getRequest()->getHeader(\'Content-Type\')[0] ?? \'\'))) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Bad request\',\'Content-Type must be "application-json"\',400);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    # Decode payload'.PHP_EOL;
    $s .= '    $this->body = (json_decode(getRequest()->getBody()->getContents(),true) ?? []);'.PHP_EOL;
    $s .= '    if (count($this->body)==0) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Bad request\',\'Invalid post body\',400);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    if ($this->table->hasField('code')) {
      $s .= PHP_EOL;
      $s .= '    # Verify code'.PHP_EOL;
      $s .= '    if (empty($this->body[\'code\'] ?? \'\')) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{code} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    if ($this->table->hasField('name')) {
      $s .= PHP_EOL;
      $s .= '    # Verify name'.PHP_EOL;
      $s .= '    if (empty($this->body[\'name\'] ?? \'\')) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{name} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    // .. there are more fields that need verification?'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    return null;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # POST
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Handle POST requests'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     Response'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function handlePOST(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $response = $this->verifyPOST($args);'.PHP_EOL;
    $s .= '    if ($response) return $response;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    # Decode body into Entity'.PHP_EOL;
    $s .= '    $entity = new '.$this->table->getClassName().'Entity($this->body);'.PHP_EOL;
    $s .= PHP_EOL;

    if ($this->table->hasField('uuid')) {
      $s .= '    # Check if a previous the item exists'.PHP_EOL;
      $s .= '    $x = (new '.$this->table->getClassName().'Dao())->fetchBy(\'uuid\',$entity->getUuid());'.PHP_EOL;
      $s .= '    if ( isset($x) ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'An item with {uuid} already exists\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    # Check if a previous the item exists'.PHP_EOL;
      $s .= '    $x = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$entity->getCode());'.PHP_EOL;
      $s .= '    if ( isset($x) ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'An item with {code} already exists\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    $s .= '    // .. there might be other fields that need to be checked?'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    # Save/Insert'.PHP_EOL;
    $s .= '    $ok = (new '.$this->table->getClassName().'Dao())->insert($entity);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    if (!$ok) {'.PHP_EOL;
    $s .= '      logger()->error(\'Failed to insert into database\',[\'rid\'=>container(\'requestId\'), \'entity\'=>$entity->asArray()]);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '      return responseJsonError(\'Failed to insert item\',\'\',500);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return responseJson($entity->asArray([\'id\']));'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;


    #
    # Verify PUT
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Verify PUT headers & body, decodes contents into $this->body'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     null|Response   If a response is returned, this should be sent to caller'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function verifyPUT(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    # Validate HTTP Request "Content-type"'.PHP_EOL;
    $s .= '    if (!preg_match(\'/application\/json/i\',(getRequest()->getHeader(\'Content-Type\')[0] ?? \'\'))) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Bad request\',\'Content-Type must be "application-json"\',400);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    # Decode payload'.PHP_EOL;
    $s .= '    $this->body = (json_decode((string)getRequest()->getBody()->getContents(),true) ?? []);'.PHP_EOL;
    $s .= '    if (count($this->body)==0) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Bad request\',\'Invalid post body\',400);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    if ($this->table->hasField('uuid')) {
      $s .= '    # The uuid we want to update'.PHP_EOL;
      $s .= '    if (empty($args[\'uuid\'] ?? \'\') ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'Query parameter {uuid} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
      $s .= PHP_EOL;
      $s .= '    # Load the existing entity'.PHP_EOL;
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'uuid\',$args[\'uuid\']);'.PHP_EOL;
      $s .= '    if (!$entity) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Not found\',\'Item with {uuid} not found\',404);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    # The code we want to update'.PHP_EOL;
      $s .= '    if (empty($args[\'code\'] ?? \'\') ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'Query parameter {code} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
      $s .= PHP_EOL;
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$args[\'code\']);'.PHP_EOL;
      $s .= '    if (!$entity) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Not found\',\'Item with {code} not found\',404);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    # Verify params'.PHP_EOL;
    if ($this->table->hasField('code')) {
      $s .= '    if (empty($this->body[\'code\'] ?? \'\')) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{code} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    if ($this->table->hasField('name')) {
      $s .= '    if (empty($this->body[\'name\'] ?? \'\')) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{name} must be specified\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    $s .= '    // .. there are more fields that need verification?'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return null;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # PUT
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Handle PUT requests'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     Response'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function handlePUT(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $response = $this->verifyPUT($args);'.PHP_EOL;
    $s .= '    if ($response) return $response;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    # Load the existing entity'.PHP_EOL;
    if ($this->table->hasField('uuid')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'uuid\',$args[\'uuid\']);'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$args[\'code\']);'.PHP_EOL;
    } else
    if ($this->table->hasField('sessionid')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'sessionid\',$args[\'sessionid\']);'.PHP_EOL;
    } else {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'id\',$args[\'id\']);'.PHP_EOL;
    }
    $s .= PHP_EOL;

    if ($this->table->hasField('code')) {
      $s .= '    # Check if new `code` exists (only if different from old)'.PHP_EOL;
      $s .= '    if ( strcasecmp($entity->getCode(), $this->body[\'code\']) != 0) {'.PHP_EOL;
      $s .= '      $x = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$this->body[\'code\']);'.PHP_EOL;
      $s .= '      if ( isset($x) ) {'.PHP_EOL;
      $s .= '        return responseJsonError(\'Bad request\',\'An item with {code} already exists\',400);'.PHP_EOL;
      $s .= '      }'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
      $s .= PHP_EOL;
    }

    $s .= '    # Set Entity properties from $this->body'.PHP_EOL;
    foreach ($this->table->getFields() as $field) {
      if ( strcasecmp($field->getName(),'uuid')==0) {
        $s .= '    // $entity->set'.$field->getUcwName().'($this->body[\''.$field->getName().'\']);'.PHP_EOL;
      } else
      if ( strcasecmp($field->getName(),'id')==0) {
        $s .= '    // $entity->set'.$field->getUcwName().'($this->body[\''.$field->getName().'\']);'.PHP_EOL;
      } else {
        $s .= '    $entity->set'.$field->getUcwName().'($this->body[\''.$field->getName().'\'] ?? $entity->get'.$field->getUcwName().'());'.PHP_EOL;
      }
    }
    $s .= PHP_EOL;

    $s .= '    # Save/Update'.PHP_EOL;
    $s .= '    $ok = (new '.$this->table->getClassName().'Dao())->update($entity);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    if (!$ok) {'.PHP_EOL;
    $s .= '      logger()->error(\'Failed to update in database\',[\'rid\'=>app(\'requestId\'), \'entity\'=>$entity->asArray()]);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '      return responseJsonError(\'Failed to update item\',\'\',500);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return responseJson($entity->asArray([\'id\']));'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;


    #
    # Verify DELETE
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Verify DELETE headers & params'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     null|Response   If a response is returned, this should be sent to caller'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function verifyDELETE(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;

    if ($this->table->hasField('uuid')) {
      $s .= '    if (empty($args[\'uuid\'] ?? null) ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{uuid} is mandatory\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    # Sanity check'.PHP_EOL;
      $s .= '    if (empty($args[\'code\'] ?? null) ) {'.PHP_EOL;
      $s .= '      return responseJsonError(\'Bad request\',\'{code} is mandatory\',400);'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    return null;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    #
    # DELETE
    #
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Handle DELETE requests'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param      array   $args   Path variables as key=value array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return     Response'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function handleDELETE(array $args)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $response = $this->verifyDELETE($args);'.PHP_EOL;
    $s .= '    if ($response) return $response;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    # Load the Entity to delete'.PHP_EOL;
    if ($this->table->hasField('id')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'id\',$args[\'id\']);'.PHP_EOL;
    } else
    if ($this->table->hasField('uuid')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'uuid\',$args[\'uuid\']);'.PHP_EOL;
    } else
    if ($this->table->hasField('code')) {
      $s .= '    $entity = (new '.$this->table->getClassName().'Dao())->fetchBy(\'code\',$args[\'code\']);'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    # Report error if no entity found'.PHP_EOL;
    $s .= '    if (!$entity) {'.PHP_EOL;
    $s .= '      return responseJsonError(\'Not found\',\'\',404);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    # Delete'.PHP_EOL;
    $s .= '    $ok = (new '.$this->table->getClassName().'Dao())->delete($entity);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    if (!$ok) {'.PHP_EOL;
    $s .= '      logger()->error(\'Failed to delete in database\', [\'rid\'=>app(\'requestId\'), \'entity\'=>$entity->asArray()]);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '      return responseJsonError(\'Internal error\',\'Failed to delete entity\',500);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return response(\'\',204);'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '}'.PHP_EOL;

    $s .= PHP_EOL;

    return $s;
  }

}
