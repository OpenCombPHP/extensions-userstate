<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\bean\BeanFactory;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class Index extends Controller
{
	public function createBeanConfig()
	{
		return array(
		
    		/**
    		 * 模型
    		 * 
    		 * list = true 返回多条记录
    		 * 
    		 */
            'model:state' => array( 'conf' => 'model/state', 'list'=>true)  ,
			
			// 视图
			'view' => array(
				'template' => 'Index.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
	    $oState = new State();
	    
	    $this->state->load() ;
	    foreach($this->state->childIterator() as $o)
	    {
	        $o->setData("title_html",$oState->getStateHtml($o->title_template,json_decode($o->title_data,true)));
	        $o->setData("body_html",$oState->getStateHtml($o->body_template,json_decode($o->body_data,true)));
	    }
	    //打印model
	    //$this->state->printStruct();
	}
	
}

?>