<?php
abstract class Model
{
    use HasAssociations;
    use HasValidations;
    use HasCallbacks;

    protected bool $modified_field_tracking = false;

    protected array $modified_fields = [];

    protected function __construct()
    {
        $this->setup();
    }

    public function __set($var, $val) {
        $this->{$var} = $val;
        if ($this->modified_field_tracking) { $this->modified_fields[$var] = $val;}
    }

    protected function setup() {}

    protected function pause_modified_field_tracking() { $this->modified_field_tracking = false; }
    protected function resume_modified_field_tracking() {$this->modified_field_tracking = true; }


    /**
     * @return string
     * Gets the table name for this model.
     */
    public static function table_name(): string
    {
        return strtolower(get_called_class()) . "s";
    }

    /**
     * @return string
     * Gets the name of the model.
     */
    public static function model_name(): string
    {
        return get_called_class();
    }

    /**
     * @return array
     */
    public static function get_attributes(): array
    {
        $query = "SELECT COLUMN_NAME AS cn FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".self::table_name()."'";
        $result = App::$db->query($query);
        $fields = [];
        foreach ($result->fetchAll(mode: PDO::FETCH_ASSOC) as $row)
        {
            $field = $row['cn'];
            $fields[] = $field;
        }
        return $fields;
    }

    // TODO: Fix return types and error handling
    public function save()
    {
        $values = $this->modified_fields;

        if (empty($this->id))
        {
            $result = App::$db->insert(table: self::table_name(), values: $values);
            $this->id = App::$db->connection->lastInsertId();
            return true;
        }

        $result = App::$db->update(table: self::table_name(), fields: array_keys($values), values: $values, where_conditions: array("id=$this->id"));
        return true;
    }

    /**
     * @param array $fields
     * @return void
     */
    public function update(array $fields) {

    }

    /**
     * @param mixed $value
     * @param string $attribute
     * @return mixed|null
     */
    public static function find(mixed $value, string $attribute = "id")
    {
        if (is_array($value)) {
            return null;
        }

        $result = App::$db->select(table: self::table_name(), substitutions: array("val" => $value),
            where_conditions: array("$attribute=:val"), limit: 1);
        $class_name = self::model_name();
        $obj = new $class_name();
        foreach ($result->fetch(mode: PDO::FETCH_ASSOC) as $column => $value) {
            $obj->{$column} = $value;
        }
        return $obj;
    }


    /**
     * @param array $attributes
     * @return mixed
     */
    public static function create(array $attributes)
    {
        // Instantiate class
        $class_name = self::model_name();
        $class = new $class_name();

        $class->pause_modified_field_tracking();
        foreach ($attributes as $attr => $value) {
            $class->{$attr} = $value;
        }
        $class->run_validations();
        if (empty($class->errors)) {
            $class->run_before_create();
            $class->run_before_save();
            $result = App::$db->insert(table: self::table_name(), values: $attributes);
            $class->id = App::$db->connection->lastInsertId();
            $class->run_after_save();
            $class->run_after_create();
        }
        $class->resume_modified_field_tracking();
        return $class;
    }


    private static function make_proxy() { return new CollectionProxy(table_name: self::table_name(), model_name: self::model_name()); }
    public static function where(array $conditions, array $values) { return self::make_proxy()->where($conditions, $values); }
    public static function group(string $group) { return self::make_proxy()->group($group); }
    public static function order(string $order) { return self::make_proxy()->order($order); }
    public static function limit(int $limit) { return self::make_proxy()->limit($limit); }
    public static function includes($includes) { return self::make_proxy()->includes($includes); }
    public static function pluck(array $fields) { return self::make_proxy()->pluck($fields); }
    public static function count() { return self::make_proxy()->count(); }
    public static function all() { return self::make_proxy()->all; }
}
?>