<?php
namespace org\opencomb\userstate ;

use org\opencomb\coresystem\mvc\controller\UserSpace;

use com\wonei\woneibridge\aspect\NamecardAspect;
use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class UpdateForwardNumber extends UserSpace
{
	public function createBeanConfig()
	{
        
	    $aOrm = array(
	    		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:state' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("stid","forwardcount") ,     
            		) ,
            ) ,
            'model:astate' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'oauth:state' ,
	                    'keys'=>array('stid'),
                        'columns' => array("stid","forwardcount") ,     
            		) ,
            ) ,
	            
		) ;
	    return  $aOrm;
	}
	
	public function process()
	{
	    $stid = $this->params["stid"];
	    $service = $this->params["service"];
	    
	    if($service == "wownei.com")
	    {
	        $this->state->loadSql("`stid` = @1 " , $this->params["stid"]);
	        echo $this->state->forwardcount;
	    }
	    
	    if($service == "")
	    {
	        /*$this->state->loadSql("`stid` = @1 " , $this->params["stid"]);
	        $num =  $this->state->forwardcount;
	        
	        $this->astate->loadSql("`stid` = @1 " , $this->params["stid"]);
	        foreach($this->astate->childIterator() as $k => $o){
	            $num = $num + $v->forwardcount;
	        }
	        echo $num;*/
	    }
	}
}
