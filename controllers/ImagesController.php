<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.07.2016
 * Time: 11:09
 */
namespace phpnt\cropper\controllers;

use app\models\Photo;
use yii\helpers\Json;
use phpnt\cropper\models\ImageForm;
use yii\web\Controller;

class ImagesController extends Controller
{
    public function actionAutoloadImage()
    {
        $imageData = \Yii::$app->request->post('imageData');
        $modelImageForm = new ImageForm();

        if($imageData['image_id'] == '0' || $imageData['image_id'] == null):
            $modelImageForm->createImage();
        else:
            $modelImageForm->updateImage();
        endif;

        if(\Yii::$app->session->get('error')):
            \Yii::$app->session->set(
                'message',
                [
                    'type'      => 'danger',
                    'icon'      => 'glyphicon glyphicon-envelope',
                    'message'   => \Yii::$app->session->get('error'),
                ]
            );
        endif;

        $imagesObject = $modelImageForm->getPhotosByLabel($label = $imageData['images_label'], $objectId = $imageData['object_id']);

        $render = ($imageData['images_num'] == 1) ? '_image' : '_image-many';

        return $this->render(
            '@app/widgets/ImageLoad/views/'.$render,
            [
                'imagesObject'              => $imagesObject,
                'modelImageForm'            => $modelImageForm,
                'modelName'                 => $imageData['modelName'],
                'id'                        => $imageData['id'],
                'object_id'                 => $imageData['object_id'],
                'images_num'                => $imageData['images_num'],
                'images_label'              => $imageData['images_label'],
                'images_temp'               => $imageData['images_temp'],
                'imageSmallWidth'           => $imageData['imageSmallWidth'],
                'imageSmallHeight'          => $imageData['imageSmallHeight'],
                'createImageText'           => $imageData['createImageText'],
                'updateImageText'           => $imageData['updateImageText'],
                'deleteImageText'           => $imageData['deleteImageText'],
                'frontendUrl'               => $imageData['frontendUrl'],
                'baseUrl'                   => $imageData['baseUrl'],
                'imagePath'                 => $imageData['imagePath'],
                'noImage'                   => $imageData['noImage'],
                'loaderImage'               => $imageData['loaderImage'],
                'backend'                   => $imageData['backend'],
                'imageClass'                => $imageData['imageClass'],
                'buttonDeleteClass'         => $imageData['buttonDeleteClass'],
                'imageContainerClass'       => $imageData['imageContainerClass'],
                'formImagesContainerClass'  => $imageData['formImagesContainerClass'],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionDeleteImage()
    {
        $imageData = Json::decode(\Yii::$app->request->post('imageData'));
        $modelImageForm = new ImageForm();
        $modelImageForm->deleteImage();
        if(\Yii::$app->session->get('error')):
            \Yii::$app->session->set(
                'message',
                [
                    'type'      => 'danger',
                    'icon'      => 'glyphicon glyphicon-envelope',
                    'message'   => \Yii::$app->session->get('error'),
                ]
            );
        endif;

        $imagesObject = $modelImageForm->getPhotosByLabel($label = $imageData['images_label'], $objectId = $imageData['object_id']);

        $render = ($imageData['images_num'] == 1) ? '_image' : '_image-many';

        return $this->render(
            '@app/widgets/ImageLoad/views/'.$render,
            [
                'imagesObject'              => $imagesObject,
                'modelImageForm'            => $modelImageForm,
                'modelName'                 => $imageData['modelName'],
                'id'                        => $imageData['id'],
                'object_id'                 => $imageData['object_id'],
                'images_num'                => $imageData['images_num'],
                'images_label'              => $imageData['images_label'],
                'images_temp'               => $imageData['images_temp'],
                'imageSmallWidth'           => $imageData['imageSmallWidth'],
                'imageSmallHeight'          => $imageData['imageSmallHeight'],
                'createImageText'           => $imageData['createImageText'],
                'updateImageText'           => $imageData['updateImageText'],
                'deleteImageText'           => $imageData['deleteImageText'],
                'frontendUrl'               => $imageData['frontendUrl'],
                'baseUrl'                   => $imageData['baseUrl'],
                'imagePath'                 => $imageData['imagePath'],
                'noImage'                   => $imageData['noImage'],
                'loaderImage'               => $imageData['loaderImage'],
                'backend'                   => $imageData['backend'],
                'imageClass'                => $imageData['imageClass'],
                'buttonDeleteClass'         => $imageData['buttonDeleteClass'],
                'imageContainerClass'       => $imageData['imageContainerClass'],
                'formImagesContainerClass'  => $imageData['formImagesContainerClass'],
            ]
        );
    }

    /**
     * @var $alias string
     * @return string
     */
    public function actionDelete($alias = null)
    {
        $modelImageForm = new ImageForm();
        $photos = $modelImageForm->getDeletedPhotos();
        $photosCount = count($photos);
        $fh = fopen(__FILE__, 'r');
        if(!flock($fh, LOCK_EX | LOCK_NB))
            die('Script blocked');
        foreach($photos as $one) {
            /* @var $one Photo */
            if ($modelImageForm->deleteImageFile($alias, $one->file)) {
                if ($modelImageForm->deleteImageFile($alias, $one->file_small)) {
                    $one->delete();
                }
            }
        }
        fclose($fh);
        echo 'Удалено '.$photosCount.' фотографий.<br>';
    }
}