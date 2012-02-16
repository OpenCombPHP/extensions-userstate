<?php
namespace org\opencomb\userstate ;

use org\opencomb\coresystem\auth\Id;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewState extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'view:newState' => array(
				'template' => 'userstate:NewState.html' ,
				'model' => 'state' ,
			) ,
            'model:state' => array( 
            		'conf' => 'userstate:model/state', 
            		'list'=>true
            		)  ,
			'perms' => array(
				'perm.logined' ,
			) ,
		) ;
	}
	
	public function process()
	{
		$this->checkPermissions('您没有使用这个功能的权限,无法继续浏览',array()) ;
	}
}
?>