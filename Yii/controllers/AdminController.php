<?php

/**
 * This controller handles all actions related to admin sections except for
 * posts and user managing,
 *
 * @author Fike Etki <etki@etki.name>
 * @version 0.1.0
 * @since 0.1.0
 * @package blogmvc
 * @subpackage yii
 */
class AdminController extends BaseController
{
    /**
     * Renders main admin menu.
     *
     * @since 0.1.0
     */
    public function actionIndex()
    {
        $user = User::model()->with('postCount', 'commentCount')
                             ->findByPk(Yii::app()->user->id);
        $this->render('index', array('user' => $user));
    }

    /**
     * Renders standard help file.
     *
     * @since 0.1.0
     */
    public function actionHelp()
    {
        $this->renderMd('help', 'application.docs.help');
    }

    /**
     * Renders developers help file.
     *
     * @since 0.1.0
     */
    public function actionDevHelp()
    {
        $this->renderMd('help', 'application.docs.help-dev');
    }

    /**
     * Renders application status.
     *
     * @since 0.1.0
     */
    public function actionStatus()
    {
        /** @var ApplicationService $service */
        $service = \Yii::app()->applicationService;
        $this->render('status', array(
            'statistics' => \ApplicationModel::getStatistics(),
            'status' => $service->getServiceInfo()
        ));
    }

    /**
     * Renders options page and saves passed options.
     *
     * @since 0.1.0
     */
    public function actionOptions()
    {
        $model = new \ApplicationModel;
        if ($data = \Yii::app()->request->getPost('ApplicationModel', false)) {
            $model->save($data); // setAndSave analog, errors fetched in view
        }
        $this->render('options', array('appModel' => $model));
    }

    /**
     * Flushes all cache and redirects user to options page.
     *
     * @since 0.1.0
     */
    public function actionFlushCache($returnUrl=null)
    {
        Yii::app()->cache->flush();
        Yii::app()->user->sendMessage(
            'cache.afterFlush',
            WebUserLayer::FLASH_SUCCESS
        );
        if ($returnUrl !== null) {
            $this->redirect($returnUrl);
        }
        $this->redirect(array('admin/options'));
    }

    /**
     * Recalculates category counters.
     *
     * @since 0.1.0
     */
    public function actionRecalculate()
    {
        \Category::model()->recalculateCounters();
        \Yii::app()->user->sendMessage(
            'category.recalculated',
            WebUserLayer::FLASH_SUCCESS
        );
        \Yii::app()->setGlobalState('lastPostUpdate', time());
        $this->redirect(array('admin/options'));
    }

    /**
     * Defines controller filters.
     *
     * @return array Lsit of filters.
     * @since 0.1.0
     */
    public function filters() {
        return array(
            'accessControl',
        );
    }

    /**
     * Returns access control rules.
     *
     * @return array Set of access control rules.
     * @since 0.1.0
     */
    public function accessRules() {
        return array(
            array('allow', 'users' => array('@'),),
            array('deny',),
        );
    }
}
