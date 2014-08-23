<?php

class SiteController extends Controller
{
    public function filters()
    {
        return array('accessControl',);
    }

    public function accessRules()
    {
       return array(
           array('allow', // allow all users to perform 'index' and'view' actions
               'roles'=>array('manage_admin'),
           ),
           array('deny', // deny all users
               'users'=>array('*'),
           ),
         );
    }
	
	/**
	 * 跳到管理页
	 */
	public function actionIndex()
	{
		$this->redirect("/accesscontrol/srbac/");
	}
	
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex2()
	{
		$auth = Yii::app()->authManager;
		$model = $auth->getAuthItems();
        $roles = Yii::app()->authManager->getRoles();
        $tasks = array();
		$tasks = yii::app()->authManager->getTasks();
        foreach($roles as $r=>$values)
        {
			$item[$r] = array();
            $temp = yii::app()->authManager->getItemChildren($r);
			$ts = $tasks;
            foreach($temp as $t=>$value)
            {
				if($tasks[$t])
				{
					unset($ts[$t]);
				}
				$item[$r]['tasks'][] = $t;
        	}
			$item[$r]['addItem'] = array_keys($ts);
        }
		$this->renderSmarty('admin/accesscontrol/view.html',array('item'=>$item));
	}
	
	//添加子节点
	public function actionAddChild()
	{
		$auth = Yii::app()->authManager;
		$auth->addItemChild($_GET['parent'],$_GET['child']);
		echo 'ok<br />';
		echo '<a href="/accesscontrol/">back</a>';
	}
	//删除父子节点的关系
	public function actionRemoveChild()
	{
		$auth = Yii::app()->authManager;
		if($auth->removeItemChild($_GET['parent'],$_GET['child']))
		{
			echo 'ok<br />';
			echo '<a href="/accesscontrol/">back</a>';
		}
		else
		{
			echo 'fail';
		}
	}
	//获得子节点
	public function actionGetItemChildren()
	{
		$auth = Yii::app()->authManager;
		if($auth->getItemChildren($_GET['item']))
		{
			foreach($auth->getItemChildren($_GET['item']) as $key=>$value)
			{
				echo '<a href="/accesscontrol/site/getItemChildren?item='.$key.'">'.$key.'</a><br />';
			}
		}
		else
		{
			echo 'none';
		}
	}
	//添加项目
	public function actionAddItem()
	{
		$auth = Yii::app()->authManager;
		$auth->createAuthItem($_GET['item'],$_GET['type'],$_GET['description']='');
		echo 'ok<br />';
		echo '<a href="/accesscontrol/">back</a>';
	}
	//删除项目
	public function actionRemoveItem()
	{
		$auth = Yii::app()->authManager;
		$auth->removeAuthItem($_GET['item']);
		echo 'ok<br />';
		echo '<a href="/accesscontrol/">back</a>';
	}
	//授权
	public function actionAssign()
	{
		$auth = Yii::app()->authManager;
		if($auth->isAssigned($_GET['item'],$_GET['userid']))
		{
			echo '此用户几经被授权此项目';
		}
		else
		{
			$auth->assign($_GET['item'],$_GET['userid']);
			echo 'ok<br />';
			echo '<a href="/accesscontrol/site/assignUser?uid='.$_GET[userid].'">back</a>';
		}
	}
	//取消授权
	public function actionRevoke()
	{
		$auth = Yii::app()->authManager;
		if($auth->isAssigned($_GET['item'],$_GET['userid']))
		{
			$auth->revoke($_GET['item'],$_GET['userid']);
			echo 'ok<br />';
			echo '<a href="/accesscontrol/site/assignUser?uid='.$_GET[userid].'">back</a>';
		}
		else
		{
			echo '没有此用户<br />';
			echo '<a href="/superinfo/">back</a>';
		}
	}
	public function actionAssignUser($uid)
	{
		$auth = Yii::app()->authManager;
        $model = $auth->getAuthItems();
        $rolesall = Yii::app()->authManager->getRoles();
        $roles = Yii::app()->authManager->getRoles($uid);
        foreach($roles as $r=>$values)
        {
            $item[$r] = array();
            $temp = yii::app()->authManager->getItemChildren($r);
            foreach($temp as $t=>$value)
            {
                $item[$r][] = $t;
            }
        }
		foreach($rolesall as $r=>$value)
		{
			if(!$item[$r])
			{
				$addroles[] = $r;
			}
		}
		$user = UserRecord::model()->find('userid=?', array($uid));
        $this->renderSmarty('admin/accesscontrol/assignuser.html',array('item'=>$item,'addroles'=>$addroles,'uid'=>$uid,'username'=>$user['username']));
	}

	public function actionlogout()
	{
		Yii::app()->user->logout();//与Yii::app()->user->logout函数相对，注销session
		$this->redirect('/');
	}
}
