<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\view\DataExchanger;

use org\jecat\framework\db\ExecuteException;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class CreateState extends Controller
{
	public function createBeanConfig()
	{
		return array(
		
			// 模型
            'model:state' =>  array( 'conf' => 'model/state' ) ,
			
			// 视图
			'view' => array(
				'template' => 'CreateState.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
	    
	    $this->state->setData("fstid",$this->params['fstid']);
	    $this->state->setData("system",$this->params['system']);
	    $this->state->setData("uid",$this->params['uid']);
	    $this->state->setData("subject",$this->params['subject']);
	    $this->state->setData("summary",$this->params['summary']);
        $this->state->setData('time',$this->params['time']) ;
	    $this->state->setData("data",$this->params['data']);
	    $this->state->setData("client",$this->params['client']);
	    $this->state->setData("client_url",$this->params['client_url']);
	    
        
        for($i = 0; $i < count($this->params['attachment']); $i++){
            $this->state->child("attachments")->createChild()
                ->setData("url",$this->params['attachment'][$i]['url'])
                ->setData("link",@$this->params['attachment'][$i]['link'])
                ->setData("type",$this->params['attachment'][$i]['type'])
                ->setData("title",@$this->params['attachment'][$i]['title']) ;
        }
        $this->state->save() ;
        
        return $this->state->stid;

	}
	
}

?>