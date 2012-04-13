<?php
namespace org\opencomb\userstate ;

use com\wonei\woneibridge\aspect\NamecardAspect;

use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class At extends Controller
{
	public function createBeanConfig()
	{
	    $aId = IdManager::singleton()->currentId() ;
	    $aOrm = array(
		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:at' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state_at' ,
            			'hasOne:state' => array(
            				'table' => 'userstate:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
            			) ,  
            			'hasOne:info' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
                            //'columns' => '*' ,        
            			) ,  
            			'where' => array( 'uid = @1',$aId->userId() ) ,
            		) ,
                    'list'=>true,
            ) ,
	            
			// 视图
			'view' => array(
				'template' => 'userstate:At.html' ,
				'model' => 'at' ,
			) ,
		) ;
	    
	    return  $aOrm;
	}
	
	public function process()
	{
	    $this->at->load() ;
	}
	
}
