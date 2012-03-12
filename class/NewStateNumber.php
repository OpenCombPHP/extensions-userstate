<?php
namespace org\opencomb\userstate ;

use org\opencomb\oauth\api\PullState;

use org\jecat\framework\bean\BeanFactory;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewStateNumber extends Controller
{
	public function createBeanConfig()
	{
		return array(
		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:state' => array( 'conf' => 'model/state', 'list'=>true)  ,
			
		) ;
	}
	
	public function process()
	{
        $this->state->prototype()->criteria()->addOrderBy('time',true);
        $this->state->prototype()->criteria()->where()->gt('time',$this->params['time']);
        
	    $this->state->load() ;
	    echo $this->state->childrenCount();exit;
	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     */
	}
	
}

?>