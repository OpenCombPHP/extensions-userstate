<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class Index extends Controller
{
	public function createBeanConfig()
	{
		return array(
		
			// 模型
            'model:state' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'state' ,
						'hasOne:info' => array(
							'table' => 'coresystem:userinfo'
						) ,
            		) ,
            )  ,
			
			// 视图
			'view' => array(
				'template' => 'State.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
	    $oState = new State();
	    echo "<pre>";print_r($oState->getTitleTemplate("blog"));echo "</pre>";
	    $this->state->load( "0", 'uid' ) ;
	}
	
}

?>