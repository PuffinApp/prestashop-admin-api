
<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class ImageController extends RestController {

    public function processPost() {
        if (!$this->key->can("images", "POST")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $response = null;

        $idProduct = Tools::getValue('id_product');

        $product = new Product((int) $idProduct);
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array) $legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = [];
            $files[0]['error'] = $this->trans('Cannot add image because product creation failed.', [], 'Admin.Catalog.Notification');
        }

        $image_uploader = new HelperImageUploader("file");
        $image_uploader->setAcceptTypes(['jpeg', 'gif', 'png', 'jpg']);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int) ($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;

            foreach ($legends as $key => $legend) {
                if (!empty($legend)) {
                    $image->legend[(int) $key] = $legend;
                }
            }

            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = $validate;
            }

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            if (!$image->add()) {
                $file['error'] = $this->trans('Error while creating additional image', [], 'Admin.Catalog.Notification');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = $this->trans('An error occurred while attempting to create a new folder.', [], 'Admin.Notifications.Error');

                    continue;
                }

                $error = 0;

                if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST:
                            $file['error'] = $this->trans('An error occurred while copying image, the file does not exist anymore.', [], 'Admin.Catalog.Notification');

                            break;

                        case ImageManager::ERROR_FILE_WIDTH:
                            $file['error'] = $this->trans('An error occurred while copying image, the file width is 0px.', [], 'Admin.Catalog.Notification');

                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT:
                            $file['error'] = $this->trans('An error occurred while copying image, check your memory limit.', [], 'Admin.Catalog.Notification');

                            break;

                        default:
                            $file['error'] = $this->trans('An error occurred while copying the image.', [], 'Admin.Catalog.Notification');

                            break;
                    }

                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            $file['error'] = $this->trans('An error occurred while copying this image:', [], 'Admin.Notifications.Error') . ' ' . stripslashes($imageType['name']);

                            continue;
                        }

                        if ($generate_hight_dpi_images) {
                            if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '2x.' . $image->image_format, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2, $image->image_format)) {
                                $file['error'] = $this->trans('An error occurred while copying this image:', [], 'Admin.Notifications.Error') . ' ' . stripslashes($imageType['name']);

                                continue;
                            }
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $product->id]);

                if (!$image->update()) {
                    $file['error'] = $this->trans('Error while updating the status.', [], 'Admin.Notifications.Error');

                    continue;
                }

                // Associate image to shop from context
                $shops = Shop::getContextListShopID();
                $image->associateTo($shops);
                $json_shops = [];

                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }

                $file['status'] = 'ok';
                $file['id'] = $image->id;
                $file['position'] = $image->position;
                $file['cover'] = $image->cover;
                $file['legend'] = $image->legend;
                $file['path'] = $image->getExistingImgPath();
                $file['shops'] = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $product->id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_' . $this->context->shop->id . '.jpg');
            }
        }
        
        if (!empty($files) && array_key_exists("error", $files[0]) && $files[0]["error"]) {
            return $this->error(
                "",
                409,
                ""
            );
        }

        $response = [
            $image_uploader->getName() => $files
        ];

        $this->success(
            "OK",
            "200",
            $response
        );
    }

    public function processDelete() {
        // Delete the image by using its ID
    }

}