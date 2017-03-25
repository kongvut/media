<?php
/**
 * Created by PhpStorm.
 * User: hackable
 * Date: 2/18/2017 AD
 * Time: 2:19 PM
 */

namespace app\controllers;

use Yii;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionUploadFile(){
        ini_set('post_max_size', '8M');
        ini_set('upload_max_filesize', '2M');
        header('Access-Control-Allow-Origin: *');
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        // upload.php
        // 'images' refers to your file input name attribute

        //VarDumper::dump($_FILES, 10, true); exit;

        if (empty($_FILES['files'])) {
            echo json_encode(['error'=>'No files found for upload.']);
            // or you can throw an exception
            return; // terminate
        }

        //VarDumper::dump($_FILES['files'], 10, true); exit;

        // a flag to see if everything is ok
        $success = null;

        // file paths to store
        $newFileItems= [];
        $pathAllFile=[];

        $fileItems = $_FILES['files']['name'];
        $newFileItems = [];

        foreach ($fileItems as $i => $fileItem) {
            if($fileItem!=''){
                $extension = end(explode(".", $fileItem));
                $newFileName = 'file_'.($i+1).'_'.date("Ymd_His") . '.' . $extension;
                //chmod(Yii::$app->basePath . '/../backend/web/fileinput/', 0777);
                $fullPath = Yii::$app->basePath . '/web/uploads/' . $newFileName;
                $fieldname = "files[" . $i . "]";
                $file = UploadedFile::getInstanceByName($fieldname);
                if($file->saveAs($fullPath)) {
                    $success = true;
                    $newFileItems[] = $newFileName;
                    $pathAllFile[] = $fullPath;
                } else {
                    $success = false;
                }

            }
        }

        //$model->file_upload = implode(',', $newFileItems);


        // check and process based on successful status
        if ($success === true) {
            // call the function to save all data to database
            // code for the following function `save_data` is not
            // mentioned in this example
            //save_data($userid, $username, $paths);

            // store a successful response (default at least an empty array). You
            // could return any additional response info you need to the plugin for
            // advanced implementations.
            $output = ['result'=>'success'];
            // for example you can get the list of files uploaded this way
            // $output = ['uploaded' => $paths];
        } elseif ($success === false) {
            $output = ['error'=>'Error while uploading images. Contact the system administrator'];
            // delete any uploaded files
            foreach ($pathAllFile as $file) {
                unlink($file);
            }
        } else {
            $output = ['error'=>'No files were processed.'];
        }

        // return a json encoded response for plugin to process successfully
        echo json_encode($output);
    }
}