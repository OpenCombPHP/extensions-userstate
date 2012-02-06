<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\view\DataExchanger;

use org\jecat\framework\db\ExecuteException;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class Add extends Controller
{
	public function createBeanConfig()
	{
		return array(
		
			// 模型
            'model:state' =>  array( 'conf' => 'model/state' ) ,
			
			// 视图
			'view' => array(
				'template' => 'Add.html' ,
				'model' => 'state' ,
				'class' => 'form' ,
				'widgets' => array(
					array('id'=>'system','class'=>'text','exchange'=>'system')  ,    //exchange 自动交换数据所对应然数据库字段名
					array('id'=>'title_template','class'=>'text','exchange'=>'title_template') ,
					array('id'=>'body_template','class'=>'text','exchange'=>'body_template') ,
					array('id'=>'title_data','class'=>'text','exchange'=>'title_data') ,
					array('id'=>'body_data','class'=>'text','exchange'=>'body_data') ,
				        
				        
					array('id'=>'subject','class'=>'text','exchange'=>'subject') ,
					array('id'=>'summary','class'=>'text','exchange'=>'summary') ,
					array('id'=>'article_title','class'=>'text','exchange'=>'article_title') ,
					array('id'=>'touid','class'=>'text','exchange'=>'touid') ,

				    array('id'=>'type1','class'=>'text') ,
					array('id'=>'url1','class'=>'text') ,
					array('id'=>'link1','class'=>'text') ,
					array('id'=>'type2','class'=>'text') ,
					array('id'=>'url2','class'=>'text') ,
					array('id'=>'link2','class'=>'text') ,
					array('id'=>'type3','class'=>'text') ,
					array('id'=>'url3','class'=>'text') ,
					array('id'=>'link3','class'=>'text') ,
				) ,
			) ,
		) ;
	}
	
	public function process()
	{
	    
	    if( $this->view->isSubmit( $this->params ) )
	    {
	    
	        // 加载 视图窗体的数据
	        $this->view->loadWidgets( $this->params ) ;
	    
	        
	        // 校验 视图窗体的数据
	        if( $this->view->verifyWidgets() )
	        {
	            $this->view->exchangeData(DataExchanger::WIDGET_TO_MODEL) ;
	            
	            $this->state->setData('time',time()) ;
	            
	            $aId = $this->requireLogined() ;
	            $this->state->setData('uid',$aId->userId()) ;
	            
	            try {
	                
                    for($i = 1; $i <= 3; $i++){
                        if($this->params['type'.$i] && $this->params['url'.$i])
                        {
                            $this->state->child("attachments")->createChild()
                            ->setData("stid",$this->params['stid'.$i])
                            ->setData("url",$this->params['url'.$i])
                            ->setData("link",$this->params['link'.$i])
                            ->setData("type",$this->params['type'.$i]) ;
                        }
                    }
	                $this->state->save() ;

	                $this->view->createMessage( Message::success, "注册成功！" ) ;
	    
	                $this->view->hideForm() ;
	    
	            } catch (ExecuteException $e) {
	    
	                if($e->isDuplicate())
	                {
	                    $this->view->createMessage(
	                            Message::error
	                            , "用户名：%s 已经存在"
	                            , $this->params->get('username')
	                    ) ;
	                }
	                else
	                {
	                    throw $e ;
	                }
	            }
	        }
	    }
	}
	
}

?>