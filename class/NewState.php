<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewState extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'view' => array(
				'template' => 'NewState.html' ,
				'model' => 'state' ,
			) ,
            'model:state' => array( 
            		'conf' => 'model/state', 
            		'list'=>true
            		)  ,
		) ;
	}
	
	public function process()
	{
	}
}
?>