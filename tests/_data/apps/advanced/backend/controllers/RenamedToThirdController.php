<?php

namespace backend\controllers;

class RenamedToThirdController extends Controller
{
    public function actionError()
    {
        return 'The duplicate of the standalone action';
    }

    public function actionAction()
    {
        return $this->render('view');
    }

    public function action123()
    {
        return $this->render('view');
    }

    public final function actionAAA123AAA123()
    {
        return $this->render('view');
    }

    protected function actionProtected()
    {
        return $this->render('view');
    }

    protected final function actionProtectedFinal()
    {
        return $this->render('view');
    }

    private function actionPrivate()
    {
        return $this->render('view');
    }

    private final function actionPrivateFinal()
    {
        return $this->render('view');
    }

    public static function actionPublicStatic()
    {
        return __METHOD__;
    }

    public static final function actionPublicStaticFinal()
    {
        return __METHOD__;
    }

    protected static function actionProtectedStatic()
    {
        return __METHOD__;
    }

    protected static final function actionProtectedStaticFinal()
    {
        return __METHOD__;
    }

    private static function actionPrivateStatic()
    {
        return __METHOD__;
    }

    private static final function actionPrivateStaticFinal()
    {
        return __METHOD__;
    }

    public function publicMethod()
    {
        return __METHOD__;
    }

    /*public function actionCommentedAsMultiline()
    {
        return $this->render('view');
    }*/

//    public function actionCommentedBySlash()
//    {
//        return $this->render('view');
//    }

#    public function actionCommentedBySharp()
#    {
#        return $this->render('view');
#    }

}