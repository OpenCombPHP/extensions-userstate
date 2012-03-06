<?php
namespace org\opencomb\userstate ;

use com\wonei\woneibridge\aspect\NamecardAspect;

use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class ListTag extends Controller
{
	public function createBeanConfig()
	{
	    $aOrm = array(
		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:tag' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state_tag' ,
            			'hasOne:state' => array(
            				'table' => 'userstate:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
            			) ,  
            		) ,
                    'list'=>true,
            ) ,
	            
			// 视图
			'view' => array(
				'template' => 'userstate:ListTag.html' ,
				'model' => 'tag' ,
			) ,
		) ;
	    
	    return  $aOrm;
	}
	
	public function process()
	{
	    $this->tag->load() ;
	}
	
}
