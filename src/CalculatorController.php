<?php

namespace Devdojo\Calculator;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PharIo\Version\OrVersionConstraintGroup;

class CalculatorController extends Controller
{
    private $excludes = ['migrations', 'password_resets', 'cms_field_configuration', 'cms_tables_configuration'];
    private $dbName;

    public function __construct()
    {
        $this->dbName = env('DB_DATABASE');
    }

    public function index()
    {
        $tables = $this->getTables();
        return view('calculator::dashboard', compact('tables'));
    }

    public function view(Request $request)
    {
        if (!in_array($request->table, $this->getTables())) {
            die('This table does not exist');
        }

        $table = $request->table;
        $tables = $this->getTables();
        $tableName = $request->table;
        //get all columns in the table
        $_columns = $this->getTableColumns($request->table);

        $fields = $this->getConfiguredFields($request->table);
        $columns = array();
       
        $index = 0;
        foreach ($_columns as $column) {
            foreach ($fields as $field) {
                if($column == $field->field_name){
                    $index++;
                    $columns[$index]['column_display_name'] = str_replace("_", " ", ucFirst($field->field_name));
                    $columns[$index]['column_name'] = $field->field_name;
                    $columns[$index]['column_type'] = $field->field_type;
                }
            }
        }

        //get all table
        $results = DB::table($request->table)->get()->toArray();
        //tranform from stf Object to array
        $results = json_decode(json_encode($results), true);

        $foreignFields = json_decode(json_encode(DB::table('cms_field_configuration')->where([
            'table_name' => $table,
            'field_type' => 'foreign'
        ])->get()), true);

        if (sizeOf($foreignFields)) {
            foreach ($results as &$result) {
                foreach ($result as $resultColumn => $resultValues)
                    foreach ($foreignFields as $field) {
                        if ($field['field_name'] == $resultColumn) {
                            $foreignDisplayField = $this->getForeignDisplayField($field['foreign_table']);
                            $result[$resultColumn] = json_decode(json_encode(DB::table($field['foreign_table'])->where('id', $resultValues)->first()), true);
                            $result[$resultColumn] = $result[$resultColumn][$foreignDisplayField];
                        }
                    }
            }
        }

      
        return view('calculator::table', compact('results', 'columns', 'tableName', 'tables', 'table'));
    }

    public function create(Request $request)
    {
        if (!in_array($request->table, $this->getTables())) {
            die('This table does not exist');
        }

        $fields = $this->getConfiguredFields($request->table);
        $tables = $this->getTables();
        $table = $request->table;

        //sets the form action
        $action = "create";

        foreach ($fields as $field) {
            $field->display_name = str_replace("_", " ", ucFirst($field->field_name));
            $field->field_value = null;

            if ($field->field_type == "foreign") {
                $foreign = DB::Table($field->foreign_table)->get()->toArray();
                $field->foreign_values = json_decode(json_encode($foreign), true);
                $field->display_foreign_field = $this->getForeignDisplayField($field->foreign_table);
            }
        }

        $compactArray = compact('fields', 'tables', 'table', 'action');

        //if we are editing a form
        if ($request->route('entry') !== null) {

            $compactArray['action'] = "update";

            $values = DB::table($request->route('table'))
                ->where('id', $request->route('entry'))
                ->get()->toArray()[0];

            $values = json_decode(json_encode($values), true);

            foreach ($fields as $field) {

                $field->field_value = $values[$field->field_name];
            }
            $compactArray['entryId'] = $request->route('entry');
        }


        return view('calculator::form', $compactArray);
    }

    public function store(Request $request)
    {

        if (!in_array($request->table, $this->getTables())) {
            die('This table does not exist, or try hacking again ;). We logged you bitch');
        }

        $validators = $this->generateValidators($request->table);
        $validators['table'] = "";

        $clean = $request->validate($validators);
        $table = $clean['table'];
        unset($clean['table']);
        unset($clean['id']);

        if ($request->has('image')) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();
            request()->image->move(public_path("images/{$table}"), $imageName);
            $clean["image"] = "images/{$table}/{$imageName}";
        }

        DB::table($table)
            ->insert($clean);
        return redirect("/cms/table/{$table}");
    }

    public function update(Request $request)
    {

        if (!in_array($request->table, $this->getTables())) {
            die('This table does not exist, or try hacking again ;). We logged you bitch');
        }

        $validators = $this->generateValidators($request->table);
        $validators['table'] = "required";
        $validators['entry'] = "required|numeric";

        $clean = $request->validate($validators);
        $table = $clean['table'];
        $entry = $clean['entry'];

        unset($clean['table']);
        unset($clean['entry']);

        DB::table($table)
            ->where('id', $entry)
            ->update($clean);

        return redirect("/cms/table/{$table}");
    }


    public function destroy($table, $entryId)
    {
        if (!in_array($table, $this->getTables())) {
            die('This table does not exist, or try hacking again ;). We logged you bitch');
        }

        $entryId = (int) $entryId;

        DB::table($table)->delete($entryId);
        return redirect("/cms/table/{$table}");
    }

    private function getForeignDisplayField($table)
    {
        return DB::table('cms_tables_configuration')->select('foreign_display_field')->where('table', $table)->first()->foreign_display_field;
    }

    private function generateValidators($table)
    {
        $configuration = $this->getConfiguredFields($table);
        $validators = array();
        foreach ($configuration as $config) {
            $conditions = [];
            switch ($config->field_type) {

                case "textfield":
                    array_push($conditions, "max:255");

                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "textarea":

                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "foreign":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    array_push($conditions, "numeric");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "id":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    array_push($conditions, "numeric");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "date":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    // array_push($conditions, "numeric");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "time":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    // array_push($conditions, "numeric");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "datetime":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    // array_push($conditions, "numeric");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "image":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    array_push($conditions, "image|mimes:jpeg,png,jpg,gif,svg|max:2048");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;

                case "checkbox":
                    if ($config->mandatory) {
                        array_push($conditions, "required");
                    }
                    array_push($conditions, "numeric");
                    array_push($conditions, "max:1");

                    $validators[$config->field_name] = implode("|", $conditions);
                    break;
            }
        }

        return $validators;
    }


    //get all the tables that are related to the website itself and not related to CMS or laravel configuration
    private function getTables()
    {
        $tablesStd = DB::select('SHOW TABLES');
        $tablesArray = json_decode(json_encode($tablesStd), true);
        $tables = array();

        foreach ($tablesArray as $table) {
            if (!in_array($table["Tables_in_{$this->dbName}"], $this->excludes)) {
                $tables[] = $table["Tables_in_{$this->dbName}"];
            }
        }
        unset($tablesStd);
        unset($tablesArray);

        return $tables;
    }

    public function configureTables()
    {
        foreach ($this->getTables() as $table) {
            DB::table('cms_tables_configuration')->insert([
                'table' => $table,
                'foreign_display_field' => ''
            ]);
        }
        echo "done";
    }


    //get columns of a table
    public function getTableColumns($table)
    {
        return DB::getSchemaBuilder()->getColumnListing($table);
    }

    public function configureCmsFields()
    {
        $tables = $this->getTables();

        foreach ($tables as $table) {
            foreach ($this->getTableColumns($table) as $column) {
                //check if field is configured
                if (!$this->isFieldConfigured($table, $column) && $column !== 'id') {
                    $guessedType = $this->guessFieldType($column);
                    $dataToPush = array(
                        'table_name' => $table,
                        'field_name' => $column,
                        'field_type' => $guessedType
                    );

                    if ($guessedType == "foreign") {

                        $dataToPush['foreign_table'] = $this->guessForeignTable($column);
                        $dataToPush['foreign_field'] = "id";
                    }

                    DB::table('cms_field_configuration')
                        ->insert($dataToPush);
                }
            }
        }

        echo "Done!";
    }

    //checks if the field exists in cms_field_configuration table
    private function isFieldConfigured($tableName, $fieldName)
    {
        $result = DB::table('cms_field_configuration')->where([
            'table_name' => $tableName,
            'field_name' => $fieldName
        ])->get();

        return $result->count();
    }

    private function guessFieldType($columnName)
    {
        $fieldTypes = array(
            'textfield' => array('name', 'firstname', 'lastname', 'middlename', 'familyname', 'first_name', 'last_name', 'middle_name', 'family_name', 'company', 'class', 'title', 'subtitle', 'type', 'longitude', 'latitude', 'street', 'floor', 'category', 'city', 'country', 'email', 'password'),
            'textarea' => array('description', 'address', 'article'),
            'date' => array('date_of_birth', 'dob'),
            'time' => array('time'),
            'datetime' => array('created_at', 'updated_at'),
            'image' => array('image', 'photo', 'picture', 'image_upload', 'photo_upload', 'slide', 'slider'),
            'checkbox' => array('selected', 'bookmarked', 'favorited', 'favorite', 'show_on_homepage', 'hidden', 'approved')
        );

        if (strpos($columnName, '_id') !== false || strpos($columnName, 'link_') !== false) {
            return 'foreign';
        }

        if (strpos($columnName, 'is_') !== false || in_array($columnName, $fieldTypes['checkbox'])) {
            return 'checkbox';
        }

        foreach ($fieldTypes as $type => $options) {
            if (in_array($columnName, $options)) {
                return $type;
            }
        }
        return 'textfield';
    }

    private function guessForeignTable($foreignField)
    {
        if (strpos($foreignField, '_id') !== false) {
            return str_replace("_id", "", $foreignField) . 's';
        }

        if (strpos($foreignField, 'link_') !== false) {
            return str_replace("link_", "", $foreignField) . 's';
        }

        return null;
    }

    private function getConfiguredFields($table)
    {
        return DB::table('cms_field_configuration')->where('table_name', $table)->get();
    }
}
