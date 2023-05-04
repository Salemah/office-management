<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/test', function () {
    return "admin_d";
});

Route::get('/kanban', function () {
    return view('admin.project.task.kanban-list');
});

Route::get('lang/change/{id}', [\App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

Route::get('location-test-1', function (\Illuminate\Http\Request $request) {

    $url = 'http://ip-api.com/json/103.230.106.42';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);  //if you need
    curl_close($ch);

    dd($response);
});

Route::get('location', function () {
    return view('location');
});

Route::get('db', function () {
    $DbName = env('DB_DATABASE');
    $get_all_table_query = "SHOW TABLES ";
    $result = DB::select(DB::raw($get_all_table_query));

    $prep = "Tables_in_$DbName";
    foreach ($result as $res) {
        $tables[] = $res->$prep;
    }

    $connect = DB::connection()->getPdo();

    $get_all_table_query = "SHOW TABLES";
    $statement = $connect->prepare($get_all_table_query);
    $statement->execute();
    $result = $statement->fetchAll();

    $output = '';
    foreach ($tables as $table) {
        $show_table_query = "SHOW CREATE TABLE " . $table . "";
        $statement = $connect->prepare($show_table_query);
        $statement->execute();
        $show_table_result = $statement->fetchAll();


        foreach ($show_table_result as $show_table_row) {
            $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
        }
        $select_query = "SELECT * FROM " . $table . "";
        $statement = $connect->prepare($select_query);
        $statement->execute();
        $r = $statement->fetchAll();
        $total_row = $statement->rowCount();

        for ($count = 0; $count < $total_row; $count++) {
            $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
            $table_column_array = array_keys($single_result);
            $table_value_array = array_values($single_result);
            $output .= "\nINSERT INTO $table (";
            $output .= "" . implode(",", $table_column_array) . ") VALUES (";
            $output .= "'" . implode("','", $table_value_array) . "');\n";
        }
    }
    $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
    $file_handle = fopen($file_name, 'w+');
    fwrite($file_handle, $output);
    fclose($file_handle);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    ob_clean();
    flush();
    readfile($file_name);
    unlink($file_name);
});

Route::get('chat', function () {
    return view('real-time.real-time-notification');
});

Route::get('chat-form', function () {
    return view('real-time.form');
});

Route::post('chat-form-submit', function (\Illuminate\Http\Request $request) {

    $name = $request->name;
    $auth = $request->auth_id;
    $message = $request->message;

    event(new \App\Events\MyEvent($name, $auth, $message));

})->name('chat-form-submit');

