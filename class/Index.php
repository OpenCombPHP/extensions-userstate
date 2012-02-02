<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
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
    		 * list = true 返回多条记录
    		 */
            'model:state' => array( 'conf' => 'model/state', 'list'=>true)  ,
			
		    /**
		     * frame
		     * frameview 子视图。子视图包含父视图。
		     */
		    'frame' => array(
		         
	            /**
	             * 'params' => array('pageNum'=>'2'),
	             * 控制器传参数
	             */
		         'params' => array('pageNum'=>'30'),
		         'frameview' => array(
                    'template' => 'Index.html' ,
	                 /**
	                  * 给视图变量
	                  * 'vars' => array('pageNum'=>'2'), 
	                  */
		          ) 
		     ) ,
		        
			// 视图
			'view' => array(
				'template' => 'StatusFrame.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
	    /**
	     * @wiki /CoreSystem
	     * 用户系统
	     */
	    /**
	     * @example 获得登陆信息（未登陆自动跳转到登陆界面）:name[1]
	     * @forwiki /CoreSystem
	     * 获得登陆信息（未登陆自动跳转到登陆界面）
	     */
	    {
	    $aId = $this->requireLogined() ;
	    }
	    
	    /**
	     * @example 获得登陆信息:name[1]
	     * @forwiki /CoreSystem
	     * 获得登陆信息
	     */
	    {
	        //$aId = IdManager::singleton()->currentId() ;
	    }
	    
	    $oState = new State();
	    
	    /**
 	     * @example sql操作
	     * @forwiki /mvc/模型/模型(Model)
	     */
	    {
        $this->state->prototype()->criteria()->addOrderBy('time',true);
        $this->state->prototype()->criteria()->where()->eq('uid',$aId->userId());
	    }
        if($this->params["system"])
        {
            $this->state->prototype()->criteria()->where()->eq('system',$this->params["system"]);
        }
        if($this->params["sex"])
        {
            $this->state->prototype()->criteria()->where()->eq('info.sex',$this->params["sex"]);
        }
        
        
        $this->state->prototype()->criteria()->setLimit($this->params['limitlen']?$this->params['limitlen']:$this->frame()->params()->get("pageNum"),$this->params['limitfrom']?$this->params['limitfrom']:0);
	        
	    $this->state->load() ;
	    foreach($this->state->childIterator() as $o)
	    {
	        $o->setData("title_html",$oState->getStateHtml($o->title_template,json_decode($o->title_data,true)));
	        $o->setData("body_html",$oState->getStateHtml($o->body_template,json_decode($o->body_data,true)));
	    }

	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     * 
	     * 打印sql
	     * DB::singleton()->executeLog() ;
	     */
	}
	
}

?>