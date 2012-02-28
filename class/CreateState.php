<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\lang\Exception;

use org\jecat\framework\mvc\controller\Request;

use org\opencomb\coresystem\auth\Id;

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
            'model:state' =>  array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'state' ,
            	        'keys'=>array('stid') ,
            	        'alias' => array(
            	                'info.nickname' => 'nickname' ,
            	        ),
            			'hasOne:info' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
            			) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_attachment',
                		),
                		'hasMany:at'=>array(    //一对多
                				'fromkeys'=>'stid',
        	                    'keys'=>array('stid',"username") ,
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_at',
                		),
                		'hasMany:tag'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_tag',
                		),
            		) ,
            ) ,
		        
	        'model:user' => array(
	                'orm' => array(
	                        'table' => 'coresystem:user' ,
	                ) ,
	        ) ,
		        
			// 视图
			'view:stateForm' => array(
				'template' => 'userstate:CreateState.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
		//只是显示表单
		if(!$this->params['title'] && !$this->params['body'] && !$this->params['attachment']){
			return;
		}
		
		//用户提交来的表单
		if( Request::isUserRequest($this->params) ){
			$this->state->setData('system',NULL) ;  //防止作弊
			$this->state->setData('uid',IdManager::singleton()->currentId()->userId()) ;
			$this->state->setData('time',time()) ;
			$this->state->setData("body",$this->params['body']);
		}else{ //系统内部直接保存数据
			$this->state->setData("forwardtid",$this->params['forwardtid']);
			$this->state->setData("stid",$this->params['stid']);
			$this->state->setData('system',$this->params['system']) ;
			if($this->params->has('uid')){
				$this->state->setData('uid',$this->params['uid']) ;
			}else{
				throw new Exception('没有指定uid,无法保存数据');
			}
			$this->state->setData("title",$this->params['title']);
			$this->state->setData("body",$this->params['body']);
			if($this->params['time']){
				$this->state->setData('time',$this->params['time']) ;
			}else{
				$this->state->setData('time',time()) ;
			}
			$this->state->setData("data",$this->params['data']);
			$this->state->setData("client",$this->params['client']);
			$this->state->setData("client_url",$this->params['client_url']);
			
			for($i = 0; $i < count($this->params['attachment']); $i++){
				$this->state->child("attachments")->createChild()
				->setData("url",$this->params['attachment'][$i]['url'])
				->setData("link",@$this->params['attachment'][$i]['link'])
				->setData("type",$this->params['attachment'][$i]['type'])
				->setData("thumbnail_pic",$this->params['attachment'][$i]['thumbnail_pic'])
				->setData("title",@$this->params['attachment'][$i]['title']) ;
			}
		}
		
		
		//at
		$title = $this->params['title'];
		//$title = "@sf333 sfef@aarongao gr @gggggg sss @wss @aa<sss @dfe:ssef @sas";
		preg_match_all("/@(.*?)[ |:|<]|@(.*)$/", $title, $aTitle);
		
		$aTitle = array_filter(array_merge($aTitle[1],$aTitle[2]));
	    
        foreach ($aTitle as $v){
    
            $uid = 0;
            //测试用户是否存在
            
            $this->user->load($this->params['service']."#".trim($v),'username') ;
            
            if($this->user->uid)
            {
                $uid = $this->user->uid;
            }
    
            $this->state->child("at")->createChild()
            ->setData("username",trim($v))
            ->setData("uid",$uid) ;
	        }
		
		
		//tag
		//$title = "#sssssssssssssss# wfef#给w#wefss sd,we wef #fff#";
		preg_match_all("/#(.*?)#/", $title, $aTag);
		
		if(!empty($aTag[1]))
		{
		    foreach ($aTag[1] as $v){
		
		        $this->state->child("tag")->createChild()
		        ->setData("title",trim($v));
		    }
		}
		
		
        try{
            
			$this->state->save(true);
			
        }catch (ExecuteException $e)
        {
			if($e->isDuplicate())
            {
            }
        }
        
        return $this->state->stid;
	}
}

?>