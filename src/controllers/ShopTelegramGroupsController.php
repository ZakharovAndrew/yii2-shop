<?php

/**
 * ShopTelegramGroupsController
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\controllers\ParentController;
use ZakharovAndrew\shop\models\ShopTelegramGroups;
use ZakharovAndrew\shop\models\ShopTelegramGroupsSearch;
use ZakharovAndrew\shop\models\ShopToTelegramGroups;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\shop\Module;

/**
 * ShopTelegramGroupsController implements the CRUD actions for ShopTelegramGroups model.
 */
class ShopTelegramGroupsController extends ParentController
{
    /**
     * Lists all ShopTelegramGroups models.
     * 
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShopTelegramGroupsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $stats = $searchModel->getGroupsStats();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * Displays a single ShopTelegramGroups model.
     * 
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Получаем связанные магазины
        $shopsDataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getShops(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'shopsDataProvider' => $shopsDataProvider,
        ]);
    }

    /**
     * Creates a new ShopTelegramGroups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShopTelegramGroups();

        if ($model->load(Yii::$app->request->post())) {
            $model->getParams();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Telegram group created successfully.'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', Module::t('There was an error creating the Telegram group.').var_export($model->getErrors(), true));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShopTelegramGroups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->getParams();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Telegram group updated successfully.'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'There was an error updating the Telegram group.').var_export($model->getErrors(), true));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShopTelegramGroups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Проверяем, есть ли связанные магазины
        $linkedShopsCount = $model->getShops()->count();
        if ($linkedShopsCount > 0) {
            Yii::$app->session->setFlash('error', 
                Yii::t('app', 'Cannot delete Telegram group. It is linked to {count} shops. Please remove all links first.', 
                ['count' => $linkedShopsCount])
            );
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Telegram group deleted successfully.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'There was an error deleting the Telegram group.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Get available groups for shop
     */
    public function actionListAvailable($shop_id, $search = '')
    {
        $shop = Shop::findOne($shop_id);
        if (!$shop) {
            return $this->renderPartial('_telegram_groups_available', ['groups' => []]);
        }

        // Получаем ID уже привязанных групп
        $linkedGroupIds = $shop->getTelegramGroupIds();

        $query = ShopTelegramGroups::find()
            ->where(['is_active' => true])
            ->andWhere(['not in', 'id', $linkedGroupIds]);

        if (!empty($search)) {
            $query->andWhere(['or',
                ['like', 'title', $search],
                ['like', 'telegram_url', $search]
            ]);
        }

        $groups = $query->orderBy(['title' => SORT_ASC])->all();

        return $this->renderPartial('_telegram_groups_available', [
            'groups' => $groups,
            'shop' => $shop
        ]);
    }

    /**
     * Link groups to shop
     */
    public function actionLinkToShop()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $shopId = Yii::$app->request->post('shop_id');
        $groupIds = Yii::$app->request->post('group_ids', []);

        $shop = Shop::findOne($shopId);
        if (!$shop) {
            return ['success' => false, 'message' => 'Shop not found'];
        }

        $successCount = 0;
        foreach ($groupIds as $groupId) {
            if ($shop->addTelegramGroup($groupId)) {
                $successCount++;
            }
        }

        return [
            'success' => true,
            'message' => "Successfully linked {$successCount} groups"
        ];
    }

    /**
     * Unlink group from shop
     */
    public function actionUnlinkFromShop()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $shopId = Yii::$app->request->post('shop_id');
        $groupId = Yii::$app->request->post('group_id');

        $shop = Shop::findOne($shopId);
        if (!$shop) {
            return ['success' => false, 'message' => 'Shop not found'];
        }

        if ($shop->removeTelegramGroup($groupId)) {
            return ['success' => true, 'message' => 'Group unlinked successfully'];
        }

        return ['success' => false, 'message' => 'Error unlinking group'];
    }

    /**
     * Get shop's telegram groups list
     */
    public function actionShopGroups($id)
    {
        $shop = Shop::findOne($id);
        if (!$shop) {
            return '';
        }

        return $this->renderPartial('_telegram_groups_list', ['model' => $shop]);
    }

    /**
     * Toggle active status
     * 
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleActive($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        
        if ($model->save()) {
            $status = $model->is_active ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', 
                Yii::t('app', 'Telegram group {status} successfully.', ['status' => $status])
            );
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'There was an error changing the status.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Link shop to telegram group
     * 
     * @param int $id Telegram group ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLinkShop($id)
    {
        $telegramGroup = $this->findModel($id);
        $model = new ShopToTelegramGroups();
        $model->telegram_group_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Shop linked successfully.'));
            return $this->redirect(['view', 'id' => $id]);
        }

        // Получаем список магазинов, которые еще не связаны с этой группой
        $linkedShopIds = $telegramGroup->getShops()->select('id')->column();
        $availableShops = Shop::find()
            ->where(['not in', 'id', $linkedShopIds])
            ->select(['id', 'name'])
            ->indexBy('id')
            ->column();

        return $this->render('link-shop', [
            'telegramGroup' => $telegramGroup,
            'model' => $model,
            'availableShops' => $availableShops,
        ]);
    }

    /**
     * Unlink shop from telegram group
     * 
     * @param int $id Telegram group ID
     * @param int $shop_id Shop ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUnlinkShop($id, $shop_id)
    {
        $telegramGroup = $this->findModel($id);
        
        $link = ShopToTelegramGroups::find()
            ->where(['telegram_group_id' => $id, 'shop_id' => $shop_id])
            ->one();

        if ($link && $link->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Shop unlinked successfully.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'There was an error unlinking the shop.'));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Get telegram groups for autocomplete
     * 
     * @param string $q Search query
     * @return \yii\web\Response
     */
    public function actionAutocomplete($q = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = ShopTelegramGroups::find()
            ->select(['id', 'title', 'telegram_url'])
            ->where(['is_active' => true]);

        if ($q) {
            $query->andWhere(['or',
                ['like', 'title', $q],
                ['like', 'telegram_url', $q]
            ]);
        }

        $groups = $query->limit(10)->asArray()->all();

        $results = [];
        foreach ($groups as $group) {
            $results[] = [
                'id' => $group['id'],
                'text' => $group['title'] . ' (' . $group['telegram_url'] . ')',
            ];
        }

        return ['results' => $results];
    }

    /**
     * Finds the ShopTelegramGroups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param int $id ID
     * @return ShopTelegramGroups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopTelegramGroups::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested Telegram group does not exist.'));
    }
}